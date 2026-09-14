<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PeminjamanExport;
use App\Exports\AlatExport;
use App\Exports\PengembalianExport;
use App\Exports\KeuanganExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'peminjaman');

        // Kategori untuk filter
        $kategori = Kategori::all();

        // Data untuk tab Peminjaman
        $peminjamanQuery = Peminjaman::with(['user', 'detailPeminjaman.alat']);

        if ($request->filled('start_date')) {
            $peminjamanQuery->whereDate('tanggal_pinjam', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $peminjamanQuery->whereDate('tanggal_pinjam', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $peminjamanQuery->where('status', $request->status);
        }

        $peminjaman = $peminjamanQuery->latest()->get();

        // Statistik Peminjaman
        $totalPeminjaman = $peminjaman->count();
        $peminjamanAktif = $peminjaman->where('status', 'dipinjam')->count();
        $peminjamanTerlambat = $peminjaman->where('status', 'terlambat')->count();
        $peminjamanDikembalikan = $peminjaman->where('status', 'dikembalikan')->count();

        // Data untuk tab Alat
        $alatQuery = Alat::with('kategori');

        if ($request->filled('kategori')) {
            $alatQuery->where('kategori_id', $request->kategori);
        }

        if ($request->filled('kondisi')) {
            $alatQuery->where('kondisi', $request->kondisi);
        }

        if ($request->filled('stok')) {
            if ($request->stok == 'aman') {
                $alatQuery->where('jumlah_tersedia', '>', 5);
            } elseif ($request->stok == 'sedikit') {
                $alatQuery->whereBetween('jumlah_tersedia', [1, 5]);
            } elseif ($request->stok == 'habis') {
                $alatQuery->where('jumlah_tersedia', 0);
            }
        }

        if ($request->filled('lokasi')) {
            $alatQuery->where('lokasi', 'like', '%' . $request->lokasi . '%');
        }

        $alat = $alatQuery->get();

        // Statistik Alat
        $totalAlat = $alat->count();
        $alatTersedia = $alat->sum('jumlah_tersedia');
        $alatDipinjam = $alat->sum('jumlah_total') - $alatTersedia;
        $alatRusak = $alat->where('kondisi', 'rusak')->count();

        // Data untuk tab Pengembalian
        $pengembalianQuery = Pengembalian::with(['peminjaman.user', 'peminjaman.detailPeminjaman.alat']);

        if ($request->filled('start_date')) {
            $pengembalianQuery->whereDate('tanggal_kembali_realisasi', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $pengembalianQuery->whereDate('tanggal_kembali_realisasi', '<=', $request->end_date);
        }

        if ($request->filled('keterlambatan')) {
            if ($request->keterlambatan == 'tepat') {
                $pengembalianQuery->whereHas('peminjaman', function($q) {
                    $q->whereRaw('tanggal_kembali_realisasi <= tanggal_kembali_rencana');
                });
            } elseif ($request->keterlambatan == 'terlambat') {
                $pengembalianQuery->whereHas('peminjaman', function($q) {
                    $q->whereRaw('tanggal_kembali_realisasi > tanggal_kembali_rencana');
                });
            }
        }

        $pengembalian = $pengembalianQuery->latest()->get();

        // Statistik Pengembalian
        $totalPengembalian = $pengembalian->count();
        $tepatWaktu = $pengembalian->filter(function($item) {
            return $item->peminjaman && 
                   $item->tanggal_kembali_realisasi <= $item->peminjaman->tanggal_kembali_rencana;
        })->count();
        $terlambat = $pengembalian->filter(function($item) {
            return $item->peminjaman && 
                   $item->tanggal_kembali_realisasi > $item->peminjaman->tanggal_kembali_rencana;
        })->count();
        $kondisiBaik = $pengembalian->filter(function($item) {
            return str_contains($item->kondisi_kembali ?? '', 'baik');
        })->count();

        return view('reports.index', compact(
            'activeTab',
            'kategori',

            // Peminjaman
            'peminjaman',
            'totalPeminjaman',
            'peminjamanAktif',
            'peminjamanTerlambat',
            'peminjamanDikembalikan',

            // Alat
            'alat',
            'totalAlat',
            'alatTersedia',
            'alatDipinjam',
            'alatRusak',

            // Pengembalian
            'pengembalian',
            'totalPengembalian',
            'tepatWaktu',
            'terlambat',
            'kondisiBaik'
        ));
    }

    // Method untuk export PDF
    public function exportPDF(Request $request)
    {
        $type = $request->get('type', 'peminjaman');

        switch ($type) {
            case 'peminjaman':
                $data = $this->getPeminjamanData($request);
                $pdf = PDF::loadView('reports.pdf.peminjaman', $data);
                return $pdf->download('laporan-peminjaman.pdf');

            case 'alat':
                $data = $this->getAlatData($request);
                $pdf = PDF::loadView('reports.pdf.alat', $data);
                return $pdf->download('laporan-alat.pdf');

            case 'pengembalian':
                $data = $this->getPengembalianData($request);
                $pdf = PDF::loadView('reports.pdf.pengembalian', $data);
                return $pdf->download('laporan-pengembalian.pdf');

            case 'keuangan':
                $data = $this->getKeuanganData($request);
                $pdf = PDF::loadView('reports.pdf.keuangan', $data);
                return $pdf->download('laporan-keuangan.pdf');

            default:
                return back()->with('error', 'Tipe laporan tidak valid');
        }
    }

    // Method untuk export Excel
    public function exportExcel(Request $request)
    {
        $type = $request->get('type', 'peminjaman');

        switch ($type) {
            case 'peminjaman':
                return Excel::download(new PeminjamanExport(
                    $request->start_date,
                    $request->end_date,
                    $request->status
                ), 'laporan-peminjaman.xlsx');

            case 'alat':
                return Excel::download(new AlatExport(
                    $request->kategori,
                    $request->kondisi,
                    $request->stok,
                    $request->lokasi
                ), 'laporan-alat.xlsx');

            case 'pengembalian':
                return Excel::download(new PengembalianExport(
                    $request->start_date,
                    $request->end_date,
                    $request->keterlambatan
                ), 'laporan-pengembalian.xlsx');

            case 'keuangan':
                return Excel::download(new KeuanganExport(
                    $request->start_date,
                    $request->end_date
                ), 'laporan-keuangan.xlsx');

            default:
                return back()->with('error', 'Tipe laporan tidak valid');
        }
    }

    // ================= PRIVATE HELPER METHODS =================

    private function getPeminjamanData(Request $request)
    {
        $peminjamanQuery = Peminjaman::with(['user', 'detailPeminjaman.alat']);

        if ($request->filled('start_date')) {
            $peminjamanQuery->whereDate('tanggal_pinjam', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $peminjamanQuery->whereDate('tanggal_pinjam', '<=', $request->end_date);
        }
        if ($request->filled('status')) {
            $peminjamanQuery->where('status', $request->status);
        }

        $peminjaman = $peminjamanQuery->latest()->get();

        return [
            'peminjaman' => $peminjaman,
            'totalPeminjaman' => $peminjaman->count(),
            'peminjamanAktif' => $peminjaman->where('status', 'dipinjam')->count(),
            'peminjamanTerlambat' => $peminjaman->where('status', 'terlambat')->count(),
            'peminjamanDikembalikan' => $peminjaman->where('status', 'dikembalikan')->count(),
        ];
    }

    private function getAlatData(Request $request)
    {
        $kategori = Kategori::all();
        $alatQuery = Alat::with('kategori');

        if ($request->filled('kategori')) {
            $alatQuery->where('kategori_id', $request->kategori);
        }
        if ($request->filled('kondisi')) {
            $alatQuery->where('kondisi', $request->kondisi);
        }
        if ($request->filled('stok')) {
            if ($request->stok == 'aman') {
                $alatQuery->where('jumlah_tersedia', '>', 5);
            } elseif ($request->stok == 'sedikit') {
                $alatQuery->whereBetween('jumlah_tersedia', [1, 5]);
            } elseif ($request->stok == 'habis') {
                $alatQuery->where('jumlah_tersedia', 0);
            }
        }
        if ($request->filled('lokasi')) {
            $alatQuery->where('lokasi', 'like', '%' . $request->lokasi . '%');
        }

        $alat = $alatQuery->get();

        return [
            'alat' => $alat,
            'totalAlat' => $alat->count(),
            'alatTersedia' => $alat->sum('jumlah_tersedia'),
            'alatDipinjam' => $alat->sum('jumlah_total') - $alat->sum('jumlah_tersedia'),
            'alatRusak' => $alat->where('kondisi', 'rusak')->count(),
            'kategori' => $kategori,
        ];
    }

    private function getPengembalianData(Request $request)
    {
        $pengembalianQuery = Pengembalian::with(['peminjaman.user', 'peminjaman.detailPeminjaman.alat']);

        if ($request->filled('start_date')) {
            $pengembalianQuery->whereDate('tanggal_kembali_realisasi', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $pengembalianQuery->whereDate('tanggal_kembali_realisasi', '<=', $request->end_date);
        }
        if ($request->filled('keterlambatan')) {
            if ($request->keterlambatan == 'tepat') {
                $pengembalianQuery->whereHas('peminjaman', function($q) {
                    $q->whereRaw('tanggal_kembali_realisasi <= tanggal_kembali_rencana');
                });
            } elseif ($request->keterlambatan == 'terlambat') {
                $pengembalianQuery->whereHas('peminjaman', function($q) {
                    $q->whereRaw('tanggal_kembali_realisasi > tanggal_kembali_rencana');
                });
            }
        }

        $pengembalian = $pengembalianQuery->latest()->get();

        return [
            'pengembalian' => $pengembalian,
            'totalPengembalian' => $pengembalian->count(),
            'tepatWaktu' => $pengembalian->filter(function($item) {
                return $item->peminjaman && 
                       $item->tanggal_kembali_realisasi <= $item->peminjaman->tanggal_kembali_rencana;
            })->count(),
            'terlambat' => $pengembalian->filter(function($item) {
                return $item->peminjaman && 
                       $item->tanggal_kembali_realisasi > $item->peminjaman->tanggal_kembali_rencana;
            })->count(),
            'kondisiBaik' => $pengembalian->filter(function($item) {
                return str_contains($item->kondisi_kembali ?? '', 'baik');
            })->count(),
        ];
    }

    private function getKeuanganData(Request $request)
    {
        $peminjamanQuery = Peminjaman::with(['user', 'detailPeminjaman.alat']);

        if ($request->filled('start_date')) {
            $peminjamanQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $peminjamanQuery->whereDate('created_at', '<=', $request->end_date);
        }
        $peminjaman = $peminjamanQuery->get();

        $pengembalianQuery = Pengembalian::with(['peminjaman.user']);
        if ($request->filled('start_date')) {
            $pengembalianQuery->whereDate('tanggal_kembali_realisasi', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $pengembalianQuery->whereDate('tanggal_kembali_realisasi', '<=', $request->end_date);
        }
        $pengembalian = $pengembalianQuery->get();

        return [
            'totalDenda' => $pengembalian->sum('denda'),
            'totalDendaDibayar' => $pengembalian->sum('denda_dibayar'),
            'sisaDenda' => $pengembalian->sum('denda') - $pengembalian->sum('denda_dibayar'),
            'totalPeminjaman' => $peminjaman->count(),
            'totalTerlambat' => $peminjaman->where('status', 'terlambat')->count(),
            'totalDendaLunas' => $pengembalian->where('status_denda', 'lunas')->count(),
            'totalDendaBelum' => $pengembalian->where('status_denda', '!=', 'lunas')->where('denda', '>', 0)->count(),
            'peminjaman' => $peminjaman,
            'pengembalian' => $pengembalian,
        ];
    }
}