<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-8">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <NuxtLink to="/mahasiswa" class="w-9 h-9 rounded-xl border border-card-border bg-card flex items-center justify-center text-ink-muted hover:text-ink hover:bg-indigo-soft/50 transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </NuxtLink>
      <div>
        <h1 class="font-heading text-2xl font-bold text-ink">Profil Saya</h1>
        <p class="text-xs text-ink-muted mt-0.5">Kelola informasi akun dan keamanan</p>
      </div>
    </div>

    <!-- Avatar + Info Ringkas -->
    <div class="card p-6 sm:p-8 flex flex-col sm:flex-row items-center sm:items-start gap-6">
      <!-- Avatar Foto / Inisial -->
      <div class="relative shrink-0 group">
        <input
          ref="fileInputRef"
          type="file"
          accept="image/png,image/jpeg,image/jpg,image/webp"
          class="hidden"
          @change="handleFileChange"
        />

        <!-- Foto Profil Gambar (jika ada) -->
        <img
          v-if="auth.user?.foto"
          :src="auth.user.foto"
          :alt="auth.user.name"
          class="w-24 h-24 rounded-3xl object-cover shadow-lg border-2 border-indigo-brand/30 transition group-hover:opacity-90"
        />

        <!-- Avatar Inisial (jika belum ada foto) -->
        <div
          v-else
          class="w-24 h-24 rounded-3xl flex items-center justify-center font-heading font-extrabold text-3xl text-white shadow-lg select-none"
          :style="{ background: 'linear-gradient(135deg, #FF6EA7 0%, #BA43F6 100%)' }"
        >
          {{ initials }}
        </div>

        <!-- Loading spinner saat upload -->
        <div
          v-if="isUploadingFoto"
          class="absolute inset-0 bg-black/60 rounded-3xl flex flex-col items-center justify-center text-white text-xs gap-1 backdrop-blur-xs"
        >
          <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
          <span class="text-[10px] font-semibold">Mengunggah...</span>
        </div>

        <!-- Tombol Kamera Ubah Foto -->
        <button
          type="button"
          @click="triggerFileInput"
          :disabled="isUploadingFoto"
          class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full bg-gradient-to-br from-[#FF6EA7] to-[#BA43F6] text-white border-2 border-card flex items-center justify-center shadow hover:scale-110 active:scale-95 transition"
          title="Ubah Foto Profil"
          aria-label="Ubah Foto Profil"
        >
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </button>
      </div>

      <div class="text-center sm:text-left min-w-0 flex-1">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
          <div>
            <h2 class="font-heading text-xl font-bold text-ink">{{ auth.user?.name }}</h2>
            <p class="text-sm text-ink-muted mt-0.5">@{{ auth.user?.username || '—' }}</p>
          </div>

          <!-- Tombol Aksi Foto Cepat -->
          <div class="flex items-center gap-2 justify-center sm:justify-end pt-1 sm:pt-0">
            <button
              type="button"
              @click="triggerFileInput"
              :disabled="isUploadingFoto"
              class="btn-secondary text-xs px-3 py-1.5"
            >
              <svg class="w-3.5 h-3.5 text-indigo-brand mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              {{ auth.user?.foto ? 'Ganti Foto' : 'Unggah Foto' }}
            </button>
            <button
              v-if="auth.user?.foto"
              type="button"
              @click="hapusFotoProfil"
              :disabled="isUploadingFoto"
              class="btn-ghost text-xs px-2.5 py-1.5 text-rose-500 hover:text-rose-600 hover:bg-rose-500/10"
              title="Hapus foto profil dan gunakan avatar inisial"
            >
              Hapus Foto
            </button>
          </div>
        </div>

        <div class="flex flex-wrap gap-2 mt-3 justify-center sm:justify-start">
          <span class="badge bg-indigo-soft/60 text-indigo-brand border border-indigo-brand/20 text-[11px] font-semibold">
            Mahasiswa
          </span>
          <span v-if="auth.user?.angkatan" class="badge bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/20 text-[11px]">
            Angkatan {{ auth.user?.angkatan }}
          </span>
          <span class="badge bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20 text-[11px]">
            Akun Aktif
          </span>
        </div>

        <!-- Alert pesan foto -->
        <div v-if="fotoMessage" class="mt-3 p-2.5 rounded-xl text-xs flex items-center gap-2" :class="fotoIsError ? 'bg-rose-500/10 text-rose-700 dark:text-rose-300 border border-rose-500/20' : 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20'">
          <svg v-if="!fotoIsError" class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <svg v-else class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>{{ fotoMessage }}</span>
        </div>

        <p class="mt-3 text-xs text-ink-muted leading-relaxed max-w-md">
          {{ auth.user?.bio || 'Belum ada bio. Tambahkan sedikit cerita tentang dirimu di bawah.' }}
        </p>
      </div>
    </div>

    <!-- Statistik Tulisan -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <div class="card p-5 text-center">
        <div class="font-heading text-2xl font-extrabold text-ink">{{ stats.total }}</div>
        <div class="text-[11px] text-ink-muted mt-1 font-medium">Total Tulisan</div>
      </div>
      <div class="card p-5 text-center">
        <div class="font-heading text-2xl font-extrabold text-emerald-500">{{ stats.terbit }}</div>
        <div class="text-[11px] text-ink-muted mt-1 font-medium">Terbit</div>
      </div>
      <div class="card p-5 text-center">
        <div class="font-heading text-2xl font-extrabold text-amber-500">{{ stats.menunggu }}</div>
        <div class="text-[11px] text-ink-muted mt-1 font-medium">Menunggu</div>
      </div>
      <div class="card p-5 text-center">
        <div class="font-heading text-2xl font-extrabold text-indigo-brand">{{ stats.draf }}</div>
        <div class="text-[11px] text-ink-muted mt-1 font-medium">Draf</div>
      </div>
    </div>

    <!-- Form Edit Profil -->
    <div class="card p-6 sm:p-8">
      <div class="flex items-center gap-2 mb-6">
        <div class="w-8 h-8 rounded-xl bg-indigo-soft/60 text-indigo-brand flex items-center justify-center">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </div>
        <h3 class="font-heading text-base font-bold text-ink">Informasi Profil</h3>
      </div>

      <!-- Alert Sukses Profil -->
      <div v-if="profilSuccess" class="mb-5 p-3.5 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-xs text-emerald-700 dark:text-emerald-300 flex items-center gap-2" role="status">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        {{ profilSuccess }}
      </div>

      <!-- Alert Error Profil -->
      <div v-if="profilError" class="mb-5 p-3.5 bg-rose-500/10 border border-rose-500/30 rounded-xl text-xs text-rose-700 dark:text-rose-300 flex items-start gap-2" role="alert">
        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ profilError }}
      </div>

      <form @submit.prevent="simpanProfil" class="space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          <div>
            <label for="profil-name" class="label">Nama Lengkap <span class="text-rose-500">*</span></label>
            <input
              id="profil-name"
              v-model="formProfil.name"
              type="text"
              required
              placeholder="Nama lengkap"
              class="input"
            />
          </div>
          <div>
            <label for="profil-username" class="label">Username <span class="text-rose-500">*</span></label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-ink-muted text-sm font-medium">@</span>
              <input
                id="profil-username"
                v-model="formProfil.username"
                type="text"
                required
                placeholder="username"
                class="input pl-7"
              />
            </div>
          </div>
        </div>

        <div>
          <label for="profil-email" class="label">Email</label>
          <input
            id="profil-email"
            :value="auth.user?.email"
            type="email"
            disabled
            class="input opacity-60 cursor-not-allowed bg-canvas"
          />
          <p class="text-[11px] text-ink-muted mt-1.5">Email tidak dapat diubah karena terkait domain kampus resmi.</p>
        </div>

        <div>
          <label for="profil-angkatan" class="label">Tahun Angkatan</label>
          <input
            id="profil-angkatan"
            v-model="formProfil.angkatan"
            type="text"
            maxlength="4"
            placeholder="cth: 2023"
            class="input max-w-xs"
          />
        </div>

        <div>
          <label for="profil-bio" class="label">Bio / Tentang Saya</label>
          <textarea
            id="profil-bio"
            v-model="formProfil.bio"
            rows="4"
            maxlength="500"
            placeholder="Ceritakan sedikit tentang dirimu, minat, atau genre tulisan favoritmu..."
            class="input resize-none"
          ></textarea>
          <p class="text-[11px] text-ink-muted mt-1.5 text-right">{{ formProfil.bio?.length || 0 }} / 500</p>
        </div>

        <div class="flex gap-3 pt-2">
          <button
            type="submit"
            :disabled="isSavingProfil"
            class="btn-primary px-6"
          >
            <span v-if="isSavingProfil" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            {{ isSavingProfil ? 'Menyimpan...' : 'Simpan Profil' }}
          </button>
          <button type="button" @click="resetFormProfil" class="btn-secondary px-5">
            Batal
          </button>
        </div>
      </form>
    </div>

    <!-- Form Ganti Password -->
    <div class="card p-6 sm:p-8">
      <div class="flex items-center gap-2 mb-6">
        <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
        </div>
        <h3 class="font-heading text-base font-bold text-ink">Keamanan Akun</h3>
      </div>

      <!-- Alert Sukses Password -->
      <div v-if="passSuccess" class="mb-5 p-3.5 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-xs text-emerald-700 dark:text-emerald-300 flex items-center gap-2" role="status">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        {{ passSuccess }}
      </div>

      <!-- Alert Error Password -->
      <div v-if="passError" class="mb-5 p-3.5 bg-rose-500/10 border border-rose-500/30 rounded-xl text-xs text-rose-700 dark:text-rose-300 flex items-start gap-2" role="alert">
        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ passError }}
      </div>

      <form @submit.prevent="gantiPassword" class="space-y-5">
        <div>
          <label for="pass-lama" class="label">Kata Sandi Saat Ini <span class="text-rose-500">*</span></label>
          <div class="relative">
            <input
              id="pass-lama"
              v-model="formPass.password_lama"
              :type="showPassLama ? 'text' : 'password'"
              required
              autocomplete="current-password"
              placeholder="Masukkan kata sandi lama"
              class="input pr-10"
            />
            <button type="button" @click="showPassLama = !showPassLama" class="absolute right-3 top-1/2 -translate-y-1/2 text-ink-muted hover:text-ink transition">
              <svg v-if="showPassLama" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
              </svg>
              <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </button>
          </div>
        </div>

        <div>
          <label for="pass-baru" class="label">Kata Sandi Baru <span class="text-rose-500">*</span></label>
          <div class="relative">
            <input
              id="pass-baru"
              v-model="formPass.password_baru"
              :type="showPassBaru ? 'text' : 'password'"
              required
              autocomplete="new-password"
              placeholder="Minimal 8 karakter"
              class="input pr-10"
            />
            <button type="button" @click="showPassBaru = !showPassBaru" class="absolute right-3 top-1/2 -translate-y-1/2 text-ink-muted hover:text-ink transition">
              <svg v-if="showPassBaru" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
              </svg>
              <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </button>
          </div>
          <!-- Indikator kekuatan password -->
          <div v-if="formPass.password_baru" class="mt-2 space-y-1">
            <div class="flex gap-1">
              <div v-for="i in 4" :key="i" class="h-1 flex-1 rounded-full transition-all"
                :class="passStrength >= i ? passStrengthColor : 'bg-card-border'"
              ></div>
            </div>
            <p class="text-[11px]" :class="passStrengthTextColor">{{ passStrengthLabel }}</p>
          </div>
        </div>

        <div>
          <label for="pass-konfirmasi" class="label">Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span></label>
          <input
            id="pass-konfirmasi"
            v-model="formPass.password_baru_confirmation"
            type="password"
            required
            autocomplete="new-password"
            placeholder="Ulangi kata sandi baru"
            class="input"
            :class="formPass.password_baru_confirmation && formPass.password_baru !== formPass.password_baru_confirmation ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/20' : ''"
          />
          <p v-if="formPass.password_baru_confirmation && formPass.password_baru !== formPass.password_baru_confirmation" class="text-[11px] text-rose-500 mt-1">
            Kata sandi tidak cocok.
          </p>
        </div>

        <div class="flex gap-3 pt-2">
          <button
            type="submit"
            :disabled="isSavingPass || !formPassValid"
            class="btn-danger px-6 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="isSavingPass" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            {{ isSavingPass ? 'Mengubah...' : 'Ubah Kata Sandi' }}
          </button>
          <button type="button" @click="resetFormPass" class="btn-secondary px-5">
            Batal
          </button>
        </div>
      </form>
    </div>

    <!-- Info Akun -->
    <div class="card p-6 sm:p-8 border-rose-500/20">
      <div class="flex items-center gap-2 mb-4">
        <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h3 class="font-heading text-base font-bold text-ink">Informasi Akun</h3>
      </div>
      <div class="space-y-3 text-xs text-ink-muted">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 py-3 border-b border-card-border">
          <span class="font-medium text-ink">Jenis Akun</span>
          <span class="badge-menunggu">Mahasiswa (Penulis)</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 py-3 border-b border-card-border">
          <span class="font-medium text-ink">Email Terdaftar</span>
          <span class="font-mono">{{ auth.user?.email }}</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 py-3">
          <span class="font-medium text-ink">Keluar dari Semua Perangkat</span>
          <button @click="auth.logout" class="btn-ghost text-rose-600 hover:text-rose-700 text-xs px-3 py-1.5 self-start">
            Keluar Sekarang
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: ['mahasiswa'],
})

