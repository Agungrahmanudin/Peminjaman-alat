<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\Activity;
use App\Models\DetailPeminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AlatController extends Controller
{
    public function index(Request $request)
    {
        $query = Alat::with('kategori');

        // Filter berdasarkan kategori
        if ($request->has('kategori_id') && $request->kategori_id != '') {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Filter berdasarkan kondisi
        if ($request->has('kondisi') && $request->kondisi != '') {
            $query->where('kondisi', $request->kondisi);
        }

        // Search berdasarkan nama atau kode
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_alat', 'like', "%{$search}%")
                    ->orWhere('kode_alat', 'like', "%{$search}%");
            });
        }

        // Filter stok
        if ($request->has('stok') && $request->stok != '') {
            if ($request->stok == 'kosong') {
                $query->where('jumlah_tersedia', 0);
            } elseif ($request->stok == 'tersedia') {
                $query->where('jumlah_tersedia', '>', 0);
            } elseif ($request->stok == 'kritis') {
                $query->where('jumlah_tersedia', '<=', 2)
                    ->where('jumlah_tersedia', '>', 0);
            }
        }

        // KHUSUS PEMINJAM
        if (auth()->user()->role === 'peminjam') {
            $query->where('jumlah_tersedia', '>', 0);
        }

        $alat = $query->orderBy('created_at', 'desc')
            ->paginate(5)
            ->appends($request->except('page'));

        $kategoriList = Kategori::all();

        return view('alat.index', compact('alat', 'kategoriList'));
    }

    // Method Create (Tambahan untuk menampilkan halaman tambah alat)
    public function create()
    {
        $kategori = Kategori::all();
        return view('alat.create', compact('kategori'));
    }

    // Method Store (Tambahan untuk menyimpan data alat baru beserta harga_alat)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_alat' => 'required|unique:alat,kode_alat',
            'nama_alat' => 'required|max:100',
            'kategori_id' => 'required|exists:kategori,id',
            'kondisi' => 'required|in:baik,rusak,perbaikan',
            'harga_alat' => 'required|integer|min:0', // Validasi Harga Alat
            'jumlah_total' => 'required|integer|min:1',
            'jumlah_tersedia' => 'required|integer|min:0',
            'lokasi' => 'required|max:100',
            'keterangan' => 'nullable|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->jumlah_tersedia > $request->jumlah_total) {
            return redirect()->back()
                ->with('error', 'Jumlah tersedia tidak boleh lebih dari jumlah total')
                ->withInput();
        }

        Alat::create($request->all());

        return redirect()->route('alat.index')
            ->with('success', 'Alat berhasil ditambahkan');
    }

    public function show(Alat $alat)
    {
        $alat->load('kategori', 'detailPeminjaman');
        return view('alat.show', compact('alat'));
    }

    public function edit(Alat $alat)
    {
        $kategori = Kategori::all();
        return view('alat.edit', compact('alat', 'kategori'));
    }

    public function update(Request $request, Alat $alat)
    {
        $validator = Validator::make($request->all(), [
            'kode_alat' => 'required|unique:alat,kode_alat,' . $alat->id,
            'nama_alat' => 'required|max:100',
            'kategori_id' => 'required|exists:kategori,id',
            'kondisi' => 'required|in:baik,rusak,perbaikan',
            'harga_alat' => 'required|integer|min:0', // Validasi Harga Alat
            'jumlah_total' => 'required|integer|min:1',
            'jumlah_tersedia' => 'required|integer|min:0',
            'lokasi' => 'required|max:100',
            'keterangan' => 'nullable|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validasi jumlah tersedia tidak lebih dari total
        if ($request->jumlah_tersedia > $request->jumlah_total) {
            return redirect()->back()
                ->with('error', 'Jumlah tersedia tidak boleh lebih dari jumlah total')
                ->withInput();
        }

        $alat->update($request->all());

        return redirect()->route('alat.index')
            ->with('success', 'Alat berhasil diperbarui');
    }

    public function destroy(Alat $alat)
    {
        // Cek apakah alat SEDANG dipinjam (belum dikembalikan)
        $sedangDipinjam = $alat->detailPeminjaman()
            ->whereHas('peminjaman', function ($query) {
                $query->whereIn('status', ['dipinjam', 'menunggu_pengembalian']);
            })
            ->exists();

        if ($sedangDipinjam) {
            return redirect()->route('alat.index')
                ->with('error', 'Tidak dapat menghapus alat yang sedang dipinjam');
        }

        DB::beginTransaction();
        try {
            // Hapus detail peminjaman yang terkait dengan alat ini
            $alat->detailPeminjaman()->delete();

            // Hapus alat
            $alat->delete();

            // Catat aktivitas
            Activity::create([
                'user_id' => Auth::id(),
                'activity' => 'Menghapus alat: ' . $alat->nama_alat . ' (Kode: ' . $alat->kode_alat . ')'
            ]);

            DB::commit();

            return redirect()->route('alat.index')
                ->with('success', 'Alat berhasil dihapus beserta riwayat peminjaman terkait');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('alat.index')
                ->with('error', 'Gagal menghapus alat: ' . $e->getMessage());
        }
    }

    public function deleteAll(Request $request)
    {
        // Cek apakah ada alat yang SEDANG dipinjam
        $alatDipinjam = Alat::whereHas('detailPeminjaman', function ($query) {
            $query->whereHas('peminjaman', function ($q) {
                $q->whereIn('status', ['dipinjam', 'menunggu_pengembalian']);
            });
        })->count();

        if ($alatDipinjam > 0) {
            return redirect()->back()
                ->with('error', 'Tidak bisa menghapus semua alat karena ada ' . $alatDipinjam . ' alat yang sedang dipinjam.');
        }

        DB::beginTransaction();
        try {
            $jumlahAlat = Alat::count();

            // Hapus semua detail peminjaman terlebih dahulu
            DB::table('detail_peminjaman')->truncate();

            // Hapus semua alat
            Alat::truncate();

            // Catat aktivitas
            Activity::create([
                'user_id' => Auth::id(),
                'activity' => 'Menghapus semua ' . $jumlahAlat . ' alat dari database beserta riwayat peminjaman'
            ]);

            DB::commit();

            return redirect()->route('alat.index')
                ->with('success', 'Berhasil menghapus semua ' . $jumlahAlat . ' alat beserta riwayat peminjaman.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menghapus semua alat: ' . $e->getMessage());
        }
    }

    // API untuk mendapatkan detail alat (untuk modal)
    public function getDetail(Alat $alat)
    {
        $alat->load('kategori');

        return response()->json([
            'success' => true,
            'data' => [
                'kode_alat' => $alat->kode_alat,
                'nama_alat' => $alat->nama_alat,
                'kategori' => $alat->kategori->nama_kategori,
                'kondisi' => $alat->kondisi,
                'harga_alat' => $alat->harga_alat, // Tambahan Harga Alat untuk ditampilkan di Modal
                'jumlah_total' => $alat->jumlah_total,
                'jumlah_tersedia' => $alat->jumlah_tersedia,
                'lokasi' => $alat->lokasi,
                'keterangan' => $alat->keterangan,
                'created_at' => $alat->created_at->format('d/m/Y H:i'),
                'updated_at' => $alat->updated_at->format('d/m/Y H:i'),
            ]
        ]);
    }

    public function checkKode(Request $request)
    {
        $request->validate([
            'kode_alat' => 'required|string',
        ]);

        $exists = Alat::where('kode_alat', $request->kode_alat)->exists();

        return response()->json([
            'available' => !$exists
        ]);
    }

    public function search(Request $request)
    {
        $keyword = $request->query('q');

        $alat = Alat::where('jumlah_tersedia', '>', 0)
            ->where(function ($query) use ($keyword) {
                $query->where('nama_alat', 'like', "%{$keyword}%")
                    ->orWhere('kode_alat', 'like', "%{$keyword}%");
            })
            ->limit(10)
            ->get(['id', 'kode_alat', 'nama_alat', 'jumlah_tersedia', 'harga_alat', 'keterangan']); // Tambahkan harga_alat di sini jika dibutuhkan di UI

        return response()->json([
            'success' => true,
            'alat' => $alat
        ]);
    }
}