<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanDenda extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_denda';
    protected $fillable = ['kode', 'nama', 'nilai', 'satuan', 'keterangan', 'aktif'];

    /**
     * Ambil nilai pengaturan berdasarkan kode
     */
    public static function getValue($kode, $default = 0)
    {
        $pengaturan = self::where('kode', $kode)
            ->where('aktif', true)
            ->first();
        
        return $pengaturan ? $pengaturan->nilai : $default;
    }

    /**
     * Ambil denda per hari
     */
    public static function getDendaPerHari()
    {
        return self::getValue('DENDA_PER_HARI', 10000);
    }

    /**
     * Ambil maksimal hari denda
     */
    public static function getMaxHariDenda()
    {
        return self::getValue('DENDA_MAX_HARI', 30);
    }
}