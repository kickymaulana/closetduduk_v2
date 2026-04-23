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
        Schema::create('pengerjaan_cacat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengerjaan_produk_id')->constrained('pengerjaan_produk')->onDelete('cascade');
            $table->foreignId('cacat_id')->constrained('cacat')->onDelete('cascade');
            $table->foreignId('user_scan_id')->constrained('users');
            $table->foreignId('proses_scan_id')->constrained('proses');
            $table->foreignId('user_pj_id')->nullable()->constrained('users');
            $table->foreignId('proses_pj_id')->nullable()->constrained('proses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengerjaan_cacat');
    }
};
