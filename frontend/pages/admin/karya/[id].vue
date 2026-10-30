<template>
  <div class="max-w-2xl space-y-6">
    <div class="flex items-center gap-3">
      <NuxtLink to="/admin/karya" class="text-slate-400 hover:text-white transition">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      </NuxtLink>
      <h1 class="text-2xl font-bold text-white">Detail Karya</h1>
    </div>

    <div v-if="loading" class="card p-10 text-center text-slate-500">Memuat...</div>
    <div v-else-if="!karya" class="card p-10 text-center text-slate-500">Karya tidak ditemukan.</div>

    <template v-else>
      <!-- Info karya -->
      <div class="card p-6 space-y-4">
        <div class="flex items-start justify-between gap-3">
          <div>
            <h2 class="text-xl font-bold text-white">{{ karya.judul }}</h2>
            <p class="text-sm text-slate-400 mt-1">{{ karya.nama_penulis }} · {{ karya.email }}</p>
          </div>
          <span :class="statusBadge(karya.status)">{{ karya.status_label }}</span>
        </div>
        <div class="flex gap-3 text-xs">
          <span class="badge-purple capitalize">{{ karya.jenis }}</span>
          <span class="text-amber-500/70 font-mono">{{ karya.nomor_kiriman }}</span>
        </div>
        <div class="pt-4 border-t border-slate-800">
          <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Isi Karya</p>
          <pre class="text-slate-300 text-sm leading-relaxed whitespace-pre-wrap font-sans">{{ karya.isi }}</pre>
        </div>
        <div v-if="karya.catatan_redaksi" class="p-3 rounded-lg bg-amber-500/10 border border-amber-500/20">
          <p class="text-xs font-semibold text-amber-400 mb-1">Catatan Redaksi</p>
          <p class="text-sm text-amber-200">{{ karya.catatan_redaksi }}</p>
        </div>
      </div>

      <!-- Riwayat -->
      <div class="card p-6">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Riwayat Status</p>
        <div class="space-y-3">
          <div v-for="r in karya.riwayat" :key="r.id" class="flex gap-3">
            <div class="w-2 h-2 rounded-full mt-1.5 shrink-0" :class="dotColor(r.status)"></div>
            <div>
              <p class="text-sm font-semibold text-white">{{ r.status_label ?? r.status }}</p>
              <p v-if="r.catatan" class="text-xs text-slate-400 mt-0.5">{{ r.catatan }}</p>
              <p class="text-xs text-slate-600 mt-0.5">{{ r.created_at }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Ubah status -->
      <div v-if="!isFinal" class="card p-6 space-y-4">
        <p class="text-sm font-semibold text-white">Ubah Status Karya</p>

        <div>
          <label class="label">Status Baru</label>
          <select v-model="statusForm.status" class="input sm:w-52">
            <option value="ditinjau">Sedang Ditinjau</option>
            <option value="perlu_revisi">Perlu Revisi</option>
            <option value="terbit">Terbit</option>
            <option value="ditolak">Ditolak</option>
          </select>
        </div>

        <div v-if="needsCatatan">
          <label class="label">Catatan Redaksi <span class="text-rose-400">*</span></label>
          <textarea v-model="statusForm.catatan" rows="3" class="input resize-none" :class="{'ring-2 ring-rose-500': statusErrors.catatan}" placeholder="Sampaikan catatan untuk penulis..." />
          <p v-if="statusErrors.catatan" class="text-rose-400 text-xs mt-1">{{ statusErrors.catatan[0] }}</p>
        </div>
        <div v-else>
          <label class="label">Catatan (opsional)</label>
          <textarea v-model="statusForm.catatan" rows="2" class="input resize-none" placeholder="Catatan tambahan..." />
        </div>

        <div v-if="statusError" class="text-rose-400 text-sm">{{ statusError }}</div>
        <div v-if="statusSuccess" class="text-emerald-400 text-sm">{{ statusSuccess }}</div>

        <div class="flex gap-3">
          <button @click="ubahStatus" class="btn-primary" :disabled="statusLoading">
            <svg v-if="statusLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            Simpan Status
          </button>
          <button v-if="canDelete" @click="hapus" class="btn-danger">Hapus Karya</button>
        </div>
      </div>
      <div v-else class="p-3 rounded-lg bg-slate-800/50 text-xs text-slate-500 border border-slate-800">
        Status <strong class="text-slate-300">{{ karya.status_label }}</strong> bersifat final — tidak dapat diubah lagi.
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'admin', middleware: 'admin' })

const route  = useRoute()
const router = useRouter()
const { request } = useApi()

const loading = ref(false)
const karya   = ref<any>(null)
const statusForm = reactive({ status: 'ditinjau', catatan: '' })
const statusLoading = ref(false)
const statusErrors  = ref<Record<string, string[]>>({})
const statusError   = ref('')
const statusSuccess = ref('')

const isFinal   = computed(() => ['terbit', 'ditolak'].includes(karya.value?.status))
const canDelete = computed(() => ['dikirim', 'ditolak'].includes(karya.value?.status))
const needsCatatan = computed(() => ['perlu_revisi', 'ditolak'].includes(statusForm.status))

function statusBadge(s: string) {
  const map: Record<string, string> = {
    dikirim: 'badge-blue', ditinjau: 'badge-amber',
    perlu_revisi: 'badge-red', terbit: 'badge-green', ditolak: 'badge-slate',
  }
  return map[s] ?? 'badge-slate'
}

function dotColor(s: string) {
  const map: Record<string, string> = {
    dikirim: 'bg-sky-500', ditinjau: 'bg-amber-500',
    perlu_revisi: 'bg-rose-500', terbit: 'bg-emerald-500', ditolak: 'bg-slate-600',
  }
  return map[s] ?? 'bg-slate-600'
}

async function loadKarya() {
  loading.value = true
  try {
    const res = await request<{ data: any }>(`/admin/karya/${route.params.id}`)
    karya.value = res.data
    if (!isFinal.value) statusForm.status = 'ditinjau'
  } catch { karya.value = null } finally { loading.value = false }
}

async function ubahStatus() {
  statusLoading.value = true
  statusErrors.value  = {}
  statusError.value   = ''
  statusSuccess.value = ''
  try {
    await request(`/admin/karya/${route.params.id}/status`, {
      method: 'PATCH',
      body: { status: statusForm.status, catatan: statusForm.catatan || undefined },
    })
    statusSuccess.value = 'Status berhasil diperbarui!'
    statusForm.catatan  = ''
    await loadKarya()
  } catch (err: any) {
    if (err?.status === 422) statusErrors.value = err.data?.errors ?? {}
    else statusError.value = err?.data?.message ?? 'Terjadi kesalahan.'
  } finally { statusLoading.value = false }
}

async function hapus() {
  if (!confirm('Hapus karya ini? Tindakan ini tidak bisa dibatalkan.')) return
  try {
    await request(`/admin/karya/${route.params.id}`, { method: 'DELETE' })
    router.push('/admin/karya')
  } catch (err: any) {
    statusError.value = err?.data?.message ?? 'Gagal menghapus karya.'
  }
}

onMounted(() => loadKarya())
</script>

