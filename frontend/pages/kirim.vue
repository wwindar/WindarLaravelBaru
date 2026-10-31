<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 py-12 space-y-10">
    <div class="text-center space-y-2">
      <h1 class="text-3xl sm:text-4xl font-bold text-white">Ruang Penulis</h1>
      <p class="text-slate-400 text-sm">Kirimkan naskah karya sastramu atau pantau proses kurasi redaksi.</p>
    </div>

    <!-- Tab Selector -->
    <div class="flex border-b border-slate-800">
      <button
        @click="activeTab = 'kirim'"
        :class="activeTab === 'kirim' ? 'border-amber-500 text-amber-400' : 'border-transparent text-slate-400 hover:text-slate-200'"
        class="flex-1 py-3 text-center border-b-2 font-semibold text-sm transition"
      >
        ✍️ Kirim Naskah Baru
      </button>
      <button
        v-if="auth.isLoggedIn"
        @click="activeTab = 'saya'"
        :class="activeTab === 'saya' ? 'border-amber-500 text-amber-400' : 'border-transparent text-slate-400 hover:text-slate-200'"
        class="flex-1 py-3 text-center border-b-2 font-semibold text-sm transition"
      >
        📚 Karya Saya ({{ karyaSayaList.length }})
      </button>
      <button
        @click="activeTab = 'lacak'"
        :class="activeTab === 'lacak' ? 'border-amber-500 text-amber-400' : 'border-transparent text-slate-400 hover:text-slate-200'"
        class="flex-1 py-3 text-center border-b-2 font-semibold text-sm transition"
      >
        🔍 Lacak / Revisi Naskah
      </button>
    </div>

    <!-- TAB 1: FORM KIRIM -->
    <div v-if="activeTab === 'kirim'">
      <!-- JIKA BELUM LOGIN: TAMPILKAN PERINGATAN EKSKLUSIF MAHASISWA UMPKU -->
      <div v-if="!auth.isLoggedIn" class="card p-8 sm:p-12 text-center space-y-5 border border-amber-500/20">
        <div class="w-16 h-16 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-3xl mx-auto shadow-inner">
          🎓
        </div>
        <div class="max-w-lg mx-auto space-y-2">
          <h2 class="text-2xl font-bold text-white">Khusus Mahasiswa Kampus</h2>
          <p class="text-slate-400 text-sm leading-relaxed">
            Pengiriman naskah karya sastra hanya dibuka untuk mahasiswa dan civitas akademika kampus resmi (<strong class="text-amber-400">@students.itspku.ac.id</strong> / <strong class="text-amber-400">@student.umpku.ac.id</strong>).
          </p>
          <p class="text-slate-500 text-xs">
            Pengunjung umum dipersilakan menikmati dan membaca karya-karya sastra yang telah terbit.
          </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
          <NuxtLink to="/login" class="btn-primary w-full sm:w-auto px-6 py-2.5">
            Masuk dengan Akun Kampus
          </NuxtLink>
          <NuxtLink to="/register" class="btn-ghost w-full sm:w-auto px-6 py-2.5">
            Daftar Akun Mahasiswa
          </NuxtLink>
        </div>
      </div>

      <!-- JIKA SUDAH LOGIN SEBAGAI MAHASISWA / ADMIN: TAMPILKAN FORM -->
      <div v-else class="card p-6 sm:p-8 space-y-6">
        <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 rounded-lg text-xs flex items-center gap-2">
          <span>✅</span>
          <span>Terautentikasi sebagai mahasiswa UMPKU: <strong>{{ auth.user?.email }}</strong></span>
        </div>

        <div v-if="suksesKirim" class="p-6 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 space-y-3">
          <h3 class="text-lg font-bold text-emerald-400">🎉 Naskah Berhasil Dikirim!</h3>
          <p class="text-sm">Simpan nomor registrasi kiriman ini untuk melacak status atau merevisi naskah:</p>
          <div class="p-3 bg-slate-900 border border-slate-700 rounded-lg font-mono text-base font-bold text-amber-400 select-all">
            {{ suksesKirim.nomor_kiriman }}
          </div>
          <p class="text-xs text-slate-400">Kamu juga bisa melihat progres kurasi naskahmu langsung di tab <strong>"Karya Saya"</strong>.</p>
          <div class="flex gap-2 pt-2">
            <button @click="resetFormKirim" class="btn-ghost text-xs">Kirim Naskah Lain</button>
            <button @click="activeTab = 'saya'; fetchKaryaSaya()" class="btn-primary text-xs">Lihat di Karya Saya →</button>
          </div>
        </div>

        <form v-else @submit.prevent="submitKarya" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="label">Nama Lengkap Mahasiswa</label>
              <input v-model="formKirim.nama_penulis" type="text" class="input bg-slate-900 text-slate-300 cursor-not-allowed" readonly />
            </div>
            <div>
              <label class="label">Email Mahasiswa UMPKU</label>
              <input v-model="formKirim.email" type="email" class="input bg-slate-900 text-slate-300 cursor-not-allowed" readonly />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
              <label class="label">Judul Karya <span class="text-rose-400">*</span></label>
              <input v-model="formKirim.judul" type="text" class="input" placeholder="Judul puisi, cerpen, atau esai..." required />
              <p v-if="errorsKirim.judul" class="text-xs text-rose-400 mt-1">{{ errorsKirim.judul[0] }}</p>
            </div>
            <div>
              <label class="label">Jenis Sastra <span class="text-rose-400">*</span></label>
              <select v-model="formKirim.jenis" class="input" required>
                <option value="puisi">Puisi</option>
                <option value="cerpen">Cerpen</option>
                <option value="esai">Esai</option>
              </select>
            </div>
          </div>

          <div>
            <label class="label">Isi Karya (Minimal 50 Karakter) <span class="text-rose-400">*</span></label>
            <textarea
              v-model="formKirim.isi"
              rows="10"
              class="input font-mono text-sm resize-y leading-relaxed"
              placeholder="Tuliskan bait puisi atau prosa karyamu di sini..."
              required
            ></textarea>
            <div class="flex justify-between items-center text-xs mt-1 text-slate-500">
              <span>{{ formKirim.isi.length }} karakter</span>
              <span v-if="formKirim.isi.length < 50" class="text-amber-500">Kurang {{ 50 - formKirim.isi.length }} karakter lagi</span>
            </div>
            <p v-if="errorsKirim.isi" class="text-xs text-rose-400 mt-1">{{ errorsKirim.isi[0] }}</p>
          </div>

          <div v-if="errorKirimMsg" class="p-3 bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm rounded-lg">
            {{ errorKirimMsg }}
          </div>

          <button type="submit" class="btn-primary w-full py-3" :disabled="loadingKirim">
            <span v-if="loadingKirim">Mengirim naskah...</span>
            <span v-else>Kirimkan ke Redaksi Aksara Kampus</span>
          </button>
        </form>
      </div>
    </div>

    <!-- TAB 3: KARYA SAYA (MAHASISWA LOGIN) -->
    <div v-else-if="activeTab === 'saya'" class="space-y-4">
      <div v-if="loadingSaya" class="card p-12 text-center text-slate-500 animate-pulse">
        Memuat naskah karyamu...
      </div>
      <div v-else-if="karyaSayaList.length === 0" class="card p-12 text-center text-slate-500 space-y-3">
        <p>Kamu belum pernah mengirimkan karya.</p>
        <button @click="activeTab = 'kirim'" class="btn-primary text-xs">Mulai Kirim Karya Sekarang</button>
      </div>
      <div v-else class="grid grid-cols-1 gap-4">
        <div
          v-for="k in karyaSayaList"
          :key="k.id"
          class="card p-6 border hover:border-amber-500/30 transition space-y-3"
        >
          <div class="flex flex-col sm:flex-row justify-between items-start gap-2">
            <div>
              <div class="flex items-center gap-2 mb-1">
                <span class="badge-purple capitalize">{{ k.jenis }}</span>
                <span class="text-xs text-amber-500/80 font-mono">{{ k.nomor_kiriman }}</span>
              </div>
              <h3 class="text-lg font-bold text-white">{{ k.judul }}</h3>
            </div>
            <span :class="badgeStatus(k.status)">
              {{ k.status_label }}
            </span>
          </div>

          <div v-if="k.catatan_redaksi" class="p-3 rounded-lg bg-amber-500/10 border border-amber-500/20 text-xs text-amber-200">
            <strong>Catatan Redaksi:</strong> {{ k.catatan_redaksi }}
          </div>

          <div class="pt-3 border-t border-slate-800 flex justify-between items-center text-xs">
            <span class="text-slate-500">{{ k.created_at }}</span>
            <button
              v-if="k.status === 'perlu_revisi'"
              @click="bukaRevisiKaryaSaya(k)"
              class="btn-primary text-xs px-3 py-1"
            >
              Ajukan Revisi ✏️
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 2: LACAK & REVISI -->
    <div v-else class="space-y-6">
      <div class="card p-6 sm:p-8 space-y-4">
        <h3 class="text-lg font-bold text-white">Lacak Kiriman Naskah</h3>
        <p class="text-xs text-slate-400">Masukkan nomor kiriman dan email yang digunakan saat mendaftar.</p>

        <form @submit.prevent="lacakKarya" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <input v-model="formLacak.nomor_kiriman" type="text" placeholder="AKS-2026-00001" class="input font-mono uppercase" required />
          <input v-model="formLacak.email" type="email" placeholder="Email Anda" class="input" required />
          <button type="submit" class="btn-primary" :disabled="loadingLacak">
            {{ loadingLacak ? 'Mengecek...' : 'Lacak Status' }}
          </button>
        </form>

        <div v-if="errorLacakMsg" class="p-3 bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm rounded-lg">
          {{ errorLacakMsg }}
        </div>
      </div>

      <!-- Detail Hasil Lacak -->
      <div v-if="hasilLacak" class="card p-6 sm:p-8 space-y-6 border border-amber-500/30">
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 border-b border-slate-800 pb-5">
          <div>
            <div class="flex items-center gap-2 mb-1">
              <span class="badge-purple capitalize">{{ hasilLacak.jenis }}</span>
              <span class="font-mono text-xs text-amber-500">{{ hasilLacak.nomor_kiriman }}</span>
            </div>
            <h2 class="text-2xl font-bold text-white">{{ hasilLacak.judul }}</h2>
            <p class="text-xs text-slate-400 mt-1">Penulis: {{ hasilLacak.nama_penulis }} ({{ hasilLacak.email }})</p>
          </div>
          <span :class="badgeStatus(hasilLacak.status)" class="text-sm px-3 py-1">
            {{ hasilLacak.status_label }}
          </span>
        </div>

        <div v-if="hasilLacak.catatan_redaksi" class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-200">
          <p class="text-xs font-bold uppercase tracking-wider text-amber-400 mb-1">Catatan Redaksi:</p>
          <p class="text-sm">{{ hasilLacak.catatan_redaksi }}</p>
        </div>

        <!-- FORM REVISI JIKA STATUS PERLU_REVISI -->
        <div v-if="hasilLacak.status === 'perlu_revisi'" class="p-6 bg-slate-950 rounded-xl border border-rose-500/30 space-y-4">
          <h4 class="text-base font-bold text-amber-400">📝 Ajukan Revisi Naskah</h4>
          <p class="text-xs text-slate-400">Redaksi meminta perbaikan naskah Anda. Silakan perbarui judul dan isi karya di bawah ini:</p>

          <form @submit.prevent="submitRevisi" class="space-y-4">
            <div>
              <label class="label">Judul Naskah</label>
              <input v-model="formRevisi.judul" type="text" class="input" required />
            </div>
            <div>
              <label class="label">Isi Naskah Revisi</label>
              <textarea v-model="formRevisi.isi" rows="8" class="input font-mono text-sm" required></textarea>
            </div>
            <div v-if="suksesRevisi" class="text-emerald-400 text-sm font-semibold">
              Revisi berhasil dikirimkan kembali ke redaksi!
            </div>
            <button type="submit" class="btn-primary" :disabled="loadingRevisi">
              {{ loadingRevisi ? 'Mengirim Revisi...' : 'Kirim Ulang Naskah Revisi' }}
            </button>
          </form>
        </div>

        <!-- Riwayat Proses -->
        <div>
          <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Riwayat Kurasi</h4>
          <div class="space-y-3">
            <div v-for="r in hasilLacak.riwayat" :key="r.id" class="p-3 bg-slate-950 rounded-lg border border-slate-800 text-xs flex justify-between items-center">
              <div>
                <p class="font-bold text-slate-200">{{ r.status_label ?? r.status }}</p>
                <p v-if="r.catatan" class="text-slate-400 mt-0.5">{{ r.catatan }}</p>
              </div>
              <span class="text-slate-600">{{ r.created_at }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'default' })

