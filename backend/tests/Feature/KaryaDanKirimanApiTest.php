<?php

namespace Tests\Feature;

use App\Enums\StatusKarya;
use App\Models\Karya;
use App\Models\RiwayatKarya;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KaryaDanKirimanApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_kirim_karya_sukses_mengembalikan_201_dan_nomor_kiriman(): void
    {
        $payload = [
            'judul' => 'Sajak Kabut di Ujung Senja',
            'jenis' => 'puisi',
            'isi' => 'Di antara kabut yang turun perlahan menyelimuti jalanan batu kampus tua, kita menanti sapaan kata yang tak kunjung terucap hingga malam menjemput.',
            'nama_penulis' => 'Adelia Citra',
            'email' => 'adelia@student.univ.ac.id',
        ];

        $response = $this->postJson('/api/kiriman', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.judul', 'Sajak Kabut di Ujung Senja')
            ->assertJsonPath('data.status', 'dikirim')
            ->assertJsonPath('data.status_label', 'Dikirim');

        $nomorKiriman = $response->json('data.nomor_kiriman');
        $this->assertNotEmpty($nomorKiriman);
        $this->assertMatchesRegularExpression('/^AKS-\d{4}-\d{5}$/', $nomorKiriman);

        $this->assertDatabaseHas('karya', [
            'nomor_kiriman' => $nomorKiriman,
            'nama_penulis' => 'Adelia Citra',
            'status' => 'dikirim',
        ]);

        $this->assertDatabaseHas('riwayat_karya', [
            'status' => 'dikirim',
        ]);
    }

    public function test_kirim_karya_gagal_validasi_mengembalikan_422_berbahasa_indonesia(): void
    {
        $response = $this->postJson('/api/kiriman', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'judul',
                    'jenis',
                    'isi',
                    'nama_penulis',
                    'email',
                ],
            ]);

        $this->assertStringContainsString('Validasi pengiriman karya gagal', $response->json('message'));
    }

    public function test_kirim_karya_gagal_jika_isi_kurang_dari_50_karakter(): void
    {
        $payload = [
            'judul' => 'Puisi Pendek',
            'jenis' => 'puisi',
            'isi' => 'Terlalu pendek.',
            'nama_penulis' => 'Adelia',
            'email' => 'adelia@student.univ.ac.id',
        ];

        $response = $this->postJson('/api/kiriman', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['isi']);

        $this->assertStringContainsString('50 karakter', $response->json('errors.isi.0'));
    }

    public function test_lacak_kiriman_dengan_email_benar_berhasil_menampilkan_riwayat(): void
    {
        $karya = Karya::create([
            'slug' => 'naskah-pengujian',
            'judul' => 'Naskah Pengujian Karya',
            'jenis' => 'cerpen',
            'isi' => 'Ini adalah naskah pengujian cerita yang dibuat dengan karakter panjang lebih dari lima puluh karakter untuk keperluan tes unit.',
            'nama_penulis' => 'Budi Santoso',
            'email' => 'budi.santoso@student.univ.ac.id',
            'status' => StatusKarya::PERLU_REVISI,
            'catatan_redaksi' => 'Mohon perbaiki paragraf penutup.',
        ]);

        RiwayatKarya::create([
            'karya_id' => $karya->id,
            'status' => StatusKarya::DIKIRIM,
            'catatan' => 'Karya dikirimkan oleh penulis.',
        ]);

        RiwayatKarya::create([
            'karya_id' => $karya->id,
            'status' => StatusKarya::PERLU_REVISI,
            'catatan' => 'Mohon perbaiki paragraf penutup.',
        ]);

        $response = $this->getJson('/api/kiriman/'.$karya->nomor_kiriman.'?email='.urlencode('budi.santoso@student.univ.ac.id'));

        $response->assertStatus(200)
            ->assertJsonPath('data.nomor_kiriman', $karya->nomor_kiriman)
            ->assertJsonPath('data.status', 'perlu_revisi')
            ->assertJsonPath('data.status_label', 'Perlu Revisi')
            ->assertJsonPath('data.catatan_redaksi', 'Mohon perbaiki paragraf penutup.')
            ->assertJsonCount(2, 'data.riwayat');
    }

    public function test_lacak_kiriman_dengan_email_salah_atau_nomor_salah_mengembalikan_404_dengan_pesan_yang_sama(): void
    {
        $karya = Karya::create([
            'slug' => 'naskah-lacak-salah',
            'judul' => 'Naskah Lacak Salah',
            'jenis' => 'puisi',
            'isi' => 'Isi puisi pengujian lebih dari lima puluh karakter untuk memastikan kelayakan naskah karya.',
            'nama_penulis' => 'Budi Santoso',
            'email' => 'budi@student.univ.ac.id',
            'status' => StatusKarya::DIKIRIM,
        ]);

        // Salah email
        $responseWrongEmail = $this->getJson('/api/kiriman/'.$karya->nomor_kiriman.'?email=orang_lain@univ.ac.id');
        $responseWrongEmail->assertStatus(404)
            ->assertJson(['message' => 'Data kiriman karya tidak ditemukan.']);

        // Salah nomor kiriman
        $responseWrongNomor = $this->getJson('/api/kiriman/AKS-2026-99999?email=budi@student.univ.ac.id');
        $responseWrongNomor->assertStatus(404)
            ->assertJson(['message' => 'Data kiriman karya tidak ditemukan.']);

        // Tanpa parameter email
        $responseNoEmail = $this->getJson('/api/kiriman/'.$karya->nomor_kiriman);
        $responseNoEmail->assertStatus(404)
            ->assertJson(['message' => 'Data kiriman karya tidak ditemukan.']);
    }

    public function test_penulis_dapat_merevisi_karya_jika_status_perlu_revisi(): void
    {
        $karya = Karya::create([
            'slug' => 'naskah-revisi',
            'judul' => 'Naskah Siap Revisi',
            'jenis' => 'cerpen',
            'isi' => 'Isi awal naskah cerita pendek sebelum diperbaiki oleh pengarang dengan teks lebih dari lima puluh karakter.',
            'nama_penulis' => 'Citra Lestari',
            'email' => 'citra@student.univ.ac.id',
            'status' => StatusKarya::PERLU_REVISI,
            'catatan_redaksi' => 'Perbaiki bagian ending.',
        ]);

        $payload = [
            'email' => 'citra@student.univ.ac.id',
            'judul' => 'Naskah Setelah Direvisi Penuh',
            'isi' => 'Isi terbaru naskah cerita pendek yang telah direvisi secara mendalam dan diperbaiki bagian penutupnya agar tuntas.',
        ];

        $response = $this->putJson('/api/kiriman/'.$karya->nomor_kiriman, $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.judul', 'Naskah Setelah Direvisi Penuh')
            ->assertJsonPath('data.status', 'dikirim')
            ->assertJsonPath('data.status_label', 'Dikirim')
            ->assertJsonPath('data.catatan_redaksi', null);

        $this->assertDatabaseHas('karya', [
            'id' => $karya->id,
            'status' => 'dikirim',
            'judul' => 'Naskah Setelah Direvisi Penuh',
            'catatan_redaksi' => null,
        ]);

        $this->assertDatabaseHas('riwayat_karya', [
            'karya_id' => $karya->id,
            'status' => 'dikirim',
        ]);
    }

    public function test_penulis_gagal_merevisi_karya_jika_status_bukan_perlu_revisi_mengembalikan_422(): void
    {
        $karya = Karya::create([
            'slug' => 'naskah-status-terbit',
            'judul' => 'Naskah Yang Sudah Terbit',
            'jenis' => 'puisi',
            'isi' => 'Isi puisi yang sudah terbit di portal sastra aksara kampus dan tidak boleh diedit lagi.',
            'nama_penulis' => 'Citra Lestari',
            'email' => 'citra@student.univ.ac.id',
            'status' => StatusKarya::TERBIT,
        ]);

        $payload = [
            'email' => 'citra@student.univ.ac.id',
            'judul' => 'Mencoba Mengubah Judul',
            'isi' => 'Isi baru yang dicoba masukkan pada karya yang sudah terbit sebelumnya oleh penulis.',
        ];

        $response = $this->putJson('/api/kiriman/'.$karya->nomor_kiriman, $payload);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Karya hanya dapat direvisi jika berstatus Perlu Revisi.',
            ]);
    }

    public function test_revisi_karya_dengan_email_salah_mengembalikan_404(): void
    {
        $karya = Karya::create([
            'slug' => 'naskah-revisi-email-salah',
            'judul' => 'Naskah Revisi',
            'jenis' => 'puisi',
            'isi' => 'Teks naskah yang siap direvisi lebih dari lima puluh karakter dengan rima yang indah.',
            'nama_penulis' => 'Citra Lestari',
            'email' => 'citra@student.univ.ac.id',
            'status' => StatusKarya::PERLU_REVISI,
        ]);

        $payload = [
            'email' => 'salah@student.univ.ac.id',
            'judul' => 'Judul Baru',
            'isi' => 'Isi baru naskah cerita lebih dari lima puluh karakter untuk pengujian revisi gagal.',
        ];

        $response = $this->putJson('/api/kiriman/'.$karya->nomor_kiriman, $payload);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Data kiriman karya tidak ditemukan.',
            ]);
    }

    public function test_dapat_melihat_daftar_karya_terbit_dan_detail(): void
    {
        Karya::create([
            'slug' => 'karya-terbit-publik',
            'judul' => 'Karya Terbit Publik',
            'jenis' => 'puisi',
            'isi' => 'Puisi yang telah terbit secara resmi untuk dinikmati oleh seluruh pembaca sastra kampus.',
            'nama_penulis' => 'Penyair Kampus',
            'email' => 'penyair@student.univ.ac.id',
            'status' => StatusKarya::TERBIT,
        ]);

        Karya::create([
            'slug' => 'karya-masih-ditinjau',
            'judul' => 'Karya Masih Ditinjau',
            'jenis' => 'puisi',
            'isi' => 'Puisi yang belum terbit dan sedang dalam proses kurasi oleh tim kurator redaksi.',
            'nama_penulis' => 'Penyair Magang',
            'email' => 'magang@student.univ.ac.id',
            'status' => StatusKarya::DITINJAU,
        ]);

        $response = $this->getJson('/api/karya');
        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));

        $responseDetail = $this->getJson('/api/karya/karya-terbit-publik');
        $responseDetail->assertStatus(200)
            ->assertJsonPath('data.slug', 'karya-terbit-publik')
            ->assertJsonPath('data.status', 'terbit')
            ->assertJsonPath('data.status_label', 'Terbit');

        $responseDetailDraft = $this->getJson('/api/karya/karya-masih-ditinjau');
        $responseDetailDraft->assertStatus(404);
    }
}

