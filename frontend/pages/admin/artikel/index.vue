<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-white">Artikel</h1>
        <p class="text-slate-400 text-sm mt-1">Kelola berita dan blog Aksara Kampus</p>
      </div>
      <NuxtLink to="/admin/artikel/buat" class="btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tulis Artikel
      </NuxtLink>
    </div>

    <!-- Filter & Search -->
    <div class="card p-4 flex flex-col sm:flex-row gap-3">
      <input v-model="filter.search" @input="debouncedFetch" type="text" placeholder="Cari judul atau penulis..." class="input flex-1" />
      <select v-model="filter.jenis" @change="fetchArtikel" class="input sm:w-36">
        <option value="">Semua Jenis</option>
        <option value="berita">Berita</option>
        <option value="blog">Blog</option>
      </select>
      <select v-model="filter.status" @change="fetchArtikel" class="input sm:w-36">
        <option value="">Semua Status</option>
        <option value="draft">Draft</option>
        <option value="terbit">Terbit</option>
      </select>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex items-center justify-center py-20 text-slate-500">
      <svg class="w-6 h-6 animate-spin mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
      Memuat...
    </div>

    <!-- Table -->
    <div v-else class="card overflow-hidden">
      <div v-if="artikel.length === 0" class="py-16 text-center text-slate-500">
        <svg class="w-10 h-10 mx-auto mb-3 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
        Tidak ada artikel ditemukan
      </div>
      <table v-else class="w-full text-sm">
        <thead>
          <tr class="border-b border-slate-800 text-xs text-slate-500 uppercase tracking-wider">
            <th class="text-left px-4 py-3">Judul</th>
            <th class="text-left px-4 py-3 hidden md:table-cell">Jenis</th>
            <th class="text-left px-4 py-3 hidden lg:table-cell">Kategori</th>
            <th class="text-left px-4 py-3">Status</th>
            <th class="text-right px-4 py-3">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/50">
          <tr v-for="art in artikel" :key="art.id" class="hover:bg-slate-800/30 transition">
            <td class="px-4 py-3">
              <p class="font-medium text-white leading-tight">{{ art.judul }}</p>
              <p class="text-xs text-slate-500 mt-0.5">{{ art.penulis }}</p>
            </td>
            <td class="px-4 py-3 hidden md:table-cell">
              <span :class="art.jenis === 'berita' ? 'badge-amber' : 'badge-blue'">{{ art.jenis }}</span>
            </td>
            <td class="px-4 py-3 hidden lg:table-cell text-slate-400">{{ art.kategori?.nama ?? '-' }}</td>
            <td class="px-4 py-3">
              <span :class="art.status === 'terbit' ? 'badge-green' : 'badge-slate'">{{ art.status }}</span>
            </td>
            <td class="px-4 py-3 text-right">
              <div class="flex items-center justify-end gap-1.5">
                <NuxtLink :to="`/admin/artikel/${art.id}/edit`" class="btn-ghost text-xs px-2.5 py-1.5">Edit</NuxtLink>
                <button @click="hapus(art.id, art.judul)" class="btn-danger text-xs px-2.5 py-1.5">Hapus</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="meta.last_page > 1" class="px-4 py-3 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
        <span>{{ meta.from }}–{{ meta.to }} dari {{ meta.total }} artikel</span>
        <div class="flex gap-1">
          <button @click="changePage(meta.current_page - 1)" :disabled="meta.current_page <= 1" class="btn-ghost px-2.5 py-1.5 disabled:opacity-30">←</button>
          <button @click="changePage(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page" class="btn-ghost px-2.5 py-1.5 disabled:opacity-30">→</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'admin', middleware: 'admin' })

const { request } = useApi()

const filter  = reactive({ search: '', jenis: '', status: '' })
const artikel = ref<any[]>([])
const meta    = ref<any>({ current_page: 1, last_page: 1, from: 0, to: 0, total: 0 })
const loading = ref(false)

async function fetchArtikel(page = 1) {
  loading.value = true
  try {
    const params = new URLSearchParams({ page: String(page), per_page: '15' })
    if (filter.search) params.append('search', filter.search)
    if (filter.jenis)  params.append('jenis', filter.jenis)
    if (filter.status) params.append('status', filter.status)

    const res = await request<{ data: any[]; meta: any }>(`/admin/artikel?${params}`)
    artikel.value = res.data
    meta.value    = res.meta
  } catch { artikel.value = [] } finally { loading.value = false }
}

let timer: ReturnType<typeof setTimeout>
const debouncedFetch = () => { clearTimeout(timer); timer = setTimeout(() => fetchArtikel(), 350) }

function changePage(page: number) { fetchArtikel(page) }

async function hapus(id: number, judul: string) {
  if (!confirm(`Hapus artikel "${judul}"?`)) return
  try {
    await request(`/admin/artikel/${id}`, { method: 'DELETE' })
    artikel.value = artikel.value.filter(a => a.id !== id)
  } catch { alert('Gagal menghapus artikel.') }
}

onMounted(() => fetchArtikel())
</script>

