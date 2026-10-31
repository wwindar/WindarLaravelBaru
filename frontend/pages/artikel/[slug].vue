<template>
  <div class="p-8 text-center">
    <p class="text-xs text-ink-muted">Mengalihkan ke naskah...</p>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
navigateTo(`/karya/${route.params.slug}`)
</script>
