<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-white">Dashboard Redaksi</h1>
      <p class="text-slate-400 text-sm mt-1">Selamat datang, <span class="text-amber-400">{{ auth.user?.name }}</span></p>
    </div>

    <!-- Stats cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="card p-5">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Artikel</p>
        <p class="text-3xl font-bold text-white mt-2">{{ stats.artikel }}</p>
        <p class="text-xs text-slate-500 mt-1">Berita & Blog</p>
      </div>
      <div class="card p-5">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Karya Masuk</p>
        <p class="text-3xl font-bold text-amber-400 mt-2">{{ stats.karya_baru }}</p>
        <p class="text-xs text-slate-500 mt-1">Perlu ditinjau</p>
      </div>
      <div class="card p-5">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Karya Terbit</p>
        <p class="text-3xl font-bold text-emerald-400 mt-2">{{ stats.karya_terbit }}</p>
        <p class="text-xs text-slate-500 mt-1">Dipublikasikan</p>
      </div>
      <div class="card p-5">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Agenda</p>
        <p class="text-3xl font-bold text-sky-400 mt-2">{{ stats.agenda }}</p>
        <p class="text-xs text-slate-500 mt-1">Mendatang</p>
      </div>
    </div>

    <!-- Quick actions -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <NuxtLink to="/admin/artikel/buat" class="card p-5 hover:border-amber-500/30 hover:bg-slate-800/50 transition group">
        <div class="w-9 h-9 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center mb-3 group-hover:bg-amber-500/20 transition">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        </div>
        <p class="font-semibold text-white text-sm">Tulis Artikel</p>
        <p class="text-xs text-slate-500 mt-0.5">Buat berita atau blog baru</p>
      </NuxtLink>

      <NuxtLink to="/admin/karya" class="card p-5 hover:border-amber-500/30 hover:bg-slate-800/50 transition group">
        <div class="w-9 h-9 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center mb-3 group-hover:bg-sky-500/20 transition">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        </div>
        <p class="font-semibold text-white text-sm">Kurasi Karya</p>
        <p class="text-xs text-slate-500 mt-0.5">Tinjau kiriman naskah penulis</p>
      </NuxtLink>

      <NuxtLink to="/admin/agenda/buat" class="card p-5 hover:border-amber-500/30 hover:bg-slate-800/50 transition group">
        <div class="w-9 h-9 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-3 group-hover:bg-emerald-500/20 transition">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <p class="font-semibold text-white text-sm">Tambah Agenda</p>
        <p class="text-xs text-slate-500 mt-0.5">Buat event sastra baru</p>
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'admin', middleware: 'admin' })

const auth   = useAuthStore()
const { request } = useApi()

const stats = reactive({
  artikel: 0,
  karya_baru: 0,
  karya_terbit: 0,
  agenda: 0,
})

onMounted(async () => {
  try {
    const [artRes, karyaRes, karyaTermRes, agendaRes] = await Promise.allSettled([
      request<{ meta: { total: number } }>('/admin/artikel'),
      request<{ meta: { total: number } }>('/admin/karya?status=dikirim'),
      request<{ meta: { total: number } }>('/admin/karya?status=terbit'),
      request<{ meta: { total: number } }>('/admin/agenda?waktu=mendatang'),
    ])

    if (artRes.status === 'fulfilled')      stats.artikel      = (artRes.value as any).meta?.total ?? 0
    if (karyaRes.status === 'fulfilled')    stats.karya_baru   = (karyaRes.value as any).meta?.total ?? 0
    if (karyaTermRes.status === 'fulfilled') stats.karya_terbit = (karyaTermRes.value as any).meta?.total ?? 0
    if (agendaRes.status === 'fulfilled')   stats.agenda       = (agendaRes.value as any).meta?.total ?? 0
  } catch {}
})
</script>

