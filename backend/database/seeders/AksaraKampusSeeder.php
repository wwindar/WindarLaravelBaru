<?php

namespace Database\Seeders;

use App\Enums\StatusTulisan;
use App\Models\Agenda;
use App\Models\Kategori;
use App\Models\Tulisan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AksaraKampusSeeder extends Seeder
{
    public function run(): void
    {
        // Kategori
        $kategoriData = [
            ['slug' => 'sastra-indonesia', 'nama' => 'Sastra Indonesia'],
            ['slug' => 'fiksi',            'nama' => 'Fiksi'],
            ['slug' => 'nonfiksi',         'nama' => 'Non-fiksi'],
            ['slug' => 'budaya',           'nama' => 'Budaya & Seni'],
            ['slug' => 'ilmiah',           'nama' => 'Ilmiah Populer'],
        ];

        foreach ($kategoriData as $k) {
            Kategori::firstOrCreate(['slug' => $k['slug']], $k);
        }

        $kategoriSastra = Kategori::where('slug', 'sastra-indonesia')->first();
        $kategoriFiksi = Kategori::where('slug', 'fiksi')->first();

        // Mahasiswa contoh
        $mahasiswa1 = User::firstOrCreate(
            ['email' => 'andi@students.itspku.ac.id'],
            [
                'name' => 'Andi Saputra',
                'username' => 'andisaputra',
                'email' => 'andi@students.itspku.ac.id',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
            ],
        );

        $mahasiswa2 = User::firstOrCreate(
            ['email' => 'putri@student.umpku.ac.id'],
            [
                'name' => 'Putri Rahayu',
                'username' => 'putrirahayu',
                'email' => 'putri@student.umpku.ac.id',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
            ],
        );

        // Tulisan contoh
        $tulisanData = [
            [
                'user_id' => $mahasiswa1->id,
                'kategori_id' => $kategoriSastra->id,
                'jenis' => 'puisi',
                'slug' => 'rindu-di-ujung-pena',
                'judul' => 'Rindu di Ujung Pena',
                'isi' => "Wahai kertas putih\nTerima curahan rinduku\nYang tak bertepi\n\nTintaku mengalir deras\nSebagaimana air mata\nYang tak terbendung\n\nDi ujung pena ini\nAku titipkan rinduku\nKepada angin malam.",
                'ringkasan' => 'Puisi tentang kerinduan yang dituangkan lewat tulisan.',
                'status' => StatusTulisan::Terbit,
                'diterbitkan_pada' => now()->subDays(5),
            ],
            [
                'user_id' => $mahasiswa1->id,
                'kategori_id' => $kategoriFiksi->id,
                'jenis' => 'cerpen',
                'slug' => 'surat-untuk-ibu',
                'judul' => 'Surat untuk Ibu',
                'isi' => 'Di sudut kamar kos yang sempit, Dimas menuliskan kata demi kata untuk ibunya di kampung. Jari-jarinya yang lelah setelah seharian bekerja paruh waktu tidak menyurutkan semangatnya. Ia ingin ibunya tahu bahwa ia baik-baik saja, meski kebenaran itu tidak seluruhnya ia ceritakan.',
                'ringkasan' => 'Cerpen tentang seorang anak rantau yang menyembunyikan kesulitannya dari ibunya.',
                'status' => StatusTulisan::Terbit,
                'diterbitkan_pada' => now()->subDays(2),
            ],
            [
                'user_id' => $mahasiswa2->id,
                'kategori_id' => $kategoriSastra->id,
                'jenis' => 'esai',
                'slug' => 'membaca-budaya-dari-lembaran-puisi',
                'judul' => 'Membaca Budaya dari Lembaran Puisi',
                'isi' => 'Puisi bukan sekadar rangkaian kata indah. Ia adalah cermin budaya yang merekam denyut kehidupan masyarakat di setiap zamannya. Dalam lembaran-lembaran puisi Chairil Anwar, kita dapat membaca kegelisahan generasi muda Indonesia pasca kemerdekaan.',
                'ringkasan' => 'Esai tentang puisi sebagai cermin budaya bangsa.',
                'status' => StatusTulisan::Menunggu,
            ],
            [
                'user_id' => $mahasiswa2->id,
                'kategori_id' => $kategoriFiksi->id,
                'jenis' => 'cerpen',
                'slug' => 'cerpen-draf-mahasiswa',
                'judul' => 'Di Balik Jendela Perpustakaan',
                'isi' => 'Anggun selalu datang ke perpustakaan pukul tiga sore. Bukan karena ia kutu buku, tetapi karena dari balik jendela perpustakaan itu ia bisa melihat lapangan basket tempat Reza berlatih.',
                'ringkasan' => 'Cerpen roman tentang seorang mahasiswi dan pengamatan rahasianya.',
                'status' => StatusTulisan::Draf,
            ],
        ];

        foreach ($tulisanData as $t) {
            Tulisan::firstOrCreate(['slug' => $t['slug']], $t);
        }

        // Agenda contoh
        $agendaData = [
            [
                'slug' => 'lomba-puisi-nasional-2026',
                'judul' => 'Lomba Puisi Nasional Aksara 2026',
                'jenis' => 'lomba',
                'deskripsi' => 'Ajang bergengsi tahunan untuk penulis puisi seluruh Indonesia.',
                'lokasi' => 'Aula Utama Kampus UMPKU',
                'mulai_pada' => now()->addMonth(),
                'selesai_pada' => now()->addMonth()->addDays(3),
                'kuota' => 100,
            ],
            [
                'slug' => 'bedah-buku-sastra-modern',
                'judul' => 'Bedah Buku: Sastra Modern Indonesia',
                'jenis' => 'bedah_buku',
                'deskripsi' => 'Diskusi mendalam tentang perkembangan sastra modern di Indonesia.',
                'lokasi' => 'Ruang Seminar Gedung B',
                'mulai_pada' => now()->addWeeks(2),
                'selesai_pada' => now()->addWeeks(2)->addHours(3),
                'kuota' => 50,
            ],
        ];

        foreach ($agendaData as $a) {
            Agenda::firstOrCreate(['slug' => $a['slug']], $a);
        }

        $this->command->info('Data awal Aksara Kampus berhasil diseed!');
        $this->command->info('Akun mahasiswa: andi@students.itspku.ac.id / password');
        $this->command->info('Akun mahasiswa: putri@student.umpku.ac.id / password');
    }
}
