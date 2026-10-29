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
        Schema::create('artikel', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('judul');
            $table->text('ringkasan');
            $table->longText('isi');
            $table->enum('jenis', ['berita', 'blog'])->default('berita');
            $table->foreignId('kategori_id')->constrained('kategori')->onDelete('restrict');
            $table->string('penulis');
            $table->enum('status', ['draft', 'terbit'])->default('draft');
            $table->timestamp('diterbitkan_pada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artikel');
    }
};

