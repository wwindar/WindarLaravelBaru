<?php

use App\Enums\StatusTulisan;
use App\Models\Berita;
use App\Models\Notifikasi;
use App\Models\Tulisan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

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
        'name' => 'required|string|max:100',
        'username' => 'nullable|string|alpha_dash|max:30|unique:users,username',
        'email' => 'required|email|max:150',
        'password' => 'required|string|min:6',
    ], [
        'name.required' => 'Nama lengkap admin wajib diisi.',
        'username.alpha_dash' => 'Username hanya boleh huruf, angka, strip, atau garis bawah.',
        'username.unique' => 'Username ini sudah digunakan.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'password.required' => 'Kata sandi wajib diisi.',
        'password.min' => 'Kata sandi minimal 6 karakter.',
    ]);

    // Update atau buat user baru dengan role 'admin'
    $user = User::updateOrCreate(
        ['email' => strtolower($validated['email'])],
        [
            'name' => $validated['name'],
            'username' => ! empty($validated['username']) ? strtolower($validated['username']) : null,
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
        ]
    );

    return redirect()->route('admin.login')->with('success', 'Akun admin dengan email '.$user->email.' berhasil dibuat/diperbarui! Silakan login sekarang.');
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

    // ── Buka Portal Frontend dengan Sesi Admin (Single Sign-On) ───────────
    Route::get('/admin/portal', function (Request $request) {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            return redirect()->route('admin.login')->with('error', 'Akses ditolak.');
        }

        // Hapus token SSO lama dan buat token baru untuk sesi frontend
        $user->tokens()->where('name', 'portal-sso')->delete();
        $token = $user->createToken('portal-sso')->plainTextToken;

        return redirect('http://localhost:3000/sso?token=' . urlencode($token));
    })->name('admin.portal');

    // ── Baca detail tulisan ───────────────────────────────────────────────
    Route::get('/admin/tulisan/{id}', function ($id) {
        if (! Auth::user()->isAdmin()) {
            return redirect()->route('admin.login')->with('error', 'Akses ditolak.');
        }
        $tulisan = Tulisan::with(['user', 'kategori', 'riwayatStatus'])->findOrFail($id);
        $laporan = $tulisan->laporan()->latest()->get();

        return view('admin.tulisan.show', compact('tulisan', 'laporan'));
    })->name('admin.tulisan.show');

    // ── Hapus permanen tulisan ────────────────────────────────────────────
    Route::delete('/admin/tulisan/{id}', function (Request $request, $id) {
        if (! Auth::user()->isAdmin()) {
            return redirect()->route('admin.login')->with('error', 'Akses ditolak.');
        }
        $tulisan = Tulisan::findOrFail($id);
        $judul = $tulisan->judul;

        // Kirim notifikasi ke penulis sebelum dihapus
        Notifikasi::create([
            'user_id' => $tulisan->user_id,
            'judul' => 'Tulisan Dihapus oleh Admin',
            'pesan' => 'Tulisan "'.$judul.'" telah dihapus oleh tim redaksi karena melanggar ketentuan.',
        ]);

        $tulisan->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Tulisan "'.$judul.'" berhasil dihapus.');
    })->name('admin.tulisan.hapus');

    // ── Aksi moderasi tulisan dari web admin ──────────────────────────────
    Route::post('/admin/tulisan/{id}/setujui', function ($id) {
        if (! Auth::user()->isAdmin()) {
            return redirect()->route('admin.login')->with('error', 'Akses ditolak.');
        }
        $tulisan = Tulisan::findOrFail($id);
        $tulisan->update([
            'status' => StatusTulisan::Terbit,
            'diterbitkan_pada' => now(),
            'catatan_editor' => null,
        ]);
        Notifikasi::create([
            'user_id' => $tulisan->user_id,
            'judul' => 'Tulisan Disetujui & Terbit',
            'pesan' => 'Tulisan "'.$tulisan->judul.'" telah disetujui dan kini terbit.',
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Tulisan "'.$tulisan->judul.'" berhasil diterbitkan.');
    })->name('admin.tulisan.setujui');

    Route::post('/admin/tulisan/{id}/revisi', function (Request $request, $id) {
        if (! Auth::user()->isAdmin()) {
            return redirect()->route('admin.login')->with('error', 'Akses ditolak.');
        }
        $catatan = $request->input('catatan_editor', '');
        if (empty(trim($catatan))) {
            return redirect()->route('admin.dashboard')->with('error', 'Catatan revisi wajib diisi.');
        }
        $tulisan = Tulisan::findOrFail($id);
        $tulisan->update([
            'status' => StatusTulisan::PerluRevisi,
            'catatan_editor' => $catatan,
        ]);
        Notifikasi::create([
            'user_id' => $tulisan->user_id,
            'judul' => 'Tulisan Perlu Revisi',
            'pesan' => 'Tulisan "'.$tulisan->judul.'" perlu direvisi. Catatan: '.$catatan,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Tulisan dikembalikan untuk direvisi.');
    })->name('admin.tulisan.revisi');

    Route::post('/admin/tulisan/{id}/tolak', function (Request $request, $id) {
        if (! Auth::user()->isAdmin()) {
            return redirect()->route('admin.login')->with('error', 'Akses ditolak.');
        }
        $catatan = $request->input('catatan_editor', '');
        if (empty(trim($catatan))) {
            return redirect()->route('admin.dashboard')->with('error', 'Catatan penolakan wajib diisi.');
        }
        $tulisan = Tulisan::findOrFail($id);
        $tulisan->update([
            'status' => StatusTulisan::Ditolak,
            'catatan_editor' => $catatan,
        ]);
        Notifikasi::create([
            'user_id' => $tulisan->user_id,
            'judul' => 'Tulisan Ditolak',
            'pesan' => 'Tulisan "'.$tulisan->judul.'" ditolak. Alasan: '.$catatan,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Tulisan berhasil ditolak.');
    })->name('admin.tulisan.tolak');

    // ── Kelola Berita / Iklan / Pengumuman oleh Admin ─────────────────────
    Route::post('/admin/berita', function (Request $request) {
        if (! Auth::user()->isAdmin()) {
            return redirect()->route('admin.login')->with('error', 'Akses ditolak.');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'ringkasan' => 'nullable|string|max:500',
            'isi' => 'required|string',
        ], [
            'judul.required' => 'Judul berita / iklan wajib diisi.',
            'isi.required' => 'Isi berita / pengumuman wajib diisi.',
        ]);

        $baseSlug = Str::slug($validated['judul']);
        $slug = $baseSlug;
        $counter = 1;
        while (Berita::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        Berita::create([
            'user_id' => Auth::id(),
            'judul' => $validated['judul'],
            'slug' => $slug,
            'ringkasan' => $validated['ringkasan'] ?? Str::limit(strip_tags($validated['isi']), 150),
            'isi' => $validated['isi'],
            'status' => 'terbit',
            'diterbitkan_pada' => now(),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Berita / Pengumuman "'.$validated['judul'].'" berhasil diterbitkan!');
    })->name('admin.berita.store');

    Route::delete('/admin/berita/{id}', function ($id) {
        if (! Auth::user()->isAdmin()) {
            return redirect()->route('admin.login')->with('error', 'Akses ditolak.');
        }

        $berita = Berita::findOrFail($id);
        $judul = $berita->judul;
        $berita->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Berita "'.$judul.'" berhasil dihapus.');
    })->name('admin.berita.hapus');

    Route::post('/admin/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    })->name('admin.logout');
});
