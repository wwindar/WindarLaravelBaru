<template>
  <span :class="badgeClass">
    <!-- Icon visual indicator -->
    <svg v-if="status === 'terbit'" class="w-3 h-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
    </svg>
    <svg v-else-if="status === 'menunggu'" class="w-3 h-3 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <svg v-else-if="status === 'perlu_revisi'" class="w-3 h-3 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
    </svg>
    <svg v-else-if="status === 'ditolak'" class="w-3 h-3 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
    </svg>
    <svg v-else class="w-3 h-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
    </svg>

    <!-- Teks status -->
    <span>{{ label || statusLabelComputed }}</span>
  </span>
</template>

<script setup lang="ts">
const props = defineProps<{
  status: 'draf' | 'menunggu' | 'terbit' | 'perlu_revisi' | 'ditolak' | string
  label?: string
}>()

const statusLabelComputed = computed(() => {
  switch (props.status) {
    case 'draf':
      return 'Draf'
    case 'menunggu':
      return 'Menunggu Persetujuan'
    case 'terbit':
      return 'Terbit'
    case 'perlu_revisi':
      return 'Perlu Revisi'
    case 'ditolak':
      return 'Ditolak'
    default:
      return props.status
  }
})

const badgeClass = computed(() => {
  switch (props.status) {
    case 'draf':
      return 'badge-draf'
    case 'menunggu':
      return 'badge-menunggu'
    case 'terbit':
      return 'badge-terbit'
    case 'perlu_revisi':
      return 'badge-revisi'
    case 'ditolak':
      return 'badge-ditolak'
    default:
      return 'badge-draf'
  }
})
</script>

