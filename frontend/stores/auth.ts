/**
 * Store autentikasi — menyimpan token Sanctum & data user admin
 */
export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: null as string | null,
    user: null as { id: number; name: string; email: string; role: string } | null,
  }),

  getters: {
    isLoggedIn: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
  },

  actions: {
    async register(name: string, username: string, email: string, password: string) {
      const config = useRuntimeConfig()
      const apiBase = config.public.apiBase as string

      const res = await $fetch<{
        message: string
        data: {
          token: string
          token_type: string
          user: { id: number; name: string; username?: string; email: string; role: string }
        }
      }>(`${apiBase}/auth/register`, {
        method: 'POST',
        body: { name, username, email, password },
        headers: { Accept: 'application/json' },
      })

      this.token = res.data.token
      this.user  = res.data.user

      if (import.meta.client) {
        localStorage.setItem('ak_token', this.token)
        localStorage.setItem('ak_user', JSON.stringify(this.user))
      }

      return res
    },

    async login(email: string, password: string) {
      const config = useRuntimeConfig()
      const apiBase = config.public.apiBase as string

      const res = await $fetch<{
        message: string
        data: {
          token: string
          token_type: string
          user: { id: number; name: string; email: string; role: string }
        }
      }>(`${apiBase}/auth/login`, {
        method: 'POST',
        body: { email, password },
        headers: { Accept: 'application/json' },
      })

      this.token = res.data.token
      this.user  = res.data.user

      // Simpan ke localStorage supaya tidak logout saat refresh
      if (import.meta.client) {
        localStorage.setItem('ak_token', this.token)
        localStorage.setItem('ak_user', JSON.stringify(this.user))
      }

      return res
    },

    logout() {
      // Panggil API logout (fire-and-forget)
      if (this.token) {
        const config = useRuntimeConfig()
        $fetch(`${config.public.apiBase}/auth/logout`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${this.token}`,
            Accept: 'application/json',
          },
        }).catch(() => {})
      }

      this.token = null
      this.user  = null

      if (import.meta.client) {
        localStorage.removeItem('ak_token')
        localStorage.removeItem('ak_user')
      }

      navigateTo('/admin/login')
    },

    /** Restore sesi dari localStorage saat app dimuat */
    restoreSession() {
      if (!import.meta.client) return

      const savedToken = localStorage.getItem('ak_token')
      const savedUser  = localStorage.getItem('ak_user')

      if (savedToken && savedUser) {
        this.token = savedToken
        try {
          this.user = JSON.parse(savedUser)
        } catch {
          this.token = null
          this.user  = null
        }
      }
    },
  },
})

