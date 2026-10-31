<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun Admin Redaksi - Aksara Kampus</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --color-bg: #FAF8F5;
            --color-surface: #FFFFFF;
            --color-surface-subtle: #F5EFE8;
            --color-border: #EAE4DC;
            --color-text: #1C1917;
            --color-text-muted: #78716C;
            --color-accent: #EA580C;
            --color-accent-gradient: linear-gradient(135deg, #FB923C 0%, #EA580C 100%);
            --color-accent-soft: #FFF1EB;
            --card-shadow: 0 16px 40px -10px rgba(234, 88, 12, 0.12);
            --font-heading: 'Sora', sans-serif;
            --font-body: 'DM Sans', sans-serif;
        }

        @media (prefers-color-scheme: dark) {
            :root:not([data-theme="light"]) {
                --color-bg: #181513;
                --color-surface: #24201D;
                --color-surface-subtle: #2E2824;
                --color-border: #3A342F;
                --color-text: #FAF8F5;
                --color-text-muted: #A8A29E;
                --color-accent: #F97316;
                --color-accent-gradient: linear-gradient(135deg, #FB923C 0%, #EA580C 100%);
                --color-accent-soft: rgba(249, 115, 22, 0.16);
                --card-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.5);
            }
        }

        body {
            background-color: var(--color-bg);
            color: var(--color-text);
            font-family: var(--font-body);
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, .font-heading {
            font-family: var(--font-heading);
        }

        .card-auth {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 24px;
            box-shadow: var(--card-shadow);
        }

        .btn-gradient {
            background: var(--color-accent-gradient);
            color: #FFFFFF;
            border: none;
            border-radius: 14px;
            font-weight: 600;
            box-shadow: 0 6px 18px rgba(234, 88, 12, 0.3);
            transition: all 0.2s ease;
        }
        .btn-gradient:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <div class="card-auth p-8 space-y-6">
            
            <div class="text-center space-y-2">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center font-bold text-white text-xl shadow-md mx-auto" style="background: var(--color-accent-gradient); font-family: var(--font-heading);">
                    AK
                </div>
                <h1 class="text-xl font-bold tracking-tight" style="color: var(--color-text);">Buat Akun Admin Pribadi</h1>
                <p class="text-xs" style="color: var(--color-text-muted);">Daftarkan email pribadi kamu untuk akses Admin Redaksi</p>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-2xl text-xs space-y-2 font-medium" style="background-color: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0;">
                    <p class="font-bold">{{ session('success') }}</p>
                    <a href="{{ route('admin.login') }}" class="inline-block mt-2 px-3 py-1.5 btn-gradient text-white text-xs font-semibold rounded-lg">
                        Langsung Masuk ke Dasbor &rarr;
                    </a>
                </div>
            @endif

            @if(session('error'))
                <div class="p-3.5 rounded-xl text-xs font-semibold" style="background-color: #FEE2E2; color: #991B1B; border: 1px solid #FECACA;">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('admin.register.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase mb-1.5" style="color: var(--color-text-muted);">Nama Lengkap Admin</label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ old('name') }}" 
                        placeholder="Nama kamu..."
                        class="w-full px-3.5 py-2.5 rounded-xl text-sm border focus:outline-none"
                        style="background-color: var(--color-surface-subtle); border-color: var(--color-border); color: var(--color-text);"
                        required
                    />
                    @error('name')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase mb-1.5" style="color: var(--color-text-muted);">Username</label>
                    <input 
                        type="text" 
                        name="username" 
                        value="{{ old('username') }}" 
                        placeholder="contoh: windar_admin"
                        class="w-full px-3.5 py-2.5 rounded-xl text-sm border focus:outline-none"
                        style="background-color: var(--color-surface-subtle); border-color: var(--color-border); color: var(--color-text);"
                    />
                    @error('username')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase mb-1.5" style="color: var(--color-text-muted);">Email Pribadi (Bebas: Gmail, dll)</label>
                    <input 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        placeholder="contoh: nama.kamu@gmail.com"
                        class="w-full px-3.5 py-2.5 rounded-xl text-sm border focus:outline-none"
                        style="background-color: var(--color-surface-subtle); border-color: var(--color-border); color: var(--color-text);"
                        required
                    />
                    @error('email')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase mb-1.5" style="color: var(--color-text-muted);">Kata Sandi Baru</label>
                    <input 
                        type="password" 
                        name="password" 
                        placeholder="Minimal 6 karakter..."
                        class="w-full px-3.5 py-2.5 rounded-xl text-sm border focus:outline-none"
                        style="background-color: var(--color-surface-subtle); border-color: var(--color-border); color: var(--color-text);"
                        minlength="6"
                        required
                    />
                    @error('password')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button 
                    type="submit" 
                    class="w-full py-2.5 btn-gradient text-sm mt-2"
                >
                    Daftarkan Sebagai Admin Redaksi
                </button>
            </form>

            <p class="text-center text-xs pt-3 border-t" style="border-color: var(--color-border); color: var(--color-text-muted);">
                Sudah punya akun admin? 
                <a href="{{ route('admin.login') }}" class="font-semibold hover:underline" style="color: var(--color-accent);">Masuk di sini</a>
            </p>

        </div>

        <p class="text-center mt-5 text-xs space-y-1" style="color: var(--color-text-muted);">
            <a href="{{ url('/') }}" class="hover:underline transition block">&larr; Kembali ke Halaman Utama Backend</a>
            <span class="block text-[11px] opacity-80">&copy; 2026 <strong style="color: var(--color-accent);">Windar Shineta</strong> &bull; Aksara Kampus</span>
        </p>
    </div>

</body>
</html>
