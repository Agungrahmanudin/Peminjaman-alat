<?php

namespace App\Exports;

use App\Models\Alat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AlatExport implements FromCollection, WithHeadings, WithMapping
{
    protected $kategori;
    protected $kondisi;
    protected $stok;
    protected $lokasi;

    public function __construct($kategori = null, $kondisi = null, $stok = null, $lokasi = null)
    {
        $this->kategori = $kategori;
        $this->kondisi = $kondisi;
        $this->stok = $stok;
        $this->lokasi = $lokasi;
    }

    public function collection()
    {
        $query = Alat::with('kategori');

        if ($this->kategori) {
            $query->where('kategori_id', $this->kategori);
        }
        if ($this->kondisi) {
            $query->where('kondisi', $this->kondisi);
        }
        if ($this->stok) {
            if ($this->stok == 'aman') {
                $query->where('jumlah_tersedia', '>', 5);
            } elseif ($this->stok == 'sedikit') {
                $query->whereBetween('jumlah_tersedia', [1, 5]);
            } elseif ($this->stok == 'habis') {
                $query->where('jumlah_tersedia', 0);
            }
        }
        if ($this->lokasi) {
            $query->where('lokasi', 'like', '%' . $this->lokasi . '%');
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Alat',
            'Nama Alat',
            'Kategori',
            'Kondisi',
            'Total Stok',
            'Tersedia',
            'Lokasi',
        ];
    }

    public function map($item): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $item->kode_alat,
            $item->nama_alat,
            $item->kategori->nama_kategori ?? '-',
            ucfirst($item->kondisi),
            $item->jumlah_total,
            $item->jumlah_tersedia,
            $item->lokasi,
        ];
    }
}