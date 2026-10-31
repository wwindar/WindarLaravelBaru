<template>
  <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-8">
    <!-- Header Dosen -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-card-border">
      <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 text-purple-800 text-xs font-bold mb-2">
          Dewan Redaksi & Kurator Kampus
        </div>
        <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-ink">Antrean Telaah Naskah</h1>
        <p class="text-xs sm:text-sm text-ink-muted mt-1">
          Periksa naskah mahasiswa yang diajukan. Setujui untuk penerbitan langsung, atau berikan catatan telaah.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <NuxtLink to="/dosen/laporan" class="btn-secondary text-xs px-4 py-2 flex items-center gap-1.5">
          <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          Daftar Laporan Publik →
        </NuxtLink>
      </div>
    </div>

    <!-- Alert Sukses -->
    <div v-if="successMsg" class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs flex items-center justify-between" role="status">
      <span>{{ successMsg }}</span>
      <button @click="successMsg = ''" class="text-emerald-700 font-bold ml-2">✕</button>
    </div>

    <!-- 4 Kondisi State -->
    <DataState
      :loading="isLoading"
      :error="errorMsg"
      :empty="antreanList.length === 0"
      empty-title="Antrean Bersih"
      empty-description="Tidak ada naskah yang sedang menunggu persetujuan saat ini."
      @retry="fetchAntrean"
    >
      <div class="space-y-6">
        <article
          v-for="item in antreanList"
          :key="item.id"
          class="card p-6 sm:p-8 space-y-4 hover:border-indigo-brand/30 transition"
        >
          <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-card-border">
            <div class="flex items-center gap-2">
              <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-soft text-indigo-brand capitalize">
                {{ item.jenis_label || item.jenis }}
              </span>
              <span v-if="item.kategori" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                {{ item.kategori.nama }}
              </span>
              <StatusBadge :status="item.status" :label="item.status_label" />
            </div>
            <span class="text-xs text-ink-subtle">
              Diajukan {{ formatDate(item.updated_at) }}
            </span>
          </div>

          <!-- Judul & Penulis -->
          <div>
            <h2 class="font-heading text-xl font-bold text-ink mb-1">
              {{ item.judul }}
            </h2>
            <p class="text-xs text-ink-muted">
              Oleh: <strong class="text-ink">{{ item.penulis?.name }}</strong> (@{{ item.penulis?.username || 'penulis' }})
            </p>
          </div>

          <!-- Ringkasan jika ada -->
          <blockquote v-if="item.ringkasan" class="p-3 rounded-xl bg-indigo-soft/20 text-xs text-ink-muted italic border-l-2 border-indigo-brand">
            "{{ item.ringkasan }}"
          </blockquote>

          <!-- Cuplikan Isi Naskah (Collapsible / Full) -->
          <div class="p-4 rounded-xl bg-canvas border border-card-border text-xs sm:text-sm text-ink leading-relaxed whitespace-pre-line max-h-56 overflow-y-auto">
            {{ item.isi }}
          </div>

          <!-- Aksi Dosen: Setujui (1-klik), Tolak, Minta Revisi -->
          <div class="pt-4 border-t border-card-border flex flex-wrap items-center justify-between gap-3">
            <span class="text-xs text-ink-muted">
              Keputusan redaksi akan langsung memberi notifikasi ke mahasiswa.
            </span>

            <div class="flex items-center gap-2">
              <button
                type="button"
                @click="bukaModalCatatan(item, 'tolak')"
                class="btn-ghost text-xs px-3.5 py-2 text-rose-600 hover:text-rose-700 hover:bg-rose-50"
              >
                Tolak Naskah
              </button>

              <button
                type="button"
                @click="bukaModalCatatan(item, 'revisi')"
                class="btn-secondary text-xs px-3.5 py-2 text-purple-700 border-purple-200 hover:bg-purple-50"
              >
                Minta Revisi...
              </button>

              <button
                type="button"
                :disabled="processingId === item.id"
                @click="setujuiTulisan(item.id)"
                class="btn-success text-xs px-4 py-2 shadow-sm font-semibold"
                title="Setujui dalam satu klik dan terbitkan langsung"
              >
                <span v-if="processingId === item.id" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin mr-1.5"></span>
                ✓ Setujui & Terbitkan
              </button>
            </div>
          </div>
        </article>
      </div>
    </DataState>

    <!-- Modal Catatan Editor (Wajib untuk Tolak / Revisi) -->
    <div
      v-if="modalState.open"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink/50 backdrop-blur-sm"
      role="dialog"
      aria-modal="true"
    >
      <div class="card w-full max-w-lg p-6 sm:p-8 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between pb-4 border-b border-card-border">
          <div>
            <h3 class="font-heading text-lg font-bold text-ink">
              {{ modalState.action === 'revisi' ? 'Minta Revisi Naskah' : 'Tolak Naskah' }}
            </h3>
            <p class="text-xs text-ink-muted mt-0.5">
              {{ modalState.action === 'revisi' ? 'Berikan catatan bagian mana yang perlu diperbaiki oleh penulis.' : 'Jelaskan alasan penolakan naskah ini secara jelas.' }}
            </p>
          </div>
          <button
            type="button"
            @click="modalState.open = false"
            class="p-2 text-ink-muted hover:text-ink rounded-lg focus-visible:ring-2 focus-visible:ring-indigo-brand"
            aria-label="Tutup modal"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="submitCatatan" class="mt-4 space-y-4">
          <div v-if="modalError" class="p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl" role="alert">
            {{ modalError }}
          </div>

          <div>
            <label for="editor-catatan" class="label">
              Catatan Editor (Wajib Diisi) <span class="text-rose-500">*</span>
            </label>
            <textarea
              id="editor-catatan"
              v-model="modalState.catatan"
              required
              rows="4"
              maxlength="1000"
              placeholder="Tuliskan catatan telaah edukatif untuk penulis..."
              class="input resize-none"
            ></textarea>
            <p class="text-[11px] text-ink-subtle mt-1">Catatan ini akan dikirimkan langsung ke notifikasi mahasiswa.</p>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4 border-t border-card-border">
            <button type="button" @click="modalState.open = false" class="btn-secondary text-xs px-4">Batal</button>
            <button
              type="submit"
              :disabled="modalSubmitting"
              class="text-xs px-5"
              :class="modalState.action === 'revisi' ? 'btn-primary' : 'btn-danger'"
            >
              <span v-if="modalSubmitting" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin mr-1.5"></span>
              {{ modalState.action === 'revisi' ? 'Kirim Permintaan Revisi' : 'Tolak Naskah Ini' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: ['dosen'],
})

