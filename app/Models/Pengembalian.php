<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    use HasFactory;

    protected $table = 'pengembalian';

    // di app/Models/Pengembalian.php
protected $fillable = [
    'peminjaman_id',
    'tanggal_kembali_realisasi',
    'kondisi_kembali',
    'denda',
    'denda_dibayar',
    'status_denda',
    'tanggal_pembayaran_denda',
    'metode_pembayaran',
    'bukti_pembayaran',
    'catatan',

    'status_pengembalian',
    'kondisi_admin',
    'catatan_admin',
];

    // Pengembalian milik satu peminjaman
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    // Method untuk menghitung persentase denda yang sudah dibayar
    public function persentaseDendaDibayar()
    {
        if ($this->denda == 0) {
            return 100;
        }

        return round(($this->denda_dibayar / $this->denda) * 100);
    }
}
