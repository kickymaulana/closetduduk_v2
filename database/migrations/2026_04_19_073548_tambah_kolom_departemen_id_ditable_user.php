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
        Schema::table('users', function (Blueprint $table) {
        $table->foreignId('departemen_id')
              ->nullable() // Sebaiknya nullable dulu agar user yang sudah ada tidak error
              ->after('id') // Meletakkan kolom setelah 'id'
              ->constrained('master_departemen') // Menunjuk ke tabel departments
              ->onDelete('set null'); // Jika departemen dihapus, kolom di user jadi null
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cara hapus foreign key: [nama_kolom]
            $table->dropForeign(['departemen_id']);
            $table->dropColumn('departemen_id');
        });
    }
};
