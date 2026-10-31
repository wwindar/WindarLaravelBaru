<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>

<script setup lang="ts">
const auth = useAuthStore()

onMounted(async () => {
  // Pulihkan sesi dari cookie jika ada token tersimpan
  if (auth.token) {
    await auth.fetchMe()
  }

  // Sinkronisasi tema tersimpan jika user pernah memilih manual
  if (import.meta.client) {
    const saved = localStorage.getItem('theme')
    if (saved === 'dark') {
      document.documentElement.setAttribute('data-theme', 'dark')
      document.documentElement.classList.add('dark')
    } else if (saved === 'light') {
      document.documentElement.setAttribute('data-theme', 'light')
      document.documentElement.classList.remove('dark')
    }
  }
})
</script>

