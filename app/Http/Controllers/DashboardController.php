<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Pengembalian;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ===== Status Alat =====
        $totalAlat = Alat::sum('jumlah_total');
        $alatTersedia = Alat::sum('jumlah_tersedia');
        $alatDipinjam = DetailPeminjaman::join('peminjaman', 'detail_peminjaman.peminjaman_id', '=', 'peminjaman.id')
            ->where('peminjaman.status', 'dipinjam')
            ->sum('detail_peminjaman.jumlah_pinjam');
        $alatRusak = Alat::where('kondisi', 'rusak')->count();

        // Set perubahan ke 0 (placeholder)
        $totalAlatChange = 0;
        $alatTersediaChange = 0;
        $alatDipinjamChange = 0;
        $alatRusakChange = 0;

        // ===== Analitik Peminjaman =====
        $totalPeminjaman = Peminjaman::count();
        $dikembalikan = Peminjaman::where('status', 'dikembalikan')->count();
        $terlambat = Peminjaman::where('status', 'terlambat')->count();
        $aktif = Peminjaman::where('status', 'aktif')->count();

        // ===== Payment Statistics =====
        $paymentLunas = Pengembalian::where('status_denda', 'lunas')->sum('denda_dibayar');
        $paymentBelumBayar = Pengembalian::where('status_denda', 'belum_bayar')->sum('denda');
        $paymentTelatBayar = Pengembalian::where('status_denda', 'telat')->sum('denda');
        $totalPendapatan = Pengembalian::sum('denda_dibayar');

        return view('dashboard', [
            'totalAlat' => $totalAlat,
            'alatDipinjam' => $alatDipinjam,
            'alatTersedia' => $alatTersedia,
            'alatRusak' => $alatRusak,
            'totalAlatChange' => $totalAlatChange,
            'alatDipinjamChange' => $alatDipinjamChange,
            'alatTersediaChange' => $alatTersediaChange,
            'alatRusakChange' => $alatRusakChange,
            'totalPeminjaman' => $totalPeminjaman,
            'dikembalikan' => $dikembalikan,
            'terlambat' => $terlambat,
            'aktif' => $aktif,
            'totalUangMasuk' => $totalPendapatan,
        ]);
    }
    
}