<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dasbor Redaksi - Aksara Kampus</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* =====================================================================
           THEME VARIABLES (Mengikuti Palet uTask: Pastel Lilac & Dark Violet)
        ===================================================================== */
        :root {
            /* Light Theme: UMPKU Campus Orange & Warm Clean Canvas */
            --color-bg: #FAF8F5;
            --color-surface: #FFFFFF;
            --color-surface-subtle: #F5EFE8;
            --color-border: #EAE4DC;
            --color-text: #1C1917;
            --color-text-muted: #78716C;

            /* Vibrant Campus Orange Accent */
            --color-accent: #EA580C;
            --color-accent-hover: #C2410C;
            --color-accent-gradient: linear-gradient(135deg, #FB923C 0%, #EA580C 100%);
            --color-accent-soft: #FFF1EB;
            --color-accent-glow: rgba(234, 88, 12, 0.25);

            /* Status Dots */
            --dot-menunggu: #D97706;
            --dot-revisi: #EA580C;
            --dot-terbit: #10B981;
            --dot-ditolak: #EF4444;
            --dot-laporan: #F97316;

            /* Status Badges */
            --badge-draf-bg: #EAE4DC;
            --badge-draf-text: #44403C;
            --badge-menunggu-bg: #FEF3C7;
            --badge-menunggu-text: #92400E;
            --badge-revisi-bg: #FFEDD5;
            --badge-revisi-text: #9A3412;
            --badge-terbit-bg: #D1FAE5;
            --badge-terbit-text: #065F46;
            --badge-ditolak-bg: #FEE2E2;
            --badge-ditolak-text: #991B1B;

            --card-shadow: 0 10px 30px -5px rgba(234, 88, 12, 0.08), 0 4px 12px -2px rgba(28, 25, 23, 0.04);

            --font-heading: 'Sora', sans-serif;
            --font-body: 'DM Sans', sans-serif;
        }

        /* Dark Mode: Sistem Otomatis & Manual Toggle */
        @media (prefers-color-scheme: dark) {
            :root:not([data-theme="light"]) {
                --color-bg: #181513;
                --color-surface: #24201D;
                --color-surface-subtle: #2E2824;
                --color-border: #3A342F;
                --color-text: #FAF8F5;
                --color-text-muted: #A8A29E;

                --color-accent: #F97316;
                --color-accent-hover: #EA580C;
                --color-accent-gradient: linear-gradient(135deg, #FB923C 0%, #EA580C 100%);
                --color-accent-soft: rgba(249, 115, 22, 0.16);
                --color-accent-glow: rgba(249, 115, 22, 0.35);

                --badge-draf-bg: rgba(234, 228, 220, 0.12);
                --badge-draf-text: #E7E5E4;
                --badge-menunggu-bg: rgba(254, 243, 199, 0.14);
                --badge-menunggu-text: #FDE68A;
                --badge-revisi-bg: rgba(255, 237, 213, 0.14);
                --badge-revisi-text: #FDBA74;
                --badge-terbit-bg: rgba(209, 250, 229, 0.14);
                --badge-terbit-text: #6EE7B7;
                --badge-ditolak-bg: rgba(254, 226, 226, 0.14);
                --badge-ditolak-text: #FCA5A5;

                --card-shadow: 0 12px 36px -6px rgba(0, 0, 0, 0.45), 0 4px 16px -2px rgba(0, 0, 0, 0.3);
            }
        }

        /* Forced Dark Theme */
        [data-theme="dark"] {
            --color-bg: #181513;
            --color-surface: #24201D;
            --color-surface-subtle: #2E2824;
            --color-border: #3A342F;
            --color-text: #FAF8F5;
            --color-text-muted: #A8A29E;

            --color-accent: #F97316;
            --color-accent-hover: #EA580C;
            --color-accent-gradient: linear-gradient(135deg, #FB923C 0%, #EA580C 100%);
            --color-accent-soft: rgba(249, 115, 22, 0.16);
            --color-accent-glow: rgba(249, 115, 22, 0.35);

            --badge-draf-bg: rgba(234, 228, 220, 0.12);
            --badge-draf-text: #E7E5E4;
            --badge-menunggu-bg: rgba(254, 243, 199, 0.14);
            --badge-menunggu-text: #FDE68A;
            --badge-revisi-bg: rgba(255, 237, 213, 0.14);
            --badge-revisi-text: #FDBA74;
            --badge-terbit-bg: rgba(209, 250, 229, 0.14);
            --badge-terbit-text: #6EE7B7;
            --badge-ditolak-bg: rgba(254, 226, 226, 0.14);
            --badge-ditolak-text: #FCA5A5;

            --card-shadow: 0 12px 36px -6px rgba(0, 0, 0, 0.45), 0 4px 16px -2px rgba(0, 0, 0, 0.3);
        }

        /* Forced Light Theme */
        [data-theme="light"] {
            --color-bg: #FAF8F5;
            --color-surface: #FFFFFF;
            --color-surface-subtle: #F5EFE8;
            --color-border: #EAE4DC;
            --color-text: #1C1917;
            --color-text-muted: #78716C;

            --color-accent: #EA580C;
            --color-accent-hover: #C2410C;
            --color-accent-gradient: linear-gradient(135deg, #FB923C 0%, #EA580C 100%);
            --color-accent-soft: #FFF1EB;
            --color-accent-glow: rgba(234, 88, 12, 0.25);

            --dot-menunggu: #D97706;
            --dot-revisi: #EA580C;
            --dot-terbit: #10B981;
            --dot-ditolak: #EF4444;
            --dot-laporan: #F97316;

            --badge-draf-bg: #EAE4DC;
            --badge-draf-text: #44403C;
            --badge-menunggu-bg: #FEF3C7;
            --badge-menunggu-text: #92400E;
            --badge-revisi-bg: #FFEDD5;
            --badge-revisi-text: #9A3412;
            --badge-terbit-bg: #D1FAE5;
            --badge-terbit-text: #065F46;
            --badge-ditolak-bg: #FEE2E2;
            --badge-ditolak-text: #991B1B;

            --card-shadow: 0 10px 30px -5px rgba(234, 88, 12, 0.08), 0 4px 12px -2px rgba(28, 25, 23, 0.04);
        }

        body {
            background-color: var(--color-bg);
            color: var(--color-text);
            font-family: var(--font-body);
            -webkit-font-smoothing: antialiased;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        h1, h2, h3, h4, .font-heading {
            font-family: var(--font-heading);
        }

        .card-modern {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .card-stat {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 18px;
            box-shadow: var(--card-shadow);
            transition: all 0.2s ease;
        }
        .card-stat:hover {
            transform: translateY(-2px);
            border-color: var(--color-accent);
        }

        /* Tombol Beraksen Gradient Mewah */
        .btn-gradient {
            background: var(--color-accent-gradient);
            color: #FFFFFF;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 4px 14px var(--color-accent-glow);
            transition: all 0.2s ease;
        }
        .btn-gradient:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px var(--color-accent-glow);
        }
        .btn-gradient:active {
            transform: translateY(0);
        }

        .btn-outline-subtle {
            background-color: transparent;
            color: var(--color-text);
            border: 1px solid var(--color-border);
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.15s ease;
        }
        .btn-outline-subtle:hover {
            background-color: var(--color-accent-soft);
            border-color: var(--color-accent);
            color: var(--color-accent);
        }

        .btn-outline-danger {
            background-color: transparent;
            color: #EF4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.15s ease;
        }
        .btn-outline-danger:hover {
            background-color: rgba(239, 68, 68, 0.08);
            border-color: #EF4444;
        }

        .status-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 9999px;
            flex-shrink: 0;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 11px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            line-height: 1;
        }

        .status-badge-draf { background-color: var(--badge-draf-bg); color: var(--badge-draf-text); }
        .status-badge-menunggu { background-color: var(--badge-menunggu-bg); color: var(--badge-menunggu-text); }
        .status-badge-revisi { background-color: var(--badge-revisi-bg); color: var(--badge-revisi-text); }
        .status-badge-terbit { background-color: var(--badge-terbit-bg); color: var(--badge-terbit-text); }
        .status-badge-ditolak { background-color: var(--badge-ditolak-bg); color: var(--badge-ditolak-text); }

        input:focus-visible, textarea:focus-visible, button:focus-visible, a:focus-visible {
            outline: 2px solid var(--color-accent);
            outline-offset: 2px;
        }
    </style>
</head>
<body class="min-h-screen">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-30 border-b backdrop-blur-md" style="background-color: rgba(var(--color-surface), 0.85); border-color: var(--color-border);">
        <div class="max-w-6xl mx-auto px-6 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-white text-base shadow-sm" style="background: var(--color-accent-gradient); font-family: var(--font-heading);">
                    AK
                </div>
                <div>
                    <h1 class="font-bold text-base leading-tight" style="color: var(--color-text);">Aksara Kampus</h1>
                    <p class="text-xs leading-tight" style="color: var(--color-text-muted);">
                        Dasbor Redaksi &bull; Portal Sastra & Artikel Kampus
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <!-- Theme Mode Toggle Button -->
                <button id="themeToggleBtn" type="button" class="btn-outline-subtle p-2 text-xs flex items-center gap-1.5" title="Ganti Tema (Terang / Gelap)">
                    <span id="themeToggleIcon" class="w-4 h-4 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </span>
                    <span id="themeToggleText" class="hidden sm:inline text-xs">Tema</span>
                </button>

                <a href="{{ route('admin.portal') }}" target="_blank" class="btn-outline-subtle px-3.5 py-1.5 text-xs flex items-center gap-1.5" title="Buka Portal Aksara Kampus (Login otomatis sebagai Admin)">
                    <span>Lihat Portal</span>
                    <span class="text-[10px]">&nearr;</span>
                </a>

                <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn-outline-danger px-3.5 py-1.5 text-xs">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>

    @php
        $totalTulisan  = \App\Models\Tulisan::count();
        $terbit        = \App\Models\Tulisan::where('status', 'terbit')->count();
        $menunggu      = \App\Models\Tulisan::where('status', 'menunggu')->count();
        $perluRevisi   = \App\Models\Tulisan::where('status', 'perlu_revisi')->count();
        $ditolak       = \App\Models\Tulisan::where('status', 'ditolak')->count();
        $laporan       = \App\Models\LaporanTulisan::where('status', 'menunggu')->count();
        $totalBerita   = \App\Models\Berita::count();

        $antrean       = \App\Models\Tulisan::with(['user', 'kategori'])
                            ->where('status', 'menunggu')
                            ->latest()
                            ->take(10)
                            ->get();

        $semua         = \App\Models\Tulisan::with(['user', 'kategori'])
                            ->whereNotIn('status', ['menunggu'])
                            ->latest()
                            ->take(10)
                            ->get();

        $beritaList    = \App\Models\Berita::with('penulis')
                            ->latest()
                            ->take(10)
                            ->get();
    @endphp

    <main class="max-w-6xl mx-auto p-6 space-y-6">

        @if(session('success'))
            <div class="p-4 rounded-2xl text-xs font-semibold flex items-center justify-between" style="background-color: var(--badge-terbit-bg); color: var(--badge-terbit-text); border: 1px solid var(--color-border);" role="status">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-xs opacity-70 hover:opacity-100 font-bold ml-2">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl text-xs font-semibold flex items-center justify-between" style="background-color: var(--badge-ditolak-bg); color: var(--badge-ditolak-text); border: 1px solid var(--color-border);" role="alert">
                <span>{{ session('error') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-xs opacity-70 hover:opacity-100 font-bold ml-2">&times;</button>
            </div>
        @endif

        <!-- Welcome Banner Card (Gaya Modern uTask) -->
        <div class="card-modern p-6 sm:p-7 relative overflow-hidden" style="background: linear-gradient(135deg, var(--color-surface) 0%, var(--color-surface-subtle) 100%);">
            <div class="absolute -right-8 -top-8 w-44 h-44 rounded-full opacity-30 pointer-events-none" style="background: var(--color-accent-gradient); filter: blur(40px);"></div>
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1.5">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold" style="background-color: var(--color-accent-soft); color: var(--color-accent);">
                        <span class="w-2 h-2 rounded-full animate-pulse" style="background-color: var(--color-accent);"></span>
                        Panel Dewan Redaksi &bull; Sesi Aktif
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold tracking-tight" style="color: var(--color-text);">
                        Selamat Datang, {{ auth()->user()->name }}
                    </h2>
                    <p class="text-xs sm:text-sm max-w-xl leading-relaxed" style="color: var(--color-text-muted);">
                        Kelola antrean naskah karya mahasiswa, publikasikan warta kegiatan sastra, dan pantau kurasi artikel kampus hari ini.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0 pt-2 sm:pt-0">
                    <div class="px-4 py-3 rounded-2xl border text-right" style="background-color: var(--color-surface); border-color: var(--color-border);">
                        <span class="text-[11px] block uppercase tracking-wider font-semibold" style="color: var(--color-text-muted);">Antrean Tertunda</span>
                        <span class="text-lg font-bold" style="color: var(--color-accent); font-family: var(--font-heading);">{{ $menunggu }} Naskah</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Statistik (Clean Neutral Numbers with Status Dots) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
            <!-- 1. Total Tulisan -->
            <div class="card-stat p-4">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium uppercase tracking-wider" style="color: var(--color-text-muted);">Total Karya</span>
                </div>
                <p class="text-2xl lg:text-3xl font-extrabold mt-2.5" style="color: var(--color-text); font-family: var(--font-heading);">{{ $totalTulisan }}</p>
            </div>

            <!-- 2. Menunggu -->
            <div class="card-stat p-4">
                <div class="flex items-center gap-2">
                    <span class="status-dot" style="background-color: var(--dot-menunggu);"></span>
                    <span class="text-xs font-medium uppercase tracking-wider" style="color: var(--color-text-muted);">Menunggu</span>
                </div>
                <p class="text-2xl lg:text-3xl font-extrabold mt-2.5" style="color: var(--color-text); font-family: var(--font-heading);">{{ $menunggu }}</p>
            </div>

            <!-- 3. Perlu Revisi -->
            <div class="card-stat p-4">
                <div class="flex items-center gap-2">
                    <span class="status-dot" style="background-color: var(--dot-revisi);"></span>
                    <span class="text-xs font-medium uppercase tracking-wider" style="color: var(--color-text-muted);">Revisi</span>
                </div>
                <p class="text-2xl lg:text-3xl font-extrabold mt-2.5" style="color: var(--color-text); font-family: var(--font-heading);">{{ $perluRevisi }}</p>
            </div>

            <!-- 4. Terbit -->
            <div class="card-stat p-4">
                <div class="flex items-center gap-2">
                    <span class="status-dot" style="background-color: var(--dot-terbit);"></span>
                    <span class="text-xs font-medium uppercase tracking-wider" style="color: var(--color-text-muted);">Terbit</span>
                </div>
                <p class="text-2xl lg:text-3xl font-extrabold mt-2.5" style="color: var(--color-text); font-family: var(--font-heading);">{{ $terbit }}</p>
            </div>

            <!-- 5. Ditolak -->
            <div class="card-stat p-4">
                <div class="flex items-center gap-2">
                    <span class="status-dot" style="background-color: var(--dot-ditolak);"></span>
                    <span class="text-xs font-medium uppercase tracking-wider" style="color: var(--color-text-muted);">Ditolak</span>
                </div>
                <p class="text-2xl lg:text-3xl font-extrabold mt-2.5" style="color: var(--color-text); font-family: var(--font-heading);">{{ $ditolak }}</p>
            </div>

            <!-- 6. Dilaporkan -->
            <div class="card-stat p-4">
                <div class="flex items-center gap-2">
                    <span class="status-dot" style="background-color: var(--dot-laporan);"></span>
                    <span class="text-xs font-medium uppercase tracking-wider" style="color: var(--color-text-muted);">Laporan</span>
                </div>
                <p class="text-2xl lg:text-3xl font-extrabold mt-2.5" style="color: var(--color-text); font-family: var(--font-heading);">{{ $laporan }}</p>
            </div>
        </div>

        <!-- Antrean Menunggu Persetujuan -->
        <div class="card-modern p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3.5 border-b" style="border-color: var(--color-border);">
                <div>
                    <h2 class="text-lg font-bold" style="color: var(--color-text);">Antrean Menunggu Persetujuan</h2>
                    <p class="text-xs mt-0.5" style="color: var(--color-text-muted);">Kurasi karya naskah mahasiswa yang diajukan untuk publikasi portal.</p>
                </div>
                <span class="text-xs px-3 py-1 rounded-full font-semibold self-start sm:self-auto" style="background-color: var(--color-accent-soft); color: var(--color-accent);">
                    {{ $menunggu }} karya dalam antrean
                </span>
            </div>

            @if($antrean->isEmpty())
                <div class="text-center py-10 space-y-2">
                    <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center opacity-40" style="background-color: var(--color-accent-soft); color: var(--color-accent);">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-xs font-medium" style="color: var(--color-text-muted);">Semua naskah telah ditelaah. Tidak ada antrean tertunda saat ini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase border-b" style="color: var(--color-text-muted); border-color: var(--color-border);">
                            <tr>
                                <th class="py-3 px-4 font-semibold">Judul Naskah</th>
                                <th class="py-3 px-4 font-semibold">Penulis</th>
                                <th class="py-3 px-4 font-semibold">Jenis</th>
                                <th class="py-3 px-4 font-semibold">Rubrik</th>
                                <th class="py-3 px-4 font-semibold">Status</th>
                                <th class="py-3 px-4 font-semibold">Aksi Redaksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color: var(--color-border);">
                            @foreach($antrean as $t)
                            <tr class="transition-colors hover:bg-black/5 dark:hover:bg-white/5">
                                <td class="py-3.5 px-4 font-semibold max-w-xs truncate" style="color: var(--color-text);">{{ $t->judul }}</td>
                                <td class="py-3.5 px-4 text-xs" style="color: var(--color-text-muted);">
                                    <span class="font-medium block" style="color: var(--color-text);">{{ $t->user?->name ?? '—' }}</span>
                                    <span>{{ $t->user?->email ?? '' }}</span>
                                </td>
                                <td class="py-3.5 px-4 capitalize text-xs" style="color: var(--color-text);">{{ $t->jenis->value ?? $t->jenis }}</td>
                                <td class="py-3.5 px-4 text-xs" style="color: var(--color-text-muted);">{{ $t->kategori?->nama ?? '—' }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="status-badge status-badge-menunggu">
                                        <span class="status-dot" style="background-color: var(--dot-menunggu);"></span>
                                        Menunggu
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('admin.tulisan.show', $t->id) }}" class="btn-outline-subtle px-3 py-1.5 text-xs">
                                            Baca
                                        </a>
                                        <!-- Tombol Utama Beraksen: Setujui -->
                                        <form method="POST" action="{{ url('/admin/tulisan/'.$t->id.'/setujui') }}">
                                            @csrf
                                            <button type="submit" class="btn-gradient px-3.5 py-1.5 text-xs">
                                                Setujui
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ url('/admin/tulisan/'.$t->id.'/revisi') }}" onsubmit="return promptCatatan(this)">
                                            @csrf
                                            <input type="hidden" name="catatan_editor" class="catatan-input" value="">
                                            <button type="submit" class="btn-outline-subtle px-3 py-1.5 text-xs">
                                                Revisi
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ url('/admin/tulisan/'.$t->id.'/tolak') }}" onsubmit="return promptCatatan(this)">
                                            @csrf
                                            <input type="hidden" name="catatan_editor" class="catatan-input" value="">
                                            <button type="submit" class="btn-outline-danger px-3 py-1.5 text-xs">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Panel Kelola Blog Iklan & Berita Terbaru (Khusus Administrator) -->
        <div class="card-modern p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3.5 border-b" style="border-color: var(--color-border);">
                <div>
                    <h2 class="text-lg font-bold" style="color: var(--color-text);">Kelola Berita dan Pengumuman Kampus</h2>
                    <p class="text-xs mt-0.5" style="color: var(--color-text-muted);">Publikasikan warta kegiatan, info sayembara sastra, dan promosi literasi kampus.</p>
                </div>
                <span class="text-xs px-3 py-1 rounded-full font-semibold self-start sm:self-auto" style="background-color: var(--color-accent-soft); color: var(--color-accent);">
                    {{ $totalBerita }} Berita Aktif
                </span>
            </div>

            <!-- Formulir Buat Berita Baru -->
            <form action="{{ url('/admin/berita') }}" method="POST" enctype="multipart/form-data" class="p-5 rounded-2xl space-y-4" style="background-color: var(--color-surface-subtle); border: 1px solid var(--color-border);">
                @csrf
                <h3 class="text-sm font-bold" style="color: var(--color-text);">Buat Berita atau Pengumuman Baru</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase mb-1.5" style="color: var(--color-text-muted);">Judul Berita atau Iklan</label>
                        <input 
                            type="text" 
                            name="judul" 
                            placeholder="Contoh: Sayembara Cipta Puisi Nasional 2026"
                            class="w-full px-3.5 py-2.5 rounded-xl text-sm border"
                            style="background-color: var(--color-surface); border-color: var(--color-border); color: var(--color-text);"
                            required
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase mb-1.5" style="color: var(--color-text-muted);">Ringkasan Singkat (Opsional)</label>
                        <input 
                            type="text" 
                            name="ringkasan" 
                            placeholder="Penjelasan ringkas untuk tampilan kartu..."
                            class="w-full px-3.5 py-2.5 rounded-xl text-sm border"
                            style="background-color: var(--color-surface); border-color: var(--color-border); color: var(--color-text);"
                        />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase mb-1.5" style="color: var(--color-text-muted);">Isi Lengkap Berita atau Iklan</label>
                    <textarea 
                        name="isi" 
                        rows="3"
                        placeholder="Tuliskan isi pengumuman secara detail di sini..."
                        class="w-full px-3.5 py-2.5 rounded-xl text-sm border"
                        style="background-color: var(--color-surface); border-color: var(--color-border); color: var(--color-text); font-family: var(--font-body);"
                        required
                    ></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase mb-1.5" style="color: var(--color-text-muted);">Foto / Gambar Acara (Opsional, maks. 2MB)</label>
                    <input 
                        type="file" 
                        name="foto"
                        accept="image/*"
                        class="w-full text-sm rounded-xl border px-3 py-2"
                        style="background-color: var(--color-surface); border-color: var(--color-border); color: var(--color-text);"
                    />
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-gradient px-5 py-2.5 text-xs">
                        Terbitkan Berita
                    </button>
                </div>
            </form>

            <!-- Tabel Daftar Berita Aktif -->
            <div class="space-y-3">
                <h3 class="text-sm font-bold" style="color: var(--color-text);">Daftar Berita Aktif</h3>

                @if($beritaList->isEmpty())
                    <p class="text-xs py-4 text-center" style="color: var(--color-text-muted);">Belum ada berita atau iklan yang diterbitkan.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs uppercase border-b" style="color: var(--color-text-muted); border-color: var(--color-border);">
                                <tr>
                                    <th class="py-3 px-4 font-semibold">Foto</th>
                                    <th class="py-3 px-4 font-semibold">Judul Pengumuman</th>
                                    <th class="py-3 px-4 font-semibold">Penulis</th>
                                    <th class="py-3 px-4 font-semibold">Tanggal Tayang</th>
                                    <th class="py-3 px-4 font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" style="border-color: var(--color-border);">
                                @foreach($beritaList as $b)
                                <tr class="transition-colors hover:bg-black/5 dark:hover:bg-white/5">
                                    <td class="py-3 px-4">
                                        @if($b->foto)
                                            <img src="{{ asset('storage/' . $b->foto) }}" alt="Foto" class="w-14 h-10 object-cover rounded-lg" />
                                        @else
                                            <div class="w-14 h-10 rounded-lg flex items-center justify-center text-lg" style="background-color: var(--color-surface-subtle);">📢</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold max-w-sm" style="color: var(--color-text);">
                                        <p class="truncate">{{ $b->judul }}</p>
                                        <p class="text-xs line-clamp-1 font-normal" style="color: var(--color-text-muted);">{{ $b->ringkasan }}</p>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs" style="color: var(--color-text-muted);">{{ $b->penulis?->name ?? 'Admin Redaksi' }}</td>
                                    <td class="py-3.5 px-4 text-xs" style="color: var(--color-text-muted);">{{ $b->diterbitkan_pada?->format('d M Y') ?? $b->created_at->format('d M Y') }}</td>
                                    <td class="py-3.5 px-4">
                                        <form method="POST" action="{{ url('/admin/berita/'.$b->id) }}" onsubmit="return confirm('Hapus berita atau iklan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-outline-danger px-3 py-1.5 text-xs">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Semua Tulisan Terbaru -->
        <div class="card-modern p-6 space-y-4">
            <div class="flex items-center justify-between pb-3.5 border-b" style="border-color: var(--color-border);">
                <div>
                    <h2 class="text-lg font-bold" style="color: var(--color-text);">Semua Tulisan</h2>
                    <p class="text-xs mt-0.5" style="color: var(--color-text-muted);">Katalog seluruh naskah yang terdaftar dalam sistem Aksara Kampus.</p>
                </div>
            </div>

            @if($semua->isEmpty())
                <p class="text-xs py-8 text-center" style="color: var(--color-text-muted);">Belum ada tulisan.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase border-b" style="color: var(--color-text-muted); border-color: var(--color-border);">
                            <tr>
                                <th class="py-3 px-4 font-semibold">Judul</th>
                                <th class="py-3 px-4 font-semibold">Penulis</th>
                                <th class="py-3 px-4 font-semibold">Jenis</th>
                                <th class="py-3 px-4 font-semibold">Status</th>
                                <th class="py-3 px-4 font-semibold">Tanggal</th>
                                <th class="py-3 px-4 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color: var(--color-border);">
                            @foreach($semua as $t)
                            @php
                                $st = $t->status instanceof \App\Enums\StatusTulisan ? $t->status->value : $t->status;
                                $stLabel = $t->status instanceof \App\Enums\StatusTulisan ? $t->status->label() : $st;
                                
                                $badgeClass = match($st) {
                                    'terbit' => 'status-badge-terbit',
                                    'menunggu' => 'status-badge-menunggu',
                                    'perlu_revisi' => 'status-badge-revisi',
                                    'ditolak' => 'status-badge-ditolak',
                                    default => 'status-badge-draf',
                                };

                                $dotColor = match($st) {
                                    'terbit' => 'var(--dot-terbit)',
                                    'menunggu' => 'var(--dot-menunggu)',
                                    'perlu_revisi' => 'var(--dot-revisi)',
                                    'ditolak' => 'var(--dot-ditolak)',
                                    default => 'var(--badge-draf-text)',
                                };
                            @endphp
                            <tr class="transition-colors hover:bg-black/5 dark:hover:bg-white/5">
                                <td class="py-3.5 px-4 font-semibold max-w-xs truncate" style="color: var(--color-text);">{{ $t->judul }}</td>
                                <td class="py-3.5 px-4 text-xs" style="color: var(--color-text-muted);">{{ $t->user?->name ?? '—' }}</td>
                                <td class="py-3.5 px-4 capitalize text-xs" style="color: var(--color-text);">{{ $t->jenis->value ?? $t->jenis }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="status-badge {{ $badgeClass }}">
                                        <span class="status-dot" style="background-color: {{ $dotColor }};"></span>
                                        {{ $stLabel }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-xs" style="color: var(--color-text-muted);">{{ $t->updated_at?->format('d M Y') }}</td>
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('admin.tulisan.show', $t->id) }}" class="btn-outline-subtle px-3 py-1.5 text-xs">
                                            Baca
                                        </a>
                                        <form method="POST" action="{{ url('/admin/tulisan/'.$t->id) }}" onsubmit="return confirm('Hapus permanen tulisan ini?\nTindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-outline-danger px-3 py-1.5 text-xs">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </main>

    <!-- Footer Dasbor Admin -->
    <footer class="mt-16 border-t py-8 text-xs" style="border-color: var(--color-border); color: var(--color-text-muted); background-color: var(--color-surface);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-white text-xs shadow-sm" style="background: var(--color-accent-gradient);">
                    AK
                </div>
                <div>
                    <span class="font-bold text-sm" style="color: var(--color-text);">Aksara Kampus</span>
                    <span class="block text-[11px] opacity-75">Panel Kontrol Redaksi & Editor Sastra Digital</span>
                </div>
            </div>
            <div class="text-center sm:text-right">
                <p class="font-medium" style="color: var(--color-text);">
                    &copy; 2026 <span class="font-bold" style="color: var(--color-accent);">Windar Shineta</span>. Seluruh Hak Cipta Dilindungi.
                </p>
                <p class="text-[11px] mt-0.5 opacity-80">Platform Literasi & Publikasi Sastra Mahasiswa Kampus</p>
            </div>
        </div>
    </footer>

    <script>
    function promptCatatan(form) {
        const catatan = prompt('Tulis catatan untuk penulis (wajib):');
        if (!catatan || catatan.trim() === '') {
            alert('Catatan wajib diisi!');
            return false;
        }
        form.querySelector('.catatan-input').value = catatan.trim();
        return true;
    }

    // Toggle Tema Manual & Sinkronisasi Sistem
    (function initTheme() {
        const root = document.documentElement;
        const btn = document.getElementById('themeToggleBtn');
        const text = document.getElementById('themeToggleText');
        const icon = document.getElementById('themeToggleIcon');

        const savedTheme = localStorage.getItem('ak_admin_theme');
        if (savedTheme === 'dark' || savedTheme === 'light') {
            root.setAttribute('data-theme', savedTheme);
        }

        function updateToggleDisplay() {
            const isDark = root.getAttribute('data-theme') === 'dark' || 
                (!root.getAttribute('data-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
            
            if (isDark) {
                text.textContent = 'Mode Gelap';
                icon.innerHTML = `<svg class="w-4 h-4 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>`;
            } else {
                text.textContent = 'Mode Terang';
                icon.innerHTML = `<svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>`;
            }
        }

        btn.addEventListener('click', () => {
            const currentIsDark = root.getAttribute('data-theme') === 'dark' || 
                (!root.getAttribute('data-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
            
            const nextTheme = currentIsDark ? 'light' : 'dark';
            root.setAttribute('data-theme', nextTheme);
            localStorage.setItem('ak_admin_theme', nextTheme);
            updateToggleDisplay();
        });

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (!localStorage.getItem('ak_admin_theme')) {
                updateToggleDisplay();
            }
        });

        updateToggleDisplay();
    })();
    </script>

</body>
</html>
