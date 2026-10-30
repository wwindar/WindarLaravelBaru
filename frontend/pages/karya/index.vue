<template>
  <div class="max-w-6xl mx-auto px-4 sm:px-6 py-12 space-y-8">
    <div>
      <h1 class="text-3xl font-bold text-white">Karya Terbit</h1>
      <p class="text-slate-400 text-sm mt-1">Puisi, cerpen mini, dan esai pilihan redaksi</p>
    </div>

    <!-- Filter -->
    <div class="flex flex-col sm:flex-row gap-3">
      <input v-model="filter.search" @input="debouncedFetch" type="text" placeholder="Cari karya atau nama penulis..." class="input flex-1" />
      <select v-model="filter.jenis" @change="fetchKarya" class="input sm:w-40">
        <option value="">Semua Jenis</option>
        <option value="puisi">Puisi</option>
        <option value="cerpen">Cerpen</option>
        <option value="esai">Esai</option>
      </select>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div v-for="i in 4" :key="i" class="card p-6 animate-pulse space-y-3">
        <div class="h-4 bg-slate-800 rounded w-1/4"></div>
        <div class="h-6 bg-slate-800 rounded w-3/4"></div>
        <div class="h-20 bg-slate-800 rounded w-full"></div>
      </div>
    </div>

    <div v-else-if="karyaList.length === 0" class="card py-16 text-center text-slate-500">
      Karya sastra tidak ditemukan.
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <NuxtLink
        v-for="k in karyaList"
        :key="k.id"
        :to="`/karya/${k.slug}`"
        class="card p-6 hover:border-amber-500/20 hover:bg-slate-800/40 transition group flex flex-col justify-between"
      >
        <div>
          <div class="flex items-center justify-between mb-3">
            <span class="badge-purple capitalize">{{ k.jenis }}</span>
            <span class="text-xs text-amber-500/60 font-mono">{{ k.nomor_kiriman }}</span>
          </div>
          <h2 class="text-xl font-bold text-white group-hover:text-amber-400 transition leading-snug">
            {{ k.judul }}
          </h2>
          <p class="text-xs text-slate-400 mt-1">Oleh {{ k.nama_penulis }}</p>
          <p class="text-sm text-slate-400 mt-4 leading-relaxed line-clamp-4 font-mono">
            {{ k.isi }}
          </p>
        </div>
        <div class="pt-4 border-t border-slate-800 mt-6 flex justify-between items-center text-xs text-slate-500">
          <span>Baca selengkapnya →</span>
        </div>
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
const filter = reactive({ search: '', jenis: '' })
const karyaList = ref<any[]>([])
const meta = ref<any>({ current_page: 1, last_page: 1 })
const loading = ref(false)

async function fetchKarya(page = 1) {
  loading.value = true
  try {
    const params = new URLSearchParams({ page: String(page), per_page: '8' })
    if (filter.search) params.append('search', filter.search)
    if (filter.jenis) params.append('jenis', filter.jenis)
    const res = await request<{ data: any[]; meta: any }>(`/karya?${params}`)
    karyaList.value = res.data
    meta.value = res.meta
  } catch {
    karyaList.value = []
  } finally {
    loading.value = false
  }
}

let timer: ReturnType<typeof setTimeout>
const debouncedFetch = () => {
  clearTimeout(timer)
  timer = setTimeout(() => fetchKarya(), 350)
}

function changePage(page: number) {
  fetchKarya(page)
}

onMounted(() => fetchKarya())
</script>

