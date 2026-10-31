<template>
  <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-8">
    <!-- Header Berita -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-6 border-b border-card-border">
      <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-bold mb-2">
          <span>📢</span> Warta, Pengumuman & Iklan Resmi
        </div>
        <h1 class="font-heading text-3xl font-extrabold text-ink">Kabar & Pengumuman Kampus</h1>
        <p class="text-xs sm:text-sm text-ink-muted mt-1">
          Informasi sayembara literasi, lomba cipta sastra, seminar kepenulisan, dan warta resmi dewan redaksi.
        </p>
      </div>

      <!-- Search Berita -->
      <div class="w-full sm:w-72">
        <label for="search-berita" class="sr-only">Cari Berita</label>
        <div class="relative">
          <input
            id="search-berita"
            v-model="searchQuery"
            type="search"
            placeholder="Cari pengumuman..."
            class="input pr-8 text-xs py-2"
            @keydown.enter="fetchBerita"
          />
          <button @click="fetchBerita" class="absolute right-2.5 top-2.5 text-ink-subtle hover:text-indigo-brand">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Data State Berita -->
    <DataState
      :loading="isLoading"
      :error="errorMessage"
      :empty="beritaList.length === 0"
      empty-title="Belum Ada Pengumuman"
      empty-description="Belum ada berita atau pengumuman yang diterbitkan oleh admin saat ini."
      @retry="fetchBerita"
    >
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <article
          v-for="item in beritaList"
          :key="item.id"
          class="card card-hover p-6 flex flex-col justify-between"
        >
          <div>
            <div class="flex items-center justify-between gap-2 mb-3">
              <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                Pengumuman Resmi
              </span>
              <span class="text-[11px] text-ink-subtle">
                {{ item.diterbitkan_pada_formatted || formatDate(item.diterbitkan_pada) }}
              </span>
            </div>

            <NuxtLink :to="`/berita/${item.slug}`" class="block group">
              <h2 class="font-heading text-lg font-bold text-ink group-hover:text-indigo-brand transition line-clamp-2 leading-snug">
                {{ item.judul }}
              </h2>
            </NuxtLink>

            <p class="text-xs text-ink-muted mt-2.5 line-clamp-3 leading-relaxed">
              {{ item.ringkasan || 'Klik selengkapnya untuk membaca pengumuman lengkap ini.' }}
            </p>
          </div>

          <div class="pt-4 mt-5 border-t border-card-border flex items-center justify-between text-xs text-ink-muted">
            <span class="font-medium text-ink truncate">
              Oleh {{ item.penulis?.name }}
            </span>
            <NuxtLink :to="`/berita/${item.slug}`" class="text-indigo-brand hover:underline font-semibold shrink-0">
              Baca Warta →
            </NuxtLink>
          </div>
        </article>
      </div>
    </DataState>
  </div>
</template>

<script setup lang="ts">
const config = useRuntimeConfig()
const apiBase = config.public.apiBase as string

const isLoading = ref(true)
const errorMessage = ref('')
const beritaList = ref<any[]>([])
const searchQuery = ref('')

async function fetchBerita() {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const params: Record<string, any> = { per_page: 15 }
    if (searchQuery.value.trim()) {
      params.search = searchQuery.value.trim()
    }
    const res = await $fetch<any>(`${apiBase}/berita`, { params })
    beritaList.value = res.data || []
  } catch (err: any) {
    errorMessage.value = err.data?.message || 'Gagal memuat berita. Pastikan backend aktif.'
  } finally {
    isLoading.value = false
  }
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
  fetchBerita()
})
</script>

