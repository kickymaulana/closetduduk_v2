<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Sistem troli sudah dihapus dari alur aplikasi
        Schema::dropIfExists('troli');
        // Tabel sisa eksperimen yang tidak memiliki model
        Schema::dropIfExists('riwayat_ganti_qr');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak di-reverse karena struktur asli dibuat oleh migration lama
    }
};