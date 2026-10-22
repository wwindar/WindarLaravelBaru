<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\Pengajuan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLayananApiTest extends TestCase
{
    use RefreshDatabase;

    protected string $adminToken = 'petugas-rahasia-123';

    public function test_admin_layanan_tanpa_token_mengembalikan_401(): void
    {
        $this->getJson('/api/admin/layanan')
            ->assertStatus(401)
            ->assertJson(['message' => 'Token admin tidak valid atau tidak disertakan.']);

        $this->postJson('/api/admin/layanan', [])
            ->assertStatus(401);

        $this->getJson('/api/admin/layanan/1')
            ->assertStatus(401);

        $this->putJson('/api/admin/layanan/1', [])
            ->assertStatus(401);

        $this->deleteJson('/api/admin/layanan/1')
            ->assertStatus(401);
    }

    public function test_admin_dapat_melihat_semua_layanan_termasuk_nonaktif_dengan_search_dan_pagination(): void
    {
        Layanan::create([
            'slug' => 'surat-aktif-kuliah',
            'nama' => 'Surat Aktif Kuliah',
            'deskripsi' => 'Keterangan aktif kuliah.',
            'persyaratan' => ['KTM'],
            'estimasi_hari' => 1,
            'aktif' => true,
        ]);

        Layanan::create([
            'slug' => 'layanan-tutup',
            'nama' => 'Layanan Khusus Tutup',
            'deskripsi' => 'Layanan nonaktif.',
            'persyaratan' => ['KTM'],
            'estimasi_hari' => 3,
            'aktif' => false,
        ]);

        $response = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->getJson('/api/admin/layanan');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        // Search test
        $searchResponse = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->getJson('/api/admin/layanan?search=tutup');

        $searchResponse->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'layanan-tutup');
    }

    public function test_admin_dapat_membuat_layanan_baru_dengan_slug_otomatis_unik(): void
    {
        $payload = [
            'nama' => 'Surat Bebas Perpustakaan',
            'deskripsi' => 'Surat keterangan bebas peminjaman buku perpustakaan.',
            'persyaratan' => ['KTM', 'Bukti Pengembalian Buku'],
            'estimasi_hari' => 2,
            'aktif' => true,
        ];

        $response = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->postJson('/api/admin/layanan', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.nama', 'Surat Bebas Perpustakaan')
            ->assertJsonPath('data.slug', 'surat-bebas-perpustakaan');

        $this->assertDatabaseHas('layanan', [
            'slug' => 'surat-bebas-perpustakaan',
            'nama' => 'Surat Bebas Perpustakaan',
        ]);

        // Buat layanan dengan nama sama, slug harus unik dengan akhiran angka
        $responseDuplicate = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->postJson('/api/admin/layanan', $payload);

        $responseDuplicate->assertStatus(201)
            ->assertJsonPath('data.slug', 'surat-bebas-perpustakaan-1');
    }

    public function test_buat_layanan_gagal_validasi_mengembalikan_422(): void
    {
        $response = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->postJson('/api/admin/layanan', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'nama',
                    'deskripsi',
                    'persyaratan',
                    'estimasi_hari',
                ],
            ]);
    }

    public function test_admin_dapat_melihat_detail_layanan(): void
    {
        $layanan = Layanan::create([
            'slug' => 'surat-aktif-kuliah',
            'nama' => 'Surat Aktif Kuliah',
            'deskripsi' => 'Keterangan aktif kuliah.',
            'persyaratan' => ['KTM'],
            'estimasi_hari' => 1,
            'aktif' => true,
        ]);

        $response = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->getJson('/api/admin/layanan/'.$layanan->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $layanan->id)
            ->assertJsonPath('data.nama', 'Surat Aktif Kuliah');

        $this->withHeader('X-Admin-Token', $this->adminToken)
            ->getJson('/api/admin/layanan/99999')
            ->assertStatus(404)
            ->assertJson(['message' => 'Layanan tidak ditemukan.']);
    }

    public function test_admin_dapat_mengubah_layanan(): void
    {
        $layanan = Layanan::create([
            'slug' => 'surat-aktif-kuliah',
            'nama' => 'Surat Aktif Kuliah',
            'deskripsi' => 'Keterangan aktif kuliah.',
            'persyaratan' => ['KTM'],
            'estimasi_hari' => 1,
            'aktif' => true,
        ]);

        $response = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->putJson('/api/admin/layanan/'.$layanan->id, [
                'nama' => 'Surat Keterangan Aktif Mahasiswa',
                'estimasi_hari' => 2,
                'aktif' => false,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.nama', 'Surat Keterangan Aktif Mahasiswa')
            ->assertJsonPath('data.slug', 'surat-keterangan-aktif-mahasiswa')
            ->assertJsonPath('data.estimasi_hari', 2)
            ->assertJsonPath('data.aktif', false);

        $this->assertDatabaseHas('layanan', [
            'id' => $layanan->id,
            'slug' => 'surat-keterangan-aktif-mahasiswa',
            'aktif' => 0,
        ]);
    }

    public function test_admin_dapat_menghapus_layanan_yang_belum_memiliki_pengajuan(): void
    {
        $layanan = Layanan::create([
            'slug' => 'layanan-baru-kosong',
            'nama' => 'Layanan Baru Kosong',
            'deskripsi' => 'Belum ada pengajuan.',
            'persyaratan' => ['KTM'],
            'estimasi_hari' => 1,
            'aktif' => true,
        ]);

        $response = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->deleteJson('/api/admin/layanan/'.$layanan->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Layanan berhasil dihapus.']);

        $this->assertDatabaseMissing('layanan', [
            'id' => $layanan->id,
        ]);
    }

    public function test_layanan_yang_sudah_dipakai_pengajuan_tidak_dapat_dihapus_mengembalikan_422(): void
    {
        $layanan = Layanan::create([
            'slug' => 'surat-aktif-kuliah',
            'nama' => 'Surat Aktif Kuliah',
            'deskripsi' => 'Keterangan aktif kuliah.',
            'persyaratan' => ['KTM'],
            'estimasi_hari' => 1,
            'aktif' => true,
        ]);

        Pengajuan::create([
            'layanan_id' => $layanan->id,
            'nama_pemohon' => 'Ahmad Rizky',
            'nim' => '220101001',
            'email' => 'ahmad@student.univ.ac.id',
            'program_studi' => 'Teknik Informatika',
            'keperluan' => 'Tunjangan',
            'dokumen' => ['KTM'],
            'status' => 'diajukan',
        ]);

        $response = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->deleteJson('/api/admin/layanan/'.$layanan->id);

        $response->assertStatus(422);
        $this->assertStringContainsString('tidak dapat dihapus', $response->json('message'));
        $this->assertStringContainsString('menonaktifkan', $response->json('message'));

        $this->assertDatabaseHas('layanan', [
            'id' => $layanan->id,
        ]);
    }
}
