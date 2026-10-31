<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-ink-muted mb-6" aria-label="Breadcrumb">
      <NuxtLink to="/" class="hover:text-indigo-brand">Beranda</NuxtLink>
      <span>/</span>
      <NuxtLink to="/agenda" class="hover:text-indigo-brand">Agenda Kegiatan</NuxtLink>
      <span>/</span>
      <span class="text-ink truncate font-medium max-w-xs">{{ agenda?.judul || 'Detail Acara' }}</span>
    </nav>

    <!-- 4 Kondisi State -->
    <DataState
      :loading="loading"
      :error="errorMsg"
      :empty="!agenda && !loading && !errorMsg"
      empty-title="Agenda Tidak Ditemukan"
      empty-description="Jadwal kegiatan sastra yang Anda cari tidak tersedia atau tautan salah."
      @retry="fetchDetail"
    >
      <template #empty-action>
        <NuxtLink to="/agenda" class="btn-primary text-xs px-4 py-2">
          Kembali ke Agenda
        </NuxtLink>
      </template>

      <article v-if="agenda" class="card p-6 sm:p-12 space-y-8">
        <header class="space-y-4 pb-6 border-b border-card-border">
          <div class="flex flex-wrap items-center gap-2">
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-soft text-indigo-brand capitalize">
              {{ formatJenis(agenda.jenis) }}
            </span>
            <span v-if="agenda.kuota" class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
              Kapasitas: {{ agenda.kuota }} Orang
            </span>
          </div>

          <h1 class="font-heading text-2xl sm:text-4xl font-extrabold text-ink leading-tight">
            {{ agenda.judul }}
          </h1>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-canvas border border-card-border text-xs text-ink">
            <div>
              <span class="text-ink-subtle block mb-0.5">Waktu Pelaksanaan:</span>
              <p class="font-semibold">{{ formatWaktu(agenda.mulai_pada) }}</p>
              <p v-if="agenda.selesai_pada" class="text-ink-muted">s.d. {{ formatWaktu(agenda.selesai_pada) }}</p>
            </div>
            <div>
              <span class="text-ink-subtle block mb-0.5">Lokasi / Tempat Acara:</span>
              <p class="font-semibold">{{ agenda.lokasi }}</p>
            </div>
          </div>
        </header>

        <div>
          <h2 class="font-heading text-base font-bold text-ink mb-3">Tentang Kegiatan</h2>
          <div class="prose max-w-none text-ink text-sm sm:text-base leading-relaxed whitespace-pre-line font-sans">
            {{ agenda.deskripsi }}
          </div>
        </div>

        <footer class="pt-8 border-t border-card-border flex items-center justify-between text-xs text-ink-muted">
          <NuxtLink to="/agenda" class="text-indigo-brand hover:underline font-semibold">
            ← Kembali ke Seluruh Agenda
          </NuxtLink>
          <NuxtLink to="/register" class="btn-primary text-xs px-4 py-2" v-if="!auth.isLoggedIn">
            Gabung Civitas Kampus
          </NuxtLink>
        </footer>
      </article>
    </DataState>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const auth = useAuthStore()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase as string

const slug = computed(() => route.params.slug as string)
const agenda = ref<any>(null)
const loading = ref(true)
const errorMsg = ref('')

function formatJenis(j: string) {
  switch (j) {
    case 'bedah_buku': return 'Bedah Buku'
    case 'open_mic': return 'Panggung Puisi Terbuka'
    case 'workshop': return 'Workshop Literasi'
    case 'lomba': return 'Sayembara Sastra'
    default: return j
  }
}

function formatWaktu(iso: string) {
  if (!iso) return ''
  const d = new Date(iso)
  return d.toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

async function fetchDetail() {
  loading.value = true
  errorMsg.value = ''
  try {
    const res = await $fetch<any>(`${apiBase}/agenda/${slug.value}`)
    agenda.value = res.data
  } catch (err: any) {
    errorMsg.value = err.data?.message || 'Kegiatan sastra tidak ditemukan.'
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchDetail())
</script>