const auth = useAuthStore()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase as string

// ─────────────────────────────────────────
// Stats tulisan
// ─────────────────────────────────────────
const stats = reactive({ total: 0, terbit: 0, menunggu: 0, draf: 0 })

onMounted(async () => {
  try {
    const res = await $fetch<{ data: any[] }>(`${apiBase}/mahasiswa/tulisan`, {
      headers: { Authorization: `Bearer ${auth.token}`, Accept: 'application/json' },
    })
    stats.total = res.data.length
    stats.terbit = res.data.filter((t: any) => t.status === 'terbit').length
    stats.menunggu = res.data.filter((t: any) => t.status === 'menunggu').length
    stats.draf = res.data.filter((t: any) => t.status === 'draf').length
  } catch { /* abaikan */ }
})

// ─────────────────────────────────────────
// Inisial avatar & Foto Profil
// ─────────────────────────────────────────
const fileInputRef = ref<HTMLInputElement | null>(null)
const isUploadingFoto = ref(false)
const fotoMessage = ref('')
const fotoIsError = ref(false)

const initials = computed(() => {
  const name = auth.user?.name || ''
  return name.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase() || 'AK'
})

function triggerFileInput() {
  fotoMessage.value = ''
  fileInputRef.value?.click()
}

async function handleFileChange(event: Event) {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return

  // Validasi ukuran sisi klien (maks 2MB)
  if (file.size > 2 * 1024 * 1024) {
    fotoIsError.value = true
    fotoMessage.value = 'Ukuran file foto maksimal 2 MB.'
    target.value = ''
    return
  }

  isUploadingFoto.value = true
  fotoMessage.value = ''
  fotoIsError.value = false

  try {
    const res = await auth.uploadFoto(file)
    fotoIsError.value = false
    fotoMessage.value = res.message || 'Foto profil berhasil diunggah!'
    setTimeout(() => { fotoMessage.value = '' }, 4000)
  } catch (err: any) {
    fotoIsError.value = true
    fotoMessage.value = err.data?.message || 'Gagal mengunggah foto profil.'
  } finally {
    isUploadingFoto.value = false
    target.value = ''
  }
}

