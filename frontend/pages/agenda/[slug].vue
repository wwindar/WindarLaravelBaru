<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 py-12 space-y-6">
    <div class="flex items-center gap-3">
      <NuxtLink to="/agenda" class="text-slate-400 hover:text-white transition">
        ← Kembali ke Agenda
      </NuxtLink>
    </div>

    <div v-if="loading" class="card p-12 text-center text-slate-500 animate-pulse">
      Memuat informasi agenda...
    </div>

    <div v-else-if="!agenda" class="card p-12 text-center text-slate-500">
      Kegiatan tidak ditemukan.
    </div>

    <article v-else class="card p-6 sm:p-10 space-y-6">
      <div class="space-y-3 border-b border-slate-800 pb-6">
        <span class="badge-green capitalize">{{ agenda.jenis?.replace('_', ' ') }}</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">
          {{ agenda.judul }}
        </h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-400 pt-2">
          <div class="p-3 bg-slate-950 rounded-lg border border-slate-800">
            <span class="block text-slate-500 font-semibold mb-0.5">Waktu Mulai:</span>
            <span class="text-slate-200">{{ formatTanggalLengkap(agenda.mulai_pada) }}</span>
          </div>
          <div class="p-3 bg-slate-950 rounded-lg border border-slate-800">
            <span class="block text-slate-500 font-semibold mb-0.5">Waktu Selesai:</span>
            <span class="text-slate-200">{{ formatTanggalLengkap(agenda.selesai_pada) }}</span>
          </div>
          <div class="p-3 bg-slate-950 rounded-lg border border-slate-800 sm:col-span-2">
            <span class="block text-slate-500 font-semibold mb-0.5">Lokasi Pelaksanaan:</span>
            <span class="text-amber-400 font-semibold">📍 {{ agenda.lokasi }}</span>
            <span v-if="agenda.kuota" class="ml-3 text-slate-400">(Kuota peserta: {{ agenda.kuota }} orang)</span>
          </div>
        </div>
      </div>

      <div class="space-y-3">
        <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-400">Deskripsi Kegiatan</h3>
        <p class="text-slate-300 text-base leading-relaxed whitespace-pre-line">
          {{ agenda.deskripsi }}
        </p>
      </div>
    </article>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'default' })

const route = useRoute()
const { request } = useApi()

const agenda = ref<any>(null)
const loading = ref(true)

function formatTanggalLengkap(d: string) {
  if (!d) return '-'
  return new Date(d).toLocaleString('id-ID', { dateStyle: 'full', timeStyle: 'short' })
}

onMounted(async () => {
  try {
    const res = await request<{ data: any }>(`/agenda/${route.params.slug}`)
    agenda.value = res.data
  } catch {
    agenda.value = null
  } finally {
    loading.value = false
  }
})
</script>

