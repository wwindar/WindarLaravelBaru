<template>
  <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-8">
    <!-- Header dengan Banner -->
    <div class="relative overflow-hidden rounded-2xl border border-card-border shadow-card">
      <img
        src="/images/karya-banner.png"
        alt="Karya Sastra Terbit"
        class="w-full h-44 sm:h-56 object-cover object-center opacity-80 dark:opacity-50"
      />
      <div class="absolute inset-0 bg-gradient-to-r from-card/95 via-card/70 to-transparent flex flex-col sm:flex-row sm:items-end justify-between gap-4 p-6 sm:p-8">
        <div>
          <span class="text-xs font-bold text-indigo-brand uppercase tracking-wider block mb-1">Perpustakaan Digital</span>
          <h1 class="font-heading text-3xl font-extrabold text-ink">Karya Sastra Terbit</h1>
          <p class="text-xs sm:text-sm text-ink-muted mt-1">Jelajahi puisi, cerpen, esai, dan artikel pemikiran mahasiswa.</p>
        </div>
        <div v-if="auth.isMahasiswa" class="shrink-0">
          <NuxtLink to="/mahasiswa/tulisan/baru" class="btn-primary text-xs px-4 py-2.5">
            + Tulis Karya Baru
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Filter & Pencarian Bar -->
    <div class="card p-5 space-y-4">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Input Search -->
        <div>
          <label for="filter-search" class="label">Pencarian Kata Kunci</label>
          <div class="relative">
            <input
              id="filter-search"
              v-model="filters.search"
              type="search"
              placeholder="Judul atau kata kunci..."
              class="input pr-8"
              @keydown.enter="applyFilters"
            />
            <span class="absolute right-3 top-2.5 text-ink-subtle pointer-events-none">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </span>
          </div>
        </div>

        <!-- Filter Jenis -->
        <div>
          <label for="filter-jenis" class="label">Jenis Karya</label>
          <select id="filter-jenis" v-model="filters.jenis" class="input" @change="applyFilters">
            <option value="">Semua Jenis</option>
            <option value="puisi">Puisi</option>
            <option value="cerpen">Cerita Pendek (Cerpen)</option>
            <option value="esai">Esai</option>
            <option value="artikel">Artikel</option>
          </select>
        </div>

        <!-- Filter Kategori -->
        <div>
          <label for="filter-kategori" class="label">Kategori Rubrik</label>
          <select id="filter-kategori" v-model="filters.kategori" class="input" @change="applyFilters">
            <option value="">Semua Kategori</option>
            <option v-for="cat in kategoriList" :key="cat.id" :value="cat.slug">
              {{ cat.nama }}
            </option>
          </select>
        </div>

        <!-- Filter Penulis -->
        <div>
          <label for="filter-penulis" class="label">Username Penulis</label>
          <input
            id="filter-penulis"
            v-model="filters.penulis"
            type="text"
            placeholder="Contoh: andisaputra"
            class="input"
            @keydown.enter="applyFilters"
          />
        </div>
      </div>

      <!-- Action filter buttons -->
      <div class="flex items-center justify-between pt-3 border-t border-card-border text-xs">
        <span class="text-ink-muted">
          Menampilkan <strong class="text-ink">{{ tulisanList.length }}</strong> dari <strong class="text-ink">{{ totalItems }}</strong> karya
        </span>
        <div class="flex items-center gap-2">
          <button type="button" @click="resetFilters" class="btn-ghost text-xs px-3 py-1.5">
            Reset Filter
          </button>
          <button type="button" @click="applyFilters" class="btn-primary text-xs px-4 py-1.5">
            Terapkan
          </button>
        </div>
      </div>
    </div>

    <!-- Data State: 4 Kondisi (Memuat, Gagal, Kosong, Berhasil) -->
    <DataState
      :loading="isLoading"
      :error="errorMessage"
      :empty="tulisanList.length === 0"
      empty-title="Karya tidak ditemukan"
      empty-description="Tidak ada karya sastra yang cocok dengan filter atau kata kunci yang Anda masukkan."
      @retry="fetchKarya"
    >
      <template #empty-action>
        <button type="button" @click="resetFilters" class="btn-secondary text-xs px-4 py-2">
          Hapus Semua Filter
        </button>
      </template>

      <!-- Grid Karya -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <article
          v-for="item in tulisanList"
          :key="item.id"
          class="card card-hover p-6 flex flex-col justify-between"
        >
          <div>
            <div class="flex items-center justify-between gap-2 mb-3">
              <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-soft text-indigo-brand capitalize">
                {{ item.jenis_label || item.jenis }}
              </span>
              <span class="text-[11px] text-ink-subtle">
                {{ formatDate(item.diterbitkan_pada) }}
              </span>
            </div>

            <NuxtLink :to="`/karya/${item.slug}`" class="block group">
              <h2 class="font-heading text-lg font-bold text-ink group-hover:text-indigo-brand transition line-clamp-2 leading-snug">
                {{ item.judul }}
              </h2>
            </NuxtLink>

            <p class="text-xs text-ink-muted mt-2.5 line-clamp-3 leading-relaxed">
              {{ item.ringkasan || 'Klik tautan untuk membaca naskah selengkapnya.' }}
            </p>
          </div>

          <div class="pt-4 mt-5 border-t border-card-border flex items-center justify-between text-xs text-ink-muted">
            <span class="font-medium text-ink truncate">
              Oleh {{ item.penulis?.name }}
            </span>
            <NuxtLink :to="`/karya/${item.slug}`" class="text-indigo-brand hover:underline font-semibold shrink-0">
              Baca naskah →
            </NuxtLink>
          </div>
        </article>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="flex items-center justify-center gap-2 pt-8">
        <button
          type="button"
          :disabled="currentPage <= 1"
          @click="changePage(currentPage - 1)"
          class="btn-secondary text-xs px-3 py-1.5"
        >
          ← Sebelumnya
        </button>
        <span class="text-xs text-ink-muted px-2">
          Halaman <strong>{{ currentPage }}</strong> dari <strong>{{ totalPages }}</strong>
        </span>
        <button
          type="button"
          :disabled="currentPage >= totalPages"
          @click="changePage(currentPage + 1)"
          class="btn-secondary text-xs px-3 py-1.5"
        >
          Berikutnya →
        </button>
      </div>
    </DataState>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const router = useRouter()
