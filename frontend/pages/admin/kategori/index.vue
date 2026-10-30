<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-white">Kategori Artikel</h1>
        <p class="text-slate-400 text-sm mt-1">Kelola rubrik kategori untuk karya dan artikel</p>
      </div>
      <button @click="openModal()" class="btn-primary">
        + Tambah Kategori
      </button>
    </div>

    <div v-if="loading" class="card p-12 text-center text-slate-500">Memuat...</div>

    <div v-else class="card overflow-hidden">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-slate-800 text-xs text-slate-500 uppercase tracking-wider">
            <th class="text-left px-4 py-3">Nama Kategori</th>
            <th class="text-left px-4 py-3">Slug</th>
            <th class="text-right px-4 py-3">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/50">
          <tr v-for="kat in kategoriList" :key="kat.id" class="hover:bg-slate-800/30 transition">
            <td class="px-4 py-3 font-semibold text-white">{{ kat.nama }}</td>
            <td class="px-4 py-3 font-mono text-xs text-amber-500/80">{{ kat.slug }}</td>
            <td class="px-4 py-3 text-right">
              <div class="flex items-center justify-end gap-1.5">
                <button @click="openModal(kat)" class="btn-ghost text-xs px-2.5 py-1.5">Edit</button>
                <button @click="hapus(kat.id, kat.nama)" class="btn-danger text-xs px-2.5 py-1.5">Hapus</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Form -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
      <div class="card p-6 w-full max-w-md space-y-4 border border-slate-700 shadow-2xl">
        <h3 class="text-lg font-bold text-white">{{ editId ? 'Edit Kategori' : 'Tambah Kategori' }}</h3>
        
        <div>
          <label class="label">Nama Kategori</label>
          <input v-model="form.nama" type="text" class="input" placeholder="Misal: Esai Budaya" required />
          <p v-if="errorMsg" class="text-xs text-rose-400 mt-1">{{ errorMsg }}</p>
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button @click="showModal = false" class="btn-ghost text-xs">Batal</button>
          <button @click="simpan" class="btn-primary text-xs" :disabled="modalLoading">
            {{ modalLoading ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'admin', middleware: 'admin' })

const { request } = useApi()
const kategoriList = ref<any[]>([])
const loading = ref(false)

const showModal = ref(false)
const editId = ref<number | null>(null)
const form = reactive({ nama: '' })
const modalLoading = ref(false)
const errorMsg = ref('')

async function fetchKategori() {
  loading.value = true
  try {
    const res = await request<{ data: any[] }>('/admin/kategori')
    kategoriList.value = res.data
  } catch {
    kategoriList.value = []
  } finally {
    loading.value = false
  }
}

function openModal(kat: any = null) {
  errorMsg.value = ''
  if (kat) {
    editId.value = kat.id
    form.nama = kat.nama
  } else {
    editId.value = null
    form.nama = ''
  }
  showModal.value = true
}

async function simpan() {
  if (!form.nama.trim()) return
  modalLoading.value = true
  errorMsg.value = ''
  try {
    if (editId.value) {
      await request(`/admin/kategori/${editId.value}`, { method: 'PUT', body: form })
    } else {
      await request('/admin/kategori', { method: 'POST', body: form })
    }
    showModal.value = false
    await fetchKategori()
  } catch (err: any) {
    errorMsg.value = err?.data?.message ?? 'Gagal menyimpan kategori.'
  } finally {
    modalLoading.value = false
  }
}

async function hapus(id: number, nama: string) {
  if (!confirm(`Hapus kategori "${nama}"?`)) return
  try {
    await request(`/admin/kategori/${id}`, { method: 'DELETE' })
    await fetchKategori()
  } catch (err: any) {
    alert(err?.data?.message ?? 'Gagal menghapus kategori.')
  }
}

onMounted(() => fetchKategori())
</script>

