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
        Schema::table('sesi_kerja', function (Blueprint $table) {
            $table->foreignId('shift_id')
              ->nullable() // Sebaiknya nullable dulu agar user yang sudah ada tidak error
              ->after('jenis') // Meletakkan kolom setelah 'id'
              ->constrained('shift') // Menunjuk ke tabel departments
              ->onDelete('set null'); // Jika departemen dihapus, kolom di user jadi null
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sesi_kerja', function (Blueprint $table) {
            // Cara hapus foreign key: [nama_kolom]
            $table->dropForeign(['shift_id']);
            $table->dropColumn('shift_id');
        });
    }
};
