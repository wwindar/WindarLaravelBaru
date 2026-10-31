<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun Admin Redaksi - Aksara Kampus</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#120e0d] text-[#f6e9d8] min-h-screen flex items-center justify-center p-4 antialiased">

    <div class="w-full max-w-md">
        <div class="bg-[#1a1514] border border-[#2c2523] rounded-2xl p-8 shadow-2xl space-y-6">
            
            <div class="text-center space-y-2">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#E89B5B] to-[#D46743] flex items-center justify-center font-bold text-[#120e0d] text-2xl shadow-lg shadow-[#E89B5B]/20 mx-auto">
                    AK
                </div>
                <h1 class="text-xl font-bold text-white">Buat Akun Admin Pribadi</h1>
                <p class="text-xs text-[#d9b18c]">Daftarkan akun Gmail pribadi kamu menjadi Admin Redaksi</p>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs space-y-2">
                    <p class="font-bold">✅ {{ session('success') }}</p>
                    <a href="{{ route('admin.login') }}" class="inline-block mt-2 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg transition">
                        Klik di sini untuk langsung Login →
                    </a>
                </div>
            @endif

            @if(session('error'))
                <div class="p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('admin.register.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[#edd0b2] mb-1.5 uppercase tracking-wider">Nama Lengkap Admin</label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ old('name') }}" 
                        placeholder="Nama kamu..."
                        class="w-full px-3.5 py-2.5 rounded-xl bg-[#221c1a] border border-[#2c2523] text-white placeholder-stone-600 text-sm focus:outline-none focus:ring-2 focus:ring-[#E89B5B]" 
                        required
                    />
                    @error('name')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#edd0b2] mb-1.5 uppercase tracking-wider">Username</label>
                    <input 
                        type="text" 
                        name="username" 
                        value="{{ old('username') }}" 
                        placeholder="contoh: windar_admin"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-[#221c1a] border border-[#2c2523] text-white placeholder-stone-600 text-sm focus:outline-none focus:ring-2 focus:ring-[#E89B5B]" 
                    />
                    @error('username')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#edd0b2] mb-1.5 uppercase tracking-wider">Email Pribadi (Bebas: Gmail, dll)</label>
                    <input 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        placeholder="contoh: nama.kamu@gmail.com"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-[#221c1a] border border-[#2c2523] text-white placeholder-stone-600 text-sm focus:outline-none focus:ring-2 focus:ring-[#E89B5B]" 
                        required
                    />
                    @error('email')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#edd0b2] mb-1.5 uppercase tracking-wider">Kata Sandi Baru</label>
                    <input 
                        type="password" 
                        name="password" 
                        placeholder="Minimal 6 karakter..."
                        class="w-full px-3.5 py-2.5 rounded-xl bg-[#221c1a] border border-[#2c2523] text-white placeholder-stone-600 text-sm focus:outline-none focus:ring-2 focus:ring-[#E89B5B]" 
                        minlength="6"
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
                    Daftarkan Sebagai Admin Redaksi
                </button>
            </form>

            <p class="text-center text-xs text-stone-400 pt-2 border-t border-[#2c2523]">
                Sudah punya akun admin? 
                <a href="{{ route('admin.login') }}" class="text-[#E89B5B] font-semibold hover:underline">Masuk di sini</a>
            </p>

        </div>

        <p class="text-center mt-4 text-xs text-stone-500">
            <a href="{{ url('/') }}" class="hover:text-[#E89B5B] transition">← Kembali ke Halaman Utama Backend</a>
        </p>
    </div>

</body>
</html>

