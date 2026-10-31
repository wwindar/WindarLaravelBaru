<template>
  <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-8">
    <!-- Header Dasbor Mahasiswa -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-card-border">
      <div>
        <span class="text-xs font-bold text-indigo-brand uppercase tracking-wider block mb-1">Ruang Penulis Mahasiswa</span>
        <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-ink">Tulisan Saya</h1>
        <p class="text-xs sm:text-sm text-ink-muted mt-1">
          Kelola naskah, pantau status kurasi dosen, dan ajukan karya untuk publikasi.
        </p>
      </div>
      <div class="flex items-center gap-2">
        <NuxtLink to="/mahasiswa/tulisan/baru" class="btn-primary text-xs px-4 py-2.5">
          + Tulis Karya Baru
        </NuxtLink>
      </div>
    </div>

    <!-- Alert Sukses Aksi -->
    <div v-if="successMsg" class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs flex items-center justify-between" role="status">
      <span>{{ successMsg }}</span>
      <button @click="successMsg = ''" class="text-emerald-700 font-bold ml-2">✕</button>
    </div>

    <!-- Filter Tab Status -->
    <div class="flex flex-wrap items-center gap-2">
      <button
        v-for="tab in tabs"
        :key="tab.value"
        type="button"
        @click="activeTab = tab.value"
        class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition"
        :class="activeTab === tab.value ? 'bg-indigo-brand text-white shadow-sm' : 'bg-white border border-card-border text-ink-muted hover:text-ink'"
      >
        {{ tab.label }}
        <span v-if="tab.count !== undefined" class="ml-1 opacity-75 font-normal">({{ tab.count }})</span>
      </button>
    </div>

    <!-- 4 Kondisi State -->
    <DataState
      :loading="isLoading"
      :error="errorMsg"
      :empty="filteredList.length === 0"
      empty-title="Belum ada tulisan"
      empty-description="Mulai tuangkan ide sastra Anda dan simpan sebagai draf atau ajukan ke dewan redaksi."
      @retry="fetchMyTulisan"
    >
      <template #empty-action>
        <NuxtLink to="/mahasiswa/tulisan/baru" class="btn-primary text-xs px-5 py-2.5">
          + Buat Tulisan Baru
        </NuxtLink>
      </template>

      <!-- Table / Card List Tulisan -->
      <div class="space-y-4">
        <div
          v-for="item in filteredList"
          :key="item.id"
          class="card p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 hover:border-indigo-brand/30 transition"
        >
          <div class="space-y-2 flex-1">
            <div class="flex flex-wrap items-center gap-2">
              <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-soft text-indigo-brand capitalize">
                {{ item.jenis_label || item.jenis }}
              </span>
              <StatusBadge :status="item.status" :label="item.status_label" />
              <span class="text-[11px] text-ink-subtle">
                Diperbarui {{ formatDate(item.updated_at) }}
              </span>
            </div>

            <h2 class="font-heading text-lg font-bold text-ink">
              {{ item.judul }}
            </h2>

            <p class="text-xs text-ink-muted line-clamp-2 leading-relaxed">
              {{ item.ringkasan || 'Belum ada ringkasan.' }}
            </p>

            <!-- Catatan Editor Dosen jika Revisi atau Ditolak -->
            <div v-if="item.catatan_editor && (item.status === 'perlu_revisi' || item.status === 'ditolak')" class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 mt-2">
              <span class="font-bold block mb-0.5">Catatan Dosen Editor:</span>
              <p class="italic">"{{ item.catatan_editor }}"</p>
            </div>
          </div>

          <!-- Aksi Per Tulisan -->
          <div class="flex flex-wrap items-center gap-2 shrink-0 self-start md:self-center">
            <!-- Tombol Ajukan (hanya saat draf atau perlu_revisi) -->
            <button
              v-if="item.status === 'draf' || item.status === 'perlu_revisi'"
              type="button"
              @click="ajukanPublikasi(item.id)"
              :disabled="submittingId === item.id"
              class="btn-primary text-xs px-3.5 py-2"
              title="Ajukan naskah ke dosen untuk ditinjau"
            >
              <span v-if="submittingId === item.id" class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
              Ajukan Publikasi
            </button>

            <!-- Tombol Edit (hanya saat draf atau perlu_revisi) -->
            <NuxtLink
              v-if="item.status === 'draf' || item.status === 'perlu_revisi'"
              :to="`/mahasiswa/tulisan/${item.id}/edit`"
              class="btn-secondary text-xs px-3 py-2"
            >
              Edit
            </NuxtLink>

            <!-- Tombol Lihat jika sudah terbit -->
            <NuxtLink
              v-if="item.status === 'terbit'"
              :to="`/karya/${item.slug}`"
              class="btn-secondary text-xs px-3 py-2 text-indigo-brand font-semibold"
            >
              Lihat di Publik ↗
            </NuxtLink>

            <!-- Tombol Hapus (hanya saat draf) -->
            <button
              v-if="item.status === 'draf'"
              type="button"
              @click="hapusDraf(item.id)"
              class="btn-ghost text-xs px-2.5 py-2 text-rose-600 hover:text-rose-700 hover:bg-rose-50"
              title="Hapus draf ini"
            >
              Hapus
            </button>
          </div>
        </div>
      </div>
    </DataState>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: ['mahasiswa'],
})

