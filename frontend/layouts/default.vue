<template>
  <div class="min-h-screen flex flex-col bg-canvas text-ink font-sans transition-colors duration-200">
    <!-- Navbar Top -->
    <header class="sticky top-0 z-40 bg-card/90 backdrop-blur-md border-b border-card-border shadow-sm">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
        <!-- Brand / Logo -->
        <NuxtLink to="/" class="flex items-center gap-3 group focus-visible:rounded-xl">
          <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[#FF6EA7] to-[#BA43F6] flex items-center justify-center font-heading font-bold text-white text-base shadow-md group-hover:scale-105 transition transform">
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
          <NuxtLink to="/berita" class="nav-item" active-class="nav-item-active">Berita & Pengumuman</NuxtLink>
          <NuxtLink to="/agenda" class="nav-item" active-class="nav-item-active">Agenda Kegiatan</NuxtLink>
        </nav>

        <!-- Right Side: User State, Theme Toggle & Action Buttons -->
        <div class="flex items-center gap-2 sm:gap-3">
          <!-- Theme Toggle Button (Light / Dark) -->
          <button
            type="button"
            @click="toggleTheme"
            class="w-9 h-9 rounded-xl flex items-center justify-center text-ink-muted hover:text-ink hover:bg-indigo-soft/60 border border-card-border transition"
            :title="isDark ? 'Ganti ke Mode Terang' : 'Ganti ke Mode Gelap'"
            aria-label="Toggle tema gelap atau terang"
          >
            <!-- Sun icon when dark -->
            <svg v-if="isDark" class="w-4 h-4 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <!-- Moon icon when light -->
            <svg v-else class="w-4 h-4 text-ink-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
          </button>

          <!-- State: Logged In Mahasiswa -->
          <template v-if="auth.isLoggedIn && auth.isMahasiswa">
            <NotificationBell />
            <NuxtLink to="/mahasiswa" class="nav-item font-semibold text-xs" active-class="nav-item-active">
              Tulisan Saya
            </NuxtLink>
            <NuxtLink to="/mahasiswa/tulisan/baru" class="btn-primary text-xs px-3.5 py-2 hidden sm:inline-flex">
              + Tulis Karya
            </NuxtLink>
            <!-- Dropdown Profil Mahasiswa -->
            <div class="relative" ref="profilDropdown">
              <button
                type="button"
                @click="showProfilMenu = !showProfilMenu"
                class="w-9 h-9 rounded-xl flex items-center justify-center font-heading font-bold text-white text-xs shadow-md hover:scale-105 transition overflow-hidden border border-card-border"
                :style="!auth.user?.foto ? { background: 'linear-gradient(135deg, #FF6EA7 0%, #BA43F6 100%)' } : {}"
                :title="auth.user?.name"
                aria-haspopup="true"
                :aria-expanded="showProfilMenu"
              >
                <img v-if="auth.user?.foto" :src="auth.user.foto" :alt="auth.user.name" class="w-full h-full object-cover" />
                <span v-else>{{ navInitials }}</span>
              </button>
              <!-- Dropdown menu -->
              <div
                v-if="showProfilMenu"
                class="absolute right-0 mt-2 w-56 bg-card border border-card-border rounded-2xl shadow-2xl z-50 overflow-hidden"
                style="background-color: var(--surface-color);"
              >
                <div class="px-4 py-3 border-b border-card-border flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-lg overflow-hidden shrink-0 flex items-center justify-center font-heading font-bold text-white text-[11px]" :style="!auth.user?.foto ? { background: 'linear-gradient(135deg, #FF6EA7 0%, #BA43F6 100%)' } : {}">
                    <img v-if="auth.user?.foto" :src="auth.user.foto" :alt="auth.user.name" class="w-full h-full object-cover" />
                    <span v-else>{{ navInitials }}</span>
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-ink truncate">{{ auth.user?.name }}</p>
                    <p class="text-[11px] text-ink-muted truncate">@{{ auth.user?.username }}</p>
                  </div>
                </div>
                <div class="py-1">
                  <NuxtLink
                    to="/mahasiswa/profil"
                    class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-ink hover:bg-indigo-soft/50 transition"
                    @click="showProfilMenu = false"
                  >
                    <svg class="w-4 h-4 text-ink-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profil Saya
                  </NuxtLink>
                  <NuxtLink
                    to="/mahasiswa"
                    class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-ink hover:bg-indigo-soft/50 transition"
                    @click="showProfilMenu = false"
                  >
                    <svg class="w-4 h-4 text-ink-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Tulisan Saya
                  </NuxtLink>
                  <div class="border-t border-card-border mt-1 pt-1">
                    <button
                      type="button"
                      @click="() => { auth.logout(); showProfilMenu = false }"
                      class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs text-rose-600 hover:bg-rose-500/10 transition"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                      </svg>
                      Keluar
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </template>

          <!-- State: Logged In Dosen / Editor -->
          <template v-else-if="auth.isLoggedIn && auth.isDosen">
            <span class="text-xs px-2.5 py-1 rounded-full bg-fuchsia-500/15 text-fuchsia-700 dark:text-fuchsia-300 font-bold border border-fuchsia-500/30 hidden sm:inline">
              {{ auth.user?.role === 'admin' ? 'Admin Redaksi' : 'Dosen Editor' }}
            </span>
            <NuxtLink to="/dosen" class="nav-item font-semibold text-xs" active-class="nav-item-active">
              Antrean Naskah
            </NuxtLink>
            <NuxtLink to="/dosen/laporan" class="nav-item font-semibold text-xs" active-class="nav-item-active">
              Laporan
            </NuxtLink>
            <a
              v-if="auth.user?.role === 'admin'"
              href="http://127.0.0.1:8000/admin/dashboard"
              target="_blank"
              class="btn-secondary text-xs px-2.5 py-1.5 hidden md:inline-flex items-center gap-1.5"
              title="Buka Dasbor Admin Laravel Backend"
            >
              <span>Dasbor Backend</span>
              <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
              </svg>
            </a>
            <!-- Dropdown Profil Dosen / Admin -->
            <div class="relative" ref="profilDropdownDosen">
              <button
                type="button"
                @click="showProfilMenu = !showProfilMenu"
                class="w-9 h-9 rounded-xl flex items-center justify-center font-heading font-bold text-white text-xs shadow-md hover:scale-105 transition overflow-hidden border border-card-border"
                :style="!auth.user?.foto ? { background: 'linear-gradient(135deg, #FF6EA7 0%, #BA43F6 100%)' } : {}"
                :title="auth.user?.name"
              >
                <img v-if="auth.user?.foto" :src="auth.user.foto" :alt="auth.user.name" class="w-full h-full object-cover" />
                <span v-else>{{ navInitials }}</span>
              </button>
              <!-- Dropdown menu -->
              <div
                v-if="showProfilMenu"
                class="absolute right-0 mt-2 w-56 bg-card border border-card-border rounded-2xl shadow-2xl z-50 overflow-hidden"
                style="background-color: var(--surface-color);"
              >
                <div class="px-4 py-3 border-b border-card-border flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-lg overflow-hidden shrink-0 flex items-center justify-center font-heading font-bold text-white text-[11px]" :style="!auth.user?.foto ? { background: 'linear-gradient(135deg, #FF6EA7 0%, #BA43F6 100%)' } : {}">
                    <img v-if="auth.user?.foto" :src="auth.user.foto" :alt="auth.user.name" class="w-full h-full object-cover" />
                    <span v-else>{{ navInitials }}</span>
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-ink truncate">{{ auth.user?.name }}</p>
                    <p class="text-[11px] text-fuchsia-600 dark:text-fuchsia-400 font-semibold truncate">{{ auth.user?.role === 'admin' ? 'Admin Redaksi' : 'Dosen Editor' }}</p>
                  </div>
                </div>
                <div class="py-1">
                  <NuxtLink
                    to="/dosen"
                    class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-ink hover:bg-indigo-soft/50 transition"
                    @click="showProfilMenu = false"
                  >
                    Antrean Naskah
                  </NuxtLink>
                  <NuxtLink
                    to="/dosen/laporan"
                    class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-ink hover:bg-indigo-soft/50 transition"
                    @click="showProfilMenu = false"
                  >
                    Laporan Pembaca
                  </NuxtLink>
                  <a
                    v-if="auth.user?.role === 'admin'"
                    href="http://127.0.0.1:8000/admin/dashboard"
                    class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-indigo-brand hover:bg-indigo-soft/50 transition font-medium"
                    @click="showProfilMenu = false"
                  >
                    Buka Dasbor Backend →
                  </a>
                  <div class="border-t border-card-border mt-1 pt-1">
                    <button
                      type="button"
                      @click="() => { auth.logout(); showProfilMenu = false }"
                      class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs text-rose-600 hover:bg-rose-500/10 transition"
                    >
                      Keluar
                    </button>
                  </div>
                </div>
              </div>
            </div>
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
    <footer class="bg-card border-t border-card-border py-12 mt-20">
      <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-8 border-b border-card-border">
          <div class="md:col-span-2">
            <div class="flex items-center gap-3 mb-3">
              <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#FF6EA7] to-[#BA43F6] text-white flex items-center justify-center font-heading font-bold text-sm shadow">
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
            <h4 class="font-heading text-xs font-bold text-ink uppercase tracking-wider mb-3">Informasi & Akses</h4>
            <ul class="space-y-2 text-xs text-ink-muted">
              <li><NuxtLink to="/berita" class="hover:text-indigo-brand transition">Berita & Iklan Kampus</NuxtLink></li>
              <li><NuxtLink to="/agenda" class="hover:text-indigo-brand transition">Agenda & Sayembara</NuxtLink></li>
              <li><NuxtLink to="/register" class="hover:text-indigo-brand transition">Gabung Penulis Mahasiswa</NuxtLink></li>
              <li><NuxtLink to="/login" class="hover:text-indigo-brand transition">Login Dosen & Mahasiswa</NuxtLink></li>
            </ul>
          </div>
        </div>
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-ink-subtle">
          <span>© {{ new Date().getFullYear() }} Aksara Kampus • Portal Komunitas Sastra</span>
          <span class="font-medium text-ink-muted">
            Crafted with ❤️ by <span class="font-semibold text-indigo-brand">Windar Shineta</span> (<span class="italic text-ink">Win Lyricist</span>)
          </span>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
const auth = useAuthStore()
const isDark = ref(false)
const showProfilMenu = ref(false)
const profilDropdown = ref<HTMLElement | null>(null)
const profilDropdownDosen = ref<HTMLElement | null>(null)

const navInitials = computed(() => {
  const name = auth.user?.name || ''
  return name.split(' ').slice(0, 2).map((n: string) => n[0]).join('').toUpperCase() || 'AK'
})

onMounted(() => {
  if (import.meta.client) {
    const saved = localStorage.getItem('theme')
    if (saved) {
      isDark.value = saved === 'dark'
    } else {
      isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    }

    // Tutup dropdown ketika klik di luar
    document.addEventListener('click', (e) => {
      const target = e.target as Node
      const isInsideMhs = profilDropdown.value && profilDropdown.value.contains(target)
      const isInsideDosen = profilDropdownDosen.value && profilDropdownDosen.value.contains(target)
      if (!isInsideMhs && !isInsideDosen) {
        showProfilMenu.value = false
      }
    })
  }
})

function toggleTheme() {
  if (!import.meta.client) return
  isDark.value = !isDark.value
  const mode = isDark.value ? 'dark' : 'light'
  document.documentElement.setAttribute('data-theme', mode)
  if (isDark.value) {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }
  localStorage.setItem('theme', mode)
}
</script>

<style scoped>
.nav-item {
  @apply px-3.5 py-2 rounded-xl text-xs font-medium text-ink-muted hover:text-ink hover:bg-indigo-soft/50 transition-colors focus-visible:ring-2 focus-visible:ring-indigo-brand;
}
.nav-item-active {
  @apply text-indigo-brand bg-indigo-soft font-semibold;
}
</style>

<style scoped>
.nav-item {
  @apply px-3.5 py-2 rounded-xl text-xs font-medium text-ink-muted hover:text-ink hover:bg-indigo-soft/50 transition-colors focus-visible:ring-2 focus-visible:ring-indigo-brand;
}
.nav-item-active {
  @apply text-indigo-brand bg-indigo-soft font-semibold;
}
</style>
