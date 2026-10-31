<template>
  <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-card-border">
      <div>
        <NuxtLink to="/dosen" class="text-xs text-indigo-brand hover:underline font-semibold flex items-center gap-1 mb-2">
          ← Kembali ke Antrean Telaah
        </NuxtLink>
        <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-ink">Daftar Laporan Masuk</h1>
        <p class="text-xs sm:text-sm text-ink-muted mt-1">
          Aduan pelanggaran dan dugaan plagiarisme naskah dari pengunjung publik.
        </p>
      </div>

      <!-- Filter Status Laporan -->
      <div class="flex items-center gap-2">
        <label for="laporan-status-filter" class="sr-only">Filter Status Laporan</label>
        <select
          id="laporan-status-filter"
          v-model="statusFilter"
          @change="fetchLaporan"
          class="input text-xs py-2 w-auto"
        >
          <option value="">Semua Status</option>
          <option value="menunggu">Menunggu</option>
          <option value="ditinjau">Sedang Ditinjau</option>
          <option value="selesai">Selesai</option>
        </select>
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
      :empty="laporanList.length === 0"
      empty-title="Tidak ada laporan"
      empty-description="Belum ada laporan pelanggaran atau plagiarisme yang masuk dari pengunjung publik."
      @retry="fetchLaporan"
    >
      <div class="space-y-4">
        <div
          v-for="item in laporanList"
          :key="item.id"
          class="card p-6 space-y-4 hover:border-indigo-brand/30 transition"
        >
          <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-card-border">
            <div class="flex items-center gap-2">
              <span
                class="px-2.5 py-0.5 rounded-full text-xs font-semibold"
                :class="statusBadgeClass(item.status)"
              >
                Status: {{ item.status_label || item.status }}
              </span>
              <span class="text-xs text-ink-subtle">
                Dilaporkan {{ formatDate(item.created_at) }}
              </span>
            </div>

            <!-- Ubah status laporan -->
            <div class="flex items-center gap-2">
              <label :for="`status-select-${item.id}`" class="text-xs text-ink-muted">Ubah Status:</label>
              <select
                :id="`status-select-${item.id}`"
                :value="item.status"
                @change="updateStatus(item.id, ($event.target as HTMLSelectElement).value)"
                class="input text-xs py-1 px-2.5 w-auto"
              >
                <option value="menunggu">Menunggu</option>
                <option value="ditinjau">Sedang Ditinjau</option>
                <option value="selesai">Selesai</option>
              </select>
            </div>
          </div>

          <div>
            <span class="text-xs text-ink-muted block mb-1">Naskah yang Dilaporkan:</span>
            <h3 class="font-heading text-lg font-bold text-ink">
              {{ item.tulisan_judul || 'Naskah #' + item.tulisan_id }}
            </h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs p-3.5 rounded-xl bg-canvas border border-card-border">
            <div>
              <span class="text-ink-subtle block">Nama Pelapor:</span>
              <span class="font-semibold text-ink">{{ item.nama_pelapor }}</span>
            </div>
            <div>
              <span class="text-ink-subtle block">Email Pelapor:</span>
              <span class="font-semibold text-ink">{{ item.email_pelapor }}</span>
            </div>
          </div>

          <div>
            <span class="label mb-1">Alasan Aduan / Indikasi:</span>
            <p class="text-xs sm:text-sm text-ink-muted leading-relaxed whitespace-pre-line p-3 rounded-xl bg-rose-50/50 border border-rose-100 text-rose-950">
              {{ item.alasan }}
            </p>
          </div>
        </div>
      </div>
    </DataState>
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
const statusFilter = ref('')
const laporanList = ref<any[]>([])

async function fetchLaporan() {
  if (!auth.token) return
  isLoading.value = true
  errorMsg.value = ''

  const params: Record<string, any> = {}
  if (statusFilter.value) params.status = statusFilter.value

  try {
    const res = await $fetch<any>(`${apiBase}/dosen/laporan`, {
      params,
      headers: {
        Authorization: `Bearer ${auth.token}`,
        Accept: 'application/json',
      },
    })
    laporanList.value = res.data || []
  } catch (err: any) {
    errorMsg.value = err.data?.message || 'Gagal memuat daftar laporan masuk.'
  } finally {
    isLoading.value = false
  }
}

async function updateStatus(id: number, newStatus: string) {
  try {
    await $fetch(`${apiBase}/dosen/laporan/${id}/status`, {
      method: 'PATCH',
      body: { status: newStatus },
      headers: {
        Authorization: `Bearer ${auth.token}`,
        Accept: 'application/json',
      },
    })
    successMsg.value = 'Status laporan berhasil diperbarui.'
    const item = laporanList.value.find((l) => l.id === id)
    if (item) {
      item.status = newStatus
      item.status_label = newStatus === 'selesai' ? 'Selesai' : newStatus === 'ditinjau' ? 'Sedang Ditinjau' : 'Menunggu'
    }
  } catch (err: any) {
    alert(err.data?.message || 'Gagal memperbarui status laporan.')
  }
}

function statusBadgeClass(st: string) {
  switch (st) {
    case 'selesai':
      return 'bg-emerald-100 text-emerald-800 border border-emerald-300'
    case 'ditinjau':
      return 'bg-amber-100 text-amber-800 border border-amber-300'
    default:
      return 'bg-rose-100 text-rose-800 border border-rose-300'
  }
}

function formatDate(iso: string) {
  if (!iso) return ''
  const d = new Date(iso)
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

onMounted(() => {
  fetchLaporan()
})
</script>

