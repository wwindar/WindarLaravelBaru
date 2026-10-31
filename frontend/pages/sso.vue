<template>
  <div class="min-h-[70vh] flex items-center justify-center bg-canvas px-4">
    <div class="card p-8 sm:p-10 text-center max-w-sm w-full shadow-lift">
      <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#FF6EA7] to-[#BA43F6] text-white flex items-center justify-center font-heading font-bold text-base shadow-md mx-auto mb-4 animate-pulse">
        AK
      </div>
      <h2 class="font-heading text-lg font-bold text-ink mb-1">Menghubungkan Sesi</h2>
      <p class="text-xs text-ink-muted mb-4">{{ statusMessage }}</p>

      <div class="w-6 h-6 border-2 border-indigo-brand border-t-transparent rounded-full animate-spin mx-auto"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const auth = useAuthStore()
const statusMessage = ref('Memverifikasi akun Admin Redaksi...')

onMounted(async () => {
  const token = route.query.token as string

  if (!token) {
    statusMessage.value = 'Token tidak ditemukan. Mengalihkan...'
    setTimeout(() => navigateTo('/'), 1000)
    return
  }

  try {
    auth.token = token
    const user = await auth.fetchMe()

    if (user) {
      statusMessage.value = `Selamat datang, ${user.name}! Mengalihkan...`
      setTimeout(() => {
        if (user.role === 'admin' || user.role === 'dosen') {
          navigateTo('/dosen')
        } else {
          navigateTo('/mahasiswa')
        }
      }, 500)
    } else {
      statusMessage.value = 'Gagal memverifikasi token. Mengalihkan...'
      setTimeout(() => navigateTo('/login'), 1500)
    }
  } catch {
    statusMessage.value = 'Terjadi kesalahan. Mengalihkan...'
    setTimeout(() => navigateTo('/login'), 1500)
  }
})
</script>

