<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Gantikan tabel riwayat_karya lama dengan laporan_tulisan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('riwayat_karya');

        Schema::create('laporan_tulisan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tulisan_id')->constrained('tulisan')->cascadeOnDelete();
            $table->string('nama_pelapor', 100);
            $table->string('email_pelapor', 150);
            $table->text('alasan');
            $table->string('status', 20)->default('menunggu'); // menunggu|ditinjau|selesai
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_tulisan');
    }
};
