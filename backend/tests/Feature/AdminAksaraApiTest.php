<?php

namespace Tests\Feature;

use App\Enums\StatusKarya;
use App\Models\Artikel;
use App\Models\Karya;
use App\Models\Kategori;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAksaraApiTest extends TestCase
{
    use RefreshDatabase;

    protected string $adminToken = 'redaksi-rahasia-123';

    protected Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kategori = Kategori::create([
            'nama' => 'Esai & Kritik',
            'slug' => 'esai-kritik',
        ]);
    }

    public function test_admin_endpoint_tanpa_token_atau_token_salah_mengembalikan_401_json(): void
    {
        // Tanpa header
        $responseTanpaToken = $this->getJson('/api/admin/artikel');
        $responseTanpaToken->assertStatus(401)
            ->assertJson([
                'message' => 'Token redaksi tidak valid atau tidak disertakan.',
            ]);

        // Token keliru
        $responseTokenSalah = $this->withHeader('X-Admin-Token', 'token-palsu-123')
            ->getJson('/api/admin/artikel');
        $responseTokenSalah->assertStatus(401)
            ->assertJson([
                'message' => 'Token redaksi tidak valid atau tidak disertakan.',
            ]);
    }

    public function test_admin_crud_artikel(): void
    {
        // 1. Create (Store)
        $payload = [
            'judul' => 'Bedah Dinamika Sastra Kampus Terkini',
            'ringkasan' => 'Ulasan komprehensif mengenai sastra kampus.',
            'isi' => 'Isi ulasan lengkap mengenai dunia sastra yang berkembang dinamis di kalangan pemuda.',
            'jenis' => 'berita',
            'kategori_id' => $this->kategori->id,
            'penulis' => 'Redaktur Utama',
            'status' => 'terbit',
        ];

        $responseStore = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->postJson('/api/admin/artikel', $payload);

        $responseStore->assertStatus(201)
            ->assertJsonPath('data.judul', 'Bedah Dinamika Sastra Kampus Terkini')
            ->assertJsonPath('data.slug', 'bedah-dinamika-sastra-kampus-terkini')
            ->assertJsonPath('data.status', 'terbit');

        $artikelId = $responseStore->json('data.id');
        $this->assertNotNull($responseStore->json('data.diterbitkan_pada'));

        // 2. Read (Show)
        $responseShow = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->getJson('/api/admin/artikel/'.$artikelId);

        $responseShow->assertStatus(200)
            ->assertJsonPath('data.id', $artikelId);

        // 3. Update
        $responseUpdate = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->putJson('/api/admin/artikel/'.$artikelId, [
                'judul' => 'Bedah Dinamika Sastra Kampus Terkini (Revisi Judul)',
                'status' => 'terbit',
            ]);

        $responseUpdate->assertStatus(200)
            ->assertJsonPath('data.judul', 'Bedah Dinamika Sastra Kampus Terkini (Revisi Judul)')
            ->assertJsonPath('data.slug', 'bedah-dinamika-sastra-kampus-terkini-revisi-judul');

        // 4. Delete
        $responseDelete = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->deleteJson('/api/admin/artikel/'.$artikelId);

        $responseDelete->assertStatus(200)
            ->assertJson(['message' => 'Artikel berhasil dihapus.']);

        $this->assertDatabaseMissing('artikel', ['id' => $artikelId]);
    }

    public function test_admin_crud_kategori(): void
    {
        // Store
        $response = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->postJson('/api/admin/kategori', ['nama' => 'Catatan Perjalanan']);

        $response->assertStatus(201)
            ->assertJsonPath('data.nama', 'Catatan Perjalanan')
            ->assertJsonPath('data.slug', 'catatan-perjalanan');

        $kategoriId = $response->json('data.id');

        // Update
        $responseUpdate = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->putJson('/api/admin/kategori/'.$kategoriId, ['nama' => 'Catatan Budaya']);

        $responseUpdate->assertStatus(200)
            ->assertJsonPath('data.nama', 'Catatan Budaya')
            ->assertJsonPath('data.slug', 'catatan-budaya');

        // Destroy
        $responseDelete = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->deleteJson('/api/admin/kategori/'.$kategoriId);

        $responseDelete->assertStatus(200)
            ->assertJson(['message' => 'Kategori berhasil dihapus.']);
    }

    public function test_admin_hapus_kategori_yang_masih_dipakai_artikel_mengembalikan_422_berbahasa_indonesia(): void
    {
        Artikel::create([
            'slug' => 'artikel-terkait-kategori',
            'judul' => 'Artikel Terkait',
            'ringkasan' => 'Ringkasan artikel',
            'isi' => 'Isi artikel panjang untuk menguji foreign key kategori.',
            'jenis' => 'blog',
            'kategori_id' => $this->kategori->id,
            'penulis' => 'Penulis',
            'status' => 'terbit',
        ]);

        $response = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->deleteJson('/api/admin/kategori/'.$this->kategori->id);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh artikel.',
            ]);

        $this->assertDatabaseHas('kategori', ['id' => $this->kategori->id]);
    }

    public function test_admin_ubah_status_karya_dan_menambah_riwayat(): void
    {
        $karya = Karya::create([
            'slug' => 'karya-kurasi',
            'judul' => 'Karya Kurasi',
            'jenis' => 'puisi',
            'isi' => 'Isi puisi yang akan dikurasi oleh tim dewan redaksi sastra aksara kampus.',
            'nama_penulis' => 'Penulis Muda',
            'email' => 'muda@student.univ.ac.id',
            'status' => StatusKarya::DIKIRIM,
        ]);

        $response = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->patchJson('/api/admin/karya/'.$karya->id.'/status', [
                'status' => 'ditinjau',
                'catatan' => 'Naskah masuk meja telaah redaktur.',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'ditinjau')
            ->assertJsonPath('data.status_label', 'Sedang Ditinjau')
            ->assertJsonPath('data.catatan_redaksi', 'Naskah masuk meja telaah redaktur.');

        $this->assertDatabaseHas('karya', [
            'id' => $karya->id,
            'status' => 'ditinjau',
        ]);

        $this->assertDatabaseHas('riwayat_karya', [
            'karya_id' => $karya->id,
            'status' => 'ditinjau',
            'catatan' => 'Naskah masuk meja telaah redaktur.',
        ]);
    }

    public function test_catatan_redaksi_wajib_diisi_saat_status_perlu_revisi_dan_ditolak(): void
    {
        $karya = Karya::create([
            'slug' => 'karya-uji-catatan',
            'judul' => 'Karya Uji Catatan',
            'jenis' => 'cerpen',
            'isi' => 'Isi cerita pendek naskah pengujian validasi catatan redaksi wajib diisi.',
            'nama_penulis' => 'Penulis',
            'email' => 'penulis@student.univ.ac.id',
            'status' => StatusKarya::DITINJAU,
        ]);

        // Perlu revisi tanpa catatan
        $responseRevisi = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->patchJson('/api/admin/karya/'.$karya->id.'/status', [
                'status' => 'perlu_revisi',
            ]);

        $responseRevisi->assertStatus(422)
            ->assertJsonValidationErrors(['catatan']);

        // Ditolak tanpa catatan
        $responseTolak = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->patchJson('/api/admin/karya/'.$karya->id.'/status', [
                'status' => 'ditolak',
            ]);

        $responseTolak->assertStatus(422)
            ->assertJsonValidationErrors(['catatan']);
    }

    public function test_status_terbit_dan_ditolak_bersifat_final_tidak_bisa_diubah_lagi(): void
    {
        // Status Terbit
        $karyaTerbit = Karya::create([
            'slug' => 'karya-final-terbit',
            'judul' => 'Karya Final Terbit',
            'jenis' => 'puisi',
            'isi' => 'Isi puisi terbit yang bersifat final dan tidak dapat diubah lagi statusnya.',
            'nama_penulis' => 'Penulis',
            'email' => 'penulis@student.univ.ac.id',
            'status' => StatusKarya::TERBIT,
        ]);

        $responseUbahTerbit = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->patchJson('/api/admin/karya/'.$karyaTerbit->id.'/status', [
                'status' => 'ditinjau',
                'catatan' => 'Mencoba kembali ke peninjauan',
            ]);

        $responseUbahTerbit->assertStatus(422)
            ->assertJson([
                'message' => 'Karya dengan status terbit atau ditolak bersifat final dan tidak dapat diubah lagi.',
            ]);

        // Status Ditolak
        $karyaDitolak = Karya::create([
            'slug' => 'karya-final-ditolak',
            'judul' => 'Karya Final Ditolak',
            'jenis' => 'puisi',
            'isi' => 'Isi puisi ditolak yang bersifat final dan tidak dapat diubah lagi statusnya.',
            'nama_penulis' => 'Penulis',
            'email' => 'penulis@student.univ.ac.id',
            'status' => StatusKarya::DITOLAK,
            'catatan_redaksi' => 'Ditolak',
        ]);

        $responseUbahDitolak = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->patchJson('/api/admin/karya/'.$karyaDitolak->id.'/status', [
                'status' => 'ditinjau',
                'catatan' => 'Mencoba kembali ke peninjauan',
            ]);

        $responseUbahDitolak->assertStatus(422)
            ->assertJson([
                'message' => 'Karya dengan status terbit atau ditolak bersifat final dan tidak dapat diubah lagi.',
            ]);
    }

    public function test_admin_dapat_menghapus_karya_berstatus_dikirim_atau_ditolak(): void
    {
        $karyaDikirim = Karya::create([
            'slug' => 'karya-hapus-dikirim',
            'judul' => 'Karya Hapus Dikirim',
            'jenis' => 'puisi',
            'isi' => 'Isi naskah karya yang masih berstatus dikirim untuk diuji penghapusannya.',
            'nama_penulis' => 'Penulis',
            'email' => 'penulis@student.univ.ac.id',
            'status' => StatusKarya::DIKIRIM,
        ]);

        $responseHapus = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->deleteJson('/api/admin/karya/'.$karyaDikirim->id);

        $responseHapus->assertStatus(200)
            ->assertJson(['message' => 'Karya berhasil dihapus.']);

        $this->assertDatabaseMissing('karya', ['id' => $karyaDikirim->id]);
    }

    public function test_admin_gagal_menghapus_karya_berstatus_terbit_atau_ditinjau(): void
    {
        $karyaTerbit = Karya::create([
            'slug' => 'karya-hapus-terbit',
            'judul' => 'Karya Hapus Terbit',
            'jenis' => 'puisi',
            'isi' => 'Isi naskah karya yang berstatus terbit dan tidak boleh dihapus secara sepihak.',
            'nama_penulis' => 'Penulis',
            'email' => 'penulis@student.univ.ac.id',
            'status' => StatusKarya::TERBIT,
        ]);

        $responseHapus = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->deleteJson('/api/admin/karya/'.$karyaTerbit->id);

        $responseHapus->assertStatus(422)
            ->assertJson([
                'message' => 'Karya hanya dapat dihapus jika berstatus Dikirim atau Ditolak.',
            ]);

        $this->assertDatabaseHas('karya', ['id' => $karyaTerbit->id]);
    }

    public function test_admin_crud_agenda(): void
    {
        $payload = [
            'judul' => 'Diskusi Bulanan Pegiat Literasi',
            'jenis' => 'bedah_buku',
            'deskripsi' => 'Diskusi bedah karya literasi bersama pegiat kampus.',
            'lokasi' => 'Ruang Baca Perpustakaan',
            'mulai_pada' => Carbon::now()->addDays(5)->toDateTimeString(),
            'selesai_pada' => Carbon::now()->addDays(5)->addHours(3)->toDateTimeString(),
            'kuota' => 40,
        ];

        $responseStore = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->postJson('/api/admin/agenda', $payload);

        $responseStore->assertStatus(201)
            ->assertJsonPath('data.judul', 'Diskusi Bulanan Pegiat Literasi')
            ->assertJsonPath('data.slug', 'diskusi-bulanan-pegiat-literasi');

        $agendaId = $responseStore->json('data.id');

        $responseDelete = $this->withHeader('X-Admin-Token', $this->adminToken)
            ->deleteJson('/api/admin/agenda/'.$agendaId);

        $responseDelete->assertStatus(200)
            ->assertJson(['message' => 'Agenda berhasil dihapus.']);
    }
}
