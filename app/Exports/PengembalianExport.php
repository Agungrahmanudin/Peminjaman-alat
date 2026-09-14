<?php

namespace App\Exports;

use App\Models\Pengembalian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PengembalianExport implements FromCollection, WithHeadings, WithMapping
{
    protected $start_date;
    protected $end_date;
    protected $keterlambatan;

    public function __construct($start_date = null, $end_date = null, $keterlambatan = null)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->keterlambatan = $keterlambatan;
    }

    public function collection()
    {
        $query = Pengembalian::with(['peminjaman.user', 'peminjaman.detailPeminjaman.alat']);

        if ($this->start_date) {
            $query->whereDate('tanggal_kembali_realisasi', '>=', $this->start_date);
        }
        if ($this->end_date) {
            $query->whereDate('tanggal_kembali_realisasi', '<=', $this->end_date);
        }
        if ($this->keterlambatan) {
            if ($this->keterlambatan == 'tepat') {
                $query->whereHas('peminjaman', function($q) {
                    $q->whereRaw('tanggal_kembali_realisasi <= tanggal_kembali_rencana');
                });
            } elseif ($this->keterlambatan == 'terlambat') {
                $query->whereHas('peminjaman', function($q) {
                    $q->whereRaw('tanggal_kembali_realisasi > tanggal_kembali_rencana');
                });
            }
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'ID Pinjam',
            'Peminjam',
            'Alat',
            'Tanggal Kembali Realisasi',
            'Status',
            'Denda',
        ];
    }

    public function map($item): array
    {
        static $no = 0;
        $no++;

        $alat = $item->peminjaman->detailPeminjaman->map(function($detail) {
            return $detail->alat->nama_alat . ' (' . $detail->jumlah_pinjam . ' pcs)';
        })->implode(', ');

        $status = '-';
        if ($item->peminjaman) {
            $status = $item->tanggal_kembali_realisasi <= $item->peminjaman->tanggal_kembali_rencana ? 'Tepat Waktu' : 'Terlambat';
        }

        return [
            $no,
            $item->peminjaman_id,
            $item->peminjaman->user->name ?? '-',
            $alat,
            \Carbon\Carbon::parse($item->tanggal_kembali_realisasi)->format('d/m/Y'),
            $status,
            number_format($item->denda, 0, ',', '.'),
        ];
    }
}