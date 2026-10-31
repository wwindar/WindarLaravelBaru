<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ==========================================
// Halaman Login, Register & Dashboard Admin Laravel Web
// ==========================================
Route::get('/admin/login', function () {
    if (Auth::check() && Auth::user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return view('admin.login');
})->name('admin.login');

Route::get('/admin/register', function () {
    return view('admin.register');
})->name('admin.register');

Route::post('/admin/register', function (Request $request) {
    $validated = $request->validate([
        'name'     => 'required|string|max:100',
        'username' => 'nullable|string|alpha_dash|max:30|unique:users,username',
        'email'    => 'required|email|max:150',
        'password' => 'required|string|min:6',
    ], [
        'name.required'       => 'Nama lengkap admin wajib diisi.',
        'username.alpha_dash' => 'Username hanya boleh huruf, angka, strip, atau garis bawah.',
        'username.unique'     => 'Username ini sudah digunakan.',
        'email.required'      => 'Email wajib diisi.',
        'email.email'         => 'Format email tidak valid.',
        'password.required'   => 'Kata sandi wajib diisi.',
        'password.min'        => 'Kata sandi minimal 6 karakter.',
    ]);

    // Update atau buat user baru dengan role 'admin'
    $user = User::updateOrCreate(
        ['email' => strtolower($validated['email'])],
        [
            'name'     => $validated['name'],
            'username' => !empty($validated['username']) ? strtolower($validated['username']) : null,
            'password' => Hash::make($validated['password']),
            'role'     => 'admin',
        ]
    );

    return redirect()->route('admin.login')->with('success', 'Akun admin dengan email ' . $user->email . ' berhasil dibuat/diperbarui! Silakan login sekarang.');
})->name('admin.register.submit');

Route::post('/admin/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ], [
        'email.required' => 'Email admin wajib diisi.',
        'password.required' => 'Kata sandi wajib diisi.',
    ]);

    $user = User::where('email', $credentials['email'])->first();

    if (! $user || ! Hash::check($credentials['password'], $user->password)) {
        return back()->with('error', 'Email atau kata sandi admin salah.')->withInput();
    }

    if (! $user->isAdmin()) {
        return back()->with('error', 'Akun ini bukan administrator / redaksi.')->withInput();
    }

    Auth::login($user);
    $request->session()->regenerate();

    return redirect()->route('admin.dashboard');
})->name('admin.login.submit');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', function () {
        if (! Auth::user()->isAdmin()) {
            Auth::logout();
            return redirect()->route('admin.login')->with('error', 'Akses ditolak.');
        }
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::post('/admin/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    })->name('admin.logout');
});
