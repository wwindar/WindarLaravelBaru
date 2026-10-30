<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 py-12 space-y-6">
    <div class="flex items-center gap-3">
      <NuxtLink to="/artikel" class="text-slate-400 hover:text-white transition">
        ← Kembali ke Artikel
      </NuxtLink>
    </div>

    <div v-if="loading" class="card p-12 text-center text-slate-500 animate-pulse">
      Memuat artikel...
    </div>

    <div v-else-if="!artikel" class="card p-12 text-center text-slate-500">
      Artikel tidak ditemukan atau belum terbit.
    </div>

    <article v-else class="card p-6 sm:p-10 space-y-6">
      <div class="space-y-3 border-b border-slate-800 pb-6">
        <div class="flex items-center gap-2">
          <span :class="artikel.jenis === 'berita' ? 'badge-amber' : 'badge-blue'">{{ artikel.jenis }}</span>
          <span v-if="artikel.kategori" class="badge-slate">{{ artikel.kategori.nama }}</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">
          {{ artikel.judul }}
        </h1>
        <div class="flex items-center gap-3 text-xs text-slate-400">
          <span>Oleh <strong class="text-slate-200">{{ artikel.penulis }}</strong></span>
          <span>•</span>
          <span>{{ formatTanggal(artikel.diterbitkan_pada || artikel.created_at) }}</span>
        </div>
      </div>

      <div v-if="artikel.ringkasan" class="text-base text-amber-200/90 bg-amber-500/5 border-l-4 border-amber-500 p-4 rounded-r-lg italic">
        {{ artikel.ringkasan }}
      </div>

      <div class="prose prose-invert max-w-none text-slate-300 text-base leading-relaxed whitespace-pre-line font-sans">
        {{ artikel.isi }}
      </div>
    </article>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'default' })

const route = useRoute()
const { request } = useApi()

const artikel = ref<any>(null)
const loading = ref(true)

function formatTanggal(d: string) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

onMounted(async () => {
  try {
    const res = await request<{ data: any }>(`/artikel/${route.params.slug}`)
    artikel.value = res.data
  } catch {
    artikel.value = null
  } finally {
    loading.value = false
  }
})
</script>

