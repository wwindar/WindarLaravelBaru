<?php

use App\Models\Kategori;
use App\Models\Tulisan;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->kategori = Kategori::create([
        'slug' => 'sastra-indonesia',
        'nama' => 'Sastra Indonesia',
    ]);

    $this->dosen = User::factory()->dosen()->create([
        'email' => 'dosen@umpku.ac.id',
    ]);

    $this->mahasiswa = User::factory()->mahasiswa()->create([
        'email' => 'andi@students.itspku.ac.id',
    ]);
});

// =========================================================================
// 1. Registrasi & Validasi Domain Email Kampus
// =========================================================================

test('registrasi gagal jika domain email bukan email resmi kampus', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'Budi Pribadi',
        'username' => 'budipribadi',
        'email' => 'budi@gmail.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('registrasi berhasil jika menggunakan email resmi kampus', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'Siti Rahma',
        'username' => 'sitirahma',
        'email' => 'siti@students.itspku.ac.id',
        'password' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => [
                'token',
                'token_type',
                'user' => ['id', 'name', 'username', 'email', 'role'],
            ],
        ])
        ->assertJsonPath('data.user.role', 'mahasiswa')
        ->assertJsonPath('data.user.email', 'siti@students.itspku.ac.id');
});

// =========================================================================
// 2. Login & Autentikasi Sanctum
// =========================================================================

test('login berhasil dengan kredensial yang valid', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'andi@students.itspku.ac.id',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => ['token', 'token_type', 'user'],
        ]);
});

test('login gagal jika password salah', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'andi@students.itspku.ac.id',
        'password' => 'passwordsalah',
    ]);

    $response->assertStatus(401)
        ->assertJsonPath('message', 'Email atau kata sandi salah.');
});

// =========================================================================
// 3. Hak Akses Tiap Peran (Role-based access control)
// =========================================================================

test('pengunjung publik ditolak saat mengakses endpoint mahasiswa dan dosen', function () {
    $this->getJson('/api/mahasiswa/tulisan')->assertStatus(401);
    $this->getJson('/api/dosen/antrean')->assertStatus(401);
});

test('mahasiswa ditolak saat mengakses endpoint khusus dosen', function () {
    $token = $this->mahasiswa->createToken('test')->plainTextToken;

    $this->withToken($token)
        ->getJson('/api/dosen/antrean')
        ->assertStatus(403)
        ->assertJsonPath('message', 'Akses ditolak. Anda tidak memiliki hak untuk melakukan tindakan ini.');
});

test('dosen ditolak saat mengakses endpoint mahasiswa', function () {
    $token = $this->dosen->createToken('test')->plainTextToken;

    $this->withToken($token)
        ->getJson('/api/mahasiswa/tulisan')
        ->assertStatus(403);
});

// =========================================================================
// 4. Alur Lengkap: Simpan Draf -> Ajukan -> Setujui -> Langsung Terbit
// =========================================================================

test('alur mahasiswa menyimpan draf lalu mengajukan tulisan', function () {
    $tokenMahasiswa = $this->mahasiswa->createToken('test')->plainTextToken;

    // 1. Simpan sebagai draf
    $storeResponse = $this->withToken($tokenMahasiswa)->postJson('/api/mahasiswa/tulisan', [
        'judul' => 'Hujan di Kampus Melati',
        'jenis' => 'cerpen',
        'kategori_id' => $this->kategori->id,
        'isi' => 'Hujan deras mengguyur atap gedung perkuliahan sore itu...',
        'ringkasan' => 'Cerita tentang kenangan perkuliahan saat hujan melanda.',
    ]);

    $storeResponse->assertStatus(201)
        ->assertJsonPath('data.status', 'draf')
        ->assertJsonPath('data.status_label', 'Draf');

    $tulisanId = $storeResponse->json('data.id');

    // 2. Ajukan tulisan ke dosen
    $ajukanResponse = $this->withToken($tokenMahasiswa)->postJson("/api/mahasiswa/tulisan/{$tulisanId}/ajukan");

    $ajukanResponse->assertStatus(200)
        ->assertJsonPath('data.status', 'menunggu')
        ->assertJsonPath('data.status_label', 'Menunggu Persetujuan');
});

test('alur dosen menyetujui tulisan dan tulisan langsung terbit otomatis', function () {
    $tulisan = Tulisan::factory()->menunggu()->create([
        'user_id' => $this->mahasiswa->id,
        'kategori_id' => $this->kategori->id,
        'judul' => 'Menembus Batas Cakrawala',
    ]);

    $tokenDosen = $this->dosen->createToken('test')->plainTextToken;

    // Dosen melihat antrean
    $this->withToken($tokenDosen)
        ->getJson('/api/dosen/antrean')
        ->assertStatus(200)
        ->assertJsonPath('data.0.id', $tulisan->id);

    // Dosen menyetujui tulisan
    $approveResponse = $this->withToken($tokenDosen)
        ->postJson("/api/dosen/tulisan/{$tulisan->id}/setujui");

    $approveResponse->assertStatus(200)
        ->assertJsonPath('data.status', 'terbit')
        ->assertJsonPath('data.status_label', 'Terbit');

    // Tulisan sekarang dapat dibaca di portal publik
    $publicResponse = $this->getJson("/api/tulisan/{$tulisan->slug}");
    $publicResponse->assertStatus(200)
        ->assertJsonPath('data.judul', 'Menembus Batas Cakrawala')
        ->assertJsonPath('data.status', 'terbit');

    // Notifikasi diterima oleh mahasiswa
    expect($this->mahasiswa->notifikasi()->count())->toBe(1);
    expect($this->mahasiswa->notifikasi()->first()->judul)->toBe('Tulisan Anda Disetujui!');
});

