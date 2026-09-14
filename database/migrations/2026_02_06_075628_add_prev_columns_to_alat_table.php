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
    Schema::table('alat', function (Blueprint $table) {
        $table->integer('jumlah_total_prev')->default(0);
        $table->integer('jumlah_tersedia_prev')->default(0);
        $table->integer('alat_rusak_prev')->default(0);
    });
}

public function down(): void
{
    Schema::table('alat', function (Blueprint $table) {
        $table->dropColumn([
            'jumlah_total_prev',
            'jumlah_tersedia_prev',
            'alat_rusak_prev'
        ]);
    });
}
};
