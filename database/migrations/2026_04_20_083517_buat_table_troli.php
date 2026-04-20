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
        Schema::create('troli', function (Blueprint $table) {
            $table->id();
            $table->string('invoice', 16)->default('-');
            $table->enum('keperluan', ['OK', 'In Proses', 'Scan'])->default('OK');
            $table->enum('jenis', ['Body', 'Tangki'])->default('Body');
            $table->enum('status', ['Proses', 'Selesai', 'Selesai Bongkar'])->default('Proses');
            // is_output = 1 artinya troli ini disiapkan untuk menampung hasil scan (wadah baru)
            // is_output = 0 artinya troli ini adalah kiriman dari dept sebelumnya (troli sumber)
            $table->boolean('is_output')->default(false);
            $table->foreignId('proses_id')->constrained('proses')->cascadeOnDelete();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('troli');
    }
};
