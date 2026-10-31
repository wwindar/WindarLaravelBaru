<template>
  <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
      <div>
        <span class="text-xs font-bold text-indigo-brand uppercase tracking-wider block mb-1">Kegiatan Literasi</span>
        <h1 class="font-heading text-3xl font-extrabold text-ink">Agenda & Sayembara Sastra</h1>
        <p class="text-xs sm:text-sm text-ink-muted mt-1">Jadwal bedah buku, lomba cipta sastra, panggung puisi terbuka, dan kelas kepenulisan.</p>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="card p-5 space-y-4">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label for="agenda-search" class="label">Cari Nama Kegiatan</label>
          <input
            id="agenda-search"
            v-model="filter.search"
            type="search"
            placeholder="Ketik judul kegiatan..."
            class="input"
            @input="debouncedFetch"
          />
        </div>

        <div>
          <label for="agenda-waktu" class="label">Periode Waktu</label>
          <select id="agenda-waktu" v-model="filter.waktu" class="input" @change="fetchAgenda">
            <option value="">Semua Waktu</option>
            <option value="mendatang">Kegiatan Mendatang</option>
            <option value="lampau">Kegiatan Selesai</option>
          </select>
        </div>

        <div>
          <label for="agenda-jenis" class="label">Jenis Acara</label>
          <select id="agenda-jenis" v-model="filter.jenis" class="input" @change="fetchAgenda">
            <option value="">Semua Format</option>
            <option value="bedah_buku">Bedah Buku</option>
            <option value="lomba">Lomba & Sayembara</option>
            <option value="open_mic">Panggung Terbuka (Open Mic)</option>
            <option value="workshop">Workshop & Lokakarya</option>
          </select>
        </div>
      </div>
    </div>

    <!-- 4 Kondisi State -->
    <DataState
      :loading="loading"
      :error="errorMsg"
      :empty="agendaList.length === 0"
      empty-title="Tidak ada agenda"
      empty-description="Belum ada jadwal kegiatan sastra yang sesuai dengan filter waktu yang Anda pilih."
      @retry="fetchAgenda"
    >
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <NuxtLink
          v-for="a in agendaList"
          :key="a.id"
          :to="`/agenda/${a.slug}`"
          class="card card-hover p-6 flex flex-col justify-between group"
        >
          <div>
            <div class="flex items-center justify-between mb-3">
              <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-soft text-indigo-brand capitalize">
                {{ formatJenis(a.jenis) }}
              </span>
              <span v-if="a.kuota" class="text-xs text-ink-subtle">
                Kuota: {{ a.kuota }} Peserta
              </span>
            </div>

            <h2 class="font-heading text-lg font-bold text-ink group-hover:text-indigo-brand transition leading-snug">
              {{ a.judul }}
            </h2>

            <div class="space-y-1 mt-3 text-xs text-ink-muted">
              <p class="flex items-center gap-1.5">
                <span>📍</span>
                <span class="truncate">{{ a.lokasi }}</span>
              </p>
              <p class="flex items-center gap-1.5">
                <span>🗓️</span>
                <span>{{ formatTanggal(a.mulai_pada) }}</span>
              </p>
            </div>

            <p class="text-xs text-ink-muted mt-3 line-clamp-3 leading-relaxed">
              {{ a.deskripsi }}
            </p>
          </div>

          <div class="pt-4 border-t border-card-border mt-5 text-xs text-indigo-brand font-semibold flex items-center justify-between">
            <span>Lihat Informasi Lengkap</span>
            <span>→</span>
          </div>
        </NuxtLink>
      </div>
    </DataState>
  </div>
</template>

<script setup lang="ts">
const config = useRuntimeConfig()
const apiBase = config.public.apiBase as string

const filter = reactive({ search: '', waktu: 'mendatang', jenis: '' })
const agendaList = ref<any[]>([])
const loading = ref(false)
const errorMsg = ref('')

function formatTanggal(d: string) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

function formatJenis(j: string) {
  switch (j) {
    case 'bedah_buku': return 'Bedah Buku'
    case 'open_mic': return 'Open Mic'
    case 'workshop': return 'Workshop'
    case 'lomba': return 'Lomba Sastra'
    default: return j
  }
}

async function fetchAgenda() {
  loading.value = true
  errorMsg.value = ''
  try {
    const params = new URLSearchParams()
    if (filter.search) params.append('search', filter.search)
    if (filter.waktu) params.append('waktu', filter.waktu)
    if (filter.jenis) params.append('jenis', filter.jenis)

    const res = await $fetch<any>(`${apiBase}/agenda?${params.toString()}`)
    agendaList.value = res.data || []
  } catch (err: any) {
    errorMsg.value = err.data?.message || 'Gagal memuat agenda kegiatan.'
  } finally {
    loading.value = false
  }
}

let timer: ReturnType<typeof setTimeout>
const debouncedFetch = () => {
  clearTimeout(timer)
  timer = setTimeout(() => fetchAgenda(), 350)
}

onMounted(() => fetchAgenda())
</script>
