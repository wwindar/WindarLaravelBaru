<template>
  <div class="max-w-xl mx-auto px-4 py-16 text-center space-y-4">
    <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-soft text-indigo-brand flex items-center justify-center font-bold text-xl">
      ✍️
    </div>
    <h1 class="font-heading text-2xl font-bold text-ink">Pengalihan ke Ruang Penulis</h1>
    <p class="text-xs text-ink-muted">
      Pengiriman naskah kini terintegrasi langsung dengan akun mahasiswa resmi. Mengalihkan Anda...
    </p>
  </div>
</template>

<script setup lang="ts">
const auth = useAuthStore()

onMounted(() => {
  if (auth.isLoggedIn && auth.isMahasiswa) {
    navigateTo('/mahasiswa/tulisan/baru')
  } else if (auth.isLoggedIn && auth.isDosen) {
    navigateTo('/dosen')
  } else {
    navigateTo('/register')
  }
})
</script>