async function hapusFotoProfil() {
  if (confirm('Yakin ingin menghapus foto profil dan kembali menggunakan avatar inisial?')) {
    isUploadingFoto.value = true
    fotoMessage.value = ''
    fotoIsError.value = false
    try {
      const res = await auth.hapusFoto()
      fotoIsError.value = false
      fotoMessage.value = res.message || 'Foto profil berhasil dihapus.'
      setTimeout(() => { fotoMessage.value = '' }, 4000)
    } catch (err: any) {
      fotoIsError.value = true
      fotoMessage.value = err.data?.message || 'Gagal menghapus foto profil.'
    } finally {
      isUploadingFoto.value = false
    }
  }
}

// ─────────────────────────────────────────
// Form Edit Profil
// ─────────────────────────────────────────
const formProfil = reactive({
  name: auth.user?.name || '',
  username: auth.user?.username || '',
  bio: auth.user?.bio || '',
  angkatan: auth.user?.angkatan || '',
})
const isSavingProfil = ref(false)
const profilSuccess = ref('')
const profilError = ref('')

function resetFormProfil() {
  formProfil.name = auth.user?.name || ''
  formProfil.username = auth.user?.username || ''
  formProfil.bio = auth.user?.bio || ''
  formProfil.angkatan = auth.user?.angkatan || ''
  profilError.value = ''
  profilSuccess.value = ''
}

