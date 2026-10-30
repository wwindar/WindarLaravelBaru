<template>
  <!-- Reuse form buat, dengan data terisi -->
  <div class="max-w-3xl space-y-6">
    <div class="flex items-center gap-3">
      <NuxtLink to="/admin/artikel" class="text-slate-400 hover:text-white transition">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      </NuxtLink>
      <h1 class="text-2xl font-bold text-white">Edit Artikel</h1>
    </div>

    <div v-if="errorMsg" class="p-3 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm">{{ errorMsg }}</div>

    <div v-if="notFound" class="card p-10 text-center text-slate-500">Artikel tidak ditemukan.</div>

    <form v-else @submit.prevent="submit" class="space-y-5">
      <div class="card p-6 space-y-5">
        <div>
          <label class="label">Judul</label>
          <input v-model="form.judul" type="text" class="input" :class="{'ring-2 ring-rose-500': errors.judul}" required />
          <p v-if="errors.judul" class="text-rose-400 text-xs mt-1">{{ errors.judul[0] }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="label">Jenis</label>
            <select v-model="form.jenis" class="input" required>
              <option value="berita">Berita</option>
              <option value="blog">Blog</option>
            </select>
          </div>
          <div>
            <label class="label">Kategori</label>
            <select v-model="form.kategori_id" class="input">
              <option value="">Tanpa Kategori</option>
              <option v-for="k in kategori" :key="k.id" :value="k.id">{{ k.nama }}</option>
            </select>
          </div>
        </div>

        <div>
          <label class="label">Penulis</label>
          <input v-model="form.penulis" type="text" class="input" required />
        </div>

        <div>
          <label class="label">Ringkasan</label>
          <textarea v-model="form.ringkasan" rows="2" class="input resize-none" />
        </div>

        <div>
          <label class="label">Isi Artikel</label>
          <textarea v-model="form.isi" rows="12" class="input resize-y font-mono text-sm leading-relaxed" :class="{'ring-2 ring-rose-500': errors.isi}" required />
          <p v-if="errors.isi" class="text-rose-400 text-xs mt-1">{{ errors.isi[0] }}</p>
        </div>

        <div>
          <label class="label">Status</label>
          <select v-model="form.status" class="input sm:w-40">
            <option value="draft">Draft</option>
            <option value="terbit">Terbit</option>
          </select>
        </div>
      </div>

      <div class="flex gap-3 justify-end">
        <NuxtLink to="/admin/artikel" class="btn-ghost">Batal</NuxtLink>
        <button type="submit" class="btn-primary" :disabled="loading">
          <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'admin', middleware: 'admin' })

const route  = useRoute()
const router = useRouter()
const { request } = useApi()

const loading  = ref(false)
const notFound = ref(false)
const errorMsg = ref('')
const errors   = ref<Record<string, string[]>>({})
const kategori = ref<any[]>([])

const form = reactive({
  judul: '', jenis: 'berita', kategori_id: '' as string|null,
  penulis: '', ringkasan: '', isi: '', status: 'draft',
})

onMounted(async () => {
  try {
    const [artRes, katRes] = await Promise.all([
      request<{ data: any }>(`/admin/artikel/${route.params.id}`),
      request<{ data: any[] }>('/admin/kategori'),
    ])
    const d = artRes.data
    Object.assign(form, {
      judul: d.judul, jenis: d.jenis,
      kategori_id: d.kategori_id ?? '',
      penulis: d.penulis, ringkasan: d.ringkasan ?? '',
      isi: d.isi, status: d.status,
    })
    kategori.value = katRes.data
  } catch (err: any) {
    if (err?.status === 404) notFound.value = true
    else errorMsg.value = 'Gagal memuat artikel.'
  }
})

async function submit() {
  loading.value = true
  errors.value  = {}
  errorMsg.value = ''
  try {
    const body = { ...form, kategori_id: form.kategori_id || null }
    await request(`/admin/artikel/${route.params.id}`, { method: 'PUT', body })
    router.push('/admin/artikel')
  } catch (err: any) {
    if (err?.status === 422) errors.value = err.data?.errors ?? {}
    else errorMsg.value = err?.data?.message ?? 'Terjadi kesalahan.'
  } finally { loading.value = false }
}
</script>

