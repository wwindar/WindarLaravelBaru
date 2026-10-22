<?php

namespace Tests\Feature;

use App\Enums\StatusPengajuan;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\RiwayatStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengajuanApiTest extends TestCase
{
    use RefreshDatabase;

    protected Layanan $layananAktif;

    protected Layanan $layananNonaktif;

    protected function setUp(): void
    {
        parent::setUp();

        $this->layananAktif = Layanan::create([
            'slug' => 'surat-aktif-kuliah',
            'nama' => 'Surat Aktif Kuliah',
            'deskripsi' => 'Surat keterangan resmi aktif kuliah.',
            'persyaratan' => [
                'Kartu Tanda Mahasiswa (KTM)',
                'Bukti Pembayaran UKT',
            ],
            'estimasi_hari' => 1,
            'aktif' => true,
        ]);

        $this->layananNonaktif = Layanan::create([
            'slug' => 'layanan-tutup',
            'nama' => 'Layanan Tutup',
            'deskripsi' => 'Layanan nonaktif.',
            'persyaratan' => ['KTM'],
            'estimasi_hari' => 3,
            'aktif' => false,
        ]);
    }

    public function test_buat_pengajuan_sukses_mengembalikan_201_dan_nomor_pelacakan(): void
    {
        $payload = [
            'layanan_id' => $this->layananAktif->id,
            'nama_pemohon' => 'Budi Utomo',
            'nim' => '220101999',
            'email' => 'budi@student.univ.ac.id',
            'program_studi' => 'Teknik Informatika',
            'keperluan' => 'Keperluan beasiswa',
            'dokumen' => [
                'Kartu Tanda Mahasiswa (KTM)',
                'Bukti Pembayaran UKT',
            ],
        ];

        $response = $this->postJson('/api/pengajuan', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.nama_pemohon', 'Budi Utomo')
            ->assertJsonPath('data.status', 'diajukan')
            ->assertJsonPath('data.status_label', 'Diajukan');

        $nomorPelacakan = $response->json('data.nomor_pelacakan');
        $this->assertNotEmpty($nomorPelacakan);
        $this->assertMatchesRegularExpression('/^PLM-\d{4}-\d{5}$/', $nomorPelacakan);

        $this->assertDatabaseHas('pengajuan', [
            'nomor_pelacakan' => $nomorPelacakan,
            'nim' => '220101999',
            'status' => 'diajukan',
        ]);

        $this->assertDatabaseHas('riwayat_status', [
            'status' => 'diajukan',
        ]);
    }

    public function test_buat_pengajuan_gagal_validasi_mengembalikan_422_berbahasa_indonesia(): void
    {
        $response = $this->postJson('/api/pengajuan', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'layanan_id',
                    'nama_pemohon',
                    'nim',
                    'email',
                    'program_studi',
                    'keperluan',
                    'dokumen',
                ],
            ]);

        $this->assertStringContainsString('Validasi gagal', $response->json('message'));
    }

    public function test_buat_pengajuan_gagal_bila_layanan_tidak_aktif(): void
    {
        $payload = [
            'layanan_id' => $this->layananNonaktif->id,
            'nama_pemohon' => 'Budi Utomo',
            'nim' => '220101999',
            'email' => 'budi@student.univ.ac.id',
            'program_studi' => 'Teknik Informatika',
            'keperluan' => 'Keperluan pengajuan',
            'dokumen' => ['KTM'],
        ];

        $response = $this->postJson('/api/pengajuan', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['layanan_id']);

        $this->assertStringContainsString('tidak aktif', $response->json('errors.layanan_id.0'));
    }

    public function test_buat_pengajuan_gagal_bila_dokumen_persyaratan_kurang_dan_menyebutkan_dokumen_yang_kurang(): void
    {
        $payload = [
            'layanan_id' => $this->layananAktif->id,
            'nama_pemohon' => 'Budi Utomo',
            'nim' => '220101999',
            'email' => 'budi@student.univ.ac.id',
            'program_studi' => 'Teknik Informatika',
            'keperluan' => 'Keperluan beasiswa',
            'dokumen' => [
                'Kartu Tanda Mahasiswa (KTM)',
                // 'Bukti Pembayaran UKT' is omitted
            ],
        ];

        $response = $this->postJson('/api/pengajuan', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['dokumen']);

        $errorMessage = $response->json('errors.dokumen.0');
        $this->assertStringContainsString('Bukti Pembayaran UKT', $errorMessage);
    }

    public function test_lacak_pengajuan_dengan_nim_benar_berhasil_menampilkan_data_dan_riwayat(): void
    {
        $pengajuan = Pengajuan::create([
            'layanan_id' => $this->layananAktif->id,
            'nama_pemohon' => 'Siti Nurhaliza',
            'nim' => '220102015',
            'email' => 'siti@student.univ.ac.id',
            'program_studi' => 'Sistem Informasi',
            'keperluan' => 'Pengajuan surat',
            'dokumen' => ['Kartu Tanda Mahasiswa (KTM)', 'Bukti Pembayaran UKT'],
            'status' => StatusPengajuan::PERLU_PERBAIKAN,
            'catatan_petugas' => 'Perbaiki scan dokumen KTM.',
        ]);

        RiwayatStatus::create([
            'pengajuan_id' => $pengajuan->id,
            'status' => StatusPengajuan::DIAJUKAN,
            'catatan' => 'Dibuat',
        ]);

        RiwayatStatus::create([
            'pengajuan_id' => $pengajuan->id,
            'status' => StatusPengajuan::PERLU_PERBAIKAN,
            'catatan' => 'Perbaiki scan dokumen KTM.',
        ]);

        $response = $this->getJson('/api/pengajuan/'.$pengajuan->nomor_pelacakan.'?nim=220102015');

        $response->assertStatus(200)
            ->assertJsonPath('data.nomor_pelacakan', $pengajuan->nomor_pelacakan)
            ->assertJsonPath('data.nama_layanan', 'Surat Aktif Kuliah')
            ->assertJsonPath('data.status', 'perlu_perbaikan')
            ->assertJsonPath('data.status_label', 'Perlu Perbaikan')
            ->assertJsonPath('data.catatan_petugas', 'Perbaiki scan dokumen KTM.')
            ->assertJsonCount(2, 'data.riwayat');
    }

    public function test_lacak_pengajuan_dengan_nomor_atau_nim_salah_mengembalikan_404_dengan_pesan_yang_sama(): void
    {
        $pengajuan = Pengajuan::create([
            'layanan_id' => $this->layananAktif->id,
            'nama_pemohon' => 'Siti Nurhaliza',
            'nim' => '220102015',
            'email' => 'siti@student.univ.ac.id',
            'program_studi' => 'Sistem Informasi',
            'keperluan' => 'Pengajuan surat',
            'dokumen' => ['KTM'],
            'status' => StatusPengajuan::DIAJUKAN,
        ]);

        // Salah NIM
        $responseWrongNim = $this->getJson('/api/pengajuan/'.$pengajuan->nomor_pelacakan.'?nim=999999999');
        $responseWrongNim->assertStatus(404)
            ->assertJson([
                'message' => 'Data pengajuan tidak ditemukan.',
            ]);

        // Salah Nomor Pelacakan
        $responseWrongNomor = $this->getJson('/api/pengajuan/PLM-2026-99999?nim=220102015');
        $responseWrongNomor->assertStatus(404)
            ->assertJson([
                'message' => 'Data pengajuan tidak ditemukan.',
            ]);

        // Tanpa parameter NIM
        $responseNoNim = $this->getJson('/api/pengajuan/'.$pengajuan->nomor_pelacakan);
        $responseNoNim->assertStatus(404)
            ->assertJson([
                'message' => 'Data pengajuan tidak ditemukan.',
            ]);
    }

    public function test_mahasiswa_dapat_memperbaiki_pengajuan_berstatus_perlu_perbaikan(): void
    {
        $pengajuan = Pengajuan::create([
            'layanan_id' => $this->layananAktif->id,
            'nama_pemohon' => 'Budi Santoso',
            'nim' => '210201042',
            'email' => 'budi.lama@student.univ.ac.id',
            'program_studi' => 'Teknik Elektro',
            'keperluan' => 'Keperluan awal',
            'dokumen' => ['Kartu Tanda Mahasiswa (KTM)'],
            'status' => StatusPengajuan::PERLU_PERBAIKAN,
            'catatan_petugas' => 'Lengkapi bukti pembayaran UKT.',
        ]);

        $payload = [
            'nim' => '210201042',
            'email' => 'budi.baru@student.univ.ac.id',
            'program_studi' => 'Teknik Elektro',
            'keperluan' => 'Keperluan perbaikan terbaru',
            'dokumen' => [
                'Kartu Tanda Mahasiswa (KTM)',
                'Bukti Pembayaran UKT',
            ],
        ];

        $response = $this->putJson('/api/pengajuan/'.$pengajuan->nomor_pelacakan, $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'diajukan')
            ->assertJsonPath('data.status_label', 'Diajukan')
            ->assertJsonPath('data.email', 'budi.baru@student.univ.ac.id')
            ->assertJsonPath('data.keperluan', 'Keperluan perbaikan terbaru')
            ->assertJsonPath('data.catatan_petugas', null);

        $this->assertDatabaseHas('pengajuan', [
            'id' => $pengajuan->id,
            'status' => 'diajukan',
            'email' => 'budi.baru@student.univ.ac.id',
        ]);

        $this->assertDatabaseHas('riwayat_status', [
            'pengajuan_id' => $pengajuan->id,
            'status' => 'diajukan',
        ]);
    }

    public function test_perbaikan_pengajuan_gagal_bila_status_bukan_perlu_perbaikan_mengembalikan_422(): void
    {
        $pengajuan = Pengajuan::create([
            'layanan_id' => $this->layananAktif->id,
            'nama_pemohon' => 'Budi Santoso',
            'nim' => '210201042',
            'email' => 'budi@student.univ.ac.id',
            'program_studi' => 'Teknik Elektro',
            'keperluan' => 'Keperluan awal',
            'dokumen' => [
                'Kartu Tanda Mahasiswa (KTM)',
                'Bukti Pembayaran UKT',
            ],
            'status' => StatusPengajuan::DIAJUKAN,
        ]);

        $payload = [
            'nim' => '210201042',
            'email' => 'budi@student.univ.ac.id',
            'program_studi' => 'Teknik Elektro',
            'keperluan' => 'Mencoba ubah status diajukan',
            'dokumen' => [
                'Kartu Tanda Mahasiswa (KTM)',
                'Bukti Pembayaran UKT',
            ],
        ];

        $response = $this->putJson('/api/pengajuan/'.$pengajuan->nomor_pelacakan, $payload);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Pengajuan hanya dapat diperbaiki jika berstatus Perlu Perbaikan.',
            ]);
    }

    public function test_perbaikan_pengajuan_gagal_bila_nomor_atau_nim_salah_mengembalikan_404(): void
    {
        $pengajuan = Pengajuan::create([
            'layanan_id' => $this->layananAktif->id,
            'nama_pemohon' => 'Budi Santoso',
            'nim' => '210201042',
            'email' => 'budi@student.univ.ac.id',
            'program_studi' => 'Teknik Elektro',
            'keperluan' => 'Keperluan awal',
            'dokumen' => ['Kartu Tanda Mahasiswa (KTM)'],
            'status' => StatusPengajuan::PERLU_PERBAIKAN,
        ]);

        $payload = [
            'nim' => '999999999', // salah NIM
            'email' => 'budi@student.univ.ac.id',
            'program_studi' => 'Teknik Elektro',
            'keperluan' => 'Coba ubah',
            'dokumen' => [
                'Kartu Tanda Mahasiswa (KTM)',
                'Bukti Pembayaran UKT',
            ],
        ];

        $this->putJson('/api/pengajuan/'.$pengajuan->nomor_pelacakan, $payload)
            ->assertStatus(404)
            ->assertJson([
                'message' => 'Data pengajuan tidak ditemukan.',
            ]);

        $payload['nim'] = '210201042';
        $this->putJson('/api/pengajuan/PLM-2026-99999', $payload)
            ->assertStatus(404)
            ->assertJson([
                'message' => 'Data pengajuan tidak ditemukan.',
            ]);
    }

    public function test_perbaikan_pengajuan_gagal_bila_dokumen_persyaratan_kurang_mengembalikan_422(): void
    {
        $pengajuan = Pengajuan::create([
            'layanan_id' => $this->layananAktif->id,
            'nama_pemohon' => 'Budi Santoso',
            'nim' => '210201042',
            'email' => 'budi@student.univ.ac.id',
            'program_studi' => 'Teknik Elektro',
            'keperluan' => 'Keperluan awal',
            'dokumen' => ['Kartu Tanda Mahasiswa (KTM)'],
            'status' => StatusPengajuan::PERLU_PERBAIKAN,
        ]);

        $payload = [
            'nim' => '210201042',
            'email' => 'budi@student.univ.ac.id',
            'program_studi' => 'Teknik Elektro',
            'keperluan' => 'Coba perbaiki tapi masih kurang dokumen',
            'dokumen' => [
                'Kartu Tanda Mahasiswa (KTM)',
                // missing 'Bukti Pembayaran UKT'
            ],
        ];

        $response = $this->putJson('/api/pengajuan/'.$pengajuan->nomor_pelacakan, $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['dokumen']);
    }
}
