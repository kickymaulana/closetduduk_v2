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
        Schema::table('troli', function (Blueprint $table) {
            // Mengubah kolom proses_id agar bisa kosong (NULL)
            $table->foreignId('proses_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('troli', function (Blueprint $table) {
            // Kembalikan ke semula (tidak boleh NULL) jika di-rollback
            // Pastikan tidak ada data NULL di tabel sebelum melakukan rollback
            $table->foreignId('proses_id')->nullable(false)->change();
        });
    }
};
