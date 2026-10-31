<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Redaksi - Aksara Kampus</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#120e0d] text-[#f6e9d8] min-h-screen font-sans antialiased">

    <!-- Top Bar -->
    <header class="border-b border-[#2c2523] bg-[#1a1514] px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#E89B5B] to-[#D46743] flex items-center justify-center font-bold text-[#120e0d]">
                AK
            </div>
            <div>
                <h1 class="font-bold text-white text-base">Aksara Kampus — Backend Redaksi</h1>
                <p class="text-[11px] text-[#d9b18c]">Admin: {{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="http://localhost:3000" target="_blank" class="px-3 py-1.5 rounded-lg border border-[#2c2523] text-xs text-[#edd0b2] hover:bg-[#221c1a]">
                Buka Frontend ↗
            </a>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-semibold hover:bg-red-500/20">
                    Keluar
                </button>
            </form>
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
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm font-semibold">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm font-semibold">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        <!-- Statistik -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <div class="p-5 rounded-2xl bg-[#1a1514] border border-[#2c2523]">
                <p class="text-xs uppercase text-stone-500 font-semibold">Total Tulisan</p>
                <p class="text-3xl font-bold text-white mt-1">{{ $totalTulisan }}</p>
            </div>
            <div class="p-5 rounded-2xl bg-[#1a1514] border border-emerald-500/20">
                <p class="text-xs uppercase text-stone-500 font-semibold">Terbit</p>
                <p class="text-3xl font-bold text-emerald-400 mt-1">{{ $terbit }}</p>
            </div>
            <div class="p-5 rounded-2xl bg-[#1a1514] border border-[#E89B5B]/30">
                <p class="text-xs uppercase text-stone-500 font-semibold">Menunggu</p>
                <p class="text-3xl font-bold text-[#E89B5B] mt-1">{{ $menunggu }}</p>
            </div>
            <div class="p-5 rounded-2xl bg-[#1a1514] border border-sky-500/20">
                <p class="text-xs uppercase text-stone-500 font-semibold">Perlu Revisi</p>
                <p class="text-3xl font-bold text-sky-400 mt-1">{{ $perluRevisi }}</p>
            </div>
            <div class="p-5 rounded-2xl bg-[#1a1514] border border-purple-500/20">
                <p class="text-xs uppercase text-stone-500 font-semibold">Laporan</p>
                <p class="text-3xl font-bold text-purple-400 mt-1">{{ $laporan }}</p>
            </div>
            <div class="p-5 rounded-2xl bg-[#1a1514] border border-amber-500/20">
                <p class="text-xs uppercase text-amber-500 font-semibold">Blog & Iklan</p>
                <p class="text-3xl font-bold text-amber-400 mt-1">{{ $totalBerita }}</p>
            </div>
        </div>

        <!-- Antrean Menunggu Persetujuan -->
        <div class="p-6 rounded-2xl bg-[#1a1514] border border-[#E89B5B]/30 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-white">📥 Antrean Menunggu Persetujuan</h2>
                <span class="px-2 py-0.5 rounded-full bg-[#E89B5B]/15 text-[#E89B5B] text-xs font-semibold">{{ $menunggu }} tulisan</span>
            </div>

            @if($antrean->isEmpty())
                <p class="text-stone-500 text-sm py-4 text-center">Tidak ada tulisan yang menunggu persetujuan.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase text-stone-500 border-b border-[#2c2523]">
                            <tr>
                                <th class="py-3 px-4">Judul</th>
                                <th class="py-3 px-4">Penulis</th>
                                <th class="py-3 px-4">Jenis</th>
                                <th class="py-3 px-4">Kategori</th>
                                <th class="py-3 px-4">Diajukan</th>
                                <th class="py-3 px-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2c2523]">
                            @foreach($antrean as $t)
                            <tr class="hover:bg-[#221c1a]">
                                <td class="py-3 px-4 font-semibold text-white max-w-xs truncate">{{ $t->judul }}</td>
                                <td class="py-3 px-4 text-xs text-stone-400">
                                    {{ $t->user?->name ?? '—' }}<br>
                                    <span class="text-stone-600">{{ $t->user?->email ?? '' }}</span>
                                </td>
                                <td class="py-3 px-4 capitalize text-xs text-[#d9b18c]">{{ $t->jenis->value ?? $t->jenis }}</td>
                                <td class="py-3 px-4 text-xs text-stone-400">{{ $t->kategori?->nama ?? '—' }}</td>
                                <td class="py-3 px-4 text-xs text-stone-500">{{ $t->updated_at?->format('d M Y') }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.tulisan.show', $t->id) }}"
                                           class="px-2.5 py-1 rounded-lg bg-[#E89B5B]/10 border border-[#E89B5B]/30 text-[#E89B5B] text-xs font-semibold hover:bg-[#E89B5B]/20">
                                            👁 Baca
                                        </a>
                                        <form method="POST" action="{{ url('/admin/tulisan/'.$t->id.'/setujui') }}">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold hover:bg-emerald-500/20">
                                                ✓ Setujui
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ url('/admin/tulisan/'.$t->id.'/revisi') }}" onsubmit="return promptCatatan(this)">
                                            @csrf
                                            <input type="hidden" name="catatan_editor" class="catatan-input" value="">
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-sky-500/10 border border-sky-500/20 text-sky-400 text-xs font-semibold hover:bg-sky-500/20">
                                                ↩ Revisi
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ url('/admin/tulisan/'.$t->id.'/tolak') }}" onsubmit="return promptCatatan(this)">
                                            @csrf
                                            <input type="hidden" name="catatan_editor" class="catatan-input" value="">
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-semibold hover:bg-red-500/20">
                                                ✗ Tolak
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

        <!-- Panel Kelola Blog Iklan & Berita Terbaru (Khusus Admin / Redaksi) -->
        <div class="p-6 rounded-2xl bg-[#1a1514] border border-amber-500/30 space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-[#2c2523]">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-400 text-xs font-bold mb-1">
                        📢 Akses Khusus Admin
                    </div>
                    <h2 class="text-lg font-bold text-white">Kelola Blog Iklan, Berita & Pengumuman Kampus</h2>
                    <p class="text-xs text-stone-400 mt-0.5">Hanya akun Administrator yang berhak menerbitkan atau menghapus berita, info sayembara, maupun pengumuman resmi.</p>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-amber-500/15 text-amber-400 text-xs font-semibold">{{ $totalBerita }} Berita Aktif</span>
            </div>

            <!-- Formulir Buat Berita / Iklan Baru -->
            <form action="{{ url('/admin/berita') }}" method="POST" class="space-y-4 p-5 rounded-xl bg-[#221c1a] border border-[#2c2523]">
                @csrf
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span>✍️</span> Buat Pengumuman / Iklan / Berita Baru
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#edd0b2] mb-1.5 uppercase">Judul Berita / Iklan</label>
                        <input 
                            type="text" 
                            name="judul" 
                            placeholder="Contoh: Info Lomba Puisi Nasional atau Iklan Seminar..."
                            class="w-full px-3.5 py-2.5 rounded-xl bg-[#1a1514] border border-[#2c2523] text-white placeholder-stone-600 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" 
                            required
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#edd0b2] mb-1.5 uppercase">Ringkasan Singkat (Opsional)</label>
                        <input 
                            type="text" 
                            name="ringkasan" 
                            placeholder="Penjelasan ringkas untuk tampilan kartu berita..."
                            class="w-full px-3.5 py-2.5 rounded-xl bg-[#1a1514] border border-[#2c2523] text-white placeholder-stone-600 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" 
                        />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#edd0b2] mb-1.5 uppercase">Isi Lengkap Berita / Iklan</label>
                    <textarea 
                        name="isi" 
                        rows="4"
                        placeholder="Tuliskan isi pengumuman atau konten promosi secara detail di sini..."
                        class="w-full px-3.5 py-2.5 rounded-xl bg-[#1a1514] border border-[#2c2523] text-white placeholder-stone-600 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 font-sans" 
                        required
                    ></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-stone-950 font-bold text-xs shadow-lg transition">
                        🚀 Terbitkan Berita / Iklan Sekarang
                    </button>
                </div>
            </form>

            <!-- Tabel Daftar Berita Aktif -->
            <div class="space-y-3">
                <h3 class="text-sm font-bold text-white flex items-center justify-between">
                    <span>📰 Daftar Berita / Iklan yang Sedang Tayang</span>
                </h3>

                @if($beritaList->isEmpty())
                    <p class="text-stone-500 text-xs py-4 text-center">Belum ada berita atau iklan yang diterbitkan.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs uppercase text-stone-500 border-b border-[#2c2523]">
                                <tr>
                                    <th class="py-3 px-4">Judul Pengumuman</th>
                                    <th class="py-3 px-4">Penulis (Admin)</th>
                                    <th class="py-3 px-4">Tanggal Tayang</th>
                                    <th class="py-3 px-4">Aksi Admin</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#2c2523]">
                                @foreach($beritaList as $b)
                                <tr class="hover:bg-[#221c1a]">
                                    <td class="py-3 px-4 font-semibold text-white max-w-sm">
                                        <p class="truncate">{{ $b->judul }}</p>
                                        <p class="text-xs text-stone-500 line-clamp-1 font-normal">{{ $b->ringkasan }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-xs text-stone-400">{{ $b->penulis?->name ?? 'Admin Redaksi' }}</td>
                                    <td class="py-3 px-4 text-xs text-stone-500">{{ $b->diterbitkan_pada?->format('d M Y') ?? $b->created_at->format('d M Y') }}</td>
                                    <td class="py-3 px-4">
                                        <form method="POST" action="{{ url('/admin/berita/'.$b->id) }}" onsubmit="return confirm('Hapus berita/iklan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-semibold hover:bg-red-500/20">
                                                🗑 Hapus Berita
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

        <!-- Tulisan Terbaru (selain menunggu) -->
        <div class="p-6 rounded-2xl bg-[#1a1514] border border-[#2c2523] space-y-4">
            <h2 class="text-lg font-bold text-white">📋 Semua Tulisan Terbaru</h2>

            @if($semua->isEmpty())
                <p class="text-stone-500 text-sm py-4 text-center">Belum ada tulisan.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase text-stone-500 border-b border-[#2c2523]">
                            <tr>
                                <th class="py-3 px-4">Judul</th>
                                <th class="py-3 px-4">Penulis</th>
                                <th class="py-3 px-4">Jenis</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2c2523]">
                            @foreach($semua as $t)
                            @php
                                $statusColors = [
                                    'terbit'      => 'bg-emerald-500/10 text-emerald-400',
                                    'draf'        => 'bg-stone-500/10 text-stone-400',
                                    'perlu_revisi'=> 'bg-sky-500/10 text-sky-400',
                                    'ditolak'     => 'bg-red-500/10 text-red-400',
                                    'menunggu'    => 'bg-[#E89B5B]/10 text-[#E89B5B]',
                                ];
                                $st = $t->status instanceof \App\Enums\StatusTulisan ? $t->status->value : $t->status;
                                $stLabel = $t->status instanceof \App\Enums\StatusTulisan ? $t->status->label() : $st;
                                $stColor = $statusColors[$st] ?? 'bg-stone-500/10 text-stone-400';
                            @endphp
                            <tr class="hover:bg-[#221c1a]">
                                <td class="py-3 px-4 font-semibold text-white max-w-xs truncate">{{ $t->judul }}</td>
                                <td class="py-3 px-4 text-xs text-stone-400">{{ $t->user?->name ?? '—' }}</td>
                                <td class="py-3 px-4 capitalize text-xs text-[#d9b18c]">{{ $t->jenis->value ?? $t->jenis }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $stColor }}">
                                        {{ $stLabel }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-xs text-stone-500">{{ $t->updated_at?->format('d M Y') }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.tulisan.show', $t->id) }}"
                                           class="px-2.5 py-1 rounded-lg bg-[#E89B5B]/10 border border-[#E89B5B]/30 text-[#E89B5B] text-xs font-semibold hover:bg-[#E89B5B]/20">
                                            👁 Baca
                                        </a>
                                        <form method="POST" action="{{ url('/admin/tulisan/'.$t->id) }}" onsubmit="return confirm('Hapus permanen tulisan ini?\nTidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-semibold hover:bg-red-500/20">
                                                🗑 Hapus
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
    </script>

</body>
</html>
