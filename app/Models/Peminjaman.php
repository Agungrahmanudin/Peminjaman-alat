<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;
    protected $table = 'peminjaman';
    protected $fillable = [
        'user_id',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali_realisasi',
        'status',
        'status_approval',
        'approved_by',
        'approved_at',
        'alasan_penolakan',
        'keperluan',
        'denda',
        'denda_dibayar',
        'status_denda',
        // Perpanjangan
        'status_perpanjangan',
        'tanggal_perpanjangan_diminta',
        'perpanjangan_approved_by',
        'perpanjangan_approved_at',
        'alasan_tolak_perpanjangan',
    ];

    protected $casts = [
        'tanggal_pinjam'               => 'datetime',
        'tanggal_kembali_rencana'      => 'datetime',
        'tanggal_kembali_realisasi'    => 'datetime',
        'approved_at'                  => 'datetime',
        'tanggal_perpanjangan_diminta' => 'date',
        'perpanjangan_approved_at'     => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function detailPeminjaman()
    {
        return $this->hasMany(DetailPeminjaman::class);
    }

    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class);
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->status_approval === 'menunggu') {
            return 'badge bg-warning';
        } elseif ($this->status_approval === 'ditolak') {
            return 'badge bg-danger';
        } elseif ($this->status === 'dipinjam') {
            return 'badge bg-info';
        } elseif ($this->status === 'terlambat') {
            return 'badge bg-danger';
        } elseif ($this->status === 'dikembalikan') {
            return 'badge bg-success';
        }
        return 'badge bg-secondary';
    }

    public function getStatusTextAttribute()
    {
        if ($this->status_approval === 'menunggu') {
            return 'Menunggu Persetujuan';
        } elseif ($this->status_approval === 'ditolak') {
            return 'Ditolak';
        } elseif ($this->status === 'menunggu_pengembalian') {
            return 'Menunggu Pengembalian';
        } elseif ($this->status === 'dipinjam') {
            return 'Dipinjam';
        } elseif ($this->status === 'terlambat') {
            return 'Terlambat';
        } elseif ($this->status === 'dikembalikan') {
            return 'Dikembalikan';
        }

        return '-';
    }
}
