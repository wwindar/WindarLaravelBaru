<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email'    => 'admin@kampus.ac.id',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);
    }

    public function test_login_berhasil_dengan_kredensial_benar(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email'    => 'admin@kampus.ac.id',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => ['token', 'token_type', 'user' => ['id', 'name', 'email', 'role']],
            ])
            ->assertJsonPath('data.user.role', 'admin')
            ->assertJsonPath('data.token_type', 'Bearer');
    }

    public function test_login_gagal_dengan_password_salah(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email'    => 'admin@kampus.ac.id',
            'password' => 'salah-password',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Email atau kata sandi salah.');
    }

    public function test_login_gagal_user_bukan_admin(): void
    {
        User::factory()->create([
            'email'    => 'penulis@kampus.ac.id',
            'password' => bcrypt('password'),
            'role'     => 'penulis',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => 'penulis@kampus.ac.id',
            'password' => 'password',
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('message', 'Akun ini tidak memiliki hak akses redaksi.');
    }

    public function test_login_validasi_field_kosong(): void
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_me_mengembalikan_profil_user_yang_login(): void
    {
        $token = $this->admin->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('data.email', 'admin@kampus.ac.id')
            ->assertJsonPath('data.role', 'admin');
    }

    public function test_me_ditolak_tanpa_token(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    public function test_logout_berhasil_dan_token_tidak_bisa_dipakai_lagi(): void
    {
        $token = $this->admin->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Logout berhasil.');

        // Token sudah dicabut — me harus 401
        $this->getJson('/api/auth/me')
            ->assertStatus(401);
    }

    public function test_bearer_token_diterima_oleh_admin_middleware(): void
    {
        $token = $this->admin->createToken('aksara-kampus-redaksi')->plainTextToken;

        // Coba akses endpoint admin menggunakan Bearer token (bukan X-Admin-Token)
        $response = $this->withToken($token)->getJson('/api/admin/artikel');

        $response->assertStatus(200);
    }
}
