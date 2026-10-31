<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel tulisan (pengganti artikel & karya lama)
        Schema::dropIfExists('riwayat_status_tulisan');
        Schema::dropIfExists('laporan_tulisan');
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('tulisan');
        Schema::create('tulisan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kategori_id')->nullable()->constrained('kategori')->nullOnDelete();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->enum('jenis', ['puisi', 'cerpen', 'artikel', 'esai'])->default('artikel');
            $table->longText('isi');
            $table->text('ringkasan')->nullable();
            $table->enum('status', ['draf', 'menunggu', 'terbit', 'perlu_revisi', 'ditolak'])->default('draf');
            $table->text('catatan_editor')->nullable();
            $table->timestamp('diterbitkan_pada')->nullable();
            $table->timestamps();

            $table->index(['status', 'diterbitkan_pada']);
            $table->index(['user_id', 'status']);
        });

        // Riwayat perubahan status tulisan
        Schema::create('riwayat_status_tulisan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tulisan_id')->constrained('tulisan')->cascadeOnDelete();
            $table->enum('status', ['draf', 'menunggu', 'terbit', 'perlu_revisi', 'ditolak']);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Laporan tulisan dari pengunjung publik
        Schema::create('laporan_tulisan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tulisan_id')->constrained('tulisan')->cascadeOnDelete();
            $table->string('nama_pelapor')->nullable();
            $table->string('email_pelapor')->nullable();
            $table->text('alasan');
            $table->enum('status', ['menunggu', 'ditinjau', 'selesai'])->default('menunggu');
            $table->timestamps();
        });

        // Notifikasi in-app untuk user
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('judul');
            $table->text('pesan')->nullable();
            $table->boolean('dibaca')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'dibaca']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('laporan_tulisan');
        Schema::dropIfExists('riwayat_status_tulisan');
        Schema::dropIfExists('tulisan');
    }
};
