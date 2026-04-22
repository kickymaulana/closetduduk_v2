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
        Schema::create('pengerjaan_produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('produk_id')->constrained('produk')->onDelete('cascade');
            $table->foreignId('sesi_kerja_id')->constrained('sesi_kerja')->onDelete('cascade');
            $table->foreignId('proses_id')->constrained('proses')->onDelete('cascade');
            $table->enum('status_kondisi', ['OK', 'In Proses', 'Buang'])->default('OK');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengerjaan_produk');
    }
};
