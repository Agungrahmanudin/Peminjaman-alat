<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alat', function (Blueprint $table) {
            $table->id();
            $table->string('kode_alat')->unique();
            $table->string('nama_alat');
            $table->foreignId('kategori_id')->constrained('kategori')->onDelete('cascade');
            $table->enum('kondisi', ['baik', 'rusak', 'perbaikan'])->default('baik');
            $table->integer('jumlah_total')->default(0);
            $table->integer('jumlah_tersedia')->default(0);
            $table->string('lokasi');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alat');
    }
};