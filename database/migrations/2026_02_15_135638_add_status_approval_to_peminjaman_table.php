<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->enum('status_approval', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu')->after('status');
            $table->foreignId('approved_by')->nullable()->constrained('users')->after('status_approval');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('alasan_penolakan')->nullable()->after('approved_at');
        });
    }

    public function down()
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn(['status_approval', 'approved_by', 'approved_at', 'alasan_penolakan']);
        });
    }
};