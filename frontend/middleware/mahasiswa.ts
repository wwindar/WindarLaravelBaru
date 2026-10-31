export default defineNuxtRouteMiddleware(() => {
  const auth = useAuthStore()

  if (!auth.isLoggedIn) {
    return navigateTo('/login')
  }

  if (auth.user?.role !== 'mahasiswa') {
    if (auth.user?.role === 'dosen') {
      return navigateTo('/dosen')
    }
    return navigateTo('/')
  }
})

