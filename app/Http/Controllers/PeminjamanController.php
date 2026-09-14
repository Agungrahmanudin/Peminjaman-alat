<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Pengembalian;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Kategori;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Admin melihat semua, user hanya melihat miliknya
        if (Auth::user()->role === 'admin') {
            $peminjaman = Peminjaman::with(['user', 'detailPeminjaman.alat.kategori'])
                ->latest()
                ->paginate(10);

            // Stats untuk admin dashboard
            $stats = $this->getDashboardStats();
        } else {
            $peminjaman = Peminjaman::with(['user', 'detailPeminjaman.alat.kategori'])
                ->where('user_id', Auth::id())
                ->latest()
                ->paginate(10);
            $stats = null;
        }

        return view('peminjaman.index', compact('peminjaman', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Paginate alat yang tersedia
        $alat_list = Alat::where('jumlah_tersedia', '>', 0)
            ->with('kategori')
            ->orderBy('kategori_id')
            ->orderBy('nama_alat')
            ->paginate(5);

        return view('peminjaman.create', compact('alat_list'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'keperluan' => 'required|string|max:500',
            'tanggal_kembali_rencana' => 'required|date|after_or_equal:today',
            'alat_id' => 'required|array|min:1',
            'alat_id.*' => 'exists:alat,id',
            'jumlah_pinjam' => 'required|array|min:1',
            'jumlah_pinjam.*' => 'required|integer|min:1',
            'kondisi_pinjam' => 'required|array|min:1',
            'kondisi_pinjam.*' => 'required|in:baik,rusak,perbaikan',
        ]);

        // Validasi jumlah array harus sama
        if (
            count($request->alat_id) !== count($request->jumlah_pinjam) ||
            count($request->alat_id) !== count($request->kondisi_pinjam)
        ) {
            return redirect()->back()
                ->with('error', 'Data alat tidak valid')
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Validasi stok untuk setiap alat
            foreach ($request->alat_id as $index => $alatId) {
                $alat = Alat::findOrFail($alatId);
                $jumlahPinjam = $request->jumlah_pinjam[$index];

                if ($alat->jumlah_tersedia < $jumlahPinjam) {
                    throw new \Exception("Stok {$alat->nama_alat} tidak mencukupi. Stok tersedia: {$alat->jumlah_tersedia}, yang dipinjam: {$jumlahPinjam}");
                }
            }

            // Buat peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' => Auth::id(),
                'tanggal_pinjam' => now(),
                'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
                'status' => 'menunggu',
                'status_approval' => 'menunggu',
                'keperluan' => $request->keperluan,
            ]);

            // Proses setiap alat yang dipinjam
            foreach ($request->alat_id as $index => $alatId) {
                $alat = Alat::findOrFail($alatId);
                $jumlahPinjam = $request->jumlah_pinjam[$index];
                $kondisiPinjam = $request->kondisi_pinjam[$index];

                // Kurangi stok alat
                $alat = Alat::findOrFail($alatId);

                if ($alat->jumlah_tersedia < $jumlahPinjam) {
                    throw new \Exception("Stok {$alat->nama_alat} tidak cukup");
                }

                $alat->decrement('jumlah_tersedia', $jumlahPinjam);

                // Buat detail peminjaman
                DetailPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah_pinjam' => $jumlahPinjam,
                    'kondisi_pinjam' => $kondisiPinjam,
                ]);
            }

            // Catat aktivitas
            Activity::create([
                'user_id' => Auth::id(),
                'activity' => 'Melakukan peminjaman alat - ID: ' . $peminjaman->id
            ]);

            DB::commit();

            return redirect()->route('peminjaman.show', $peminjaman->id)
                ->with('success', 'Peminjaman berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal membuat peminjaman: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Peminjaman $peminjaman)
    {
        // Cek otorisasi manual (tanpa policy)
        if (Auth::user()->role !== 'admin' && $peminjaman->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $peminjaman->load(['user', 'detailPeminjaman.alat.kategori', 'pengembalian']);

        // Hitung denda jika terlambat
        $denda = 0;
        $terlambatHari = 0;

        if ($peminjaman->status === 'dipinjam' && $peminjaman->tanggal_kembali_rencana < now()) {
            $terlambatHari = now()->diffInDays($peminjaman->tanggal_kembali_rencana);
            $denda = $terlambatHari * 50000;
        }

        return view('peminjaman.show', compact('peminjaman', 'denda', 'terlambatHari'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Peminjaman $peminjaman)
    {
        // Cek otorisasi manual (tanpa policy)
        if (Auth::user()->role !== 'admin' && $peminjaman->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Hanya bisa edit jika status masih dipinjam
        if ($peminjaman->status !== 'dipinjam') {
            return redirect()->route('peminjaman.index')
                ->with('error', 'Peminjaman yang sudah dikembalikan tidak dapat diedit');
        }

        // Ambil alat yang tersedia + alat yang sudah dipinjam (untuk edit)
        $alat_list = Alat::where('jumlah_tersedia', '>', 0)
            ->orWhereHas('detailPeminjaman', function ($query) use ($peminjaman) {
                $query->where('peminjaman_id', $peminjaman->id);
            })
            ->with('kategori')
            ->orderBy('kategori_id')
            ->orderBy('nama_alat')
            ->get();

        $peminjaman->load('detailPeminjaman.alat');

        return view('peminjaman.edit', compact('peminjaman', 'alat_list'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Peminjaman $peminjaman)
    {
        // Cek otorisasi manual (tanpa policy)
        if (Auth::user()->role !== 'admin' && $peminjaman->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Hanya bisa update jika status masih dipinjam
        if ($peminjaman->status !== 'dipinjam') {
            return redirect()->back()
                ->with('error', 'Peminjaman yang sudah dikembalikan tidak dapat diedit');
        }

        $request->validate([
            'keperluan' => 'required|string|max:500',
            'tanggal_kembali_rencana' => 'required|date',
            'alat_id' => 'required|array|min:1',
            'alat_id.*' => 'exists:alat,id',
            'jumlah_pinjam' => 'required|array|min:1',
            'jumlah_pinjam.*' => 'required|integer|min:1',
            'kondisi_pinjam' => 'required|array|min:1',
            'kondisi_pinjam.*' => 'required|in:baik,rusak,perbaikan',
        ]);

        DB::beginTransaction();

        try {
            // Update data peminjaman
            $peminjaman->update([
                'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
                'keperluan' => $request->keperluan,
            ]);

            // Kembalikan stok alat dari detail lama
            $oldDetails = $peminjaman->detailPeminjaman;
            foreach ($oldDetails as $detail) {
                $alat = $detail->alat;
                $alat->update([
                    'jumlah_tersedia' => $alat->jumlah_tersedia + $detail->jumlah_pinjam
                ]);
            }

            // Hapus detail lama
            $peminjaman->detailPeminjaman()->delete();

            // Validasi stok untuk detail baru
            foreach ($request->alat_id as $index => $alatId) {
                $alat = Alat::findOrFail($alatId);
                $jumlahPinjam = $request->jumlah_pinjam[$index];

                if ($alat->jumlah_tersedia < $jumlahPinjam) {
                    throw new \Exception("Stok {$alat->nama_alat} tidak mencukupi. Stok tersedia: {$alat->jumlah_tersedia}, yang dipinjam: {$jumlahPinjam}");
                }
            }

            // Buat detail baru
            foreach ($request->alat_id as $index => $alatId) {
                $alat = Alat::findOrFail($alatId);
                $jumlahPinjam = $request->jumlah_pinjam[$index];
                $kondisiPinjam = $request->kondisi_pinjam[$index];

                // Kurangi stok alat
                $alat->update([
                    'jumlah_tersedia' => $alat->jumlah_tersedia - $jumlahPinjam
                ]);

                // Buat detail peminjaman
                DetailPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah_pinjam' => $jumlahPinjam,
                    'kondisi_pinjam' => $kondisiPinjam,
                ]);
            }

            // Catat aktivitas
            Activity::create([
                'user_id' => Auth::id(),
                'activity' => 'Mengedit peminjaman alat - ID: ' . $peminjaman->id
            ]);

            DB::commit();

            return redirect()->route('peminjaman.show', $peminjaman->id)
                ->with('success', 'Peminjaman berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal memperbarui peminjaman: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
   /**
     * Remove the specified resource from storage.
     */
    public function destroy(Peminjaman $peminjaman)
    {
        // Cek otorisasi manual (tanpa policy)
        if (Auth::user()->role !== 'admin' && $peminjaman->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        DB::beginTransaction();

        try {
            // PENTING: Hanya kembalikan stok jika statusnya BUKAN 'dikembalikan'.
            // Karena jika 'dikembalikan', stok sudah bertambah secara otomatis 
            // pada saat proses form pengembalian (method frompengembalian).
            if ($peminjaman->status !== 'dikembalikan') {
                foreach ($peminjaman->detailPeminjaman as $detail) {
                    $alat = $detail->alat;
                    $alat->update([
                        'jumlah_tersedia' => $alat->jumlah_tersedia + $detail->jumlah_pinjam
                    ]);
                }
            }

            // Hapus relasi detail peminjaman terlebih dahulu untuk menghindari Error Constraint Database
            $peminjaman->detailPeminjaman()->delete();
            
            // Hapus relasi pengembalian jika ada
            if ($peminjaman->pengembalian) {
                $peminjaman->pengembalian()->delete();
            }

            // Hapus peminjaman utama
            $peminjaman->delete();

            // Catat aktivitas
            Activity::create([
                'user_id' => Auth::id(),
                'activity' => 'Menghapus data peminjaman alat - ID: ' . $peminjaman->id
            ]);

            DB::commit();

            // Menggunakan back() agar user tetap berada di tab tabel yang sama
            return redirect()->back()
                ->with('success', 'Riwayat peminjaman berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menghapus peminjaman: ' . $e->getMessage());
        }
    }

    /**
     * Proses pengembalian alat
     */
    public function frompengembalian(Request $request, Peminjaman $peminjaman)
    {
        // Hanya admin yang bisa proses pengembalian
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // Hanya bisa kembalikan jika status masih dipinjam
        if ($peminjaman->status !== 'dipinjam') {
            return redirect()->back()
                ->with('error', 'Peminjaman ini sudah dikembalikan sebelumnya');
        }

        $request->validate([
            'tanggal_kembali_realisasi' => 'required|date|after_or_equal:tanggal_pinjam',
            'kondisi_kembali' => 'required|array',
            'kondisi_kembali.*' => 'in:baik,rusak,perbaikan',
            'catatan' => 'nullable|string|max:500',
            'denda' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Hitung keterlambatan
            $terlambatHari = 0;
            if ($request->tanggal_kembali_realisasi > $peminjaman->tanggal_kembali_rencana) {
                $terlambatHari = \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)
                    ->diffInDays(\Carbon\Carbon::parse($request->tanggal_kembali_realisasi));
            }

            // Update status peminjaman dan tanggal kembali
            $peminjaman->update([
                'tanggal_kembali_realisasi' => $request->tanggal_kembali_realisasi,
                'status' => $terlambatHari > 0 ? 'terlambat' : 'dikembalikan',
            ]);

            // Update kondisi alat dan tambah stok
            foreach ($peminjaman->detailPeminjaman as $detail) {
                $alat = $detail->alat;

                // Update kondisi alat jika berubah
                if (isset($request->kondisi_kembali[$detail->id])) {
                    $kondisiKembali = $request->kondisi_kembali[$detail->id];

                    // Jika kondisi kembali berbeda, update di master alat
                    if ($kondisiKembali !== $detail->kondisi_pinjam) {
                        $alat->kondisi = $kondisiKembali;
                        $alat->save();
                    }
                }

                // Tambah stok yang dikembalikan
                $alat->update([
                    'jumlah_tersedia' => $alat->jumlah_tersedia + $detail->jumlah_pinjam
                ]);
            }

            // Buat data pengembalian
            $peminjaman->pengembalian()->create([
                'tanggal_kembali_realisasi' => $request->tanggal_kembali_realisasi,
                'kondisi_kembali' => implode(',', $request->kondisi_kembali),
                'denda' => $request->denda ?? 0,
                'catatan' => $request->catatan,
            ]);

            // Catat aktivitas
            Activity::create([
                'user_id' => Auth::id(),
                'activity' => 'Memproses pengembalian alat - Peminjaman ID: ' . $peminjaman->id
            ]);

            DB::commit();

            return redirect()->route('peminjaman.show', $peminjaman->id)
                ->with('success', 'Pengembalian berhasil diproses!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * API untuk mencari alat (untuk autocomplete)
     */
    public function searchAlat(Request $request)
    {
        $keyword = $request->query('q');

        $alat = Alat::where('jumlah_tersedia', '>', 0)
            ->where(function ($query) use ($keyword) {
                $query->where('nama_alat', 'like', "%{$keyword}%")
                    ->orWhere('kode_alat', 'like', "%{$keyword}%");
            })
            ->with('kategori')
            ->limit(10)
            ->get(['id', 'kode_alat', 'nama_alat', 'jumlah_tersedia', 'kondisi', 'kategori_id']);

        return response()->json($alat);
    }

    /**
     * API untuk mendapatkan detail peminjaman (untuk modal pengembalian)
     */
    public function detail(Request $request, Peminjaman $peminjaman)
    {
        // Cek otorisasi manual (tanpa policy)
        if (Auth::user()->role !== 'admin' && $peminjaman->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $peminjaman->load(['user', 'detailPeminjaman.alat.kategori']);

        $detail = $peminjaman->detailPeminjaman->map(function ($item) {
            return [
                'id' => $item->id,
                'alat' => [
                    'nama_alat' => $item->alat->nama_alat,
                    'kode_alat' => $item->alat->kode_alat,
                ],
                'kondisi_sebelum' => $item->kondisi_pinjam,
                'jumlah_pinjam' => $item->jumlah_pinjam,
            ];
        });

        return response()->json([
            'peminjaman' => $peminjaman,
            'detail' => $detail
        ]);
    }
    /**
     * Menyetujui peminjaman (hanya admin/petugas)
     */
    /**
     * Menyetujui peminjaman (hanya admin/petugas)
     */
    /**
     * Menyetujui peminjaman (hanya admin/petugas)
     */
    public function approve(Request $request, Peminjaman $peminjaman)
    {
        // Log untuk debugging
        \Log::info('=== APPROVE DIPANGGIL ===');
        \Log::info('ID Peminjaman: ' . $peminjaman->id);
        \Log::info('User: ' . Auth::user()->name . ' (' . Auth::user()->role . ')');

        try {
            // CEK STATUS - harus menunggu
            if ($peminjaman->status_approval !== 'menunggu') {
                \Log::warning('Status bukan menunggu: ' . $peminjaman->status_approval);
                return redirect()->route('peminjaman.index')
                    ->with('error', 'Peminjaman ini sudah diproses sebelumnya.');
            }

            // UPDATE STATUS - sama seperti reject
            $peminjaman->status_approval = 'disetujui';
            $peminjaman->status = 'dipinjam';
            $peminjaman->approved_by = Auth::id();
            $peminjaman->approved_at = now();
            $peminjaman->save();

            \Log::info('Approve berhasil. Status sekarang: ' . $peminjaman->status_approval);

            // Catat aktivitas
            Activity::create([
                'user_id' => Auth::id(),
                'activity' => 'Menyetujui peminjaman - ID: ' . $peminjaman->id
            ]);

            return redirect()->route('peminjaman.index')
                ->with('success', 'Peminjaman berhasil disetujui.');
        } catch (\Exception $e) {
            \Log::error('Approve error: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());

            return redirect()->route('peminjaman.index')
                ->with('error', 'Gagal menyetujui: ' . $e->getMessage());
        }
    }
    /**
     * Menampilkan form pengembalian (hanya admin/petugas)
     */
    public function pengembalian(Peminjaman $peminjaman)
    {
        // Hanya admin dan petugas yang bisa akses
        if (Auth::user()->role === 'peminjam') {
            abort(403, 'Unauthorized action.');
        }

        // Cek status
        if (
            $peminjaman->status_approval !== 'disetujui' ||
            !in_array($peminjaman->status, ['dipinjam', 'menunggu_pengembalian'])
        ) {
            return redirect()->route('peminjaman.index')
                ->with('error', 'Peminjaman ini tidak dapat diproses pengembaliannya.');
        }

        $peminjaman->load(['user', 'detailPeminjaman.alat']);

        return view('peminjaman.pengembalian', compact('peminjaman'));
    }
    /**
     * Menolak peminjaman (hanya admin/petugas)
     */
    /**
     * Menolak peminjaman (hanya admin/petugas)
     */
    public function reject(Request $request, Peminjaman $peminjaman)
    {
        // Hanya admin dan petugas yang bisa menolak
        if (Auth::user()->role === 'peminjam') {
            abort(403, 'Unauthorized action.');
        }

        // Validasi input
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        // Cek apakah status masih menunggu
        if ($peminjaman->status_approval !== 'menunggu') {
            return redirect()->route('peminjaman.index')
                ->with('error', 'Peminjaman ini sudah diproses sebelumnya.');
        }

        try {
            // Update status peminjaman menjadi ditolak
            $peminjaman->update([
                'status_approval' => 'ditolak',
                'status' => 'ditolak',
                'alasan_penolakan' => $request->alasan_penolakan,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Catat aktivitas
            Activity::create([
                'user_id' => Auth::id(),
                'activity' => 'Menolak peminjaman - ID: ' . $peminjaman->id . ' | Alasan: ' . $request->alasan_penolakan
            ]);

            // Redirect ke halaman index peminjaman dengan pesan sukses
            return redirect()->route('peminjaman.index')
                ->with('success', 'Peminjaman berhasil ditolak.');
        } catch (\Exception $e) {
            return redirect()->route('peminjaman.index')
                ->with('error', 'Gagal menolak peminjaman: ' . $e->getMessage());
        }
    }
    public function ajukanPengembalian(Peminjaman $peminjaman)
    {
        // hanya pemilik
        if ($peminjaman->user_id != auth()->id()) {
            abort(403);
        }

        // hanya yang masih dipinjam
        if ($peminjaman->status != 'dipinjam') {
            return redirect()->back()
                ->with('error', 'Peminjaman ini tidak bisa diajukan pengembalian.');
        }

        // update status
        $peminjaman->update([
            'status' => 'menunggu_pengembalian',
            'status_pengembalian' => 'menunggu',
        ]);

        return redirect()->back()
            ->with('success', 'Pengembalian berhasil diajukan.');
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        return [
            'total_alat' => Alat::sum('jumlah_total'),
            'stok_tersedia' => Alat::sum('jumlah_tersedia'),
            'sedang_dipinjam' => Alat::sum('jumlah_total') - Alat::sum('jumlah_tersedia'),
            'alat_rusak' => Alat::where('kondisi', 'rusak')->sum('jumlah_total'),
            'alat_perbaikan' => Alat::where('kondisi', 'perbaikan')->sum('jumlah_total'),
            'total_kategori' => Kategori::count(),
            'peminjaman_aktif' => Peminjaman::where('status', 'dipinjam')->count(),
            'total_peminjam' => User::where('role', 'peminjam')->count(),
            'peminjaman_terlambat' => Peminjaman::where('status', 'terlambat')->count(),
            'peminjaman_dikembalikan' => Peminjaman::where('status', 'dikembalikan')->count(),
        ];
    }

    /**
     * Ajukan perpanjangan (peminjam) — menunggu persetujuan admin
     */
    public function perpanjang(Request $request, Peminjaman $peminjaman)
    {
        // Hanya pemilik
        if ($peminjaman->user_id !== Auth::id()) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke peminjaman ini.');
        }

        // Hanya jika status dipinjam atau terlambat
        if (!in_array($peminjaman->status, ['dipinjam', 'terlambat'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Peminjaman ini tidak dapat diperpanjang.');
        }

        // Tidak boleh ada pengajuan perpanjangan yang masih menunggu
        if ($peminjaman->status_perpanjangan === 'menunggu') {
            return redirect()->route('dashboard')
                ->with('warning', 'Pengajuan perpanjangan Anda sedang menunggu persetujuan admin.');
        }

        $request->validate([
            'tanggal_perpanjangan_diminta' => 'required|date|after:' . $peminjaman->tanggal_kembali_rencana->format('Y-m-d'),
        ]);

        $peminjaman->update([
            'status_perpanjangan'          => 'menunggu',
            'tanggal_perpanjangan_diminta' => $request->tanggal_perpanjangan_diminta,
            'perpanjangan_approved_by'     => null,
            'perpanjangan_approved_at'     => null,
            'alasan_tolak_perpanjangan'    => null,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Pengajuan perpanjangan berhasil dikirim. Menunggu persetujuan admin.');
    }

    /**
     * Admin menyetujui perpanjangan
     */
    public function approvePerpanjang(Peminjaman $peminjaman)
    {
        if ($peminjaman->status_perpanjangan !== 'menunggu') {
            return redirect()->back()->with('error', 'Tidak ada pengajuan perpanjangan yang menunggu.');
        }

        $peminjaman->update([
            'tanggal_kembali_rencana'  => $peminjaman->tanggal_perpanjangan_diminta,
            'status'                   => 'dipinjam',
            'status_perpanjangan'      => 'disetujui',
            'perpanjangan_approved_by' => Auth::id(),
            'perpanjangan_approved_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Perpanjangan disetujui. Tanggal kembali diperbarui ke ' . $peminjaman->tanggal_perpanjangan_diminta->format('d/m/Y') . '.');
    }

    /**
     * Admin menolak perpanjangan
     */
    public function rejectPerpanjang(Request $request, Peminjaman $peminjaman)
    {
        if ($peminjaman->status_perpanjangan !== 'menunggu') {
            return redirect()->back()->with('error', 'Tidak ada pengajuan perpanjangan yang menunggu.');
        }

        $request->validate([
            'alasan_tolak_perpanjangan' => 'nullable|string|max:500',
        ]);

        $peminjaman->update([
            'status_perpanjangan'       => 'ditolak',
            'perpanjangan_approved_by'  => Auth::id(),
            'perpanjangan_approved_at'  => now(),
            'alasan_tolak_perpanjangan' => $request->alasan_tolak_perpanjangan,
        ]);

        return redirect()->back()
            ->with('success', 'Pengajuan perpanjangan ditolak.');
    }
}
