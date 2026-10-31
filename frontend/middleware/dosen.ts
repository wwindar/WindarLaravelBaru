export default defineNuxtRouteMiddleware(() => {
  const auth = useAuthStore()

  if (!auth.isLoggedIn) {
    return navigateTo('/login')
  }

  if (auth.user?.role !== 'dosen') {
    if (auth.user?.role === 'mahasiswa') {
      return navigateTo('/mahasiswa')
    }
    return navigateTo('/')
  }
})
