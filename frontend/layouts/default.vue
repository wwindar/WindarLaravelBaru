<template>
  <div class="min-h-screen flex flex-col bg-ink-950 text-parchment-200">
    <!-- Navbar -->
    <header class="sticky top-0 z-50 border-b border-ink-800/80 bg-ink-950/85 backdrop-blur-md">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-4">
        <!-- Logo -->
        <NuxtLink to="/" class="flex items-center gap-3 group">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-ochre-400 to-terracotta-500 flex items-center justify-center font-bold text-ink-950 text-base shadow-lg shadow-ochre-400/20 group-hover:scale-105 transition transform">
            AK
          </div>
          <div>
            <span class="font-bold text-parchment-50 text-base tracking-tight block">Aksara Kampus</span>
            <span class="text-[10px] text-ochre-400/80 font-mono tracking-wider -mt-0.5 block">UMPKU</span>
          </div>
        </NuxtLink>

        <!-- Nav links -->
        <nav class="hidden md:flex items-center gap-1">
          <NuxtLink to="/" class="nav-link" exact-active-class="nav-link-active">Beranda</NuxtLink>
          <NuxtLink to="/artikel" class="nav-link" active-class="nav-link-active">Artikel</NuxtLink>
          <NuxtLink to="/karya" class="nav-link" active-class="nav-link-active">Karya Sastra</NuxtLink>
          <NuxtLink to="/agenda" class="nav-link" active-class="nav-link-active">Agenda</NuxtLink>
          <NuxtLink to="/kirim" class="nav-link" active-class="nav-link-active">Kirim Karya</NuxtLink>
        </nav>

        <!-- Auth buttons -->
        <div class="flex items-center gap-2">
          <template v-if="auth.isLoggedIn">
            <span class="text-xs text-ochre-300 font-semibold hidden sm:inline">
              Halo, {{ auth.user?.name?.split(' ')[0] }}
            </span>
            <button @click="auth.logout()" class="btn-ghost text-xs px-3 py-1.5 text-terracotta-400 hover:text-terracotta-300">
              Keluar
            </button>
            <NuxtLink v-if="auth.isAdmin" to="/admin" class="btn-primary text-xs px-3 py-1.5">
              Panel Redaksi
            </NuxtLink>
          </template>
          <template v-else>
            <NuxtLink to="/login" class="btn-ghost text-xs px-3 py-1.5">
              Masuk
            </NuxtLink>
            <NuxtLink to="/register" class="btn-primary text-xs px-3 py-1.5">
              Gabung Mahasiswa
            </NuxtLink>
          </template>
        </div>
      </div>
    </header>

    <!-- Page content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="border-t border-ink-800/80 bg-ink-900/40 py-8 mt-16">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-parchment-500">
        <span class="flex items-center gap-2">
          <span class="w-5 h-5 rounded-md bg-gradient-to-tr from-ochre-400 to-terracotta-500 inline-flex items-center justify-center text-ink-950 font-bold text-[10px]">AK</span>
          © {{ new Date().getFullYear() }} Aksara Kampus • Universitas Muhammadiyah Kudus
        </span>
        <span class="text-sage-300/80">Ruang Dialektika & Komunitas Sastra Kampus</span>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
const auth = useAuthStore()
</script>

<style scoped>
.nav-link {
  @apply px-3 py-1.5 rounded-xl text-sm text-parchment-400 hover:text-parchment-100 hover:bg-ink-850 transition-colors;
}
.nav-link-active {
  @apply text-ochre-400 bg-ochre-400/10 font-semibold;
}
</style>
