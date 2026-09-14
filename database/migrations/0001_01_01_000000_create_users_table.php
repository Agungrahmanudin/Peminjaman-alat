<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
    $table->id();

    $table->string('name')->nullable();
    $table->string('username')->unique();
    $table->string('company')->nullable();
    $table->string('email')->unique()->nullable();
    $table->string('password');
    $table->enum('role', ['admin', 'user', 'peminjam'])->default('peminjam');
    $table->string('phone')->nullable();
    $table->string('location')->nullable();
    $table->string('avatar')->nullable();
    $table->string('cover')->nullable();
    $table->boolean('status')->default(true);
    $table->timestamp('email_verified_at')->nullable();
    $table->rememberToken();
    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
