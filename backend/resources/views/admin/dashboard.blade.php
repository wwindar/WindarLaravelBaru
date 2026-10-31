<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Redaksi - Laravel Backend</title>
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
                Buka Frontend Web ↗
            </a>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-semibold hover:bg-red-500/20">
                    Keluar
                </button>
            </form>
        </div>
    </header>

    <main class="max-w-6xl mx-auto p-6 space-y-6">
        <!-- Status Bar -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-5 rounded-2xl bg-[#1a1514] border border-[#2c2523]">
                <p class="text-xs uppercase text-stone-500 font-semibold">Total Artikel</p>
                <p class="text-3xl font-bold text-white mt-1">{{ \App\Models\Artikel::count() }}</p>
            </div>
            <div class="p-5 rounded-2xl bg-[#1a1514] border border-[#2c2523]">
                <p class="text-xs uppercase text-stone-500 font-semibold">Karya Terbit</p>
                <p class="text-3xl font-bold text-emerald-400 mt-1">{{ \App\Models\Karya::where('status', 'terbit')->count() }}</p>
            </div>
            <div class="p-5 rounded-2xl bg-[#1a1514] border border-[#2c2523]">
                <p class="text-xs uppercase text-stone-500 font-semibold">Karya Perlu Kurasi</p>
                <p class="text-3xl font-bold text-[#E89B5B] mt-1">{{ \App\Models\Karya::whereIn('status', ['dikirim', 'ditinjau'])->count() }}</p>
            </div>
            <div class="p-5 rounded-2xl bg-[#1a1514] border border-[#2c2523]">
                <p class="text-xs uppercase text-stone-500 font-semibold">Agenda</p>
                <p class="text-3xl font-bold text-sky-400 mt-1">{{ \App\Models\Agenda::count() }}</p>
            </div>
        </div>

        <!-- Panel Karya Menunggu Kurasi -->
        <div class="p-6 rounded-2xl bg-[#1a1514] border border-[#2c2523] space-y-4">
            <h2 class="text-lg font-bold text-white">Daftar Kiriman Naskah Mahasiswa</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase text-stone-500 border-b border-[#2c2523]">
                        <tr>
                            <th class="py-3 px-4">No. Registrasi</th>
                            <th class="py-3 px-4">Judul Karya</th>
                            <th class="py-3 px-4">Penulis</th>
                            <th class="py-3 px-4">Jenis</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2c2523]">
                        @foreach(\App\Models\Karya::latest()->take(6)->get() as $k)
                        <tr class="hover:bg-[#221c1a]">
                            <td class="py-3 px-4 font-mono text-xs text-[#E89B5B]">{{ $k->nomor_kiriman }}</td>
                            <td class="py-3 px-4 font-semibold text-white">{{ $k->judul }}</td>
                            <td class="py-3 px-4 text-xs text-stone-400">{{ $k->nama_penulis }}</td>
                            <td class="py-3 px-4 capitalize text-xs">{{ $k->jenis }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $k->status->value === 'terbit' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-[#E89B5B]/10 text-[#E89B5B]' }}">
                                    {{ $k->status->label() }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>

