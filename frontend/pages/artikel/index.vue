<template>
  <div class="max-w-6xl mx-auto px-4 sm:px-6 py-12 space-y-8">
    <div>
      <h1 class="text-3xl font-bold text-white">Artikel</h1>
      <p class="text-slate-400 text-sm mt-1">Berita & blog komunitas sastra kampus</p>
    </div>

    <!-- Filter -->
    <div class="flex flex-col sm:flex-row gap-3">
      <input v-model="filter.search" @input="debouncedFetch" type="text" placeholder="Cari artikel..." class="input flex-1" />
      <select v-model="filter.jenis" @change="fetchArtikel" class="input sm:w-36">
        <option value="">Semua</option>
        <option value="berita">Berita</option>
        <option value="blog">Blog</option>
      </select>
    </div>

    <!-- Grid -->
    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="i in 6" :key="i" class="card p-5 animate-pulse space-y-3">
        <div class="h-3 bg-slate-800 rounded w-1/3" />
        <div class="h-5 bg-slate-800 rounded w-3/4" />
        <div class="h-12 bg-slate-800 rounded w-full" />
      </div>
    </div>

    <div v-else-if="artikel.length === 0" class="card py-16 text-center text-slate-500">Artikel tidak ditemukan.</div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <NuxtLink
        v-for="art in artikel"
        :key="art.id"
        :to="`/artikel/${art.slug}`"
        class="card p-5 hover:border-amber-500/20 hover:bg-slate-800/40 transition group flex flex-col"
      >
        <div class="flex items-center gap-2 mb-3">
          <span :class="art.jenis === 'berita' ? 'badge-amber' : 'badge-blue'">{{ art.jenis }}</span>
          <span v-if="art.kategori" class="text-xs text-slate-500">{{ art.kategori.nama }}</span>
        </div>
        <h2 class="font-bold text-white group-hover:text-amber-400 transition leading-snug flex-1">{{ art.judul }}</h2>
        <p class="text-sm text-slate-400 mt-2 leading-relaxed line-clamp-2">{{ art.ringkasan }}</p>
        <p class="text-xs text-slate-600 mt-3">{{ art.penulis }} · {{ formatTanggal(art.diterbitkan_pada) }}</p>
      </NuxtLink>
    </div>

    <!-- Pagination -->
    <div v-if="meta.last_page > 1" class="flex items-center justify-center gap-2">
      <button @click="changePage(meta.current_page - 1)" :disabled="meta.current_page <= 1" class="btn-ghost px-3 py-2 disabled:opacity-30">← Sebelumnya</button>
      <span class="text-sm text-slate-500">{{ meta.current_page }} / {{ meta.last_page }}</span>
      <button @click="changePage(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page" class="btn-ghost px-3 py-2 disabled:opacity-30">Berikutnya →</button>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'default' })

const { request } = useApi()
const filter  = reactive({ search: '', jenis: '' })
const artikel = ref<any[]>([])
const meta    = ref<any>({ current_page: 1, last_page: 1 })
const loading = ref(false)

function formatTanggal(d: string) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

async function fetchArtikel(page = 1) {
  loading.value = true
  try {
    const params = new URLSearchParams({ page: String(page), per_page: '9' })
    if (filter.search) params.append('search', filter.search)
    if (filter.jenis)  params.append('jenis', filter.jenis)
    const res = await request<{ data: any[]; meta: any }>(`/artikel?${params}`)
    artikel.value = res.data
    meta.value    = res.meta
  } catch { artikel.value = [] } finally { loading.value = false }
}

let timer: ReturnType<typeof setTimeout>
const debouncedFetch = () => { clearTimeout(timer); timer = setTimeout(() => fetchArtikel(), 350) }
function changePage(page: number) { fetchArtikel(page) }

onMounted(() => fetchArtikel())
</script>