const auth = useAuthStore()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase as string

const isLoading = ref(true)
const errorMsg = ref('')
const successMsg = ref('')
const tulisanList = ref<any[]>([])
const submittingId = ref<number | null>(null)
const activeTab = ref('semua')

const tabs = computed(() => [
  { label: 'Semua Naskah', value: 'semua', count: tulisanList.value.length },
  { label: 'Draf', value: 'draf', count: tulisanList.value.filter((t) => t.status === 'draf').length },
  { label: 'Menunggu', value: 'menunggu', count: tulisanList.value.filter((t) => t.status === 'menunggu').length },
  { label: 'Terbit', value: 'terbit', count: tulisanList.value.filter((t) => t.status === 'terbit').length },
  { label: 'Perlu Revisi', value: 'perlu_revisi', count: tulisanList.value.filter((t) => t.status === 'perlu_revisi').length },
  { label: 'Ditolak', value: 'ditolak', count: tulisanList.value.filter((t) => t.status === 'ditolak').length },
])

const filteredList = computed(() => {
  if (activeTab.value === 'semua') return tulisanList.value
  return tulisanList.value.filter((t) => t.status === activeTab.value)
})

async function fetchMyTulisan() {
  if (!auth.token) return
  isLoading.value = true
  errorMsg.value = ''
  try {
    const res = await $fetch<any>(`${apiBase}/mahasiswa/tulisan`, {
      headers: {
        Authorization: `Bearer ${auth.token}`,
        Accept: 'application/json',
      },
    })
    tulisanList.value = res.data || []
  } catch (err: any) {
    errorMsg.value = err.data?.message || 'Gagal memuat naskah Anda.'
  } finally {
    isLoading.value = false
  }
}

async function ajukanPublikasi(id: number) {
  if (!confirm('Yakin ingin mengajukan tulisan ini untuk publikasi? Naskah akan diperiksa oleh dosen editor.')) {
    return
  }

  submittingId.value = id
  successMsg.value = ''
  try {
    await $fetch(`${apiBase}/mahasiswa/tulisan/${id}/ajukan`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${auth.token}`,
        Accept: 'application/json',
      },
    })
    successMsg.value = 'Naskah berhasil diajukan dan sedang menunggu persetujuan dosen.'
    await fetchMyTulisan()
  } catch (err: any) {
    alert(err.data?.message || 'Gagal mengajukan tulisan.')
  } finally {
    submittingId.value = null
  }
}

async function hapusDraf(id: number) {
  if (!confirm('Hapus draf naskah ini? Tindakan ini tidak dapat dibatalkan.')) return

  try {
    await $fetch(`${apiBase}/mahasiswa/tulisan/${id}`, {
      method: 'DELETE',
      headers: {
        Authorization: `Bearer ${auth.token}`,
        Accept: 'application/json',
      },
    })
    successMsg.value = 'Draf naskah berhasil dihapus.'
    tulisanList.value = tulisanList.value.filter((t) => t.id !== id)
  } catch (err: any) {
    alert(err.data?.message || 'Gagal menghapus draf.')
  }
}

function formatDate(iso: string) {
  if (!iso) return ''
  const d = new Date(iso)
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

onMounted(() => {
  fetchMyTulisan()
})
</script>

