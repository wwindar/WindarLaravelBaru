<?php

namespace Tests\Feature;

use App\Models\Layanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayananApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Layanan::create([
            'slug' => 'surat-aktif-kuliah',
            'nama' => 'Surat Aktif Kuliah',
            'deskripsi' => 'Surat keterangan resmi mahasiswa aktif perkuliahan.',
            'persyaratan' => ['KTM', 'Bukti UKT', 'KRS'],
            'estimasi_hari' => 1,
            'aktif' => true,
        ]);

        Layanan::create([
            'slug' => 'legalisasi-dokumen',
            'nama' => 'Legalisasi Dokumen',
            'deskripsi' => 'Pengesahan salinan ijazah dan transkrip akademik.',
            'persyaratan' => ['Scan Ijazah', 'KTM'],
            'estimasi_hari' => 2,
            'aktif' => true,
        ]);

        Layanan::create([
            'slug' => 'layanan-nonaktif',
            'nama' => 'Layanan Khusus Tutup',
            'deskripsi' => 'Layanan sementara tidak aktif.',
            'persyaratan' => ['Surat Permohonan'],
            'estimasi_hari' => 3,
            'aktif' => false,
        ]);
    }

    public function test_dapat_menampilkan_daftar_layanan_aktif(): void
    {
        $response = $this->getJson('/api/layanan');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'slug',
                        'nama',
                        'deskripsi',
                        'persyaratan',
                        'estimasi_hari',
                        'aktif',
                    ],
                ],
                'meta' => [
                    'total',
                ],
            ]);

        $this->assertCount(2, $response->json('data'));
        $this->assertEquals(2, $response->json('meta.total'));
    }

    public function test_pencarian_layanan_berdasarkan_nama_dan_deskripsi_mengabaikan_kapitalisasi_dan_spasi(): void
    {
        $response = $this->getJson('/api/layanan?search='.urlencode('   KULIAH   '));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'surat-aktif-kuliah');

        $responseDeskripsi = $this->getJson('/api/layanan?search=IJAZAH');
        $responseDeskripsi->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'legalisasi-dokumen');
    }

    public function test_pencarian_layanan_kosong_menghasilkan_data_kosong_dengan_status_200(): void
    {
        $response = $this->getJson('/api/layanan?search=kata_kunci_palsu_tidak_ada');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [],
                'meta' => [
                    'total' => 0,
                ],
            ]);
    }

    public function test_detail_layanan_berhasil_menampilkan_persyaratan(): void
    {
        $response = $this->getJson('/api/layanan/surat-aktif-kuliah');

        $response->assertStatus(200)
            ->assertJsonPath('data.slug', 'surat-aktif-kuliah')
            ->assertJsonPath('data.persyaratan', ['KTM', 'Bukti UKT', 'KRS']);
    }

    public function test_detail_layanan_tidak_ditemukan_mengembalikan_404_berbahasa_indonesia(): void
    {
        $response = $this->getJson('/api/layanan/layanan-tidak-ada');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Layanan tidak ditemukan.',
            ]);
    }
}
