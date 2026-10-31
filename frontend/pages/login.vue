<template>
  <div class="min-h-[calc(100vh-4rem)] flex items-stretch">
    <!-- Panel Kiri: Indigo Gelap #1E1650 (Desktop) -->
    <div class="hidden lg:flex lg:w-1/2 bg-[#1E1650] text-white p-12 lg:p-16 flex-col justify-between relative overflow-hidden">
      <!-- Background subtle pattern -->
      <div class="absolute -right-24 -bottom-24 w-96 h-96 rounded-full bg-indigo-brand/20 blur-3xl pointer-events-none"></div>
      <div class="absolute -left-12 -top-12 w-80 h-80 rounded-full bg-indigo-500/10 blur-2xl pointer-events-none"></div>

      <!-- Header kiri -->
      <div class="relative z-10">
        <NuxtLink to="/" class="inline-flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-indigo-brand text-white flex items-center justify-center font-heading font-bold text-base shadow">
            AK
          </div>
          <span class="font-heading font-bold text-xl text-white tracking-tight">Aksara Kampus</span>
        </NuxtLink>
      </div>

      <!-- Kutipan sastra / info peran -->
      <div class="relative z-10 space-y-6 max-w-md my-auto py-12">
        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-indigo-200 border border-white/15">
          Portal Mahasiswa & Dosen Editor
        </span>
        <h2 class="font-heading text-3xl xl:text-4xl font-extrabold text-white leading-tight">
          Satu Pintu Masuk untuk Berkarya dan Mengurasi.
        </h2>
        <p class="text-sm text-indigo-200/80 leading-relaxed">
          Mahasiswa dapat mengelola draf tulisan dan mengajukan publikasi. Dosen dan dewan redaksi dapat langsung memeriksa antrean telaah karya.
        </p>

        <!-- Akun contoh panduan cepat -->
        <div class="p-4 rounded-xl bg-white/5 border border-white/10 text-xs space-y-2">
          <p class="font-semibold text-white">Akun Contoh Pengujian (Password: password)</p>
          <div class="grid grid-cols-2 gap-2 text-indigo-200/90 text-[11px]">
            <div>
              <span class="block text-indigo-300 font-bold">Mahasiswa:</span>
              <span>andi@students.itspku.ac.id</span>
            </div>
            <div>
              <span class="block text-indigo-300 font-bold">Dosen Editor:</span>
              <span>dosen@umpku.ac.id</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer kiri -->
      <div class="relative z-10 text-xs text-indigo-300/60">
        © {{ new Date().getFullYear() }} Aksara Kampus • Sistem Terintegrasi Sastra Digital
      </div>
    </div>

    <!-- Panel Kanan: Formulir Login Terang & Lembut -->
    <div class="w-full lg:w-1/2 bg-canvas flex items-center justify-center p-6 sm:p-12 lg:p-16">
      <div class="card w-full max-w-md p-8 sm:p-10 shadow-lift">
        <!-- Logo mobile -->
        <div class="lg:hidden flex items-center gap-2 mb-6">
          <div class="w-8 h-8 rounded-xl bg-indigo-brand text-white flex items-center justify-center font-heading font-bold text-xs">
            AK
          </div>
          <span class="font-heading font-bold text-sm text-ink">Aksara Kampus</span>
        </div>

        <div class="mb-6">
          <h1 class="font-heading text-2xl font-bold text-ink">Selamat Datang</h1>
          <p class="text-xs text-ink-muted mt-1">Masuk ke akun mahasiswa atau dosen untuk melanjutkan.</p>
        </div>

        <!-- Alert Error -->
        <div v-if="errorMessage" class="mb-5 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 flex items-start gap-2" role="alert">
          <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>{{ errorMessage }}</span>
        </div>

        <!-- Form Login -->
        <form @submit.prevent="handleLogin" class="space-y-4">
          <div>
            <label for="login-email" class="label">Email Terdaftar <span class="text-rose-500">*</span></label>
            <input
              id="login-email"
              v-model="email"
              type="email"
              required
              autocomplete="email"
              placeholder="nama@kampus.ac.id"
              class="input"
            />
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label for="login-password" class="label mb-0">Kata Sandi <span class="text-rose-500">*</span></label>
            </div>
            <input
              id="login-password"
              v-model="password"
              type="password"
              required
              autocomplete="current-password"
              placeholder="••••••••"
              class="input"
            />
          </div>

          <button
            type="submit"
            :disabled="isLoading"
            class="btn-primary w-full py-3 text-sm font-semibold mt-2 shadow"
          >
            <span v-if="isLoading" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></span>
            Masuk ke Aksara Kampus
          </button>
        </form>

        <!-- Link Registrasi Mahasiswa -->
        <div class="mt-8 pt-6 border-t border-card-border text-center text-xs text-ink-muted">
          Belum punya akun mahasiswa?
          <NuxtLink to="/register" class="text-indigo-brand font-semibold hover:underline ml-1">
            Daftar dengan Email Kampus
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: ['guest'],
})

const auth = useAuthStore()
const email = ref('')
const password = ref('')
const isLoading = ref(false)
const errorMessage = ref('')

async function handleLogin() {
  if (isLoading.value) return
  isLoading.value = true
  errorMessage.value = ''

  try {
    const res = await auth.login(email.value, password.value)
    if (res.data.user.role === 'dosen') {
      navigateTo('/dosen')
    } else {
      navigateTo('/mahasiswa')
    }
  } catch (err: any) {
    errorMessage.value = err.data?.message || 'Email atau kata sandi tidak cocok.'
  } finally {
    isLoading.value = false
  }
}
</script>