async function simpanProfil() {
  if (isSavingProfil.value) return
  isSavingProfil.value = true
  profilError.value = ''
  profilSuccess.value = ''
  try {
    await auth.updateProfil({
      name: formProfil.name,
      username: formProfil.username,
      bio: formProfil.bio || undefined,
      angkatan: formProfil.angkatan || undefined,
    })
    profilSuccess.value = 'Profil berhasil diperbarui!'
    setTimeout(() => { profilSuccess.value = '' }, 4000)
  } catch (err: any) {
    const messages = err.data?.errors
    if (messages) {
      profilError.value = Object.values(messages).flat().join(' ')
    } else {
      profilError.value = err.data?.message || 'Gagal menyimpan profil.'
    }
  } finally {
    isSavingProfil.value = false
  }
}

// ─────────────────────────────────────────
// Form Ganti Password
// ─────────────────────────────────────────
const formPass = reactive({
  password_lama: '',
  password_baru: '',
  password_baru_confirmation: '',
})
const isSavingPass = ref(false)
const passSuccess = ref('')
const passError = ref('')
const showPassLama = ref(false)
const showPassBaru = ref(false)

const formPassValid = computed(() => {
  return formPass.password_lama.length > 0
    && formPass.password_baru.length >= 8
    && formPass.password_baru === formPass.password_baru_confirmation
})

