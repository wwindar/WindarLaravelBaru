<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ Str::limit($tulisan->judul, 45) }} - Baca Naskah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Sora:wght@500;600;700;800&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
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

            --card-shadow: 0 10px 30px -5px rgba(186, 104, 200, 0.1), 0 4px 12px -2px rgba(186, 104, 200, 0.05);
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
        }

        .prose-content {
            font-family: 'Lora', Georgia, serif;
            line-height: 1.95;
            white-space: pre-wrap;
        }

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

    <!-- Top Bar -->
    <header class="border-b" style="background-color: var(--color-surface); border-color: var(--color-border);">
        <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="btn-outline-subtle px-3 py-1.5 text-xs">
                    &larr; Kembali ke Dasbor
                </a>
                <span class="text-xs" style="color: var(--color-text-muted);">Aksara Kampus &bull; Telaah Naskah</span>
            </div>

            <div class="flex items-center gap-2">
                @php
                    $st = $tulisan->status instanceof \App\Enums\StatusTulisan ? $tulisan->status->value : $tulisan->status;
                @endphp

                @if($st === 'menunggu')
                    <form method="POST" action="{{ url('/admin/tulisan/'.$tulisan->id.'/setujui') }}">
                        @csrf
                        <button type="submit" class="btn-gradient px-4 py-1.5 text-xs">
                            Setujui
                        </button>
                    </form>
                    <form method="POST" action="{{ url('/admin/tulisan/'.$tulisan->id.'/revisi') }}" onsubmit="return promptCatatan(this)">
                        @csrf
                        <input type="hidden" name="catatan_editor" class="catatan-input" value="">
                        <button type="submit" class="btn-outline-subtle px-3 py-1.5 text-xs">
                            Revisi
                        </button>
                    </form>
                    <form method="POST" action="{{ url('/admin/tulisan/'.$tulisan->id.'/tolak') }}" onsubmit="return promptCatatan(this)">
                        @csrf
                        <input type="hidden" name="catatan_editor" class="catatan-input" value="">
                        <button type="submit" class="btn-outline-danger px-3 py-1.5 text-xs">
                            Tolak
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ url('/admin/tulisan/'.$tulisan->id) }}" onsubmit="return confirm('Hapus permanen tulisan ini?\nTindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-outline-danger px-3 py-1.5 text-xs">
                        Hapus Konten
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-6 py-8 space-y-6">

        @if(session('success'))
            <div class="p-4 rounded-2xl text-xs font-semibold" style="background-color: var(--badge-terbit-bg); color: var(--badge-terbit-text); border: 1px solid var(--color-border);" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl text-xs font-semibold" style="background-color: var(--badge-ditolak-bg); color: var(--badge-ditolak-text); border: 1px solid var(--color-border);" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <!-- Informasi Meta Naskah -->
        <div class="card-modern p-6 sm:p-8 space-y-5">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-bold leading-tight" style="color: var(--color-text);">{{ $tulisan->judul }}</h1>
                    <p class="text-xs sm:text-sm mt-1.5" style="color: var(--color-text-muted);">
                        Oleh <span class="font-semibold" style="color: var(--color-text);">{{ $tulisan->user?->name ?? '—' }}</span>
                        <span>({{ $tulisan->user?->email ?? '' }})</span>
                    </p>
                </div>

                @php
                    $stLabel = $tulisan->status instanceof \App\Enums\StatusTulisan ? $tulisan->status->label() : $st;
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
                <span class="status-badge {{ $badgeClass }}">
                    <span class="status-dot" style="background-color: {{ $dotColor }};"></span>
                    {{ $stLabel }}
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs pt-3 border-t" style="border-color: var(--color-border);">
                <div>
                    <span class="block uppercase font-medium" style="color: var(--color-text-muted);">Jenis</span>
                    <span class="font-semibold capitalize mt-0.5 block" style="color: var(--color-text);">{{ $tulisan->jenis instanceof \App\Enums\JenisTulisan ? $tulisan->jenis->label() : $tulisan->jenis }}</span>
                </div>
                <div>
                    <span class="block uppercase font-medium" style="color: var(--color-text-muted);">Kategori</span>
                    <span class="font-semibold mt-0.5 block" style="color: var(--color-text);">{{ $tulisan->kategori?->nama ?? '—' }}</span>
                </div>
                <div>
                    <span class="block uppercase font-medium" style="color: var(--color-text-muted);">Diajukan</span>
                    <span class="font-semibold mt-0.5 block" style="color: var(--color-text);">{{ $tulisan->updated_at?->format('d M Y, H:i') }}</span>
                </div>
                <div>
                    <span class="block uppercase font-medium" style="color: var(--color-text-muted);">Diterbitkan</span>
                    <span class="font-semibold mt-0.5 block" style="color: var(--color-text);">{{ $tulisan->diterbitkan_pada?->format('d M Y') ?? 'Belum terbit' }}</span>
                </div>
            </div>

            @if($tulisan->ringkasan)
            <div class="p-4 rounded-2xl text-xs sm:text-sm" style="background-color: var(--color-surface-subtle); border: 1px solid var(--color-border);">
                <span class="font-semibold block mb-1 text-[11px] uppercase tracking-wider" style="color: var(--color-text-muted);">Ringkasan</span>
                <p style="color: var(--color-text);">{{ $tulisan->ringkasan }}</p>
            </div>
            @endif

            @if($tulisan->catatan_editor)
            <div class="p-4 rounded-2xl text-xs sm:text-sm" style="background-color: var(--badge-revisi-bg); color: var(--badge-revisi-text);">
                <span class="font-semibold block mb-1 text-[11px] uppercase tracking-wider">Catatan Editor Sebelumnya</span>
                <p>{{ $tulisan->catatan_editor }}</p>
            </div>
            @endif
        </div>

        <!-- Isi Naskah Tulisan -->
        <div class="card-modern p-6 sm:p-10 space-y-4">
            <h2 class="text-xs uppercase font-semibold tracking-widest pb-3 border-b" style="color: var(--color-text-muted); border-color: var(--color-border);">
                Isi Naskah Karya
            </h2>
            <div class="prose-content text-base sm:text-lg pt-2" style="color: var(--color-text);">{{ $tulisan->isi }}</div>
        </div>

        <!-- Keputusan Redaksi (Khusus status menunggu) -->
        @if($st === 'menunggu')
        <div class="card-modern p-6 space-y-4">
            <h2 class="text-base font-bold" style="color: var(--color-text);">Keputusan Redaksi</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <form method="POST" action="{{ url('/admin/tulisan/'.$tulisan->id.'/setujui') }}">
                    @csrf
                    <button type="submit" class="btn-gradient w-full py-2.5 text-xs">
                        Setujui dan Terbitkan
                    </button>
                </form>

                <form method="POST" action="{{ url('/admin/tulisan/'.$tulisan->id.'/revisi') }}" class="space-y-2">
                    @csrf
                    <input type="text" name="catatan_editor" placeholder="Catatan revisi untuk penulis (wajib)" required
                        class="w-full px-3 py-2 rounded-xl text-xs border"
                        style="background-color: var(--color-surface-subtle); border-color: var(--color-border); color: var(--color-text);">
                    <button type="submit" class="btn-outline-subtle w-full py-2 text-xs">
                        Minta Revisi
                    </button>
                </form>

                <form method="POST" action="{{ url('/admin/tulisan/'.$tulisan->id.'/tolak') }}" class="space-y-2">
                    @csrf
                    <input type="text" name="catatan_editor" placeholder="Alasan penolakan (wajib)" required
                        class="w-full px-3 py-2 rounded-xl text-xs border"
                        style="background-color: var(--color-surface-subtle); border-color: var(--color-border); color: var(--color-text);">
                    <button type="submit" class="btn-outline-danger w-full py-2 text-xs">
                        Tolak Tulisan
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($laporan->count() > 0)
        <div class="card-modern p-6 space-y-3" style="border-color: rgba(236, 72, 153, 0.4);">
            <div class="flex items-center gap-2">
                <span class="status-dot" style="background-color: var(--dot-laporan);"></span>
                <h2 class="text-base font-bold" style="color: var(--color-text);">Laporan Masuk ({{ $laporan->count() }})</h2>
            </div>
            <div class="space-y-2">
                @foreach($laporan as $lap)
                <div class="p-3.5 rounded-xl text-xs border" style="background-color: var(--color-surface-subtle); border-color: var(--color-border);">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="font-semibold" style="color: var(--color-text);">{{ $lap->nama_pelapor ?? 'Anonim' }}</span>
                            @if($lap->email_pelapor)
                                <span style="color: var(--color-text-muted);">({{ $lap->email_pelapor }})</span>
                            @endif
                            <p class="mt-1" style="color: var(--color-text);">{{ $lap->alasan }}</p>
                        </div>
                        <span class="text-xs" style="color: var(--color-text-muted);">{{ $lap->created_at->format('d M Y') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </main>

    <!-- Footer Baca Naskah Admin -->
    <footer class="mt-16 border-t py-8 text-xs" style="border-color: var(--color-border); color: var(--color-text-muted); background-color: var(--color-surface);">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-white text-xs shadow-sm" style="background: var(--color-accent-gradient);">
                    AK
                </div>
                <div>
                    <span class="font-bold text-sm" style="color: var(--color-text);">Aksara Kampus</span>
                    <span class="block text-[11px] opacity-75">Panel Telaah Naskah Redaksi</span>
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

    // Mengikuti tema yang tersimpan di Dasbor
    (function syncTheme() {
        const savedTheme = localStorage.getItem('ak_admin_theme');
        if (savedTheme === 'dark' || savedTheme === 'light') {
            document.documentElement.setAttribute('data-theme', savedTheme);
        }
    })();
    </script>

</body>
</html>
