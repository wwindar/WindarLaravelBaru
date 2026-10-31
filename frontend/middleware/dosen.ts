export default defineNuxtRouteMiddleware(() => {
  const auth = useAuthStore()

  if (!auth.isLoggedIn) {
    return navigateTo('/login')
  }

  // 'admin' dan 'dosen' keduanya bisa akses halaman dosen/redaksi
  const isDosOrAdmin = auth.user?.role === 'dosen' || auth.user?.role === 'admin'
  if (!isDosOrAdmin) {
    if (auth.user?.role === 'mahasiswa') {
      return navigateTo('/mahasiswa')
    }
    return navigateTo('/')
  }
})

