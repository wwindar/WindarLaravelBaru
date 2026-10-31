<template>
  <div class="min-h-screen flex flex-col bg-canvas text-ink font-sans">
    <!-- Navbar Top -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-card-border">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
        <!-- Brand / Logo -->
        <NuxtLink to="/" class="flex items-center gap-3 group focus-visible:rounded-xl">
          <div class="w-10 h-10 rounded-2xl bg-indigo-brand flex items-center justify-center font-heading font-bold text-white text-base shadow-sm group-hover:scale-105 transition transform">
            AK
          </div>
          <div>
            <span class="font-heading font-bold text-base text-ink tracking-tight block leading-tight">Aksara Kampus</span>
            <span class="text-[11px] text-ink-muted block leading-tight font-medium">Portal Sastra & Artikel</span>
          </div>
        </NuxtLink>

        <!-- Navigation Links (Desktop) -->
        <nav class="hidden md:flex items-center gap-1.5" aria-label="Menu Utama">
          <NuxtLink to="/" class="nav-item" exact-active-class="nav-item-active">Beranda</NuxtLink>
          <NuxtLink to="/karya" class="nav-item" active-class="nav-item-active">Karya Sastra</NuxtLink>
          <NuxtLink to="/agenda" class="nav-item" active-class="nav-item-active">Agenda Kegiatan</NuxtLink>
        </nav>

        <!-- Right Side: User State & Action Buttons -->
        <div class="flex items-center gap-2 sm:gap-3">
          <!-- State: Logged In Mahasiswa -->
          <template v-if="auth.isLoggedIn && auth.isMahasiswa">
            <NotificationBell />
            <NuxtLink to="/mahasiswa" class="nav-item font-semibold text-xs" active-class="nav-item-active">
              Tulisan Saya
            </NuxtLink>
            <NuxtLink to="/mahasiswa/tulisan/baru" class="btn-primary text-xs px-3.5 py-2 hidden sm:inline-flex">
              + Tulis Karya
            </NuxtLink>
            <button
              type="button"
              @click="auth.logout"
              class="btn-ghost text-xs px-2.5 py-2 text-rose-600 hover:text-rose-700"
              title="Keluar akun"
            >
              Keluar
            </button>
          </template>

          <!-- State: Logged In Dosen / Editor -->
          <template v-else-if="auth.isLoggedIn && auth.isDosen">
            <span class="text-xs px-2.5 py-1 rounded-full bg-purple-100 text-purple-800 font-bold border border-purple-200 hidden sm:inline">
              Dosen / Editor
            </span>
            <NuxtLink to="/dosen" class="nav-item font-semibold text-xs" active-class="nav-item-active">
              Antrean Naskah
            </NuxtLink>
            <NuxtLink to="/dosen/laporan" class="nav-item font-semibold text-xs" active-class="nav-item-active">
              Laporan
            </NuxtLink>
            <button
              type="button"
              @click="auth.logout"
              class="btn-ghost text-xs px-2.5 py-2 text-rose-600 hover:text-rose-700"
              title="Keluar akun"
            >
              Keluar
            </button>
          </template>

          <!-- State: Guest / Belum Login -->
          <template v-else>
            <NuxtLink to="/login" class="btn-secondary text-xs px-3.5 py-2">
              Masuk
            </NuxtLink>
            <NuxtLink to="/register" class="btn-primary text-xs px-3.5 py-2">
              Daftar Mahasiswa
            </NuxtLink>
          </template>
        </div>
      </div>
    </header>

    <!-- Content Slot -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-card-border py-12 mt-20">
      <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-8 border-b border-card-border">
          <div class="md:col-span-2">
            <div class="flex items-center gap-3 mb-3">
              <div class="w-8 h-8 rounded-xl bg-indigo-brand text-white flex items-center justify-center font-heading font-bold text-sm">
                AK
              </div>
              <span class="font-heading font-bold text-base text-ink">Aksara Kampus</span>
            </div>
            <p class="text-xs text-ink-muted leading-relaxed max-w-sm">
              Wadah apresiasi dan publikasi sastra digital untuk civitas akademika kampus. Menghidupkan tradisi literasi melalui puisi, cerpen, artikel kritis, dan esai.
            </p>
          </div>
          <div>
            <h4 class="font-heading text-xs font-bold text-ink uppercase tracking-wider mb-3">Kanal Sastra</h4>
            <ul class="space-y-2 text-xs text-ink-muted">
              <li><NuxtLink to="/karya?jenis=puisi" class="hover:text-indigo-brand transition">Puisi Kampus</NuxtLink></li>
              <li><NuxtLink to="/karya?jenis=cerpen" class="hover:text-indigo-brand transition">Cerita Pendek</NuxtLink></li>
              <li><NuxtLink to="/karya?jenis=esai" class="hover:text-indigo-brand transition">Esai & Kritik</NuxtLink></li>
              <li><NuxtLink to="/karya?jenis=artikel" class="hover:text-indigo-brand transition">Artikel Ilmiah Populer</NuxtLink></li>
            </ul>
          </div>
          <div>
            <h4 class="font-heading text-xs font-bold text-ink uppercase tracking-wider mb-3">Peran & Akses</h4>
            <ul class="space-y-2 text-xs text-ink-muted">
              <li><NuxtLink to="/register" class="hover:text-indigo-brand transition">Gabung Penulis Mahasiswa</NuxtLink></li>
              <li><NuxtLink to="/login" class="hover:text-indigo-brand transition">Login Dosen & Mahasiswa</NuxtLink></li>
              <li><NuxtLink to="/agenda" class="hover:text-indigo-brand transition">Agenda & Sayembara</NuxtLink></li>
            </ul>
          </div>
        </div>
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-ink-subtle">
          <span>© {{ new Date().getFullYear() }} Aksara Kampus • Portal Komunitas Sastra</span>
          <span>Desain Terang & Lembut • InfinityFree Ready</span>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
const auth = useAuthStore()
</script>

<style scoped>
.nav-item {
  @apply px-3.5 py-2 rounded-xl text-xs font-medium text-ink-muted hover:text-ink hover:bg-indigo-soft/50 transition-colors focus-visible:ring-2 focus-visible:ring-indigo-brand;
}
.nav-item-active {
  @apply text-indigo-brand bg-indigo-soft font-semibold;
}
</style>
