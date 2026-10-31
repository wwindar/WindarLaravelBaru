<template>
  <div class="space-y-16 sm:space-y-24 py-8 sm:py-12">
    <!-- 1. Hero Section dengan Gambar Banner -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6">
      <div
        class="relative overflow-hidden rounded-3xl border border-card-border p-8 sm:p-14 lg:p-16 text-center shadow-card"
        style="
          background-image: url('/hero-perpustakaan-umpku.png');
          background-size: cover;
          background-position: center bottom;
          background-repeat: no-repeat;
        "
      >
        <!-- Overlay radial tipis hanya di belakang blok teks tengah, sisi kiri-kanan tetap terlihat -->
        <div
          class="absolute inset-0 pointer-events-none"
          style="background: radial-gradient(ellipse 65% 100% at 50% 50%, rgba(255,255,255,0.55) 0%, rgba(255,255,255,0.30) 45%, transparent 75%);"
        ></div>

        <!-- Konten Hero: TETAP LENGKAP & TIDAK DIUBAH-UBAH -->
        <div class="relative z-10">
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-soft/90 backdrop-blur-xs text-indigo-brand text-xs font-semibold mb-6 border border-indigo-brand/20">
            <span class="w-2 h-2 rounded-full bg-indigo-brand animate-pulse"></span>
            Platform Literasi & Sastra Digital Kampus
          </div>

          <h1 class="font-heading text-3xl sm:text-5xl lg:text-6xl font-extrabold text-ink tracking-tight max-w-4xl mx-auto leading-tight">
            Menghidupkan Kata, <br class="hidden sm:inline" />
            <span class="text-indigo-brand">Merawat Nalar Sastra</span> Kampus
          </h1>

          <p class="mt-5 text-base sm:text-lg text-ink-muted max-w-2xl mx-auto leading-relaxed">
            Ruang terbuka bagi mahasiswa untuk menerbitkan puisi, cerpen, esai kritis, dan artikel pemikiran yang dikurasi langsung oleh dewan dosen editor.
          </p>

          <!-- Kolom Pencarian Cepat di Hero -->
          <form @submit.prevent="handleHeroSearch" class="mt-8 max-w-xl mx-auto flex items-center gap-2 p-1.5 bg-white border border-card-border rounded-2xl shadow-card focus-within:border-indigo-brand focus-within:ring-2 focus-within:ring-indigo-soft">
            <label for="hero-search" class="sr-only">Cari judul atau isi karya sastra</label>
            <div class="pl-3 text-ink-subtle">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              id="hero-search"
              v-model="searchQuery"
              type="search"
              placeholder="Cari judul puisi, cerpen, esai, atau penulis..."
              class="w-full py-2.5 px-2 text-sm text-ink bg-transparent focus:outline-none placeholder-ink-subtle"
            />
            <button type="submit" class="btn-primary text-xs px-5 py-2.5 shrink-0">
              Cari
            </button>
          </form>

          <!-- CTA Links -->
          <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <NuxtLink to="/karya" class="btn-secondary text-xs px-4 py-2 shadow-xs">
              Jelajahi Seluruh Karya
            </NuxtLink>
            <NuxtLink v-if="!auth.isLoggedIn" to="/register" class="btn-ghost text-xs px-4 py-2 text-indigo-brand hover:bg-indigo-soft font-semibold">
              Daftar Sebagai Penulis →
            </NuxtLink>
            <NuxtLink v-else-if="auth.isMahasiswa" to="/mahasiswa/tulisan/baru" class="btn-primary text-xs px-4 py-2">
              + Mulai Menulis Sekarang
            </NuxtLink>
          </div>
        </div>
      </div>
    </section>

    <!-- 2. Alur Penerbitan (Tulis -> Ajukan -> Setujui -> Terbit) -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6">
      <div class="text-center max-w-2xl mx-auto mb-12">
        <span class="text-xs font-bold text-indigo-brand uppercase tracking-wider block mb-1">Mekanisme Kurasi</span>
        <h2 class="font-heading text-2xl sm:text-3xl font-bold text-ink">Alur Publikasi Naskah Kampus</h2>
        <p class="text-xs sm:text-sm text-ink-muted mt-2">
          Setiap naskah melalui proses telaah yang transparan demi menjaga mutu karya dan integritas intelektual.
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Langkah 1: Tulis -->
        <div class="card p-6 relative group hover:border-indigo-brand/40 transition">
          <div class="w-10 h-10 rounded-xl bg-indigo-soft text-indigo-brand font-heading font-bold text-base flex items-center justify-center mb-4">
            1
          </div>
          <h3 class="font-heading text-base font-bold text-ink mb-2">Tulis Draf</h3>
          <p class="text-xs text-ink-muted leading-relaxed">
            Mahasiswa menyusun naskah (puisi, cerpen, esai, atau artikel) dan menyimpannya sebagai draf pribadi sewaktu-waktu.
          </p>
        </div>

        <!-- Langkah 2: Ajukan -->
        <div class="card p-6 relative group hover:border-indigo-brand/40 transition">
          <div class="w-10 h-10 rounded-xl bg-indigo-soft text-indigo-brand font-heading font-bold text-base flex items-center justify-center mb-4">
            2
          </div>
          <h3 class="font-heading text-base font-bold text-ink mb-2">Ajukan Publikasi</h3>
          <p class="text-xs text-ink-muted leading-relaxed">
            Satu klik "Ajukan publikasi" mengirim naskah ke antrean telaah dosen/editor dengan status Menunggu Persetujuan.
          </p>
        </div>

        <!-- Langkah 3: Setujui / Revisi -->
        <div class="card p-6 relative group hover:border-indigo-brand/40 transition">
          <div class="w-10 h-10 rounded-xl bg-indigo-soft text-indigo-brand font-heading font-bold text-base flex items-center justify-center mb-4">
            3
          </div>
          <h3 class="font-heading text-base font-bold text-ink mb-2">Telaah Dosen</h3>
          <p class="text-xs text-ink-muted leading-relaxed">
            Dosen menelaah naskah. Dosen dapat menyetujui, meminta revisi dengan catatan wajib, atau menolak secara objektif.
          </p>
        </div>

        <!-- Langkah 4: Terbit -->
        <div class="card p-6 relative group hover:border-indigo-brand/40 transition">
          <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 font-heading font-bold text-base flex items-center justify-center mb-4">
            4
          </div>
          <h3 class="font-heading text-base font-bold text-ink mb-2">Terbit Otomatis</h3>
          <p class="text-xs text-ink-muted leading-relaxed">
            Karya yang disetujui langsung terbit di katalog publik. Mahasiswa menerima notifikasi in-app bahwa karyanya telah tayang.
          </p>
        </div>
      </div>
    </section>

    <!-- 3. Tulisan Baru Terbit (Card Grid dengan 4 Kondisi State) -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6">
      <!-- Banner Karya Sastra -->
      <div class="relative overflow-hidden rounded-2xl mb-8 border border-card-border shadow-card">
        <img
          src="/images/karya-banner.png"
          alt="Koleksi Karya Sastra Kampus"
          class="w-full h-40 sm:h-52 object-cover object-center opacity-80 dark:opacity-50"
        />
        <div class="absolute inset-0 bg-gradient-to-r from-card/90 via-card/60 to-transparent flex flex-col sm:flex-row sm:items-end justify-between gap-4 p-6 sm:p-8">
          <div>
            <span class="text-xs font-bold text-indigo-brand uppercase tracking-wider block mb-1">Katalog Publik</span>
            <h2 class="font-heading text-2xl sm:text-3xl font-bold text-ink">Tulisan Baru Terbit</h2>
            <p class="text-xs sm:text-sm text-ink-muted mt-1">Karya pilihan mahasiswa yang telah lolos kurasi kurator redaksi.</p>
          </div>
          <NuxtLink to="/karya" class="btn-secondary text-xs px-4 py-2 self-start sm:self-auto shrink-0">
            Lihat Semua ({{ totalKarya }}) →
          </NuxtLink>
        </div>
      </div>

      <DataState
        :loading="isLoading"
        :error="errorMessage"
        :empty="tulisanList.length === 0"
        empty-title="Belum ada tulisan terbit"
        empty-description="Jadilah penulis pertama yang menerbitkan karyanya di Aksara Kampus!"
        @retry="fetchLatestTulisan"
      >
        <template #empty-action>
          <NuxtLink v-if="!auth.isLoggedIn" to="/register" class="btn-primary text-xs px-5 py-2.5">
            Daftar & Kirim Naskah
          </NuxtLink>
          <NuxtLink v-else-if="auth.isMahasiswa" to="/mahasiswa/tulisan/baru" class="btn-primary text-xs px-5 py-2.5">
            + Tulis Karya Pertama
          </NuxtLink>
        </template>

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
                <h3 class="font-heading text-lg font-bold text-ink group-hover:text-indigo-brand transition line-clamp-2 leading-snug">
                  {{ item.judul }}
                </h3>
              </NuxtLink>

              <p class="text-xs text-ink-muted mt-2.5 line-clamp-3 leading-relaxed">
                {{ item.ringkasan || 'Klik tautan untuk membaca selengkapnya.' }}
              </p>
            </div>

            <div class="pt-4 mt-4 border-t border-card-border flex items-center justify-between text-xs text-ink-muted">
              <span class="font-medium text-ink truncate">
                Oleh {{ item.penulis?.name }}
              </span>
              <NuxtLink :to="`/karya/${item.slug}`" class="text-indigo-brand hover:underline font-semibold shrink-0">
                Baca naskah →
              </NuxtLink>
            </div>
          </article>
        </div>
      </DataState>
    </section>

    <!-- 3.5. Berita, Iklan & Pengumuman Kampus Terbaru -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6">
      <div class="card p-6 sm:p-8 bg-gradient-to-r from-amber-50/50 via-white to-amber-50/30 border-amber-200">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-lg shrink-0">
              📢
            </div>
            <div>
              <h2 class="font-heading text-xl sm:text-2xl font-bold text-ink">Warta & Pengumuman Redaksi</h2>
              <p class="text-xs text-ink-muted mt-0.5">Info sayembara cipta sastra, seminar kepenulisan, dan iklan kampus resmi.</p>
            </div>
          </div>
          <NuxtLink to="/berita" class="btn-secondary text-xs px-4 py-2 self-start sm:self-auto shrink-0">
            Lihat Semua Warta →
          </NuxtLink>
        </div>

        <div v-if="beritaList.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <article
            v-for="b in beritaList"
            :key="b.id"
            class="p-5 rounded-2xl bg-card border border-card-border hover:border-amber-400 transition shadow-sm flex flex-col justify-between"
          >
            <div>
              <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 bg-amber-500/10 px-2 py-0.5 rounded-full inline-block mb-2 border border-amber-500/20">
                Info Resmi
              </span>
              <NuxtLink :to="`/berita/${b.slug}`" class="block group">
                <h3 class="font-heading font-bold text-sm text-ink group-hover:text-indigo-brand transition line-clamp-2 leading-snug">
                  {{ b.judul }}
                </h3>
              </NuxtLink>
              <p class="text-xs text-ink-muted mt-2 line-clamp-2 leading-relaxed">
                {{ b.ringkasan }}
              </p>
            </div>
            <div class="pt-3 mt-3 border-t border-card-border flex items-center justify-between text-[11px] text-ink-subtle">
              <span>{{ b.diterbitkan_pada_formatted || formatDate(b.diterbitkan_pada) }}</span>
              <NuxtLink :to="`/berita/${b.slug}`" class="text-indigo-brand font-semibold hover:underline">
                Baca →
              </NuxtLink>
            </div>
          </article>
        </div>
        <div v-else class="text-center py-6 text-xs text-ink-muted">
          Belum ada pengumuman resmi yang tayang saat ini.
        </div>
      </div>
    </section>

    <!-- 4. Penjelasan Tiga Peran Pengguna -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6">
      <div class="card p-8 sm:p-12 border-indigo-brand/20 bg-gradient-to-b from-card to-indigo-soft/10">
        <div class="max-w-2xl mb-10">
          <span class="text-xs font-bold text-indigo-brand uppercase tracking-wider block mb-1">Struktur Komunitas</span>
          <h2 class="font-heading text-2xl sm:text-3xl font-bold text-ink">Tiga Peran di Aksara Kampus</h2>
          <p class="text-xs sm:text-sm text-ink-muted mt-2">
            Sistem dirancang dengan hak akses terpisah demi alur kerja penerbitan yang rapi, akuntabel, dan aman.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Peran 1: Mahasiswa -->
          <div class="p-6 rounded-2xl bg-card border border-card-border shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-indigo-soft text-indigo-brand flex items-center justify-center font-bold text-sm mb-4">
              01
            </div>
            <h3 class="font-heading text-base font-bold text-ink mb-2">Mahasiswa (Penulis)</h3>
            <ul class="space-y-2 text-xs text-ink-muted">
              <li>• Masuk menggunakan email resmi kampus</li>
              <li>• Membuat, mengedit, & mengelola draf tulisan</li>
              <li>• Mengajukan naskah untuk telaah kurasi</li>
              <li>• Memantau status (Draf, Menunggu, Terbit, Revisi, Ditolak)</li>
              <li>• Mendapat notifikasi in-app saat status diperbarui</li>
            </ul>
          </div>

          <!-- Peran 2: Dosen / Editor -->
          <div class="p-6 rounded-2xl bg-card border border-card-border shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-orange-500/15 text-orange-600 dark:text-orange-300 flex items-center justify-center font-bold text-sm mb-4">
              02
            </div>
            <h3 class="font-heading text-base font-bold text-ink mb-2">Dosen / Editor</h3>
            <ul class="space-y-2 text-xs text-ink-muted">
              <li>• Akun disiapkan melalui seeder dewan redaksi</li>
              <li>• Membaca antrean naskah yang masuk</li>
              <li>• Menyetujui naskah dalam satu klik (langsung terbit)</li>
              <li>• Meminta revisi atau menolak dengan catatan edukatif</li>
              <li>• Meninjau laporan aduan plagiasi dari publik</li>
            </ul>
          </div>

          <!-- Peran 3: Pengunjung Publik -->
          <div class="p-6 rounded-2xl bg-card border border-card-border shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-amber-500/15 text-amber-600 dark:text-amber-300 flex items-center justify-center font-bold text-sm mb-4">
              03
            </div>
            <h3 class="font-heading text-base font-bold text-ink mb-2">Pengunjung Publik</h3>
            <ul class="space-y-2 text-xs text-ink-muted">
              <li>• Tanpa perlu login atau mendaftar</li>
              <li>• Bebas membaca seluruh karya yang telah terbit</li>
              <li>• Cari naskah dan filter kategori/penulis/tanggal</li>
              <li>• Membagikan karya ke media sosial / tautan langsung</li>
              <li>• Laporkan tulisan jika menemukan indikasi plagiarisme</li>
            </ul>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
