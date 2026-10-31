<template>
  <div class="min-h-[calc(100vh-4rem)] flex items-stretch">
    <!-- Panel Kiri: Slate-Violet Gradient (uTask Dark Aesthetic) -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-[#231A3B] via-[#1B162E] to-[#120F20] text-white p-12 lg:p-16 flex-col justify-between relative overflow-hidden border-r border-[#352E54]/50">
      <!-- Background subtle ambient glow -->
      <div class="absolute -right-24 -bottom-24 w-96 h-96 rounded-full bg-fuchsia-600/20 blur-3xl pointer-events-none"></div>
      <div class="absolute -left-12 -top-12 w-80 h-80 rounded-full bg-pink-500/15 blur-2xl pointer-events-none"></div>

      <div class="relative z-10">
        <NuxtLink to="/" class="inline-flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[#FF6EA7] to-[#BA43F6] text-white flex items-center justify-center font-heading font-bold text-base shadow-lg shadow-fuchsia-900/30">
            AK
          </div>
          <span class="font-heading font-bold text-xl text-white tracking-tight">Aksara Kampus</span>
        </NuxtLink>
      </div>

      <div class="relative z-10 space-y-6 max-w-md my-auto py-12">
        <span class="inline-block px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white/10 text-pink-200 border border-white/15 backdrop-blur-sm">
          Registrasi Penulis Mahasiswa
        </span>
        <h2 class="font-heading text-3xl xl:text-4xl font-extrabold text-white leading-tight">
          Suarakan Gagasan, Tumbuhkan Tradisi Literasi.
        </h2>
        <p class="text-sm text-fuchsia-100/80 leading-relaxed">
          Pendaftaran dibuka eksklusif bagi mahasiswa civitas akademika untuk memastikan karya yang diterbitkan dapat dipertanggungjawabkan keasliannya.
        </p>

        <div class="p-4 rounded-2xl bg-white/5 border border-white/10 text-xs space-y-2 text-fuchsia-100/90 backdrop-blur-sm">
          <p class="font-semibold text-white flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            Domain Email Resmi yang Diterima:
          </p>
          <div class="space-y-1 text-[11px] pt-1">
            <p>• @students.itspku.ac.id</p>
            <p>• @student.umpku.ac.id</p>
            <p>• @itspku.ac.id / @umpku.ac.id</p>
          </div>
        </div>
      </div>

      <div class="relative z-10 text-xs text-fuchsia-200/50">
        © {{ new Date().getFullYear() }} Aksara Kampus • Komunitas Sastra Mahasiswa
      </div>
    </div>

    <!-- Panel Kanan: Form Registrasi -->
    <div class="w-full lg:w-1/2 bg-canvas flex items-center justify-center p-6 sm:p-12 lg:p-16">
      <div class="card w-full max-w-md p-8 sm:p-10 shadow-lift border border-card-border">
        <div class="mb-6">
          <h1 class="font-heading text-2xl font-bold text-ink">Daftar Akun Penulis</h1>
          <p class="text-xs text-ink-muted mt-1">Gunakan email resmi kampus Anda untuk mulai berkarya.</p>
        </div>

        <!-- Alert Error -->
        <div v-if="errorMessage" class="mb-5 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 flex items-start gap-2" role="alert">
          <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>{{ errorMessage }}</span>
        </div>

        <form @submit.prevent="handleRegister" class="space-y-4">
          <div>
            <label for="reg-name" class="label">Nama Lengkap <span class="text-rose-500">*</span></label>
            <input
              id="reg-name"
              v-model="name"
              type="text"
              required
              placeholder="Contoh: Sekar Ayu Kinanti"
              class="input"
            />
          </div>

          <div>
            <label for="reg-username" class="label">Username <span class="text-rose-500">*</span></label>
            <input
              id="reg-username"
              v-model="username"
              type="text"
              required
              placeholder="Contoh: sekarayu"
              class="input"
            />
          </div>

          <div>
            <label for="reg-email" class="label">Email Resmi Kampus <span class="text-rose-500">*</span></label>
            <input
              id="reg-email"
              v-model="email"
              type="email"
              required
              placeholder="nim@students.itspku.ac.id"
              class="input"
            />
            <p class="text-[11px] text-ink-subtle mt-1">Wajib menggunakan domain kampus yang sah.</p>
          </div>

          <div>
            <label for="reg-password" class="label">Kata Sandi (Minimal 6 Karakter) <span class="text-rose-500">*</span></label>
            <input
              id="reg-password"
              v-model="password"
              type="password"
              required
              minlength="6"
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
            Daftar Akun Mahasiswa
          </button>
        </form>

        <div class="mt-8 pt-6 border-t border-card-border text-center text-xs text-ink-muted">
          Sudah memiliki akun?
          <NuxtLink to="/login" class="text-indigo-brand font-semibold hover:underline ml-1">
            Masuk di sini
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
const name = ref('')
const username = ref('')
const email = ref('')
const password = ref('')
const isLoading = ref(false)
const errorMessage = ref('')

async function handleRegister() {
  if (isLoading.value) return
  isLoading.value = true
  errorMessage.value = ''

  try {
    await auth.register(name.value, username.value, email.value, password.value)
    navigateTo('/mahasiswa')
  } catch (err: any) {
    errorMessage.value = err.data?.message || 'Registrasi gagal. Pastikan format email kampus benar.'
  } finally {
    isLoading.value = false
  }
}
</script>
