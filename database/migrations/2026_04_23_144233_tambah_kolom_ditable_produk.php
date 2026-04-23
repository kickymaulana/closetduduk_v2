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
            $table->string('nomor_mesin')->nullable()->after('id');
            $table->string('nomor_mould')->nullable()->after('nomor_mesin');
            $table->string('asal_slip')->nullable()->after('nomor_mould');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn(['nomor_mesin', 'nomor_mould', 'asal_slip']);
        });
    }
};
