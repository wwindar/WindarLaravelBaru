<template>
  <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink/50 backdrop-blur-sm" role="dialog" aria-modal="true">
    <div class="card w-full max-w-lg p-6 sm:p-8 animate-in fade-in zoom-in-95 duration-150">
      <!-- Header -->
      <div class="flex items-center justify-between pb-4 border-b border-card-border">
        <div>
          <h3 class="font-heading text-lg font-bold text-ink">Laporkan Tulisan</h3>
          <p class="text-xs text-ink-muted mt-0.5">Adanya indikasi plagiarisme atau pelanggaran norma</p>
        </div>
        <button
          type="button"
          @click="close"
          class="p-2 text-ink-muted hover:text-ink rounded-lg focus-visible:ring-2 focus-visible:ring-indigo-brand"
          aria-label="Tutup modal"
        >
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Sukses -->
      <div v-if="isSuccess" class="py-8 text-center">
        <div class="w-12 h-12 mx-auto rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h4 class="font-heading text-base font-bold text-ink mb-1">Laporan Terkirim</h4>
        <p class="text-xs text-ink-muted max-w-sm mx-auto">
          Terima kasih atas kepedulian Anda menjaga integritas sastra kampus. Dewan kurator akan segera meninjau laporan ini.
        </p>
        <button type="button" @click="close" class="btn-primary mt-6 text-xs px-5">Tutup</button>
      </div>

      <!-- Form -->
      <form v-else @submit.prevent="submitReport" class="mt-4 space-y-4">
        <div v-if="errorMessage" class="p-3 text-xs bg-rose-50 border border-rose-200 text-rose-700 rounded-xl" role="alert">
          {{ errorMessage }}
        </div>

        <div>
          <label for="report-name" class="label">Nama Lengkap Anda <span class="text-rose-500">*</span></label>
          <input
            id="report-name"
            v-model="form.nama_pelapor"
            type="text"
            required
            placeholder="Contoh: Rian Pratama"
            class="input"
          />
        </div>

        <div>
          <label for="report-email" class="label">Email Anda <span class="text-rose-500">*</span></label>
          <input
            id="report-email"
            v-model="form.email_pelapor"
            type="email"
            required
            placeholder="nama@email.com"
            class="input"
          />
        </div>

        <div>
          <label for="report-reason" class="label">Alasan Pelaporan <span class="text-rose-500">*</span></label>
          <textarea
            id="report-reason"
            v-model="form.alasan"
            required
            rows="4"
            placeholder="Jelaskan secara rinci indikasi pelanggaran, sumber karya asli bila ada dugaan plagiasi, atau bagian naskah yang melanggar..."
            class="input resize-none"
          ></textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-card-border">
          <button type="button" @click="close" class="btn-secondary text-xs px-4">Batal</button>
          <button type="submit" :disabled="isSubmitting" class="btn-danger text-xs px-5">
            <span v-if="isSubmitting" class="inline-block w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin mr-1.5"></span>
            Kirim Laporan
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  modelValue: boolean
  tulisanId: number
}>()

const emit = defineEmits(['update:modelValue'])

const config = useRuntimeConfig()
const apiBase = config.public.apiBase as string

const form = reactive({
  nama_pelapor: '',
  email_pelapor: '',
  alasan: '',
})

const isSubmitting = ref(false)
const isSuccess = ref(false)
const errorMessage = ref('')

function close() {
  emit('update:modelValue', false)
  // Reset setelah tertutup
  setTimeout(() => {
    isSuccess.value = false
    errorMessage.value = ''
    form.nama_pelapor = ''
    form.email_pelapor = ''
    form.alasan = ''
  }, 200)
}

async function submitReport() {
  if (isSubmitting.value) return
  isSubmitting.value = true
  errorMessage.value = ''

  try {
    await $fetch(`${apiBase}/tulisan/${props.tulisanId}/lapor`, {
      method: 'POST',
      body: form,
      headers: { Accept: 'application/json' },
    })
    isSuccess.value = true
  } catch (err: any) {
    errorMessage.value = err.data?.message || 'Gagal mengirim laporan. Periksa isian form.'
  } finally {
    isSubmitting.value = false
  }
}
</script>
