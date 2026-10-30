<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-2xl font-bold text-white">Karya Masuk</h1>
      <p class="text-slate-400 text-sm mt-1">Tinjau dan kurasi naskah kiriman penulis</p>
    </div>

    <!-- Filter -->
    <div class="card p-4 flex flex-col sm:flex-row gap-3">
      <input v-model="filter.search" @input="debouncedFetch" type="text" placeholder="Cari judul atau penulis..." class="input flex-1" />
      <select v-model="filter.status" @change="fetchKarya" class="input sm:w-44">
        <option value="">Semua Status</option>
        <option value="dikirim">Dikirim</option>
        <option value="ditinjau">Sedang Ditinjau</option>
        <option value="perlu_revisi">Perlu Revisi</option>
        <option value="terbit">Terbit</option>
        <option value="ditolak">Ditolak</option>
      </select>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex items-center justify-center py-20 text-slate-500">
      <svg class="w-6 h-6 animate-spin mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
      Memuat...
    </div>

    <!-- Table -->
    <div v-else class="card overflow-hidden">
      <div v-if="karya.length === 0" class="py-16 text-center text-slate-500">Tidak ada karya ditemukan.</div>
      <table v-else class="w-full text-sm">
        <thead>
          <tr class="border-b border-slate-800 text-xs text-slate-500 uppercase tracking-wider">
            <th class="text-left px-4 py-3">Nomor / Judul</th>
            <th class="text-left px-4 py-3 hidden md:table-cell">Jenis</th>
            <th class="text-left px-4 py-3 hidden sm:table-cell">Penulis</th>
            <th class="text-left px-4 py-3">Status</th>
            <th class="text-right px-4 py-3">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/50">
          <tr v-for="k in karya" :key="k.id" class="hover:bg-slate-800/30 transition">
            <td class="px-4 py-3">
              <p class="font-medium text-white leading-tight">{{ k.judul }}</p>
              <p class="text-xs text-amber-500/70 font-mono mt-0.5">{{ k.nomor_kiriman }}</p>
            </td>
            <td class="px-4 py-3 hidden md:table-cell">
              <span class="badge-purple capitalize">{{ k.jenis }}</span>
            </td>
            <td class="px-4 py-3 hidden sm:table-cell text-slate-400">{{ k.nama_penulis }}</td>
            <td class="px-4 py-3">
              <span :class="statusBadge(k.status)">{{ k.status_label }}</span>
            </td>
            <td class="px-4 py-3 text-right">
              <div class="flex items-center justify-end gap-1.5">
                <NuxtLink :to="`/admin/karya/${k.id}`" class="btn-ghost text-xs px-2.5 py-1.5">Detail</NuxtLink>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <!-- Pagination -->
      <div v-if="meta.last_page > 1" class="px-4 py-3 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
        <span>{{ meta.from }}–{{ meta.to }} dari {{ meta.total }} karya</span>
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
const filter = reactive({ search: '', status: '' })
const karya  = ref<any[]>([])
const meta   = ref<any>({ current_page: 1, last_page: 1, from: 0, to: 0, total: 0 })
const loading = ref(false)

async function fetchKarya(page = 1) {
  loading.value = true
  try {
    const params = new URLSearchParams({ page: String(page), per_page: '15' })
    if (filter.search) params.append('search', filter.search)
    if (filter.status) params.append('status', filter.status)
    const res = await request<{ data: any[]; meta: any }>(`/admin/karya?${params}`)
    karya.value = res.data
    meta.value  = res.meta
  } catch { karya.value = [] } finally { loading.value = false }
}

let timer: ReturnType<typeof setTimeout>
const debouncedFetch = () => { clearTimeout(timer); timer = setTimeout(() => fetchKarya(), 350) }
function changePage(page: number) { fetchKarya(page) }

function statusBadge(status: string) {
  const map: Record<string, string> = {
    dikirim: 'badge-blue',
    ditinjau: 'badge-amber',
    perlu_revisi: 'badge-red',
    terbit: 'badge-green',
    ditolak: 'badge-slate',
  }
  return map[status] ?? 'badge-slate'
}

onMounted(() => fetchKarya())
</script>

