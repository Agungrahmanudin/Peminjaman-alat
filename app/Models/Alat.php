<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alat extends Model
{
    use HasFactory;

    protected $table = 'alat';

    protected $fillable = [
        'kode_alat',
        'nama_alat',
        'kategori_id',
        'kondisi',
        'harga_alat', // <-- Kolom baru ditambahkan di sini
        'jumlah_total',
        'jumlah_tersedia',
        'lokasi',
        'keterangan',
    ];

    protected $casts = [
        'jumlah_total' => 'integer',
        'jumlah_tersedia' => 'integer',
        'harga_alat' => 'integer', // <-- Memastikan data menjadi integer
    ];

    /**
     * Relasi ke kategori
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /**
     * Relasi ke detail peminjaman
     */
    public function detailPeminjaman(): HasMany
    {
        return $this->hasMany(DetailPeminjaman::class, 'alat_id');
    }

    public function pinjam($jumlah)
    {
        if ($this->jumlah_tersedia < $jumlah) {
            throw new \Exception("Stok tidak cukup");
        }

        $this->decrement('jumlah_tersedia', $jumlah);
    }

    public function kembali($jumlah)
    {
        $this->increment('jumlah_tersedia', $jumlah);
    }

    /**
     * Accessor untuk status stok
     */
    public function getStatusStokAttribute(): string
    {
        if ($this->jumlah_tersedia > 5) {
            return 'aman';
        } elseif ($this->jumlah_tersedia > 0) {
            return 'sedikit';
        } else {
            return 'habis';
        }
    }

    /**
     * Scope untuk alat yang tersedia
     */
    public function scopeTersedia($query)
    {
        return $query->where('jumlah_tersedia', '>', 0);
    }

    /**
     * Scope untuk alat dengan kondisi tertentu
     */
    public function scopeKondisi($query, $kondisi)
    {
        return $query->where('kondisi', $kondisi);
    }

    /**
     * Scope untuk alat dalam kategori tertentu
     */
    public function scopeDalamKategori($query, $kategoriId)
    {
        return $query->where('kategori_id', $kategoriId);
    }

    /**
     * Method untuk mengecek ketersediaan
     */
    public function isTersedia(): bool
    {
        return $this->jumlah_tersedia > 0 && $this->kondisi === 'baik';
    }

    /**
     * Method untuk mengurangi stok tersedia
     */
    public function kurangiStok(int $jumlah = 1): bool
    {
        if ($this->jumlah_tersedia >= $jumlah) {
            $this->jumlah_tersedia -= $jumlah;
            return $this->save();
        }
        return false;
    }

    /**
     * Method untuk menambah stok tersedia
     */
    public function tambahStok(int $jumlah = 1): bool
    {
        $this->jumlah_tersedia += $jumlah;

        // Pastikan tidak melebihi jumlah total
        if ($this->jumlah_tersedia > $this->jumlah_total) {
            $this->jumlah_tersedia = $this->jumlah_total;
        }

        return $this->save();
    }
}