<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambah kolom di tabel peminjaman
        Schema::table('peminjaman', function (Blueprint $table) {
            if (!Schema::hasColumn('peminjaman', 'denda_dibayar')) {
                $table->integer('denda_dibayar')->default(0)->after('status');
            }
            
            if (!Schema::hasColumn('peminjaman', 'status_denda')) {
                $table->enum('status_denda', ['belum', 'sebagian', 'lunas'])->default('belum')->after('denda_dibayar');
            }
        });

        // Tambah kolom di tabel pengembalian
        Schema::table('pengembalian', function (Blueprint $table) {
            if (!Schema::hasColumn('pengembalian', 'denda_dibayar')) {
                $table->integer('denda_dibayar')->default(0)->after('denda');
            }
            
            if (!Schema::hasColumn('pengembalian', 'status_denda')) {
                $table->enum('status_denda', ['belum', 'sebagian', 'lunas'])->default('belum')->after('denda_dibayar');
            }
            
            if (!Schema::hasColumn('pengembalian', 'tanggal_pembayaran_denda')) {
                $table->timestamp('tanggal_pembayaran_denda')->nullable()->after('status_denda');
            }
            
            if (!Schema::hasColumn('pengembalian', 'metode_pembayaran')) {
                $table->string('metode_pembayaran')->nullable()->after('tanggal_pembayaran_denda');
            }
            
            if (!Schema::hasColumn('pengembalian', 'bukti_pembayaran')) {
                $table->string('bukti_pembayaran')->nullable()->after('metode_pembayaran');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn(['denda_dibayar', 'status_denda']);
        });

        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dropColumn([
                'denda_dibayar', 
                'status_denda', 
                'tanggal_pembayaran_denda',
                'metode_pembayaran',
                'bukti_pembayaran'
            ]);
        });
    }
};