<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-8">
    <div class="flex items-center justify-between pb-6 border-b border-card-border">
      <div>
        <NuxtLink to="/mahasiswa" class="text-xs text-indigo-brand hover:underline font-semibold flex items-center gap-1 mb-2">
          ← Kembali ke Tulisan Saya
        </NuxtLink>
        <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-ink">Edit Naskah</h1>
        <p class="text-xs sm:text-sm text-ink-muted mt-1">Perbaiki dan sempurnakan naskah karya Anda.</p>
      </div>
    </div>

    <!-- Alert Catatan Editor jika Ada -->
    <div v-if="catatanEditor" class="p-4 bg-purple-50 border border-purple-200 text-purple-900 rounded-2xl text-xs space-y-1">
      <span class="font-bold block">Catatan Perbaikan dari Dosen Editor:</span>
      <p class="italic">"{{ catatanEditor }}"</p>
    </div>

    <!-- Alert Error -->
    <div v-if="errorMessage" class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs" role="alert">
      {{ errorMessage }}
    </div>

    <DataState
      :loading="isLoading"
      :error="loadError"
      @retry="fetchTulisan"
    >
      <form @submit.prevent="updateTulisan(false)" class="card p-6 sm:p-10 space-y-6">
        <div>
          <label for="edit-judul" class="label">Judul Tulisan <span class="text-rose-500">*</span></label>
          <input
            id="edit-judul"
            v-model="form.judul"
            type="text"
            required
            maxlength="255"
            class="input font-heading text-base font-bold"
          />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="edit-jenis" class="label">Jenis Karya <span class="text-rose-500">*</span></label>
            <select id="edit-jenis" v-model="form.jenis" required class="input">
              <option value="puisi">Puisi</option>
              <option value="cerpen">Cerita Pendek (Cerpen)</option>
              <option value="esai">Esai</option>
              <option value="artikel">Artikel Pemikiran</option>
            </select>
          </div>

          <div>
            <label for="edit-kategori" class="label">Rubrik Kategori</label>
            <select id="edit-kategori" v-model="form.kategori_id" class="input">
              <option :value="null">Pilih Kategori (Opsional)</option>
              <option v-for="cat in kategoriList" :key="cat.id" :value="cat.id">
                {{ cat.nama }}
              </option>
            </select>
          </div>
        </div>

        <div>
          <label for="edit-ringkasan" class="label">Ringkasan / Sinopsis Singkat</label>
          <textarea
            id="edit-ringkasan"
            v-model="form.ringkasan"
            rows="2"
            maxlength="500"
            class="input resize-none"
          ></textarea>
        </div>

        <div>
          <label for="edit-isi" class="label">Naskah Lengkap <span class="text-rose-500">*</span></label>
          <textarea
            id="edit-isi"
            v-model="form.isi"
            required
            rows="14"
            class="input resize-y font-sans leading-relaxed text-sm sm:text-base"
          ></textarea>
        </div>

        <div class="pt-6 border-t border-card-border flex flex-col sm:flex-row items-center justify-between gap-4">
          <NuxtLink to="/mahasiswa" class="btn-secondary text-xs px-4 py-2.5 w-full sm:w-auto">
            Batal
          </NuxtLink>

          <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto justify-end">
            <button
              type="button"
              :disabled="isSubmitting"
              @click="updateTulisan(false)"
              class="btn-secondary text-xs px-5 py-2.5 flex-1 sm:flex-none"
            >
              <span v-if="isSubmitting && !directAjukan" class="w-3.5 h-3.5 border-2 border-indigo-brand border-t-transparent rounded-full animate-spin mr-1.5"></span>
              Perbarui Naskah
            </button>

            <button
              type="button"
              :disabled="isSubmitting"
              @click="updateTulisan(true)"
              class="btn-primary text-xs px-5 py-2.5 flex-1 sm:flex-none"
            >
              <span v-if="isSubmitting && directAjukan" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin mr-1.5"></span>
              Perbarui & Ajukan Ulang ↗
            </button>
          </div>
        </div>
      </form>
    </DataState>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: ['mahasiswa'],
})

const route = useRoute()
const auth = useAuthStore()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase as string

const id = computed(() => route.params.id as string)

const form = reactive({
  judul: '',
  jenis: 'puisi',
  kategori_id: null as number | null,
  ringkasan: '',
  isi: '',
})

const catatanEditor = ref('')
const kategoriList = ref<any[]>([])
const isLoading = ref(true)
const loadError = ref('')
const isSubmitting = ref(false)
const directAjukan = ref(false)
const errorMessage = ref('')

async function fetchKategori() {
  try {
    const res = await $fetch<any>(`${apiBase}/kategori`)
    kategoriList.value = res.data || []
  } catch {}
}

async function fetchTulisan() {
  isLoading.value = true
  loadError.value = ''
  try {
    const res = await $fetch<any>(`${apiBase}/mahasiswa/tulisan/${id.value}`, {
      headers: {
        Authorization: `Bearer ${auth.token}`,
        Accept: 'application/json',
      },
    })
    const data = res.data
    form.judul = data.judul
    form.jenis = data.jenis
    form.kategori_id = data.kategori?.id || null
    form.ringkasan = data.ringkasan || ''
    form.isi = data.isi || ''
    catatanEditor.value = data.catatan_editor || ''
  } catch (err: any) {
    loadError.value = err.data?.message || 'Gagal memuat detail naskah.'
  } finally {
    isLoading.value = false
  }
}

async function updateTulisan(shouldAjukan: boolean) {
  if (isSubmitting.value) return
  isSubmitting.value = true
  directAjukan.value = shouldAjukan
  errorMessage.value = ''

  try {
    await $fetch(`${apiBase}/mahasiswa/tulisan/${id.value}`, {
      method: 'PUT',
      body: form,
      headers: {
        Authorization: `Bearer ${auth.token}`,
        Accept: 'application/json',
      },
    })

    if (shouldAjukan) {
      await $fetch(`${apiBase}/mahasiswa/tulisan/${id.value}/ajukan`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${auth.token}`,
          Accept: 'application/json',
        },
      })
    }

    navigateTo('/mahasiswa')
  } catch (err: any) {
    errorMessage.value = err.data?.message || 'Gagal memperbarui naskah.'
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  fetchKategori()
  fetchTulisan()
})
</script>

