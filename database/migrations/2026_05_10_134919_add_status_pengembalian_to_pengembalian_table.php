<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->enum('status_pengembalian', [
                'menunggu',
                'disetujui',
                'ditolak'
            ])->default('menunggu');

            $table->string('kondisi_admin')->nullable();
            $table->text('catatan_admin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            //
        });
    }
};
