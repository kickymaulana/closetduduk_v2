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
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('qrcode', 10);
            $table->string('nama', 100);
            $table->enum('jenis', ['Body', 'Tangki'])->default('Body');
            $table->enum('status_akhir', ['OK', 'In Proses', 'Buang'])->default('OK');
            $table->enum('sudah_scan', ['Belum', 'Sudah'])->default('Belum');
            $table->unsignedBigInteger('troli_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
