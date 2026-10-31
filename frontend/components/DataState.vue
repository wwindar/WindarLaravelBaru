<template>
  <div>
    <!-- 1. Kondisi Memuat -->
    <div v-if="loading" class="py-12 px-4 text-center">
      <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-indigo-soft text-indigo-brand mb-3 animate-pulse">
        <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
      <p class="font-heading text-sm font-semibold text-ink">{{ loadingText || 'Memuat data...' }}</p>
      <p class="text-xs text-ink-muted mt-0.5">Mohon tunggu sejenak</p>
    </div>

    <!-- 2. Kondisi Gagal -->
    <div v-else-if="error" class="card p-8 text-center border-rose-200 bg-rose-50/50 my-4" role="alert">
      <div class="w-12 h-12 mx-auto rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mb-3">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <h3 class="font-heading text-base font-bold text-ink mb-1">Gagal Memuat Data</h3>
      <p class="text-xs text-ink-muted max-w-md mx-auto mb-5">{{ errorText || error }}</p>
      <button
        type="button"
        @click="$emit('retry')"
        class="btn-primary text-xs px-5 py-2 inline-flex items-center gap-2"
      >
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        Coba Lagi
      </button>
    </div>

    <!-- 3. Kondisi Kosong -->
    <div v-else-if="empty" class="card p-12 text-center my-4">
      <div class="w-14 h-14 mx-auto rounded-2xl bg-indigo-soft/60 text-indigo-brand flex items-center justify-center mb-3">
        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
        </svg>
      </div>
      <h3 class="font-heading text-base font-bold text-ink mb-1">{{ emptyTitle || 'Belum Ada Data' }}</h3>
      <p class="text-xs text-ink-muted max-w-sm mx-auto mb-4">{{ emptyDescription || 'Tidak ada konten yang sesuai dengan filter atau antrean saat ini.' }}</p>
      <slot name="empty-action" />
    </div>

    <!-- 4. Kondisi Berhasil -->
    <slot v-else />
  </div>
</template>

<script setup lang="ts">
defineProps<{
  loading?: boolean
  loadingText?: string
  error?: string | boolean | null
  errorText?: string
  empty?: boolean
  emptyTitle?: string
  emptyDescription?: string
}>()

defineEmits(['retry'])
</script>
