<template>
  <div class="relative" ref="bellRef">
    <!-- Tombol Lonceng -->
    <button
      type="button"
      @click="isOpen = !isOpen"
      class="relative p-2 rounded-xl text-ink-muted hover:text-ink hover:bg-indigo-soft/60 transition focus-visible:ring-2 focus-visible:ring-indigo-brand"
      :aria-label="`Notifikasi ${unreadCount > 0 ? unreadCount + ' belum dibaca' : ''}`"
      :aria-expanded="isOpen"
    >
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>
      <!-- Badge Unread -->
      <span
        v-if="unreadCount > 0"
        class="absolute top-1 right-1 flex items-center justify-center min-w-4 h-4 px-1 text-[10px] font-bold text-white bg-rose-500 rounded-full ring-2 ring-white animate-pulse"
      >
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>

    <!-- Dropdown Popover -->
    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl shadow-2xl z-50 overflow-hidden border border-card-border"
      style="background-color: var(--surface-color);"
    >
      <div
        class="p-4 border-b border-card-border flex items-center justify-between"
        style="background-color: var(--surface-color);"
      >
        <div class="flex items-center gap-2">
          <h3 class="font-heading text-sm font-bold text-ink">Notifikasi</h3>
          <span v-if="unreadCount > 0" class="px-2 py-0.5 text-[10px] font-bold bg-indigo-brand text-white rounded-full">
            {{ unreadCount }} Baru
          </span>
        </div>
        <button
          v-if="unreadCount > 0"
          type="button"
          @click="markAllAsRead"
          class="text-xs text-indigo-brand hover:underline font-semibold focus-visible:ring-1"
        >
          Tandai semua dibaca
        </button>
      </div>

      <!-- State: Loading -->
      <div v-if="isLoading" class="p-6 text-center text-xs text-ink-muted" style="background-color: var(--surface-color);">
        <div class="inline-block w-5 h-5 border-2 border-indigo-brand border-t-transparent rounded-full animate-spin mb-2"></div>
        <p>Memuat notifikasi...</p>
      </div>

      <!-- State: Error -->
      <div v-else-if="errorMsg" class="p-4 text-center text-xs text-rose-600 bg-rose-500/10">
        <p>{{ errorMsg }}</p>
        <button @click="fetchNotifications" class="mt-2 text-xs font-semibold text-rose-700 underline">Coba lagi</button>
      </div>

      <!-- State: Empty -->
      <div v-else-if="notifications.length === 0" class="p-8 text-center text-ink-muted" style="background-color: var(--surface-color);">
        <svg class="w-8 h-8 mx-auto text-ink-subtle mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
        <p class="text-xs">Belum ada notifikasi.</p>
      </div>

      <!-- State: Success / List -->
      <div v-else class="max-h-80 overflow-y-auto divide-y divide-card-border" style="background-color: var(--surface-color);">
        <div
          v-for="item in notifications"
          :key="item.id"
          @click="!item.dibaca && markAsRead(item.id)"
          class="p-3.5 transition flex items-start gap-3 text-xs cursor-pointer select-none"
          :class="item.dibaca ? 'hover:bg-canvas/40 opacity-75' : 'bg-indigo-soft/25 hover:bg-indigo-soft/35'"
        >
          <div class="mt-1 w-2 h-2 rounded-full shrink-0" :class="item.dibaca ? 'bg-transparent' : 'bg-indigo-brand'"></div>
          <div class="flex-1 min-w-0">
            <p class="font-semibold text-ink leading-snug">{{ item.judul }}</p>
            <p class="text-ink-muted mt-1 leading-relaxed text-[11px]">{{ item.pesan }}</p>
            <span class="text-[10px] text-ink-subtle mt-1.5 block">{{ formatDate(item.created_at) }}</span>
          </div>
          <button
            v-if="!item.dibaca"
            type="button"
            @click.stop="markAsRead(item.id)"
            class="text-xs font-semibold text-indigo-brand hover:underline shrink-0 px-2 py-1 rounded-lg hover:bg-indigo-brand/10 transition"
            title="Tandai sudah dibaca"
          >
            Tandai Dibaca
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface NotifItem {
  id: number
  judul: string
  pesan: string
  dibaca: boolean
  created_at: string
}

const auth = useAuthStore()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase as string

const isOpen = ref(false)
const isLoading = ref(false)
const errorMsg = ref('')
const notifications = ref<NotifItem[]>([])
const bellRef = ref<HTMLElement | null>(null)

const unreadCount = computed(() => notifications.value.filter((n) => !n.dibaca).length)

async function fetchNotifications() {
  if (!auth.token) return
  isLoading.value = true
  errorMsg.value = ''
  try {
    const res = await $fetch<{ data: NotifItem[] }>(`${apiBase}/notifikasi`, {
      headers: {
        Authorization: `Bearer ${auth.token}`,
        Accept: 'application/json',
      },
    })
    notifications.value = res.data
  } catch (err: any) {
    errorMsg.value = 'Gagal memuat notifikasi.'
  } finally {
    isLoading.value = false
  }
}

async function markAsRead(id: number) {
  if (!auth.token) return
  try {
    await $fetch(`${apiBase}/notifikasi/${id}/baca`, {
      method: 'PATCH',
      headers: {
        Authorization: `Bearer ${auth.token}`,
        Accept: 'application/json',
      },
    })
    const item = notifications.value.find((n) => n.id === id)
    if (item) item.dibaca = true
  } catch {}
}

async function markAllAsRead() {
  if (!auth.token) return
  try {
    await $fetch(`${apiBase}/notifikasi/baca-semua`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${auth.token}`,
        Accept: 'application/json',
      },
    })
    notifications.value.forEach((n) => (n.dibaca = true))
  } catch {}
}

function formatDate(iso: string) {
  if (!iso) return ''
  const d = new Date(iso)
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit',
  })
}

// Tutup popover jika klik di luar
onMounted(() => {
  if (auth.isLoggedIn) {
    fetchNotifications()
  }

  const handleClickOutside = (e: MouseEvent) => {
    if (bellRef.value && !bellRef.value.contains(e.target as Node)) {
      isOpen.value = false
    }
  }
  window.addEventListener('click', handleClickOutside)
  onUnmounted(() => window.removeEventListener('click', handleClickOutside))
})
</script>

