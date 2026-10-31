<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Baca: {{ Str::limit($tulisan->judul, 50) }} — Admin Aksara Kampus</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .prose-content {
            font-family: 'Lora', Georgia, serif;
            line-height: 1.9;
            white-space: pre-wrap;
        }
    </style>
</head>
<body class="bg-[#120e0d] text-[#f6e9d8] min-h-screen font-sans antialiased">

    <!-- Top Bar -->
    <header class="sticky top-0 z-10 border-b border-[#2c2523] bg-[#1a1514]/95 backdrop-blur px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1.5 text-sm text-[#d9b18c] hover:text-white transition">
                ← Kembali ke Dashboard
            </a>
            <span class="text-stone-600">|</span>
            <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-[#E89B5B] to-[#D46743] flex items-center justify-center font-bold text-[#120e0d] text-xs">AK</div>
            <span class="text-sm font-semibold text-white">Admin Redaksi</span>
        </div>

        {{-- Aksi cepat di header --}}
        <div class="flex items-center gap-2">
            @php
                $st = $tulisan->status instanceof \App\Enums\StatusTulisan ? $tulisan->status->value : $tulisan->status;
            @endphp

            @if($st === 'menunggu')
                <form method="POST" action="{{ url('/admin/tulisan/'.$tulisan->id.'/setujui') }}">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-xs font-bold hover:bg-emerald-500/25 transition">
                        ✓ Setujui & Terbitkan
                    </button>
                </form>
                <form method="POST" action="{{ url('/admin/tulisan/'.$tulisan->id.'/revisi') }}" onsubmit="return promptCatatan(this)">
                    @csrf
                    <input type="hidden" name="catatan_editor" class="catatan-input" value="">
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-sky-500/15 border border-sky-500/30 text-sky-400 text-xs font-bold hover:bg-sky-500/25 transition">
                        ↩ Minta Revisi
                    </button>
                </form>
                <form method="POST" action="{{ url('/admin/tulisan/'.$tulisan->id.'/tolak') }}" onsubmit="return promptCatatan(this)">
                    @csrf
                    <input type="hidden" name="catatan_editor" class="catatan-input" value="">
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-500/15 border border-red-500/30 text-red-400 text-xs font-bold hover:bg-red-500/25 transition">
                        ✗ Tolak
                    </button>
                </form>
                <span class="text-stone-600 mx-1">|</span>
            @endif

            <form method="POST" action="{{ url('/admin/tulisan/'.$tulisan->id) }}" onsubmit="return confirmDelete(this)" id="form-hapus">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-900/30 border border-red-700/40 text-red-400 text-xs font-bold hover:bg-red-900/50 transition">
                    🗑 Hapus Konten
                </button>
            </form>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-6 py-8 space-y-6">

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">⚠️ {{ session('error') }}</div>
        @endif

        {{-- Meta Info Tulisan --}}
        <div class="p-5 rounded-2xl bg-[#1a1514] border border-[#2c2523] space-y-4">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-white leading-tight">{{ $tulisan->judul }}</h1>
                    <p class="text-sm text-[#d9b18c] mt-1">
                        oleh <span class="font-semibold text-white">{{ $tulisan->user?->name ?? '—' }}</span>
                        <span class="text-stone-500">({{ $tulisan->user?->email ?? '' }})</span>
                    </p>
                </div>

                @php
                    $statusColors = [
                        'terbit'      => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30',
                        'draf'        => 'bg-stone-500/15 text-stone-400 border-stone-500/30',
                        'perlu_revisi'=> 'bg-sky-500/15 text-sky-400 border-sky-500/30',
                        'ditolak'     => 'bg-red-500/15 text-red-400 border-red-500/30',
                        'menunggu'    => 'bg-[#E89B5B]/15 text-[#E89B5B] border-[#E89B5B]/30',
                    ];
                    $stLabel = $tulisan->status instanceof \App\Enums\StatusTulisan ? $tulisan->status->label() : $st;
                    $stColor = $statusColors[$st] ?? 'bg-stone-500/15 text-stone-400 border-stone-500/30';
                @endphp
                <span class="shrink-0 px-3 py-1.5 rounded-full text-sm font-bold border {{ $stColor }}">
                    {{ $stLabel }}
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div class="p-3 rounded-xl bg-[#221c1a] border border-[#2c2523]">
                    <p class="text-stone-500 uppercase font-semibold mb-1">Jenis</p>
                    <p class="text-white capitalize font-semibold">{{ $tulisan->jenis instanceof \App\Enums\JenisTulisan ? $tulisan->jenis->label() : $tulisan->jenis }}</p>
                </div>
                <div class="p-3 rounded-xl bg-[#221c1a] border border-[#2c2523]">
                    <p class="text-stone-500 uppercase font-semibold mb-1">Kategori</p>
                    <p class="text-white font-semibold">{{ $tulisan->kategori?->nama ?? '—' }}</p>
                </div>
                <div class="p-3 rounded-xl bg-[#221c1a] border border-[#2c2523]">
                    <p class="text-stone-500 uppercase font-semibold mb-1">Diajukan</p>
                    <p class="text-white font-semibold">{{ $tulisan->updated_at?->format('d M Y, H:i') }}</p>
                </div>
                <div class="p-3 rounded-xl bg-[#221c1a] border border-[#2c2523]">
                    <p class="text-stone-500 uppercase font-semibold mb-1">Diterbitkan</p>
                    <p class="text-white font-semibold">{{ $tulisan->diterbitkan_pada?->format('d M Y') ?? '—' }}</p>
                </div>
            </div>

            @if($tulisan->ringkasan)
            <div class="p-3 rounded-xl bg-[#2a1f1c] border border-[#E89B5B]/20">
                <p class="text-xs text-[#d9b18c] uppercase font-semibold mb-1">Ringkasan</p>
                <p class="text-sm text-[#f6e9d8]">{{ $tulisan->ringkasan }}</p>
            </div>
            @endif

            @if($tulisan->catatan_editor)
            <div class="p-3 rounded-xl bg-sky-950/40 border border-sky-500/20">
                <p class="text-xs text-sky-400 uppercase font-semibold mb-1">📝 Catatan Editor Sebelumnya</p>
                <p class="text-sm text-sky-200">{{ $tulisan->catatan_editor }}</p>
            </div>
            @endif
        </div>

        {{-- Isi Konten --}}
        <div class="p-8 rounded-2xl bg-[#1a1514] border border-[#2c2523]">
            <h2 class="text-xs uppercase font-semibold text-stone-500 mb-6 tracking-widest">ISI TULISAN</h2>
            <div class="prose-content text-[#f0e6d5] text-[17px]">{{ $tulisan->isi }}</div>
        </div>

        {{-- Panel Aksi Bawah (untuk menunggu) --}}
        @if($st === 'menunggu')
        <div class="p-6 rounded-2xl bg-[#1f1b10] border border-[#E89B5B]/30 space-y-4">
            <h2 class="text-base font-bold text-[#E89B5B]">⚖️ Keputusan Redaksi</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                {{-- Setujui --}}
                <form method="POST" action="{{ url('/admin/tulisan/'.$tulisan->id.'/setujui') }}">
                    @csrf
                    <button type="submit" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm transition">
                        ✓ Setujui & Terbitkan
                    </button>
                </form>

                {{-- Revisi --}}
                <form method="POST" action="{{ url('/admin/tulisan/'.$tulisan->id.'/revisi') }}" class="space-y-2">
                    @csrf
                    <input type="text" name="catatan_editor" placeholder="Catatan revisi untuk penulis (wajib)" required
                        class="w-full px-3 py-2 rounded-xl bg-[#221c1a] border border-[#2c2523] text-white placeholder-stone-600 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-sm transition">
                        ↩ Minta Revisi
                    </button>
                </form>

                {{-- Tolak --}}
                <form method="POST" action="{{ url('/admin/tulisan/'.$tulisan->id.'/tolak') }}" class="space-y-2">
                    @csrf
                    <input type="text" name="catatan_editor" placeholder="Alasan penolakan (wajib)" required
                        class="w-full px-3 py-2 rounded-xl bg-[#221c1a] border border-[#2c2523] text-white placeholder-stone-600 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-red-700 hover:bg-red-600 text-white font-bold text-sm transition">
                        ✗ Tolak Tulisan
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Laporan --}}
        @if($laporan->count() > 0)
        <div class="p-6 rounded-2xl bg-red-950/30 border border-red-700/30 space-y-3">
            <h2 class="text-base font-bold text-red-400">🚨 Laporan Masuk ({{ $laporan->count() }})</h2>
            <div class="space-y-2">
                @foreach($laporan as $lap)
                <div class="p-3 rounded-xl bg-[#1a1514] border border-red-700/20 text-sm">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-red-300 font-semibold">{{ $lap->nama_pelapor ?? 'Anonim' }}
                                @if($lap->email_pelapor)
                                    <span class="text-stone-500 font-normal">({{ $lap->email_pelapor }})</span>
                                @endif
                            </p>
                            <p class="text-stone-300 mt-1">{{ $lap->alasan }}</p>
                        </div>
                        <span class="shrink-0 ml-3 text-xs text-stone-500">{{ $lap->created_at->format('d M Y') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Riwayat Status --}}
        @if($tulisan->riwayatStatus->count() > 0)
        <div class="p-6 rounded-2xl bg-[#1a1514] border border-[#2c2523] space-y-3">
            <h2 class="text-base font-bold text-white">🕒 Riwayat Status</h2>
            <div class="space-y-2">
                @foreach($tulisan->riwayatStatus()->latest()->get() as $r)
                @php
                    $rSt = $r->status instanceof \App\Enums\StatusTulisan ? $r->status->value : $r->status;
                    $rLabel = $r->status instanceof \App\Enums\StatusTulisan ? $r->status->label() : $rSt;
                    $rColor = $statusColors[$rSt] ?? 'bg-stone-500/15 text-stone-400 border-stone-500/30';
                @endphp
                <div class="flex items-start gap-3 text-sm">
                    <span class="shrink-0 mt-0.5 px-2 py-0.5 rounded-full text-xs font-semibold border {{ $rColor }}">{{ $rLabel }}</span>
                    <div class="flex-1">
                        @if($r->catatan)
                            <p class="text-stone-300">{{ $r->catatan }}</p>
                        @endif
                        <p class="text-stone-600 text-xs">{{ $r->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Hapus di bawah (zone berbahaya) --}}
        <div class="p-5 rounded-2xl bg-red-950/20 border border-red-700/30">
            <h2 class="text-sm font-bold text-red-400 mb-2">⚠️ Zona Berbahaya</h2>
            <p class="text-xs text-stone-500 mb-3">Menghapus konten ini bersifat permanen dan tidak dapat dibatalkan. Penulis akan menerima notifikasi.</p>
            <form method="POST" action="{{ url('/admin/tulisan/'.$tulisan->id) }}" onsubmit="return confirmDelete(this)">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-xl bg-red-800 hover:bg-red-700 text-white font-bold text-sm transition">
                    🗑 Hapus Konten Ini Secara Permanen
                </button>
            </form>
        </div>

    </div>

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

    function confirmDelete(form) {
        return confirm('⚠️ HAPUS PERMANEN?\n\nKonten ini akan dihapus dan tidak dapat dikembalikan.\nPenulis akan mendapat notifikasi.\n\nLanjutkan?');
    }
    </script>

</body>
</html>