const { request } = useApi()
const auth = useAuthStore()

const activeTab = ref<'kirim' | 'saya' | 'lacak'>('kirim')

// Form Kirim
const formKirim = reactive({
  nama_penulis: '',
  email: '',
  judul: '',
  jenis: 'puisi',
  isi: '',
})
const loadingKirim = ref(false)
const errorKirimMsg = ref('')
const errorsKirim = ref<Record<string, string[]>>({})
const suksesKirim = ref<any>(null)

// Karya Saya
const karyaSayaList = ref<any[]>([])
const loadingSaya = ref(false)

async function fetchKaryaSaya() {
  if (!auth.isLoggedIn) return
  loadingSaya.value = true
  try {
    const res = await request<{ data: any[] }>('/auth/karya-saya')
    karyaSayaList.value = res.data
  } catch {
    karyaSayaList.value = []
  } finally {
    loadingSaya.value = false
  }
}

function bukaRevisiKaryaSaya(k: any) {
  formLacak.nomor_kiriman = k.nomor_kiriman
  formLacak.email = auth.user?.email || k.email
  activeTab.value = 'lacak'
  lacakKarya()
}

onMounted(() => {
  if (auth.isLoggedIn && auth.user) {
    formKirim.nama_penulis = auth.user.name
    formKirim.email = auth.user.email
    fetchKaryaSaya()
  }
})