const auth = useAuthStore()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase as string

const isLoading = ref(true)
const errorMsg = ref('')
const successMsg = ref('')
const antreanList = ref<any[]>([])
const processingId = ref<number | null>(null)

const modalState = reactive({
  open: false,
  tulisanId: 0,
  action: 'revisi' as 'revisi' | 'tolak',
  catatan: '',
})
const modalSubmitting = ref(false)
const modalError = ref('')

async function fetchAntrean() {
  if (!auth.token) return
  isLoading.value = true
  errorMsg.value = ''
  try {
    const res = await $fetch<any>(`${apiBase}/dosen/antrean?with_isi=1`, {
      headers: {
        Authorization: `Bearer ${auth.token}`,
        Accept: 'application/json',
      },
    })
    antreanList.value = res.data || []
  } catch (err: any) {
    errorMsg.value = err.data?.message || 'Gagal memuat antrean naskah.'
  } finally {
    isLoading.value = false
  }
}

async function setujuiTulisan(id: number) {
  processingId.value = id
  successMsg.value = ''
  try {
    await $fetch(`${apiBase}/dosen/tulisan/${id}/setujui`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${auth.token}`,
        Accept: 'application/json',
      },
    })
    successMsg.value = 'Naskah berhasil disetujui dan telah langsung terbit di katalog publik!'
    antreanList.value = antreanList.value.filter((item) => item.id !== id)
  } catch (err: any) {
    alert(err.data?.message || 'Gagal menyetujui tulisan.')
  } finally {
    processingId.value = null
  }
}

function bukaModalCatatan(item: any, action: 'revisi' | 'tolak') {
  modalState.open = true
  modalState.tulisanId = item.id
  modalState.action = action
  modalState.catatan = ''
  modalError.value = ''
}

async function submitCatatan() {
  if (modalSubmitting.value) return
  if (!modalState.catatan.trim()) {
    modalError.value = 'Catatan editor wajib diisi.'
    return
  }

  modalSubmitting.value = true
  modalError.value = ''

  const endpoint = modalState.action === 'revisi' ? 'revisi' : 'tolak'

  try {
    await $fetch(`${apiBase}/dosen/tulisan/${modalState.tulisanId}/${endpoint}`, {
      method: 'POST',
      body: { catatan: modalState.catatan },
      headers: {
        Authorization: `Bearer ${auth.token}`,
        Accept: 'application/json',
      },
    })

    successMsg.value = modalState.action === 'revisi'
      ? 'Permintaan revisi berhasil dikirimkan ke mahasiswa beserta catatan Anda.'
      : 'Naskah telah ditolak beserta catatan alasan penolakan.'

    antreanList.value = antreanList.value.filter((item) => item.id !== modalState.tulisanId)
    modalState.open = false
  } catch (err: any) {
    modalError.value = err.data?.message || 'Gagal memproses tindakan.'
  } finally {
    modalSubmitting.value = false
  }
}

function formatDate(iso: string) {
  if (!iso) return ''
  const d = new Date(iso)
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit',
  })
}

onMounted(() => {
  fetchAntrean()
})
</script>

