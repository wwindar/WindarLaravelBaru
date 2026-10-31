export default defineNuxtRouteMiddleware(() => {
  const auth = useAuthStore()

  if (auth.isLoggedIn) {
    if (auth.user?.role === 'dosen') {
      return navigateTo('/dosen')
    }
    return navigateTo('/mahasiswa')
  }
})
