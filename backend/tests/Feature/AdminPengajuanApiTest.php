<?php

namespace Tests\Feature;

use App\Enums\StatusPengajuan;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\RiwayatStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPengajuanApiTest extends TestCase
{
    use RefreshDatabase;

    protected Layanan $layanan;

    protected string $adminToken = 'petugas-rahasia-123';

    protected function setUp(): void
    {
        parent::setUp();

        $this->layanan = Layanan::create([
            'slug' => 'surat-aktif-kuliah',
            'nama' => 'Surat Aktif Kuliah',
            'deskripsi' => 'Surat aktif kuliah.',
            'persyaratan' => ['KTM'],
            'estimasi_hari' => 1,
            'aktif' => true,
        ]);
    }

    public function test_admin_endpoint_tanpa_token_atau_token_salah_mengembalikan_401_json(): void
    {
        // Tanpa token
        $responseNoToken = $this->getJson('/api/admin/pengajuan');
        $responseNoToken->assertStatus(401)
            ->assertJson([
                'message' => 'Token admin tidak valid atau tidak disertakan.',
            ]);

        // Token salah
        $responseWrongToken = $this->withHeader('X-Admin-Token', 'token-palsu')
            ->getJson('/api/admin/pengajuan');
        $responseWrongToken->assertStatus(401)
            ->assertJson([
                'message' => 'Token admin tidak valid atau tidak disertakan.',
            ]);
    }

    public function test_admin_dapat_melihat_daftar_pengajuan_dengan_filter_status_dan_search(): void
    {
        $p1 = Pengajuan::create([
            'layanan_id' => $this->layanan->id,
            'nama_pemohon' => 'Ahmad Rizky',
            'nim' => '220101001',
            'email' => 'ahmad@student.univ.ac.id',
            'program_studi' => 'Teknik Informatika',
            'keperluan' => 'Keperluan A',
            'dokumen' => ['KTM'],
            'status' => StatusPengajuan::DIAJUKAN,
        ]);

        $p2 = Pengajuan::create([
            'layanan_id' => $this->layanan->id,
            'nama_pemohon' => 'Siti Fatimah',
            'nim' => '220101002',
            'email' => 'siti@student.univ.ac.id',
            'program_studi' => 'Sistem Informasi',
            'keperluan' => 'Keperluan B',
            'dokumen' => ['KTM'],
            'status' => StatusPengajuan::DIPERIKSA,
        ]);

        // List all
        $response = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->getJson('/api/admin/pengajuan');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'nomor_pelacakan',
                        'nama_pemohon',
                        'nim',
                        'status',
                        'status_label',
                    ],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(2, 'data');

        // Filter status
        $responseFilter = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->getJson('/api/admin/pengajuan?status=diajukan');

        $responseFilter->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.nim', '220101001');

        // Search by name
        $responseSearch = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->getJson('/api/admin/pengajuan?search=Siti');

        $responseSearch->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.nama_pemohon', 'Siti Fatimah');
    }

    public function test_admin_dapat_melihat_detail_pengajuan_beserta_riwayat(): void
    {
        $pengajuan = Pengajuan::create([
            'layanan_id' => $this->layanan->id,
            'nama_pemohon' => 'Dewi Anggraini',
            'nim' => '230301019',
            'email' => 'dewi@student.univ.ac.id',
            'program_studi' => 'Manajemen',
            'keperluan' => 'Pendaftaran beasiswa',
            'dokumen' => ['KTM'],
            'status' => StatusPengajuan::DIAJUKAN,
        ]);

        RiwayatStatus::create([
            'pengajuan_id' => $pengajuan->id,
            'status' => StatusPengajuan::DIAJUKAN,
            'catatan' => 'Pengajuan masuk',
        ]);

        $response = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->getJson('/api/admin/pengajuan/'.$pengajuan->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $pengajuan->id)
            ->assertJsonPath('data.nama_pemohon', 'Dewi Anggraini')
            ->assertJsonCount(1, 'data.riwayat');
    }

    public function test_admin_dapat_mengubah_status_pengajuan_dan_menambah_riwayat(): void
    {
        $pengajuan = Pengajuan::create([
            'layanan_id' => $this->layanan->id,
            'nama_pemohon' => 'Fajar Prasetyo',
            'nim' => '200101088',
            'email' => 'fajar@student.univ.ac.id',
            'program_studi' => 'Teknik Informatika',
            'keperluan' => 'Surat beasiswa',
            'dokumen' => ['KTM'],
            'status' => StatusPengajuan::DIAJUKAN,
        ]);

        $response = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->patchJson('/api/admin/pengajuan/'.$pengajuan->id.'/status', [
                'status' => 'diperiksa',
                'catatan' => 'Sedang dalam proses verifikasi berkas.',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'diperiksa')
            ->assertJsonPath('data.status_label', 'Sedang Diperiksa');

        $this->assertDatabaseHas('pengajuan', [
            'id' => $pengajuan->id,
            'status' => 'diperiksa',
            'catatan_petugas' => 'Sedang dalam proses verifikasi berkas.',
        ]);

        $this->assertDatabaseHas('riwayat_status', [
            'pengajuan_id' => $pengajuan->id,
            'status' => 'diperiksa',
            'catatan' => 'Sedang dalam proses verifikasi berkas.',
        ]);
    }

    public function test_catatan_wajib_diisi_saat_status_perlu_perbaikan_atau_ditolak(): void
    {
        $pengajuan = Pengajuan::create([
            'layanan_id' => $this->layanan->id,
            'nama_pemohon' => 'Budi Santoso',
            'nim' => '210201042',
            'email' => 'budi@student.univ.ac.id',
            'program_studi' => 'Teknik Elektro',
            'keperluan' => 'Legalisasi',
            'dokumen' => ['KTM'],
            'status' => StatusPengajuan::DIPERIKSA,
        ]);

        // Perlu perbaikan tanpa catatan
        $responsePerbaikan = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->patchJson('/api/admin/pengajuan/'.$pengajuan->id.'/status', [
                'status' => 'perlu_perbaikan',
            ]);

        $responsePerbaikan->assertStatus(422)
            ->assertJsonValidationErrors(['catatan']);

        // Ditolak tanpa catatan
        $responseTolak = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->patchJson('/api/admin/pengajuan/'.$pengajuan->id.'/status', [
                'status' => 'ditolak',
            ]);

        $responseTolak->assertStatus(422)
            ->assertJsonValidationErrors(['catatan']);
    }

    public function test_status_selesai_dan_ditolak_bersifat_final_tidak_bisa_diubah_lagi(): void
    {
        // Status Selesai
        $pengajuanSelesai = Pengajuan::create([
            'layanan_id' => $this->layanan->id,
            'nama_pemohon' => 'Fajar Selesai',
            'nim' => '200101001',
            'email' => 'fajar@student.univ.ac.id',
            'program_studi' => 'Teknik Informatika',
            'keperluan' => 'Surat selesai',
            'dokumen' => ['KTM'],
            'status' => StatusPengajuan::SELESAI,
        ]);

        $responseUbahSelesai = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->patchJson('/api/admin/pengajuan/'.$pengajuanSelesai->id.'/status', [
                'status' => 'diperiksa',
                'catatan' => 'Mencoba membuka kembali',
            ]);

        $responseUbahSelesai->assertStatus(422)
            ->assertJson([
                'message' => 'Pengajuan dengan status selesai atau ditolak tidak dapat diubah lagi.',
            ]);

        // Status Ditolak
        $pengajuanDitolak = Pengajuan::create([
            'layanan_id' => $this->layanan->id,
            'nama_pemohon' => 'Rina Ditolak',
            'nim' => '200101002',
            'email' => 'rina@student.univ.ac.id',
            'program_studi' => 'Akuntansi',
            'keperluan' => 'Surat ditolak',
            'dokumen' => ['KTM'],
            'status' => StatusPengajuan::DITOLAK,
            'catatan_petugas' => 'Ditolak',
        ]);

        $responseUbahDitolak = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->patchJson('/api/admin/pengajuan/'.$pengajuanDitolak->id.'/status', [
                'status' => 'diperiksa',
                'catatan' => 'Mencoba mengubah kembali',
            ]);

        $responseUbahDitolak->assertStatus(422)
            ->assertJson([
                'message' => 'Pengajuan dengan status selesai atau ditolak tidak dapat diubah lagi.',
            ]);
    }

    public function test_admin_dapat_menghapus_pengajuan_berstatus_diajukan_atau_ditolak(): void
    {
        // Status diajukan
        $pengajuanDiajukan = Pengajuan::create([
            'layanan_id' => $this->layanan->id,
            'nama_pemohon' => 'Hapus Diajukan',
            'nim' => '200101003',
            'email' => 'hapus1@student.univ.ac.id',
            'program_studi' => 'Teknik Informatika',
            'keperluan' => 'Surat diajukan',
            'dokumen' => ['KTM'],
            'status' => StatusPengajuan::DIAJUKAN,
        ]);

        RiwayatStatus::create([
            'pengajuan_id' => $pengajuanDiajukan->id,
            'status' => StatusPengajuan::DIAJUKAN,
            'catatan' => 'Dibuat',
        ]);

        $responseDiajukan = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->deleteJson('/api/admin/pengajuan/'.$pengajuanDiajukan->id);

        $responseDiajukan->assertStatus(200)
            ->assertJson(['message' => 'Pengajuan berhasil dihapus.']);

        $this->assertDatabaseMissing('pengajuan', ['id' => $pengajuanDiajukan->id]);
        $this->assertDatabaseMissing('riwayat_status', ['pengajuan_id' => $pengajuanDiajukan->id]);

        // Status ditolak
        $pengajuanDitolak = Pengajuan::create([
            'layanan_id' => $this->layanan->id,
            'nama_pemohon' => 'Hapus Ditolak',
            'nim' => '200101004',
            'email' => 'hapus2@student.univ.ac.id',
            'program_studi' => 'Teknik Informatika',
            'keperluan' => 'Surat ditolak',
            'dokumen' => ['KTM'],
            'status' => StatusPengajuan::DITOLAK,
            'catatan_petugas' => 'Ditolak',
        ]);

        $responseDitolak = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->deleteJson('/api/admin/pengajuan/'.$pengajuanDitolak->id);

        $responseDitolak->assertStatus(200)
            ->assertJson(['message' => 'Pengajuan berhasil dihapus.']);

        $this->assertDatabaseMissing('pengajuan', ['id' => $pengajuanDitolak->id]);
    }

    public function test_admin_tidak_dapat_menghapus_pengajuan_berstatus_selain_diajukan_dan_ditolak_mengembalikan_422(): void
    {
        $pengajuanDiperiksa = Pengajuan::create([
            'layanan_id' => $this->layanan->id,
            'nama_pemohon' => 'Tidak Boleh Hapus',
            'nim' => '200101005',
            'email' => 'tahap1@student.univ.ac.id',
            'program_studi' => 'Teknik Informatika',
            'keperluan' => 'Surat diperiksa',
            'dokumen' => ['KTM'],
            'status' => StatusPengajuan::DIPERIKSA,
        ]);

        $response = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->deleteJson('/api/admin/pengajuan/'.$pengajuanDiperiksa->id);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Pengajuan hanya dapat dihapus apabila berstatus Diajukan atau Ditolak.',
            ]);

        $this->assertDatabaseHas('pengajuan', ['id' => $pengajuanDiperiksa->id]);
    }

    public function test_hapus_pengajuan_tanpa_token_mengembalikan_401(): void
    {
        $this->deleteJson('/api/admin/pengajuan/1')
            ->assertStatus(401)
            ->assertJson(['message' => 'Token admin tidak valid atau tidak disertakan.']);
    }
}