const auth = useAuthStore()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase as string

const searchQuery = ref('')
const isLoading = ref(true)
const errorMessage = ref('')
const tulisanList = ref<any[]>([])
const totalKarya = ref(0)
const beritaList = ref<any[]>([])

function handleHeroSearch() {
  if (searchQuery.value.trim()) {
    navigateTo({ path: '/karya', query: { search: searchQuery.value.trim() } })
  } else {
    navigateTo('/karya')
  }
}

async function fetchLatestTulisan() {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const res = await $fetch<any>(`${apiBase}/tulisan?per_page=6`)
    tulisanList.value = res.data || []
    totalKarya.value = res.meta?.total || tulisanList.value.length
  } catch (err: any) {
    errorMessage.value = err.data?.message || 'Gagal memuat daftar tulisan terbaru. Pastikan backend aktif.'
  } finally {
    isLoading.value = false
  }
}

async function fetchLatestBerita() {
  try {
    const res = await $fetch<any>(`${apiBase}/berita?per_page=3`)
    beritaList.value = res.data || []
  } catch {}
}

function formatDate(iso: string) {
  if (!iso) return 'Baru terbit'
  const d = new Date(iso)
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

onMounted(() => {
  fetchLatestTulisan()
  fetchLatestBerita()
})
</script>