const auth = useAuthStore()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase as string

const isLoading = ref(true)
const errorMessage = ref('')
const tulisanList = ref<any[]>([])
const kategoriList = ref<any[]>([])

const currentPage = ref(1)
const totalPages = ref(1)
const totalItems = ref(0)

const filters = reactive({
  search: (route.query.search as string) || '',
  jenis: (route.query.jenis as string) || '',
  kategori: (route.query.kategori as string) || '',
  penulis: (route.query.penulis as string) || '',
})

async function fetchKategori() {
  try {
    const res = await $fetch<any>(`${apiBase}/kategori`)
    kategoriList.value = res.data || []
  } catch {}
}

async function fetchKarya() {
  isLoading.value = true
  errorMessage.value = ''

  const params: Record<string, any> = {
    page: currentPage.value,
    per_page: 9,
  }

  if (filters.search) params.search = filters.search
  if (filters.jenis) params.jenis = filters.jenis
  if (filters.kategori) params.kategori = filters.kategori
  if (filters.penulis) params.penulis = filters.penulis

  try {
    const res = await $fetch<any>(`${apiBase}/tulisan`, { params })
    tulisanList.value = res.data || []
    currentPage.value = res.meta?.current_page || 1
    totalPages.value = res.meta?.last_page || 1
    totalItems.value = res.meta?.total || tulisanList.value.length
  } catch (err: any) {
    errorMessage.value = err.data?.message || 'Gagal memuat katalog karya sastra. Coba periksa koneksi atau backend.'
  } finally {
    isLoading.value = false
  }
}

function applyFilters() {
  currentPage.value = 1
  router.push({
    path: '/karya',
    query: {
      ...(filters.search ? { search: filters.search } : {}),
      ...(filters.jenis ? { jenis: filters.jenis } : {}),
      ...(filters.kategori ? { kategori: filters.kategori } : {}),
      ...(filters.penulis ? { penulis: filters.penulis } : {}),
    },
  })
  fetchKarya()
}

function resetFilters() {
  filters.search = ''
  filters.jenis = ''
  filters.kategori = ''
  filters.penulis = ''
  currentPage.value = 1
  router.push({ path: '/karya' })
  fetchKarya()
}

function changePage(page: number) {
  currentPage.value = page
  fetchKarya()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function formatDate(iso: string) {
  if (!iso) return ''
  const d = new Date(iso)
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

onMounted(() => {
  fetchKategori()
  fetchKarya()
})
</script>
