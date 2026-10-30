<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 py-12 space-y-6">
    <div class="flex items-center gap-3">
      <NuxtLink to="/karya" class="text-slate-400 hover:text-white transition">
        ← Kembali ke Daftar Karya
      </NuxtLink>
    </div>

    <div v-if="loading" class="card p-12 text-center text-slate-500 animate-pulse">
      Memuat naskah sastra...
    </div>

    <div v-else-if="!karya" class="card p-12 text-center text-slate-500">
      Karya sastra tidak ditemukan.
    </div>

    <article v-else class="card p-6 sm:p-10 space-y-8">
      <div class="space-y-3 border-b border-slate-800 pb-6 text-center">
        <span class="badge-purple capitalize">{{ karya.jenis }}</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">
          {{ karya.judul }}
        </h1>
        <p class="text-sm text-slate-400">
          Karya <strong class="text-amber-400">{{ karya.nama_penulis }}</strong>
        </p>
      </div>

      <div class="text-slate-200 text-base sm:text-lg leading-relaxed whitespace-pre-line font-mono bg-slate-950/60 p-6 rounded-xl border border-slate-800/80">
        {{ karya.isi }}
      </div>

      <div class="flex justify-between items-center text-xs text-slate-500 pt-4 border-t border-slate-800">
        <span>No. Registrasi: <span class="font-mono text-amber-500/80">{{ karya.nomor_kiriman }}</span></span>
        <span>Aksara Kampus</span>
      </div>
    </article>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'default' })

const route = useRoute()
const { request } = useApi()

const karya = ref<any>(null)
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await request<{ data: any }>(`/karya/${route.params.slug}`)
    karya.value = res.data
  } catch {
    karya.value = null
  } finally {
    loading.value = false
  }
})
</script>

