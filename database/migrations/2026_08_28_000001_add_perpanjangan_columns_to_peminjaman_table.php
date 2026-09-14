<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Status pengajuan perpanjangan: null = belum pernah ajukan, menunggu, disetujui, ditolak
            $table->enum('status_perpanjangan', ['menunggu', 'disetujui', 'ditolak'])
                  ->nullable()->default(null)->after('status_approval');

            // Tanggal perpanjangan yang diminta peminjam
            $table->date('tanggal_perpanjangan_diminta')->nullable()->after('status_perpanjangan');

            // Siapa admin yang merespons
            $table->foreignId('perpanjangan_approved_by')->nullable()
                  ->constrained('users')->nullOnDelete()->after('tanggal_perpanjangan_diminta');

            $table->timestamp('perpanjangan_approved_at')->nullable()->after('perpanjangan_approved_by');
            $table->text('alasan_tolak_perpanjangan')->nullable()->after('perpanjangan_approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropConstrainedForeignId('perpanjangan_approved_by');
            $table->dropColumn([
                'status_perpanjangan',
                'tanggal_perpanjangan_diminta',
                'perpanjangan_approved_at',
                'alasan_tolak_perpanjangan',
            ]);
        });
    }
};
