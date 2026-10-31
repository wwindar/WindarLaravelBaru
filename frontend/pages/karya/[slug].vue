<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-ink-muted mb-6" aria-label="Breadcrumb">
      <NuxtLink to="/" class="hover:text-indigo-brand">Beranda</NuxtLink>
      <span>/</span>
      <NuxtLink to="/karya" class="hover:text-indigo-brand">Karya Sastra</NuxtLink>
      <span>/</span>
      <span class="text-ink truncate font-medium max-w-xs">{{ karya?.judul || 'Detail Karya' }}</span>
    </nav>

    <!-- 4 Kondisi State -->
    <DataState
      :loading="isLoading"
      :error="errorMessage"
      :empty="!karya && !isLoading && !errorMessage"
      empty-title="Karya tidak ditemukan"
      empty-description="Karya ini mungkin belum disetujui, draf, atau tautan yang Anda tuju salah."
      @retry="fetchDetail"
    >
      <template #empty-action>
        <NuxtLink to="/karya" class="btn-primary text-xs px-4 py-2">
          Kembali ke Katalog
        </NuxtLink>
      </template>

      <article v-if="karya" class="card p-6 sm:p-12 space-y-8">
        <!-- Metadata Header -->
        <header class="space-y-4 pb-6 border-b border-card-border">
          <div class="flex flex-wrap items-center gap-2">
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-soft text-indigo-brand capitalize">
              {{ karya.jenis_label || karya.jenis }}
            </span>
            <span v-if="karya.kategori" class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
              Rubrik: {{ karya.kategori.nama }}
            </span>
            <StatusBadge :status="karya.status" :label="karya.status_label" />
          </div>

          <h1 class="font-heading text-2xl sm:text-4xl font-extrabold text-ink leading-tight">
            {{ karya.judul }}
          </h1>

          <div class="flex flex-wrap items-center justify-between gap-4 pt-2 text-xs text-ink-muted">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-indigo-brand/10 text-indigo-brand font-bold flex items-center justify-center font-heading text-sm">
                {{ karya.penulis?.name?.charAt(0) || 'P' }}
              </div>
              <div>
                <span class="font-bold text-ink block">{{ karya.penulis?.name }}</span>
                <span class="text-ink-subtle">@{{ karya.penulis?.username || 'penulis' }} • Diterbitkan {{ formatDate(karya.diterbitkan_pada) }}</span>
              </div>
            </div>

            <!-- Tombol Aksi: Bagikan & Laporkan -->
            <div class="flex items-center gap-2">
              <button
                type="button"
                @click="shareKarya"
                class="btn-secondary text-xs px-3.5 py-1.5 flex items-center gap-1.5"
                title="Bagikan tautan karya"
              >
                <svg class="w-4 h-4 text-indigo-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                </svg>
                <span>{{ copied ? 'Tersalin!' : 'Bagikan' }}</span>
              </button>

              <button
                type="button"
                @click="showReportModal = true"
                class="btn-ghost text-xs px-3.5 py-1.5 text-rose-600 hover:text-rose-700 hover:bg-rose-50 flex items-center gap-1.5"
                title="Laporkan karya ini jika ada indikasi plagiarisme atau pelanggaran"
              >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Laporkan tulisan</span>
              </button>
            </div>
          </div>
        </header>

        <!-- Ringkasan / Epigraf jika ada -->
        <blockquote v-if="karya.ringkasan" class="p-4 rounded-xl bg-indigo-soft/30 border-l-4 border-indigo-brand text-xs sm:text-sm text-ink-muted italic">
          "{{ karya.ringkasan }}"
        </blockquote>

        <!-- Naskah Isi Utama -->
        <div class="prose max-w-none text-ink text-sm sm:text-base leading-relaxed whitespace-pre-line font-sans select-text">
          {{ karya.isi }}
        </div>

        <!-- Footer Karya -->
        <footer class="pt-8 border-t border-card-border flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-ink-muted">
          <NuxtLink to="/karya" class="text-indigo-brand hover:underline font-semibold flex items-center gap-1">
            ← Kembali ke Katalog Karya
          </NuxtLink>
          <div class="flex items-center gap-2">
            <span>Diterbitkan di portal <strong>Aksara Kampus</strong></span>
          </div>
        </footer>
      </article>
    </DataState>

    <!-- Modal Pelaporan Tulisan -->
    <ReportModal
      v-if="karya"
      v-model="showReportModal"
      :tulisan-id="karya.id"
    />
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase as string

const slug = computed(() => route.params.slug as string)
const isLoading = ref(true)
const errorMessage = ref('')
const karya = ref<any>(null)
const copied = ref(false)
const showReportModal = ref(false)

async function fetchDetail() {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const res = await $fetch<any>(`${apiBase}/tulisan/${slug.value}`)
    karya.value = res.data
  } catch (err: any) {
    errorMessage.value = err.data?.message || 'Naskah karya tidak ditemukan atau belum berstatus terbit.'
  } finally {
    isLoading.value = false
  }
}

function shareKarya() {
  if (navigator.share) {
    navigator.share({
      title: karya.value?.judul,
      text: karya.value?.ringkasan || 'Baca karya ini di Aksara Kampus',
      url: window.location.href,
    }).catch(() => {})
  } else if (navigator.clipboard) {
    navigator.clipboard.writeText(window.location.href)
    copied.value = true
    setTimeout(() => (copied.value = false), 2000)
  }
}

function formatDate(iso: string) {
  if (!iso) return ''
  const d = new Date(iso)
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}

onMounted(() => {
  fetchDetail()
})
</script>
