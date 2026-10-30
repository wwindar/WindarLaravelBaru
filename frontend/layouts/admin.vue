<template>
  <div class="min-h-screen flex bg-ink-950 text-parchment-200">
    <!-- Sidebar -->
    <aside class="hidden md:flex w-60 flex-col border-r border-ink-800 bg-ink-900/80 fixed inset-y-0 left-0 z-40 backdrop-blur-md">
      <!-- Logo -->
      <div class="p-5 border-b border-ink-800">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-ochre-400 to-terracotta-500 flex items-center justify-center font-bold text-ink-950 text-sm shadow-md">
            AK
          </div>
          <div>
            <p class="font-bold text-parchment-50 text-sm leading-tight">Aksara Kampus</p>
            <p class="text-[10px] text-ochre-400/90 font-mono tracking-wider">Redaksi UMPKU</p>
          </div>
        </div>
      </div>

      <!-- Nav -->
      <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
        <NuxtLink to="/admin" class="sidebar-link" exact-active-class="sidebar-link-active">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          Dashboard
        </NuxtLink>
        <NuxtLink to="/admin/artikel" class="sidebar-link" active-class="sidebar-link-active">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
          Artikel
        </NuxtLink>
        <NuxtLink to="/admin/karya" class="sidebar-link" active-class="sidebar-link-active">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          Karya Masuk
        </NuxtLink>
        <NuxtLink to="/admin/agenda" class="sidebar-link" active-class="sidebar-link-active">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          Agenda
        </NuxtLink>
        <NuxtLink to="/admin/kategori" class="sidebar-link" active-class="sidebar-link-active">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
          Kategori
        </NuxtLink>
      </nav>

      <!-- User info + Logout -->
      <div class="p-3 border-t border-ink-800">
        <div class="flex items-center gap-2 px-3 py-2 mb-1">
          <div class="w-7 h-7 rounded-full bg-ochre-400/20 text-ochre-400 flex items-center justify-center text-xs font-bold">
            {{ auth.user?.name?.charAt(0).toUpperCase() ?? 'A' }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-parchment-100 truncate">{{ auth.user?.name ?? 'Admin' }}</p>
            <p class="text-[10px] text-parchment-500 truncate">{{ auth.user?.email }}</p>
          </div>
        </div>
        <button @click="auth.logout()" class="sidebar-link w-full text-terracotta-400 hover:text-terracotta-300 hover:bg-terracotta-500/10">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          Keluar
        </button>
      </div>
    </aside>

    <!-- Mobile topbar -->
    <div class="md:hidden fixed top-0 inset-x-0 z-50 border-b border-ink-800 bg-ink-950/95 backdrop-blur px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-ochre-400 to-terracotta-500 flex items-center justify-center font-bold text-ink-950 text-xs">AK</div>
        <span class="font-bold text-parchment-50 text-sm">Redaksi UMPKU</span>
      </div>
      <button @click="auth.logout()" class="text-xs text-terracotta-400 hover:text-terracotta-300 font-semibold">Keluar</button>
    </div>

    <!-- Main area -->
    <div class="flex-1 md:ml-60 flex flex-col min-h-screen">
      <main class="flex-1 p-4 sm:p-6 mt-14 md:mt-0">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
const auth = useAuthStore()
</script>

<style scoped>
.sidebar-link {
  @apply flex items-center gap-3 px-3 py-2 rounded-xl text-sm text-parchment-400 hover:text-parchment-100 hover:bg-ink-850 transition-colors w-full;
}
.sidebar-link-active {
  @apply text-ochre-400 bg-ochre-400/15 font-semibold;
}
</style>
