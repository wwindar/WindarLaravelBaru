<?php

namespace Database\Seeders;

use App\Models\Berita;
use App\Models\User;
use Illuminate\Database\Seeder;

class BeritaSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::whereIn('role', ['admin', 'dosen'])->first() ?? User::first();

        if (! $admin) {
            return;
        }

        $beritaList = [
            [
                'judul' => 'Pengumuman Sayembara Cipta Puisi Nasional 2026',
                'slug' => 'pengumuman-sayembara-cipta-puisi-nasional-2026',
                'ringkasan' => 'Dewan Redaksi Aksara Kampus membuka pendaftaran sayembara cipta puisi bertema Keberanian Bersuara untuk seluruh mahasiswa aktif.',
                'isi' => "Halo Civitas Akademika!\n\nDewan Redaksi Aksara Kampus dengan bangga mengumumkan Sayembara Cipta Puisi Nasional 2026. Kompetisi ini mengusung tema 'Keberanian Bersuara di Era Digital'. Terbuka untuk seluruh mahasiswa jenjang diploma dan sarjana di seluruh perguruan tinggi Indonesia.\n\nSyarat dan Ketentuan:\n1. Naskah karya asli dan belum pernah dipublikasikan di media cetak maupun daring nasional.\n2. Tiap peserta dapat mengirimkan maksimal 2 karya puisi melalui portal Aksara Kampus.\n3. Batas akhir pengiriman naskah adalah 30 November 2026.\n\nPemenang akan mendapatkan hadiah total jutaan rupiah serta penerbitan karya dalam antologi cetak resmi Aksara Kampus.",
                'status' => 'terbit',
                'diterbitkan_pada' => now()->subDays(1),
            ],
            [
                'judul' => 'Workshop Penulisan Esai Kritis bersama Redaksi Aksara',
                'slug' => 'workshop-penulisan-esai-kritis-bersama-redaksi-aksara',
                'ringkasan' => 'Tingkatkan kemampuan bernalar analitis dan menyusun esai argumentatif yang memikat bersama dewan kurator sastra.',
                'isi' => "Menulis esai bukan sekadar merangkai opini tanpa dasar. Diperlukan ketajaman berpikir, struktur argumen yang kokoh, serta gaya bahasa yang lugas namun menggugah nurani pembaca.\n\nIkuti Workshop Penulisan Esai Kritis yang akan diselenggarakan pada akhir pekan ini secara daring melalui Zoom Meeting. Gratis dan terbuka untuk seluruh mahasiswa terdaftar.\n\nPembicara:\n- Dr. Budi Santoso (Dosen & Kurator Esai Aksara Kampus)\n- Prof. Sari Dewi (Pemerhati Sastra & Budaya)\n\nSegera amankan slot Anda melalui tautan pendaftaran di kanal pengumuman!",
                'status' => 'terbit',
                'diterbitkan_pada' => now()->subDays(3),
            ],
            [
                'judul' => 'Info Kerja Sama: Aksara Kampus Hadir di Portal Digital',
                'slug' => 'info-kerja-sama-aksara-kampus-hadir-di-portal-digital',
                'ringkasan' => 'Aksara Kampus kini telah terintegrasi penuh sebagai media apresiasi karya sastra dan pemikiran kampus.',
                'isi' => "Setelah melalui tahap pengembangan intensif, portal Aksara Kampus resmi diluncurkan secara publik. Platform ini didedikasikan untuk memfasilitasi karya sastra mahasiswa (puisi, cerpen, esai, dan artikel) dengan mekanisme kurasi yang transparan dan mendidik dari dewan dosen kurator.\n\nMari bersama merawat nalar kritis dan kreativitas sastra di lingkungan kampus kita tercinta!",
                'status' => 'terbit',
                'diterbitkan_pada' => now()->subDays(6),
            ],
        ];

        foreach ($beritaList as $data) {
            Berita::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['user_id' => $admin->id])
            );
        }
    }
}
