<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Admin Redaksi - Aksara Kampus</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ochre: { 400: '#E89B5B', 500: '#d8833e' },
                        terracotta: { 500: '#D46743', 600: '#b84e2c' },
                        ink: { 800: '#2c2523', 850: '#221c1a', 900: '#1a1514', 950: '#120e0d' },
                        parchment: { 100: '#fbf4eb', 200: '#f6e9d8', 400: '#edd0b2', 500: '#d9b18c' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#120e0d] text-[#f6e9d8] min-h-screen flex items-center justify-center p-4 antialiased">

    <div class="w-full max-w-sm">
        <div class="bg-[#1a1514] border border-[#2c2523] rounded-2xl p-8 shadow-2xl space-y-6">
            
            <div class="text-center space-y-2">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#E89B5B] to-[#D46743] flex items-center justify-center font-bold text-[#120e0d] text-2xl shadow-lg shadow-[#E89B5B]/20 mx-auto">
                    AK
                </div>
                <h1 class="text-xl font-bold text-white">Login Admin Redaksi</h1>
                <p class="text-xs text-[#d9b18c]">Panel Kontrol Laravel Backend</p>
            </div>

            @if(session('success'))
                <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[#edd0b2] mb-1.5 uppercase tracking-wider">Email Admin</label>
                    <input 
                        type="email" 
                        name="email" 
                        value="{{ old('email', 'admin@kampus.ac.id') }}" 
                        class="w-full px-3.5 py-2.5 rounded-xl bg-[#221c1a] border border-[#2c2523] text-white placeholder-stone-600 text-sm focus:outline-none focus:ring-2 focus:ring-[#E89B5B]" 
                        required
                    />
                    @error('email')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#edd0b2] mb-1.5 uppercase tracking-wider">Kata Sandi</label>
                    <input 
                        type="password" 
                        name="password" 
                        value="password"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-[#221c1a] border border-[#2c2523] text-white placeholder-stone-600 text-sm focus:outline-none focus:ring-2 focus:ring-[#E89B5B]" 
                        required
                    />
                    @error('password')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button 
                    type="submit" 
                    class="w-full py-2.5 rounded-xl bg-gradient-to-r from-[#E89B5B] to-[#D46743] hover:from-[#d8833e] hover:to-[#b84e2c] text-[#120e0d] font-bold text-sm shadow-lg transition"
                >
                    Masuk ke Dashboard Laravel
                </button>
            </form>

            <div class="p-3 rounded-xl bg-[#221c1a] border border-[#2c2523] text-xs text-[#d9b18c] space-y-1">
                <p class="font-semibold text-[#E89B5B]">Akun Default Redaksi:</p>
                <p>Email: <code class="text-white font-mono">admin@kampus.ac.id</code></p>
                <p>Password: <code class="text-white font-mono">password</code></p>
                <div class="pt-2 mt-2 border-t border-[#2c2523] text-center">
                    <p class="text-stone-400">Mau pakai email pribadi / Gmail kamu?</p>
                    <a href="{{ route('admin.register') }}" class="inline-block mt-1 text-[#E89B5B] font-bold hover:underline">
                        + Buat Akun Admin Pribadi di Sini
                    </a>
                </div>
            </div>

        </div>

        <p class="text-center mt-4 text-xs text-stone-500">
            <a href="{{ url('/') }}" class="hover:text-[#E89B5B] transition">← Kembali ke Halaman Utama Backend</a>
        </p>
    </div>

</body>
</html>

