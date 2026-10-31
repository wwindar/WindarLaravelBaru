<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-8">
    <!-- Tombol Kembali -->
    <div class="flex items-center justify-between">
      <NuxtLink to="/berita" class="btn-ghost text-xs px-3 py-2 text-ink-muted hover:text-ink inline-flex items-center gap-1.5">
        ← Kembali ke Daftar Pengumuman
      </NuxtLink>
    </div>

    <!-- Data State untuk Detail Berita -->
    <DataState
      :loading="isLoading"
      :error="errorMessage"
      :empty="!berita"
      empty-title="Pengumuman Tidak Ditemukan"
      empty-description="Warta atau pengumuman yang Anda cari mungkin telah ditarik atau belum terbit."
      @retry="fetchDetail"
    >
      <article v-if="berita" class="card p-6 sm:p-10 space-y-6">
        <!-- Tag & Tanggal -->
        <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-card-border">
          <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
              Pengumuman Resmi
            </span>
          </div>
          <span class="text-xs text-ink-subtle">
            Diterbitkan {{ berita.diterbitkan_pada_formatted || formatDate(berita.diterbitkan_pada) }}
          </span>
        </div>

        <!-- Judul & Info Penulis (Admin) -->
        <div>
          <h1 class="font-heading text-2xl sm:text-4xl font-extrabold text-ink leading-tight mb-3">
            {{ berita.judul }}
          </h1>
          <p class="text-xs sm:text-sm text-ink-muted">
            Dipublikasikan oleh: <strong class="text-ink">{{ berita.penulis?.name }}</strong> (Dewan Redaksi Aksara Kampus)
          </p>
        </div>

        <!-- Ringkasan Highlight -->
        <blockquote v-if="berita.ringkasan" class="p-4 rounded-2xl bg-amber-50/60 border-l-4 border-amber-500 text-xs sm:text-sm text-amber-950 italic leading-relaxed">
          "{{ berita.ringkasan }}"
        </blockquote>

        <!-- Isi Berita -->
        <div class="pt-4 text-ink leading-relaxed text-sm sm:text-base whitespace-pre-line border-t border-card-border space-y-4">
          {{ berita.isi }}
        </div>

        <!-- Footer Card -->
        <div class="pt-6 border-t border-card-border flex flex-wrap items-center justify-between gap-4 text-xs text-ink-muted">
          <span>Portal Sastra & Literasi Aksara Kampus</span>
          <NuxtLink to="/berita" class="btn-secondary text-xs px-4 py-2">
            Jelajahi Berita Lainnya →
          </NuxtLink>
        </div>
      </article>
    </DataState>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase as string

const slug = route.params.slug as string
const isLoading = ref(true)
const errorMessage = ref('')
const berita = ref<any>(null)

async function fetchDetail() {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const res = await $fetch<any>(`${apiBase}/berita/${slug}`)
    berita.value = res.data
  } catch (err: any) {
    errorMessage.value = err.data?.message || 'Gagal memuat berita.'
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
  fetchDetail()
})
</script>

