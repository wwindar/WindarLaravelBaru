<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Gantikan tabel artikel lama dengan tabel tulisan baru.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('artikel');

        Schema::create('tulisan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kategori_id')->nullable()->constrained('kategori')->nullOnDelete();
            $table->string('jenis', 20); // puisi|cerpen|artikel|esai
            $table->string('slug')->unique();
            $table->string('judul', 255);
            $table->longText('isi');
            $table->text('ringkasan')->nullable();
            $table->string('status', 30)->default('draf'); // backed enum sebagai string
            $table->text('catatan_editor')->nullable();
            $table->timestamp('diterbitkan_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tulisan');
    }
};
