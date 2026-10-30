<template>
  <div>
    <!-- Hero Section -->
    <section class="relative overflow-hidden py-24 sm:py-32">
      <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[700px] h-[450px] bg-ochre-400/10 rounded-full blur-3xl"></div>
        <div class="absolute top-40 right-10 w-[400px] h-[300px] bg-sage-300/10 rounded-full blur-3xl"></div>
      </div>
      <div class="max-w-6xl mx-auto px-4 sm:px-6 relative z-10 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-ochre-400/30 bg-ochre-400/10 text-ochre-300 text-xs font-semibold mb-6">
          <span class="w-2 h-2 rounded-full bg-ochre-400 animate-pulse"></span>
          Portal Komunitas Sastra UMPKU
        </div>
        <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold text-parchment-50 tracking-tight leading-tight">
          Aksara<br>
          <span class="bg-gradient-to-r from-ochre-400 via-terracotta-400 to-sage-300 bg-clip-text text-transparent">Kampus</span>
        </h1>
        <p class="mt-6 text-base sm:text-lg text-parchment-400 max-w-2xl mx-auto leading-relaxed">
          Ruang dialektika dan ekspresi sastra mahasiswa — puisi, cerpen, esai, kabar komunitas literasi, dan agenda kreatif.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-3 mt-8">
          <NuxtLink to="/karya" class="btn-primary px-6 py-2.5">
            Baca Karya Terbaru
          </NuxtLink>
          <NuxtLink to="/kirim" class="btn-ghost px-6 py-2.5">
            Kirim Naskahmu
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- Artikel Terbaru -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 pb-16">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-parchment-100">Artikel & Berita</h2>
          <p class="text-xs text-parchment-500 mt-0.5">Ulasan dan liputan literasi terkini</p>
        </div>
        <NuxtLink to="/artikel" class="text-sm font-semibold text-ochre-400 hover:text-ochre-300 transition">Lihat semua →</NuxtLink>
      </div>

      <div v-if="loadingArtikel" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div v-for="i in 3" :key="i" class="card p-5 animate-pulse space-y-3">
          <div class="h-3 bg-ink-800 rounded w-1/3" />
          <div class="h-5 bg-ink-800 rounded w-3/4" />
          <div class="h-3 bg-ink-800 rounded w-full" />
          <div class="h-3 bg-ink-800 rounded w-5/6" />
        </div>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <NuxtLink
          v-for="art in artikelTerbaru"
          :key="art.id"
          :to="`/artikel/${art.slug}`"
          class="card p-6 hover:border-ochre-400/40 hover:bg-ink-850/80 transition-all group flex flex-col justify-between"
        >
          <div>
            <div class="flex items-center gap-2 mb-3">
              <span :class="art.jenis === 'berita' ? 'badge-ochre' : 'badge-sage'">{{ art.jenis }}</span>
              <span v-if="art.kategori" class="text-xs text-parchment-400">{{ art.kategori.nama }}</span>
            </div>
            <h3 class="font-bold text-lg text-parchment-100 group-hover:text-ochre-300 transition leading-snug">{{ art.judul }}</h3>
            <p class="text-sm text-parchment-400 mt-2 leading-relaxed line-clamp-3">{{ art.ringkasan }}</p>
          </div>
          <p class="text-xs text-parchment-500 mt-4 pt-3 border-t border-ink-800/80">{{ art.penulis }}</p>
        </NuxtLink>
      </div>
    </section>

    <!-- Karya Pilihan -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 pb-16">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-parchment-100">Karya Sastra Terbit</h2>
          <p class="text-xs text-parchment-500 mt-0.5">Puisi, cerpen, dan esai pilihan redaksi</p>
        </div>
        <NuxtLink to="/karya" class="text-sm font-semibold text-ochre-400 hover:text-ochre-300 transition">Lihat semua →</NuxtLink>
      </div>

      <div v-if="loadingKarya" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div v-for="i in 4" :key="i" class="card p-6 animate-pulse space-y-3">
          <div class="h-3 bg-ink-800 rounded w-1/4" />
          <div class="h-5 bg-ink-800 rounded w-2/3" />
          <div class="h-16 bg-ink-800 rounded w-full" />
        </div>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <NuxtLink
          v-for="k in karyaPilihan"
          :key="k.id"
          :to="`/karya/${k.slug}`"
          class="card p-6 hover:border-terracotta-500/40 hover:bg-ink-850/80 transition-all group flex flex-col justify-between"
        >
          <div>
            <div class="flex items-center justify-between mb-3">
              <span class="badge-terracotta capitalize">{{ k.jenis }}</span>
              <span class="text-xs text-ochre-400/70 font-mono">{{ k.nomor_kiriman }}</span>
            </div>
            <h3 class="font-bold text-xl text-parchment-100 group-hover:text-ochre-300 transition">{{ k.judul }}</h3>
            <p class="text-xs text-parchment-400 mt-1">Oleh <strong class="text-parchment-200">{{ k.nama_penulis }}</strong></p>
            <p class="text-sm text-parchment-300/80 mt-4 leading-relaxed line-clamp-3 font-mono bg-ink-950/60 p-3.5 rounded-xl border border-ink-800/60">
              {{ k.isi?.substring(0, 160) }}...
            </p>
          </div>
          <span class="text-xs text-ochre-400 font-semibold mt-4 block">Baca naskah lengkap →</span>
        </NuxtLink>
      </div>
    </section>

    <!-- Agenda Sastra -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 pb-20">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-parchment-100">Agenda Kegiatan</h2>
          <p class="text-xs text-parchment-500 mt-0.5">Lomba, bedah buku, dan kelas kepenulisan</p>
        </div>
        <NuxtLink to="/agenda" class="text-sm font-semibold text-ochre-400 hover:text-ochre-300 transition">Lihat semua →</NuxtLink>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div v-if="loadingAgenda" v-for="i in 3" :key="i" class="card p-5 animate-pulse space-y-3">
          <div class="h-3 bg-ink-800 rounded w-1/3" />
          <div class="h-5 bg-ink-800 rounded w-2/3" />
        </div>
        <div v-else-if="agenda.length === 0" class="col-span-full text-center py-8 text-parchment-500 text-sm">Belum ada agenda mendatang.</div>
        <NuxtLink
          v-else
          v-for="a in agenda"
          :key="a.id"
          :to="`/agenda/${a.slug}`"
          class="card p-6 hover:border-sage-300/40 hover:bg-ink-850/80 transition-all group flex flex-col justify-between"
        >
          <div>
            <span class="badge-sage mb-3 inline-block capitalize">{{ a.jenis.replace('_', ' ') }}</span>
            <h3 class="font-bold text-lg text-parchment-100 group-hover:text-sage-300 transition">{{ a.judul }}</h3>
            <p class="text-xs text-parchment-400 mt-2">📍 {{ a.lokasi }}</p>
            <p class="text-xs text-ochre-400 font-mono mt-1">🗓️ {{ formatTanggal(a.mulai_pada) }}</p>
          </div>
          <span class="text-xs text-sage-300 font-semibold mt-4 pt-3 border-t border-ink-800/80 block">Detail Kegiatan →</span>
        </NuxtLink>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'default' })

const { request } = useApi()

const artikelTerbaru = ref<any[]>([])
const karyaPilihan   = ref<any[]>([])
const agenda         = ref<any[]>([])
const loadingArtikel = ref(true)
const loadingKarya   = ref(true)
const loadingAgenda  = ref(true)

function formatTanggal(d: string) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

onMounted(async () => {
  Promise.allSettled([
    request<{ data: any[] }>('/artikel?per_page=6').then(r => { artikelTerbaru.value = r.data; loadingArtikel.value = false }).catch(() => { loadingArtikel.value = false }),
    request<{ data: any[] }>('/karya?per_page=4').then(r => { karyaPilihan.value = r.data; loadingKarya.value = false }).catch(() => { loadingKarya.value = false }),
    request<{ data: any[] }>('/agenda?waktu=mendatang&per_page=3').then(r => { agenda.value = r.data; loadingAgenda.value = false }).catch(() => { loadingAgenda.value = false }),
  ])
})
</script>
