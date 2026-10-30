<template>
  <div class="max-w-6xl mx-auto px-4 sm:px-6 py-12 space-y-8">
    <div>
      <h1 class="text-3xl font-bold text-white">Agenda Sastra</h1>
      <p class="text-slate-400 text-sm mt-1">Jadwal kegiatan lomba, bedah buku, panggung puisi terbuka, dan workshop literasi</p>
    </div>

    <!-- Filter -->
    <div class="flex flex-col sm:flex-row gap-3">
      <input v-model="filter.search" @input="debouncedFetch" type="text" placeholder="Cari nama kegiatan..." class="input flex-1" />
      <select v-model="filter.waktu" @change="fetchAgenda" class="input sm:w-44">
        <option value="">Semua Waktu</option>
        <option value="mendatang">Mendatang</option>
        <option value="lampau">Telah Selesai</option>
      </select>
      <select v-model="filter.jenis" @change="fetchAgenda" class="input sm:w-44">
        <option value="">Semua Jenis</option>
        <option value="bedah_buku">Bedah Buku</option>
        <option value="lomba">Lomba Sastra</option>
        <option value="open_mic">Open Mic</option>
        <option value="workshop">Workshop</option>
      </select>
    </div>

    <!-- Grid -->
    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="i in 3" :key="i" class="card p-6 animate-pulse space-y-3">
        <div class="h-4 bg-slate-800 rounded w-1/3"></div>
        <div class="h-6 bg-slate-800 rounded w-3/4"></div>
        <div class="h-16 bg-slate-800 rounded w-full"></div>
      </div>
    </div>

    <div v-else-if="agendaList.length === 0" class="card py-16 text-center text-slate-500">
      Tidak ada kegiatan sastra yang cocok.
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <NuxtLink
        v-for="a in agendaList"
        :key="a.id"
        :to="`/agenda/${a.slug}`"
        class="card p-6 hover:border-amber-500/20 hover:bg-slate-800/40 transition group flex flex-col justify-between"
      >
        <div>
          <div class="flex items-center justify-between mb-3">
            <span class="badge-green capitalize">{{ a.jenis.replace('_', ' ') }}</span>
            <span v-if="a.kuota" class="text-xs text-slate-400">Kuota: {{ a.kuota }}</span>
          </div>
          <h2 class="text-lg font-bold text-white group-hover:text-amber-400 transition leading-snug">
            {{ a.judul }}
          </h2>
          <p class="text-xs text-slate-400 mt-2">📍 {{ a.lokasi }}</p>
          <p class="text-xs text-slate-500 mt-1">
            🗓️ {{ formatTanggal(a.mulai_pada) }}
          </p>
          <p class="text-sm text-slate-400 mt-3 line-clamp-2 leading-relaxed">
            {{ a.deskripsi }}
          </p>
        </div>
        <div class="pt-4 border-t border-slate-800 mt-4 text-xs text-amber-500 font-semibold">
          Detail Kegiatan →
        </div>
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'default' })

const { request } = useApi()
const filter = reactive({ search: '', waktu: 'mendatang', jenis: '' })
const agendaList = ref<any[]>([])
const loading = ref(false)

function formatTanggal(d: string) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

async function fetchAgenda() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filter.search) params.append('search', filter.search)
    if (filter.waktu) params.append('waktu', filter.waktu)
    if (filter.jenis) params.append('jenis', filter.jenis)

    const res = await request<{ data: any[] }>(`/agenda?${params}`)
    agendaList.value = res.data
  } catch {
    agendaList.value = []
  } finally {
    loading.value = false
  }
}

let timer: ReturnType<typeof setTimeout>
const debouncedFetch = () => {
  clearTimeout(timer)
  timer = setTimeout(() => fetchAgenda(), 350)
}

onMounted(() => fetchAgenda())
</script>

