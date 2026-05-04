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
        Schema::table('produk', function (Blueprint $table) {
            $table->foreignId('kualitas_id')->nullable()->after('troli_id')->constrained('kualitas')->nullOnDelete();
            $table->foreignId('warna_id')->nullable()->after('kualitas_id')->constrained('warna')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropForeign(['kualitas_id']);
            $table->dropForeign(['warna_id']);
            $table->dropColumn(['kualitas_id', 'warna_id']);
        });
    }
};
