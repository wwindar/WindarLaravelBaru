<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-8">
    <div class="flex items-center justify-between pb-6 border-b border-card-border">
      <div>
        <NuxtLink to="/mahasiswa" class="text-xs text-indigo-brand hover:underline font-semibold flex items-center gap-1 mb-2">
          ← Kembali ke Tulisan Saya
        </NuxtLink>
        <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-ink">Tulis Karya Baru</h1>
        <p class="text-xs sm:text-sm text-ink-muted mt-1">Susun naskah Anda. Anda dapat menyimpannya sebagai draf terlebih dahulu.</p>
      </div>
    </div>

    <!-- Alert Error -->
    <div v-if="errorMessage" class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs" role="alert">
      {{ errorMessage }}
    </div>

    <form @submit.prevent="saveTulisan(false)" class="card p-6 sm:p-10 space-y-6">
      <!-- Judul -->
      <div>
        <label for="tulisan-judul" class="label">Judul Tulisan <span class="text-rose-500">*</span></label>
        <input
          id="tulisan-judul"
          v-model="form.judul"
          type="text"
          required
          maxlength="255"
          placeholder="Tuliskan judul puisi, cerpen, esai, atau artikel Anda..."
          class="input font-heading text-base font-bold"
        />
      </div>

      <!-- Baris Jenis & Kategori -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label for="tulisan-jenis" class="label">Jenis Karya <span class="text-rose-500">*</span></label>
          <select id="tulisan-jenis" v-model="form.jenis" required class="input">
            <option value="puisi">Puisi</option>
            <option value="cerpen">Cerita Pendek (Cerpen)</option>
            <option value="esai">Esai</option>
            <option value="artikel">Artikel Pemikiran</option>
          </select>
        </div>

        <div>
          <label for="tulisan-kategori" class="label">Rubrik Kategori</label>
          <select id="tulisan-kategori" v-model="form.kategori_id" class="input">
            <option :value="null">Pilih Kategori (Opsional)</option>
            <option v-for="cat in kategoriList" :key="cat.id" :value="cat.id">
              {{ cat.nama }}
            </option>
          </select>
        </div>
      </div>

      <!-- Ringkasan / Epigraf -->
      <div>
        <label for="tulisan-ringkasan" class="label">Ringkasan / Sinopsis Singkat</label>
        <textarea
          id="tulisan-ringkasan"
          v-model="form.ringkasan"
          rows="2"
          maxlength="500"
          placeholder="Ringkasan singkat naskah untuk cuplikan pada kartu katalog (maks. 500 karakter)..."
          class="input resize-none"
        ></textarea>
      </div>

      <!-- Isi Naskah -->
      <div>
        <label for="tulisan-isi" class="label">Naskah Lengkap <span class="text-rose-500">*</span></label>
        <textarea
          id="tulisan-isi"
          v-model="form.isi"
          required
          rows="14"
          placeholder="Tuliskan bait puisi, alur cerpen, atau argumentasi esai Anda di sini..."
          class="input resize-y font-sans leading-relaxed text-sm sm:text-base"
        ></textarea>
      </div>

      <!-- Tombol Aksi Simpan -->
      <div class="pt-6 border-t border-card-border flex flex-col sm:flex-row items-center justify-between gap-4">
        <NuxtLink to="/mahasiswa" class="btn-secondary text-xs px-4 py-2.5 w-full sm:w-auto">
          Batal
        </NuxtLink>

        <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto justify-end">
          <button
            type="button"
            :disabled="isSubmitting"
            @click="saveTulisan(false)"
            class="btn-secondary text-xs px-5 py-2.5 flex-1 sm:flex-none"
          >
            <span v-if="isSubmitting && !directAjukan" class="w-3.5 h-3.5 border-2 border-indigo-brand border-t-transparent rounded-full animate-spin mr-1.5"></span>
            Simpan sebagai Draf
          </button>

          <button
            type="button"
            :disabled="isSubmitting"
            @click="saveTulisan(true)"
            class="btn-primary text-xs px-5 py-2.5 flex-1 sm:flex-none"
          >
            <span v-if="isSubmitting && directAjukan" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin mr-1.5"></span>
            Simpan & Langsung Ajukan ↗
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: ['mahasiswa'],
})

const auth = useAuthStore()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase as string

const form = reactive({
  judul: '',
  jenis: 'puisi',
  kategori_id: null as number | null,
  ringkasan: '',
  isi: '',
})

const kategoriList = ref<any[]>([])
const isSubmitting = ref(false)
const directAjukan = ref(false)
const errorMessage = ref('')

async function fetchKategori() {
  try {
    const res = await $fetch<any>(`${apiBase}/kategori`)
    kategoriList.value = res.data || []
  } catch {}
}

async function saveTulisan(shouldAjukan: boolean) {
  if (isSubmitting.value) return
  if (!form.judul.trim() || !form.isi.trim()) {
    errorMessage.value = 'Judul dan isi tulisan wajib diisi.'
    return
  }

  isSubmitting.value = true
  directAjukan.value = shouldAjukan
  errorMessage.value = ''

  try {
    const res = await $fetch<any>(`${apiBase}/mahasiswa/tulisan`, {
      method: 'POST',
      body: form,
      headers: {
        Authorization: `Bearer ${auth.token}`,
        Accept: 'application/json',
      },
    })

    const createdId = res.data?.id

    if (shouldAjukan && createdId) {
      await $fetch(`${apiBase}/mahasiswa/tulisan/${createdId}/ajukan`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${auth.token}`,
          Accept: 'application/json',
        },
      })
    }

    navigateTo('/mahasiswa')
  } catch (err: any) {
    errorMessage.value = err.data?.message || 'Gagal menyimpan tulisan. Pastikan isian lengkap.'
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  fetchKategori()
})
</script>

