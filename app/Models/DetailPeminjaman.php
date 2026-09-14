<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPeminjaman extends Model
{
    use HasFactory;

    protected $table = 'detail_peminjaman';

    protected $fillable = [
        'peminjaman_id',
        'alat_id',
        'jumlah_pinjam',
        'kondisi_pinjam',
    ];

    // Detail milik satu peminjaman
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    // Detail milik satu alat
    public function alat()
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }
}
