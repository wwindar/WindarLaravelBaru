<?php

namespace Tests\Feature;

use App\Models\Artikel;
use App\Models\Kategori;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtikelApiTest extends TestCase
{
    use RefreshDatabase;

    protected Kategori $kategori1;

    protected Kategori $kategori2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kategori1 = Kategori::create([
            'nama' => 'Puisi',
            'slug' => 'puisi',
        ]);

        $this->kategori2 = Kategori::create([
            'nama' => 'Kabar Komunitas',
            'slug' => 'kabar-komunitas',
        ]);

        // Buat 10 artikel terbit
        for ($i = 1; $i <= 10; $i++) {
            Artikel::create([
                'slug' => "artikel-terbit-{$i}",
                'judul' => "Antologi Sastra Kampus Seri {$i}",
                'ringkasan' => "Ringkasan tulisan ulasan sastra nomor {$i}.",
                'isi' => "Isi lengkap artikel mengenai perkembangan sastra kampus edisi {$i}. Mengulas berbagai sudut pandang pemikiran mahasiswa.",
                'jenis' => $i % 2 === 0 ? 'berita' : 'blog',
                'kategori_id' => $i % 2 === 0 ? $this->kategori2->id : $this->kategori1->id,
                'penulis' => 'Penulis Kampus',
                'status' => 'terbit',
                'diterbitkan_pada' => Carbon::now()->subDays(11 - $i),
            ]);
        }

        // Buat artikel draft
        Artikel::create([
            'slug' => 'artikel-masih-draft',
            'judul' => 'Rencana Rahasia Komunitas',
            'ringkasan' => 'Ringkasan naskah rahasia.',
            'isi' => 'Isi naskah yang belum disetujui untuk terbit dan masih dalam bentuk rancangan draf awal.',
            'jenis' => 'berita',
            'kategori_id' => $this->kategori2->id,
            'penulis' => 'Editor Magang',
            'status' => 'draft',
            'diterbitkan_pada' => null,
        ]);
    }

    public function test_dapat_menampilkan_daftar_artikel_terbit_dengan_pagination_dan_meta(): void
    {
        $response = $this->getJson('/api/artikel');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'slug',
                        'judul',
                        'ringkasan',
                        'isi',
                        'jenis',
                        'kategori_id',
                        'penulis',
                        'status',
                        'status_label',
                        'diterbitkan_pada',
                    ],
                ],
                'links',
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                ],
            ]);

        // Default pagination 9 per halaman
        $this->assertCount(9, $response->json('data'));
        $this->assertEquals(10, $response->json('meta.total'));

        // Cek halaman 2
        $responsePage2 = $this->getJson('/api/artikel?page=2');
        $responsePage2->assertStatus(200);
        $this->assertCount(1, $responsePage2->json('data'));
    }

    public function test_pencarian_artikel_mengabaikan_kapitalisasi_dan_spasi_tepi(): void
    {
        $response = $this->getJson('/api/artikel?search='.urlencode('   SERI 5   '));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'artikel-terbit-5');

        // Cari berdasarkan ringkasan
        $responseRingkasan = $this->getJson('/api/artikel?search=NOMOR+3');
        $responseRingkasan->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'artikel-terbit-3');
    }

    public function test_pencarian_artikel_kosong_mengembalikan_status_200_dengan_data_kosong(): void
    {
        $response = $this->getJson('/api/artikel?search=kata_kunci_fiktif_tidak_akan_ditemukan');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [],
                'meta' => [
                    'total' => 0,
                ],
            ]);
    }

    public function test_filter_artikel_berdasarkan_jenis_dan_kategori(): void
    {
        // Filter jenis berita
        $responseBerita = $this->getJson('/api/artikel?jenis=berita');
        $responseBerita->assertStatus(200);
        $this->assertEquals(5, $responseBerita->json('meta.total'));

        // Filter slug kategori puisi
        $responseKategori = $this->getJson('/api/artikel?kategori=puisi');
        $responseKategori->assertStatus(200);
        $this->assertEquals(5, $responseKategori->json('meta.total'));
    }

    public function test_detail_artikel_terbit_berhasil(): void
    {
        $response = $this->getJson('/api/artikel/artikel-terbit-1');

        $response->assertStatus(200)
            ->assertJsonPath('data.slug', 'artikel-terbit-1')
            ->assertJsonPath('data.status', 'terbit')
            ->assertJsonPath('data.status_label', 'Terbit');
    }

    public function test_detail_artikel_draft_mengembalikan_404_berbahasa_indonesia(): void
    {
        $response = $this->getJson('/api/artikel/artikel-masih-draft');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Artikel tidak ditemukan atau belum diterbitkan.',
            ]);
    }

    public function test_detail_artikel_tidak_ditemukan_mengembalikan_404_berbahasa_indonesia(): void
    {
        $response = $this->getJson('/api/artikel/slug-yang-tidak-ada');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Artikel tidak ditemukan atau belum diterbitkan.',
            ]);
    }
}

