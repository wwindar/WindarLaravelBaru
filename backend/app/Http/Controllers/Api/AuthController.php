<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Registrasi akun mahasiswa / penulis baru khusus civitas UMPKU & ITS PKU.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'alpha_dash', 'min:3', 'max:30', 'unique:users,username'],
            'email'    => [
                'required',
                'email',
                'unique:users,email',
                'max:150',
                function ($attribute, $value, $fail) {
                    $domain = strtolower(substr(strrchr($value, '@'), 1));
                    $allowedDomains = ['umpku.ac.id', 'student.umpku.ac.id', 'itspku.ac.id', 'students.itspku.ac.id'];
                    
                    $isAllowed = false;
                    foreach ($allowedDomains as $allowed) {
                        if ($domain === $allowed || str_ends_with($domain, '.' . $allowed)) {
                            $isAllowed = true;
                            break;
                        }
                    }

                    if (! $isAllowed) {
                        $fail('Pendaftaran naskah hanya dibuka untuk mahasiswa civitas akademika (wajib menggunakan email kampus resmi seperti @students.itspku.ac.id, @itspku.ac.id, @umpku.ac.id, atau @student.umpku.ac.id).');
                    }
                },
            ],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, tanda strip, atau garis bawah.',
            'username.min'      => 'Username minimal 3 karakter.',
            'username.unique'   => 'Username ini sudah digunakan, silakan pilih yang lain.',
            'email.required'    => 'Email kampus wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email ini sudah terdaftar. Silakan langsung masuk.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min'      => 'Kata sandi minimal 6 karakter.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'username' => strtolower($validated['username']),
            'email'    => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role'     => 'penulis',
        ]);

        $token = $user->createToken('aksara-kampus-user')->plainTextToken;

        return response()->json([
            'message' => 'Pendaftaran berhasil. Selamat datang di Aksara Kampus!',
            'data'    => [
                'token'      => $token,
                'token_type' => 'Bearer',
                'user'       => [
                    'id'       => $user->id,
                    'name'     => $user->name,
                    'username' => $user->username,
                    'email'    => $user->email,
                    'role'     => $user->role,
                ],
            ],
        ], 201);
    }

    /**
     * Login admin atau mahasiswa — kembalikan Bearer token Sanctum.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau kata sandi salah.',
            ], 401);
        }

        // Hapus token lama agar tidak menumpuk
        $user->tokens()->delete();

        $tokenName = $user->isAdmin() ? 'aksara-kampus-redaksi' : 'aksara-kampus-user';
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil. Selamat datang di Aksara Kampus.',
            'data'    => [
                'token'      => $token,
                'token_type' => 'Bearer',
                'user'       => [
                    'id'       => $user->id,
                    'name'     => $user->name,
                    'username' => $user->username,
                    'email'    => $user->email,
                    'role'     => $user->role,
                ],
            ],
        ]);
    }

    /**
     * Ambil profil user yang sedang login.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'id'       => $user->id,
                'name'     => $user->name,
                'username' => $user->username,
                'email'    => $user->email,
                'role'     => $user->role,
            ],
        ]);
    }

    /**
     * Logout — cabut token yang sedang dipakai.
     */
    public function logout(Request $request): JsonResponse
    {
        // Hapus semua token milik user agar tidak ada token yang masih aktif.
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }
}
