<template>
  <div class="min-h-screen flex items-center justify-center p-4">
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-40 w-96 h-96 bg-amber-500/5 rounded-full blur-3xl" />
      <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-rose-500/5 rounded-full blur-3xl" />
    </div>

    <div class="w-full max-w-sm relative">
      <div class="card p-8 shadow-2xl">
        <div class="text-center mb-8">
          <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-500 to-rose-500 flex items-center justify-center font-bold text-slate-900 text-2xl shadow-lg shadow-amber-500/20 mx-auto mb-4">
            AK
          </div>
          <h1 class="text-xl font-bold text-white">Gabung Aksara Kampus</h1>
          <p class="text-sm text-slate-400 mt-1">Khusus Mahasiswa & Civitas UMPKU</p>
          <div class="mt-3 p-2.5 rounded-lg bg-amber-500/10 border border-amber-500/20 text-xs text-amber-300">
            🔒 Wajib menggunakan email resmi kampus: <strong class="text-white">@umpku.ac.id</strong> atau <strong class="text-white">@student.umpku.ac.id</strong>
          </div>
        </div>

        <div v-if="errorMsg" class="mb-4 p-3 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm">
          {{ errorMsg }}
        </div>

        <form @submit.prevent="handleRegister" class="space-y-4">
          <div>
            <label class="label">Nama Lengkap</label>
            <input
              v-model="form.name"
              type="text"
              class="input"
              placeholder="Sekar Ayu Kinanti"
              required
            />
          </div>

          <div>
            <label class="label">Email Resmi Kampus UMPKU</label>
            <input
              v-model="form.email"
              type="email"
              class="input"
              placeholder="nim@student.umpku.ac.id"
              required
            />
            <p class="text-[11px] text-slate-500 mt-1">Gunakan format @umpku.ac.id atau @student.umpku.ac.id</p>
          </div>

          <div>
            <label class="label">Kata Sandi (Minimal 6 Karakter)</label>
            <input
              v-model="form.password"
              type="password"
              class="input"
              placeholder="••••••••"
              minlength="6"
              required
            />
          </div>

          <button
            type="submit"
            class="btn-primary w-full py-2.5 mt-2"
            :disabled="loading"
          >
            {{ loading ? 'Mendaftar...' : 'Daftar Akun Penulis' }}
          </button>
        </form>

        <p class="text-center mt-6 text-xs text-slate-400">
          Sudah punya akun?
          <NuxtLink to="/login" class="text-amber-400 hover:underline font-semibold ml-1">Masuk di sini</NuxtLink>
        </p>
      </div>

      <p class="text-center mt-4 text-xs text-slate-500">
        <NuxtLink to="/" class="hover:text-amber-400 transition">← Kembali ke portal publik</NuxtLink>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'blank' })

const auth = useAuthStore()
const router = useRouter()

const form = reactive({ name: '', email: '', password: '' })
const loading = ref(false)
const errorMsg = ref('')

async function handleRegister() {
  loading.value = true
  errorMsg.value = ''

  try {
    await auth.register(form.name, form.email, form.password)
    router.push('/kirim')
  } catch (err: any) {
    if (err?.data?.errors) {
      const firstKey = Object.keys(err.data.errors)[0]
      errorMsg.value = err.data.errors[firstKey][0]
    } else {
      errorMsg.value = err?.data?.message ?? 'Gagal mendaftar. Silakan coba lagi.'
    }
  } finally {
    loading.value = false
  }
}
</script>

