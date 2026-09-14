<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('peminjaman', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users');
    $table->date('tanggal_pinjam')->nullable();
    $table->date('tanggal_kembali_rencana')->nullable();
    $table->enum('status', ['dipinjam','dikembalikan','terlambat']);
    $table->string('keperluan')->nullable();
    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};