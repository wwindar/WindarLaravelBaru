<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-white">Agenda Kegiatan</h1>
        <p class="text-slate-400 text-sm mt-1">Kelola agenda sastra kampus</p>
      </div>
      <button @click="openModal()" class="btn-primary">
        + Tambah Agenda
      </button>
    </div>

    <div v-if="loading" class="card p-12 text-center text-slate-500">Memuat...</div>

    <div v-else class="card overflow-hidden">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-slate-800 text-xs text-slate-500 uppercase tracking-wider">
            <th class="text-left px-4 py-3">Judul Kegiatan</th>
            <th class="text-left px-4 py-3">Jenis</th>
            <th class="text-left px-4 py-3">Lokasi</th>
            <th class="text-left px-4 py-3">Waktu Mulai</th>
            <th class="text-right px-4 py-3">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/50">
          <tr v-for="a in agendaList" :key="a.id" class="hover:bg-slate-800/30 transition">
            <td class="px-4 py-3 font-semibold text-white">{{ a.judul }}</td>
            <td class="px-4 py-3 capitalize"><span class="badge-green">{{ a.jenis?.replace('_', ' ') }}</span></td>
            <td class="px-4 py-3 text-slate-300 text-xs">{{ a.lokasi }}</td>
            <td class="px-4 py-3 text-slate-400 text-xs">{{ a.mulai_pada }}</td>
            <td class="px-4 py-3 text-right">
              <div class="flex items-center justify-end gap-1.5">
                <button @click="openModal(a)" class="btn-ghost text-xs px-2.5 py-1.5">Edit</button>
                <button @click="hapus(a.id, a.judul)" class="btn-danger text-xs px-2.5 py-1.5">Hapus</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Form -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm overflow-y-auto">
      <div class="card p-6 w-full max-w-lg space-y-4 border border-slate-700 shadow-2xl my-8">
        <h3 class="text-lg font-bold text-white">{{ editId ? 'Edit Agenda' : 'Tambah Agenda' }}</h3>
        
        <div>
          <label class="label">Judul Agenda</label>
          <input v-model="form.judul" type="text" class="input" placeholder="Nama acara..." required />
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="label">Jenis</label>
            <select v-model="form.jenis" class="input">
              <option value="bedah_buku">Bedah Buku</option>
              <option value="lomba">Lomba</option>
              <option value="open_mic">Open Mic</option>
              <option value="workshop">Workshop</option>
            </select>
          </div>
          <div>
            <label class="label">Kuota Peserta</label>
            <input v-model.number="form.kuota" type="number" class="input" placeholder="Opsional" />
          </div>
        </div>

        <div>
          <label class="label">Lokasi</label>
          <input v-model="form.lokasi" type="text" class="input" placeholder="Gedung / Ruang / Link Zoom" required />
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="label">Mulai Pada</label>
            <input v-model="form.mulai_pada" type="datetime-local" class="input" required />
          </div>
          <div>
            <label class="label">Selesai Pada</label>
            <input v-model="form.selesai_pada" type="datetime-local" class="input" required />
          </div>
        </div>

        <div>
          <label class="label">Deskripsi</label>
          <textarea v-model="form.deskripsi" rows="4" class="input font-sans text-sm" placeholder="Rincian agenda..." required></textarea>
        </div>

        <p v-if="errorMsg" class="text-xs text-rose-400">{{ errorMsg }}</p>

        <div class="flex justify-end gap-2 pt-2">
          <button @click="showModal = false" class="btn-ghost text-xs">Batal</button>
          <button @click="simpan" class="btn-primary text-xs" :disabled="modalLoading">
            {{ modalLoading ? 'Menyimpan...' : 'Simpan Agenda' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'admin', middleware: 'admin' })

const { request } = useApi()
const agendaList = ref<any[]>([])
const loading = ref(false)

const showModal = ref(false)
const editId = ref<number | null>(null)
const form = reactive({
  judul: '',
  jenis: 'workshop',
  lokasi: '',
  mulai_pada: '',
  selesai_pada: '',
  deskripsi: '',
  kuota: null as number | null,
})
const modalLoading = ref(false)
const errorMsg = ref('')

async function fetchAgenda() {
  loading.value = true
  try {
    const res = await request<{ data: any[] }>('/admin/agenda')
    agendaList.value = res.data
  } catch {
    agendaList.value = []
  } finally {
    loading.value = false
  }
}

function openModal(a: any = null) {
  errorMsg.value = ''
  if (a) {
    editId.value = a.id
    form.judul = a.judul
    form.jenis = a.jenis
    form.lokasi = a.lokasi
    form.mulai_pada = a.mulai_pada ? a.mulai_pada.replace(' ', 'T').substring(0, 16) : ''
    form.selesai_pada = a.selesai_pada ? a.selesai_pada.replace(' ', 'T').substring(0, 16) : ''
    form.deskripsi = a.deskripsi
    form.kuota = a.kuota
  } else {
    editId.value = null
    form.judul = ''
    form.jenis = 'workshop'
    form.lokasi = ''
    form.mulai_pada = ''
    form.selesai_pada = ''
    form.deskripsi = ''
    form.kuota = null
  }
  showModal.value = true
}

async function simpan() {
  if (!form.judul.trim()) return
  modalLoading.value = true
  errorMsg.value = ''
  try {
    const payload = {
      ...form,
      mulai_pada: form.mulai_pada.replace('T', ' '),
      selesai_pada: form.selesai_pada.replace('T', ' '),
    }
    if (editId.value) {
      await request(`/admin/agenda/${editId.value}`, { method: 'PUT', body: payload })
    } else {
      await request('/admin/agenda', { method: 'POST', body: payload })
    }
    showModal.value = false
    await fetchAgenda()
  } catch (err: any) {
    errorMsg.value = err?.data?.message ?? 'Gagal menyimpan agenda.'
  } finally {
    modalLoading.value = false
  }
}

async function hapus(id: number, judul: string) {
  if (!confirm(`Hapus agenda "${judul}"?`)) return
  try {
    await request(`/admin/agenda/${id}`, { method: 'DELETE' })
    await fetchAgenda()
  } catch (err: any) {
    alert(err?.data?.message ?? 'Gagal menghapus agenda.')
  }
}

onMounted(() => fetchAgenda())
</script>

