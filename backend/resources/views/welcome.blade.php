<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aksara Kampus - Portal Komunitas Sastra</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-amber-950/20 text-slate-100 min-h-screen font-sans antialiased bg-slate-950 selection:bg-amber-600 selection:text-white">

    <!-- Top Header -->
    <div class="relative overflow-hidden border-b border-amber-900/30 bg-slate-900/80 backdrop-blur-md">
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-96 bg-amber-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-4 relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-600 to-rose-600 flex items-center justify-center shadow-lg shadow-amber-600/20 font-serif font-bold text-white text-xl">
                    AK
                </div>
                <div>
                    <h1 class="font-bold text-lg text-white tracking-tight flex items-center gap-2">
                        Aksara Kampus
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> REST API Aktif
                        </span>
                    </h1>
                    <p class="text-xs text-slate-400">Portal Komunitas Sastra Kampus &bull; Puisi, Cerpen, Esai, & Berita</p>
                </div>
            </div>

            <div class="flex items-center gap-2 text-xs">
                <span class="px-3 py-1.5 rounded-lg bg-slate-800 border border-slate-700/80 text-amber-200 font-mono">
                    Token: redaksi-rahasia-123
                </span>
                <span class="px-3 py-1.5 rounded-lg bg-slate-800 border border-slate-700/80 text-slate-300 font-mono">
                    Port: 8001
                </span>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-10 space-y-10">

        <!-- Hero Notice -->
        <div class="p-6 rounded-2xl bg-gradient-to-br from-amber-950/40 via-slate-900 to-slate-900/90 border border-amber-500/20 shadow-xl relative overflow-hidden">
            <div class="relative z-10 max-w-3xl space-y-3">
                <div class="inline-flex items-center gap-2 text-xs font-semibold text-amber-400 uppercase tracking-wider">
                    <span>📚 Ruang Dialektika & Sastra</span>
                    <span>&bull;</span>
                    <span>Backend Ready</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">
                    Selamat Datang di REST API Aksara Kampus
                </h2>
                <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                    Server backend telah berhasil dimigrasikan untuk domain komunitas sastra. Endpoint publik dan endpoint redaksi siap diakses oleh frontend Anda melalui port <b>8001</b>.
                </p>
            </div>
        </div>

        <!-- Grid Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
            <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800">
                <span class="text-amber-400 font-bold uppercase tracking-wider block mb-1">1. Berita & Blog</span>
                <p class="text-slate-400">10 artikel orisinal seputar sastra, ulasan buku, dan kabar komunitas literasi.</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800">
                <span class="text-emerald-400 font-bold uppercase tracking-wider block mb-1">2. Karya Sastra</span>
                <p class="text-slate-400">Publikasi puisi, cerpen mini, dan esai kritis yang telah melalui proses kurasi.</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800">
                <span class="text-sky-400 font-bold uppercase tracking-wider block mb-1">3. Kirim & Lacak</span>
                <p class="text-slate-400">Penulis dapat mengirim naskah, melacak status, dan merevisi draf naskah.</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800">
                <span class="text-rose-400 font-bold uppercase tracking-wider block mb-1">4. Agenda Sastra</span>
                <p class="text-slate-400">Jadwal sayembara, bedah naskah, panggung puisi terbuka, dan kelas kepenulisan.</p>
            </div>
        </div>

        <!-- Interactive Live Tester -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-800">
                <div>
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        ⚡ Quick Live Tester
                    </h3>
                    <p class="text-xs text-slate-400">Klik tombol di bawah untuk menguji respons JSON realtime dari backend.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button onclick="testApi('/api/kategori')" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium border border-slate-700 transition">
                        Daftar Kategori
                    </button>
                    <button onclick="testApi('/api/artikel')" class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-500 text-white text-xs font-medium transition shadow-sm">
                        Artikel Terbit
                    </button>
                    <button onclick="testApi('/api/karya')" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium border border-slate-700 transition">
                        Karya Terbit
                    </button>
                    <button onclick="testApi('/api/kiriman/AKS-2026-00001?email=sekar.ayu@student.univ.ac.id')" class="px-3 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-xs font-medium transition shadow-sm">
                        Lacak Karya #1
                    </button>
                    <button onclick="testApi('/api/agenda')" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium border border-slate-700 transition">
                        Agenda Kegiatan
                    </button>
                </div>
            </div>

            <!-- Terminal Output Window -->
            <div class="rounded-xl bg-slate-950 border border-slate-800/80 overflow-hidden font-mono text-xs">
                <div class="px-4 py-2 bg-slate-900 border-b border-slate-800/80 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-rose-500/80"></span>
                        <span class="w-3 h-3 rounded-full bg-amber-500/80"></span>
                        <span class="w-3 h-3 rounded-full bg-emerald-500/80"></span>
                        <span class="text-slate-400 ml-2" id="request-label">Menunggu pengujian...</span>
                    </div>
                    <span id="response-status" class="text-slate-500">HTTP Status: -</span>
                </div>
                <pre id="output-box" class="p-4 text-slate-300 max-h-80 overflow-y-auto leading-relaxed">// Klik tombol di atas untuk menampilkan respon API.</pre>
            </div>
        </div>

    </main>

    <footer class="border-t border-slate-800/80 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Aksara Kampus &bull; Komunitas Sastra Kampus &bull; S1 Informatika
    </footer>

    <script>
        async function testApi(url) {
            const outputBox = document.getElementById('output-box');
            const label = document.getElementById('request-label');
            const status = document.getElementById('response-status');

            label.textContent = 'GET ' + url + ' ...';
            status.textContent = 'Memuat...';
            outputBox.textContent = 'Loading...';

            try {
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const json = await res.json();
                status.textContent = 'HTTP ' + res.status + ' ' + (res.ok ? 'OK' : 'Error');
                status.className = res.ok ? 'text-emerald-400 font-bold' : 'text-rose-400 font-bold';
                label.textContent = 'GET ' + url;
                outputBox.textContent = JSON.stringify(json, null, 2);
            } catch (err) {
                status.textContent = 'Gagal';
                status.className = 'text-rose-400 font-bold';
                outputBox.textContent = 'Error: ' + err.message;
            }
        }
    </script>
</body>
</html>
