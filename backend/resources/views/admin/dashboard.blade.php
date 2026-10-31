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
    @endphp

    <main class="max-w-6xl mx-auto p-6 space-y-6">

        <!-- Statistik -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <div class="col-span-2 md:col-span-1 p-5 rounded-2xl bg-[#1a1514] border border-[#2c2523]">
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
            <div class="p-5 rounded-2xl bg-[#1a1514] border border-red-500/20">
                <p class="text-xs uppercase text-stone-500 font-semibold">Ditolak</p>
                <p class="text-3xl font-bold text-red-400 mt-1">{{ $ditolak }}</p>
            </div>
            <div class="p-5 rounded-2xl bg-[#1a1514] border border-purple-500/20">
                <p class="text-xs uppercase text-stone-500 font-semibold">Laporan</p>
                <p class="text-3xl font-bold text-purple-400 mt-1">{{ $laporan }}</p>
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