// Kekuatan password
const passStrength = computed(() => {
  const p = formPass.password_baru
  if (!p) return 0
  let score = 0
  if (p.length >= 8) score++
  if (/[A-Z]/.test(p)) score++
  if (/[0-9]/.test(p)) score++
  if (/[^A-Za-z0-9]/.test(p)) score++
  return score
})
const passStrengthColor = computed(() => {
  const colors = ['bg-rose-400', 'bg-orange-400', 'bg-amber-400', 'bg-emerald-400']
  return colors[passStrength.value - 1] || 'bg-rose-400'
})
const passStrengthTextColor = computed(() => {
  const colors = ['text-rose-500', 'text-orange-500', 'text-amber-500', 'text-emerald-500']
  return colors[passStrength.value - 1] || 'text-rose-500'
})
const passStrengthLabel = computed(() => {
  const labels = ['Sangat Lemah', 'Lemah', 'Cukup', 'Kuat']
  return labels[passStrength.value - 1] || 'Sangat Lemah'
})

function resetFormPass() {
  formPass.password_lama = ''
  formPass.password_baru = ''
  formPass.password_baru_confirmation = ''
  passError.value = ''
  passSuccess.value = ''
}

async function gantiPassword() {
  if (isSavingPass.value || !formPassValid.value) return
  isSavingPass.value = true
  passError.value = ''
  passSuccess.value = ''
  try {
    const res = await auth.gantiPassword({
      password_lama: formPass.password_lama,
      password_baru: formPass.password_baru,
      password_baru_confirmation: formPass.password_baru_confirmation,
    })
    passSuccess.value = res.message || 'Kata sandi berhasil diubah!'
    resetFormPass()
    setTimeout(() => { passSuccess.value = '' }, 5000)
  } catch (err: any) {
    const messages = err.data?.errors
    if (messages) {
      passError.value = Object.values(messages).flat().join(' ')
    } else {
      passError.value = err.data?.message || 'Gagal mengubah kata sandi.'
    }
  } finally {
    isSavingPass.value = false
  }
}
</script>
