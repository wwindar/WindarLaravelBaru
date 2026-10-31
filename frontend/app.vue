<template>
  <div class="dark">
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </div>
</template>

<script setup lang="ts">
const auth = useAuthStore()

onMounted(async () => {
  // Pulihkan sesi dari cookie jika ada token tersimpan
  if (auth.token) {
    await auth.fetchMe()
  }
})
</script>