// =========================================================================
// 5. Alur Revisi & Penolakan dengan Catatan Wajib
// =========================================================================

test('dosen meminta revisi dengan catatan wajib lalu mahasiswa mengeditnya', function () {
    $tulisan = Tulisan::factory()->menunggu()->create([
        'user_id' => $this->mahasiswa->id,
        'kategori_id' => $this->kategori->id,
    ]);

    // Dosen login
    Sanctum::actingAs($this->dosen);

    // Minta revisi tanpa catatan harus gagal validasi
    $this->postJson("/api/dosen/tulisan/{$tulisan->id}/revisi", [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['catatan']);

    // Minta revisi dengan catatan berhasil
    $revisiResponse = $this->postJson("/api/dosen/tulisan/{$tulisan->id}/revisi", [
        'catatan' => 'Perbaiki tanda baca pada bait ke-3.',
    ]);

    $revisiResponse->assertStatus(200)
        ->assertJsonPath('data.status', 'perlu_revisi')
        ->assertJsonPath('data.status_label', 'Perlu Revisi');

    // Mahasiswa menerima notifikasi
    expect($this->mahasiswa->notifikasi()->first()->judul)->toBe('Tulisan Anda Perlu Direvisi');

    // Mahasiswa login untuk mengedit
    Sanctum::actingAs($this->mahasiswa);

    $updateResponse = $this->putJson("/api/mahasiswa/tulisan/{$tulisan->id}", [
        'judul' => 'Judul Diperbarui',
        'jenis' => $tulisan->jenis->value,
        'kategori_id' => $this->kategori->id,
        'isi' => 'Isi puisi yang sudah diperbaiki tanda bacanya secara tuntas.',
    ]);

    $updateResponse->assertStatus(200)
        ->assertJsonPath('data.judul', 'Judul Diperbarui');

    // Mahasiswa mengajukan kembali
    $this->postJson("/api/mahasiswa/tulisan/{$tulisan->id}/ajukan")
        ->assertStatus(200)
        ->assertJsonPath('data.status', 'menunggu');
});

test('dosen menolak tulisan dengan catatan wajib', function () {
    $tulisan = Tulisan::factory()->menunggu()->create([
        'user_id' => $this->mahasiswa->id,
        'kategori_id' => $this->kategori->id,
    ]);

    $tokenDosen = $this->dosen->createToken('test')->plainTextToken;

    $this->withToken($tokenDosen)
        ->postJson("/api/dosen/tulisan/{$tulisan->id}/tolak", [
            'catatan' => 'Karya ini tidak memenuhi kriteria tema kampus.',
        ])
        ->assertStatus(200)
        ->assertJsonPath('data.status', 'ditolak')
        ->assertJsonPath('data.status_label', 'Ditolak');

    expect($this->mahasiswa->notifikasi()->first()->judul)->toBe('Tulisan Anda Ditolak');
});

// =========================================================================
// 6. Publik Melaporkan Tulisan & Dosen Memeriksa Laporan
// =========================================================================

test('pengunjung publik dapat melaporkan tulisan terbit', function () {
    $tulisan = Tulisan::factory()->terbit()->create([
        'user_id' => $this->mahasiswa->id,
        'kategori_id' => $this->kategori->id,
    ]);

    $response = $this->postJson("/api/tulisan/{$tulisan->id}/lapor", [
        'nama_pelapor' => 'Agus Santoso',
        'email_pelapor' => 'agus@gmail.com',
        'alasan' => 'Indikasi plagiarisme bait pertama dari penyair terkenal.',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('message', 'Laporan berhasil dikirim. Terima kasih atas kontribusi Anda.');

    // Dosen dapat membaca daftar laporan
    $tokenDosen = $this->dosen->createToken('test')->plainTextToken;
    $this->withToken($tokenDosen)
        ->getJson('/api/dosen/laporan')
        ->assertStatus(200)
        ->assertJsonPath('data.0.alasan', 'Indikasi plagiarisme bait pertama dari penyair terkenal.');
});

// =========================================================================
// 7. Notifikasi In-App: Menandai Dibaca
// =========================================================================

test('mahasiswa dapat membaca daftar notifikasi dan menandai sudah dibaca', function () {
    $this->mahasiswa->notifikasi()->create([
        'judul' => 'Halo Mahasiswa',
        'pesan' => 'Ini pesan notifikasi perdana Anda.',
        'dibaca' => false,
    ]);

    $tokenMahasiswa = $this->mahasiswa->createToken('test')->plainTextToken;

    // List notifikasi
    $listResponse = $this->withToken($tokenMahasiswa)->getJson('/api/notifikasi');
    $listResponse->assertStatus(200)
        ->assertJsonPath('data.0.dibaca', false);

    $notifId = $listResponse->json('data.0.id');

    // Tandai satu notifikasi dibaca
    $this->withToken($tokenMahasiswa)
        ->patchJson("/api/notifikasi/{$notifId}/baca")
        ->assertStatus(200)
        ->assertJsonPath('data.dibaca', true);
});
