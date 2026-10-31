export default defineNuxtRouteMiddleware(() => {
  const auth = useAuthStore()

  if (!auth.isLoggedIn) {
    return navigateTo('/login')
  }

  if (auth.user?.role !== 'mahasiswa') {
    if (auth.user?.role === 'dosen' || auth.user?.role === 'admin') {
      return navigateTo('/dosen')
    }
    return navigateTo('/')
  }
})

