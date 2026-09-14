<?php

namespace App\Exports;

use App\Models\Peminjaman;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PeminjamanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $start_date;
    protected $end_date;
    protected $status;

    public function __construct($start_date = null, $end_date = null, $status = null)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->status = $status;
    }

    public function collection()
    {
        $query = Peminjaman::with(['user', 'detailPeminjaman.alat']);

        if ($this->start_date) {
            $query->whereDate('tanggal_pinjam', '>=', $this->start_date);
        }
        if ($this->end_date) {
            $query->whereDate('tanggal_pinjam', '<=', $this->end_date);
        }
        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'ID Peminjaman',
            'Peminjam',
            'Alat',
            'Tanggal Pinjam',
            'Tanggal Rencana Kembali',
            'Tanggal Realisasi Kembali',
            'Status',
        ];
    }

    public function map($item): array
    {
        static $no = 0;
        $no++;

        $alat = $item->detailPeminjaman->map(function($detail) {
            return $detail->alat->nama_alat . ' (' . $detail->jumlah_pinjam . ' pcs)';
        })->implode(', ');

        return [
            $no,
            $item->id,
            $item->user->name ?? '-',
            $alat,
            \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y'),
            \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d/m/Y'),
            $item->tanggal_kembali_realisasi ? \Carbon\Carbon::parse($item->tanggal_kembali_realisasi)->format('d/m/Y') : '-',
            ucfirst($item->status),
        ];
    }
}