<?php

namespace App\Http\Controllers;

use App\Models\Pengembalian;
use App\Models\Peminjaman;
use App\Models\Alat;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PengembalianController extends Controller
{
    /**
     * Display a listing of the resource.
     * - Admin/Petugas : lihat semua peminjaman aktif + semua riwayat
     * - Peminjam      : TIDAK ada peminjaman aktif (tidak bisa proses),
     *                   riwayat hanya milik sendiri
     */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $isPeminjam = $user->role === 'peminjam';

        // Peminjaman aktif — hanya untuk admin & petugas
        if (!$isPeminjam) {
            $peminjamanAktif = Peminjaman::whereIn('status', [
                'dipinjam',
                'menunggu_pengembalian'
            ])
                ->where('status_approval', 'disetujui')
                ->with(['user', 'detailPeminjaman.alat'])
                ->when($request->search, function ($q, $search) {
                    $q->whereHas(
                        'user',
                        fn($query) =>
                        $query->where('name', 'like', "%{$search}%")
                    );
                })
                ->paginate(10);
        } else {
            $peminjamanAktif = collect(); // kosong untuk peminjam
        }

        // Riwayat pengembalian
        $riwayatQuery = Pengembalian::with(['peminjaman.user'])->latest();

        // Peminjam hanya lihat riwayat milik sendiri
        if ($isPeminjam) {
            $riwayatQuery->whereHas(
                'peminjaman',
                fn($q) =>
                $q->where('user_id', $user->id)
            );
        }

        $riwayatPengembalian = $riwayatQuery->paginate(10);

        return view('pengembalian.index', compact('peminjamanAktif', 'riwayatPengembalian', 'isPeminjam'));
    }

    /**
     * Show the form for creating a new resource.
     * Hanya admin & petugas (sudah dibatasi di route).
     */
    public function create($peminjaman_id = null)
    {
        Log::info('Create pengembalian - parameter: ' . ($peminjaman_id ?? 'null'));

        if ($peminjaman_id) {
            $peminjaman = Peminjaman::with(['user', 'detailPeminjaman.alat'])
                ->where('id', $peminjaman_id)
                ->where('status', 'menunggu_pengembalian')
                ->where('status_approval', 'disetujui')
                ->firstOrFail();

            return view('pengembalian.create', compact('peminjaman'));
        }

        return redirect()->route('pengembalian.index')
            ->with('info', 'Silakan pilih peminjaman yang akan dikembalikan.');
    }

    /**
     * Store a newly created resource in storage.
     * Hanya admin & petugas (sudah dibatasi di route).
     */
    public function store(Request $request)
    {
        Log::info('=== PROSES PENGEMBALIAN ALAT ===');

        // 1. Validasi disesuaikan dengan Form Blade
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'tanggal_kembali_realisasi' => 'required|date',
            'kondisi_kembali' => 'required|array',
            'kondisi_kembali.*' => 'required|in:baik,rusak,hilang', // Disesuaikan dengan form
            'persen_denda' => 'nullable|array', // Menangkap input persentase
            'denda_dibayar' => 'nullable|integer|min:0',
            'metode_pembayaran' => 'nullable|string',
            'catatan' => 'nullable|string', // Untuk textarea paling bawah
        ]);

        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::with('detailPeminjaman.alat')->findOrFail($request->peminjaman_id);

            if ($peminjaman->status !== 'menunggu_pengembalian') {
                throw new \Exception('Peminjaman ini sudah dikembalikan sebelumnya.');
            }

            if ($peminjaman->status_approval !== 'disetujui') {
                throw new \Exception('Peminjaman belum disetujui.');
            }

            // =========================
            // 1. HITUNG DENDA KETERLAMBATAN
            // =========================
            $hariTerlambat = now()->gt($peminjaman->tanggal_kembali_rencana)
                ? Carbon::parse($peminjaman->tanggal_kembali_rencana)->diffInDays(now())
                : 0;

            $dendaKeterlambatan = $hariTerlambat * 10000;

            // =========================
            // 2. HITUNG DENDA KERUSAKAN (Berdasarkan persentase Form JS)
            // =========================
            $dendaKerusakan = 0;

            foreach ($peminjaman->detailPeminjaman as $detail) {
                $kondisi = $request->kondisi_kembali[$detail->id] ?? 'baik';
                $persen  = $request->persen_denda[$detail->id] ?? 0;
                $harga   = $detail->alat->harga_alat ?? 0;
                $jumlah  = $detail->jumlah_pinjam;

                if ($kondisi === 'rusak') {
                    // Denda berdasarkan persentase
                    $dendaKerusakan += ($persen / 100) * $harga * $jumlah;
                } elseif ($kondisi === 'hilang') {
                    // Hilang = 100% ganti rugi
                    $dendaKerusakan += $harga * $jumlah;
                }
            }
            // Bulatkan agar tidak ada koma
            $dendaKerusakan = round($dendaKerusakan);

            // =========================
            // 3. TOTAL DENDA
            // =========================
            $totalDenda = $dendaKeterlambatan + $dendaKerusakan;
            $dendaDibayar = $request->denda_dibayar ?? 0;

            // =========================
            // 4. STATUS DENDA
            // =========================
            if ($totalDenda == 0) {
                $statusDenda = 'tidak_ada';
            } else {
                $statusDenda = $dendaDibayar >= $totalDenda
                    ? 'lunas'
                    : ($dendaDibayar > 0 ? 'sebagian' : 'belum');
            }

            // =========================
            // 5. UPDATE PEMINJAMAN
            // =========================
            $peminjaman->update([
                'status' => ($totalDenda > 0 && $dendaDibayar < $totalDenda)
                    ? 'terlambat'
                    : 'dikembalikan',

                'tanggal_kembali_realisasi' => Carbon::parse($request->tanggal_kembali_realisasi),
                'denda_dibayar' => $dendaDibayar,
                'status_denda' => $statusDenda,
            ]);

            // =========================
            // 6. UPDATE ALAT
            // =========================
            foreach ($peminjaman->detailPeminjaman as $detail) {

                $alat = Alat::find($detail->alat_id);

                if (!$alat) continue;

                $kondisi = $request->kondisi_kembali[$detail->id] ?? 'baik';

                if ($kondisi === 'baik') {
                    $alat->increment('jumlah_tersedia', $detail->jumlah_pinjam);
                } elseif ($kondisi === 'rusak') {
                    // Kalau rusak, masuk status perbaikan
                    $alat->increment('jumlah_tersedia', $detail->jumlah_pinjam);
                    $alat->update([
                        'kondisi' => 'perbaikan'
                    ]);
                } elseif ($kondisi === 'hilang') {
                    // Hilang = stok total berkurang
                    $alat->update([
                        'jumlah_total' => $alat->jumlah_total - $detail->jumlah_pinjam,
                        'jumlah_tersedia' => max(0, $alat->jumlah_tersedia),
                        'kondisi' => 'hilang'
                    ]);
                }
            }

            // =========================
            // 7. SIMPAN PENGEMBALIAN
            // =========================
            $pengembalian = Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tanggal_kembali_realisasi' => Carbon::parse($request->tanggal_kembali_realisasi),
                'kondisi_kembali' => json_encode($request->kondisi_kembali),
                'denda' => $totalDenda,
                'denda_dibayar' => $dendaDibayar,
                'status_denda' => $statusDenda,
                'metode_pembayaran' => $dendaDibayar > 0 ? $request->metode_pembayaran : null,
                'catatan' => $request->catatan,
            ]);

            Activity::create([
                'user_id' => Auth::id(),
                'activity' => 'Memproses pengembalian alat - Peminjaman ID: ' . $peminjaman->id .
                    ' | Total Denda: Rp ' . number_format($totalDenda, 0, ',', '.'),
            ]);

            DB::commit();

            return redirect()->route('pengembalian.show', $pengembalian->id)
                ->with('success', 'Pengembalian berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error pengembalian: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     * Peminjam hanya boleh lihat milik sendiri.
     */
    public function show(Pengembalian $pengembalian)
    {
        if (
            Auth::user()->role === 'peminjam' &&
            $pengembalian->peminjaman->user_id !== Auth::id()
        ) {
            abort(403, 'Unauthorized action.');
        }

        $pengembalian->load(['peminjaman.user', 'peminjaman.detailPeminjaman.alat']);

        $kondisiKembali = json_decode($pengembalian->kondisi_kembali, true);
        $sisaDenda      = $pengembalian->denda - $pengembalian->denda_dibayar;
        $persentase     = $pengembalian->denda > 0
            ? round(($pengembalian->denda_dibayar / $pengembalian->denda) * 100, 2)
            : 0;

        return view('pengembalian.show', compact('pengembalian', 'kondisiKembali', 'sisaDenda', 'persentase'));
    }
    public function destroy(Pengembalian $pengembalian)
    {
        DB::beginTransaction();

        try {
            // hanya hapus pengembalian
            $pengembalian->delete();

            DB::commit();

            return redirect()->route('pengembalian.index')
                ->with('success', 'Data pengembalian berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
    /**
     * Proses pembayaran denda — hanya admin & petugas (dibatasi di route).
     */
    public function bayarDenda(Request $request, Pengembalian $pengembalian)
    {
        $request->validate([
            'jumlah_bayar'       => 'required|integer|min:1000',
            'metode_pembayaran'  => 'required|string|max:50',
            'catatan'            => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $jumlahBayar       = $request->jumlah_bayar;
            $totalDenda        = $pengembalian->denda;
            $sudahDibayar      = $pengembalian->denda_dibayar;
            $totalSetelahBayar = $sudahDibayar + $jumlahBayar;
            $sisaDenda         = $totalDenda - $sudahDibayar;

            if ($jumlahBayar > $sisaDenda) {
                throw new \Exception('Jumlah bayar melebihi sisa denda');
            }

            $statusDenda = $totalSetelahBayar >= $totalDenda ? 'lunas' : 'sebagian';

            $pengembalian->update([
                'denda_dibayar'           => $totalSetelahBayar,
                'status_denda'            => $statusDenda,
                'tanggal_pembayaran_denda' => now(),
                'metode_pembayaran'       => $request->metode_pembayaran,
                'catatan'                 => $request->catatan
                    ? ($pengembalian->catatan . "\n[Pembayaran Denda: " . $request->catatan . "]")
                    : $pengembalian->catatan,
            ]);

            $pengembalian->peminjaman->update([
                'denda_dibayar' => $totalSetelahBayar,
                'status_denda'  => $statusDenda,
            ]);

            Activity::create([
                'user_id'  => Auth::id(),
                'activity' => 'Pembayaran denda - Pengembalian ID: ' . $pengembalian->id .
                    ' | Bayar: Rp ' . number_format($jumlahBayar, 0, ',', '.'),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Pembayaran denda berhasil! ' .
                    ($statusDenda === 'lunas'
                        ? 'Denda telah lunas.'
                        : 'Sisa denda: Rp ' . number_format($totalDenda - $totalSetelahBayar, 0, ',', '.')));
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal memproses pembayaran denda: ' . $e->getMessage())
                ->withInput();
        }
    }
}
