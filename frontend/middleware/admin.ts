/** Middleware: halaman /admin/* wajib sudah login sebagai admin */
export default defineNuxtRouteMiddleware((to) => {
  const auth = useAuthStore()

  // Restore sesi dari localStorage setiap kali navigasi
  auth.restoreSession()

  // Izinkan akses ke halaman login tanpa token
  if (to.path === '/admin/login') return

  // Blok akses jika belum login
  if (!auth.isLoggedIn) {
    return navigateTo('/admin/login')
  }
})

