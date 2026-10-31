<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Registrasi akun mahasiswa baru dengan email kampus.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'username' => strtolower($validated['username']),
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role' => 'mahasiswa',
        ]);

        $token = $user->createToken('aksara-kampus')->plainTextToken;

        return response()->json([
            'message' => 'Pendaftaran berhasil. Selamat datang di Aksara Kampus!',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $this->formatUser($user),
            ],
        ], 201);
    }

    /**
     * Login — kembalikan Bearer token Sanctum.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $user = User::where('email', strtolower($validated['email']))->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Email atau kata sandi salah.',
            ], 401);
        }

        // Hapus token lama agar tidak menumpuk.
        $user->tokens()->delete();

        $token = $user->createToken('aksara-kampus')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil. Selamat datang di Aksara Kampus.',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $this->formatUser($user),
            ],
        ]);
    }

    /**
     * Ambil profil user yang sedang login.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->formatUser($request->user()),
        ]);
    }

    /**
     * Update profil user yang sedang login.
     */
    public function updateProfil(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username,' . $user->id],
            'bio'      => ['nullable', 'string', 'max:500'],
            'angkatan' => ['nullable', 'string', 'max:10', 'regex:/^\d{4}$/'],
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'username.unique'   => 'Username sudah dipakai pengguna lain.',
            'bio.max'           => 'Bio tidak boleh lebih dari 500 karakter.',
            'angkatan.regex'    => 'Angkatan harus berupa tahun 4 digit (cth: 2023).',
        ]);

        $user->update([
            'name'     => $validated['name'],
            'username' => strtolower($validated['username']),
            'bio'      => $validated['bio'] ?? null,
            'angkatan' => $validated['angkatan'] ?? null,
        ]);

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'data'    => $this->formatUser($user->fresh()),
        ]);
    }

    /**
     * Ganti kata sandi user yang sedang login.
     */
    public function gantiPassword(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'password_lama'  => ['required', 'string'],
            'password_baru'  => ['required', 'string', 'min:8', 'confirmed', Password::min(8)],
        ], [
            'password_lama.required'  => 'Kata sandi lama wajib diisi.',
            'password_baru.required'  => 'Kata sandi baru wajib diisi.',
            'password_baru.min'       => 'Kata sandi baru minimal 8 karakter.',
            'password_baru.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        if (! Hash::check($request->password_lama, $user->password)) {
            return response()->json([
                'message'  => 'Kata sandi lama tidak sesuai.',
                'errors'   => ['password_lama' => ['Kata sandi lama tidak sesuai.']],
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password_baru)]);

        // Hapus semua token lama, paksa login ulang
        $user->tokens()->delete();
        $newToken = $user->createToken('aksara-kampus')->plainTextToken;

        return response()->json([
            'message' => 'Kata sandi berhasil diubah. Silakan masuk kembali dengan kata sandi baru.',
            'data'    => ['token' => $newToken],
        ]);
    }

    /**
     * Logout — cabut token yang sedang dipakai.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Upload / perbarui foto profil.
     */
    public function uploadFoto(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'foto' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'foto.required' => 'File foto wajib dipilih.',
            'foto.image'    => 'File harus berupa gambar.',
            'foto.mimes'    => 'Format gambar yang diperbolehkan: jpeg, png, jpg, webp.',
            'foto.max'      => 'Ukuran foto maksimal 2 MB.',
        ]);

        $file = $request->file('foto');
        $ext = $file->getClientOriginalExtension();
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $ext;

        $targetDir = public_path('uploads/avatars');
        if (! file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Hapus foto lama jika ada
        if ($user->foto && file_exists(public_path($user->foto))) {
            @unlink(public_path($user->foto));
        }

        $file->move($targetDir, $filename);

        $relativePath = 'uploads/avatars/' . $filename;
        $user->update(['foto' => $relativePath]);

        return response()->json([
            'message' => 'Foto profil berhasil diperbarui.',
            'data'    => $this->formatUser($user->fresh()),
        ]);
    }

    /**
     * Hapus foto profil (kembali ke avatar inisial).
     */
    public function hapusFoto(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->foto && file_exists(public_path($user->foto))) {
            @unlink(public_path($user->foto));
        }

        $user->update(['foto' => null]);

        return response()->json([
            'message' => 'Foto profil berhasil dihapus.',
            'data'    => $this->formatUser($user->fresh()),
        ]);
    }

    /**
     * Helper — format data user ke array yang konsisten.
     */
    private function formatUser(User $user): array
    {
        $fotoUrl = null;
        if ($user->foto) {
            if (str_starts_with($user->foto, 'http://') || str_starts_with($user->foto, 'https://')) {
                $fotoUrl = $user->foto;
            } else {
                $fotoUrl = url($user->foto);
            }
        }

        return [
            'id'       => $user->id,
            'name'     => $user->name,
            'username' => $user->username,
            'email'    => $user->email,
            'role'     => $user->role,
            'bio'      => $user->bio,
            'angkatan' => $user->angkatan,
            'foto'     => $fotoUrl,
        ];
    }
}