function resetFormKirim() {
  suksesKirim.value = null
  formKirim.judul = ''
  formKirim.isi = ''
}

async function submitKarya() {
  loadingKirim.value = true
  errorsKirim.value = {}
  errorKirimMsg.value = ''
  try {
    const res = await request<{ data: any }>('/kiriman', {
      method: 'POST',
      body: formKirim,
    })
    suksesKirim.value = res.data
  } catch (err: any) {
    if (err?.status === 422) {
      errorsKirim.value = err.data?.errors ?? {}
    } else {
      errorKirimMsg.value = err?.data?.message ?? 'Gagal mengirim naskah.'
    }
  } finally {
    loadingKirim.value = false
  }
}

// Form Lacak
const formLacak = reactive({ nomor_kiriman: '', email: '' })
const loadingLacak = ref(false)
const errorLacakMsg = ref('')
const hasilLacak = ref<any>(null)

// Form Revisi
const formRevisi = reactive({ judul: '', isi: '' })
const loadingRevisi = ref(false)
const suksesRevisi = ref(false)

async function lacakKarya() {
  loadingLacak.value = true
  errorLacakMsg.value = ''
  hasilLacak.value = null
  suksesRevisi.value = false
  try {
    const res = await request<{ data: any }>(
      `/kiriman/${formLacak.nomor_kiriman.trim()}?email=${encodeURIComponent(formLacak.email.trim())}`
    )
    hasilLacak.value = res.data
    formRevisi.judul = res.data.judul
    formRevisi.isi = res.data.isi
  } catch (err: any) {
    errorLacakMsg.value = err?.data?.message ?? 'Data kiriman tidak ditemukan atau email salah.'
  } finally {
    loadingLacak.value = false
  }
}

async function submitRevisi() {
  loadingRevisi.value = true
  try {
    const res = await request<{ data: any }>(`/kiriman/${hasilLacak.value.nomor_kiriman}`, {
      method: 'PUT',
      body: {
        email: formLacak.email,
        judul: formRevisi.judul,
        isi: formRevisi.isi,
      },
    })
    suksesRevisi.value = true
    await lacakKarya()
  } catch (err: any) {
    alert(err?.data?.message ?? 'Gagal mengirim revisi.')
  } finally {
    loadingRevisi.value = false
  }
}

function badgeStatus(status: string) {
  const map: Record<string, string> = {
    dikirim: 'badge-blue',
    ditinjau: 'badge-amber',
    perlu_revisi: 'badge-red',
    terbit: 'badge-green',
    ditolak: 'badge-slate',
  }
  return map[status] ?? 'badge-slate'
}
</script>

