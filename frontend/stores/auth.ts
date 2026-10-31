/**
 * Store autentikasi — menggunakan Nuxt Cookie (bukan localStorage)
 * Mendukung peran: 'mahasiswa', 'dosen'
 */
export interface AuthUser {
  id: number
  name: string
  username?: string
  email: string
  role: 'mahasiswa' | 'dosen' | string
  bio?: string | null
  angkatan?: string | null
}

export const useAuthStore = defineStore('auth', () => {
  const tokenCookie = useCookie<string | null>('ak_token', {
    default: () => null,
    maxAge: 60 * 60 * 24 * 7, // 7 hari
    sameSite: 'lax',
    secure: false, // sesuaikan false untuk localhost & shared hosting http
  })

  const userCookie = useCookie<AuthUser | null>('ak_user', {
    default: () => null,
    maxAge: 60 * 60 * 24 * 7,
    sameSite: 'lax',
    secure: false,
  })

  const token = ref<string | null>(tokenCookie.value)
  const user = ref<AuthUser | null>(userCookie.value)

  const isLoggedIn = computed(() => !!token.value)
  const isMahasiswa = computed(() => user.value?.role === 'mahasiswa')
  // 'admin' dan 'dosen' dianggap setara sebagai redaksi
  const isDosen = computed(() => user.value?.role === 'dosen' || user.value?.role === 'admin')

  // Sinkronkan state dengan cookie
  watch(token, (val) => {
    tokenCookie.value = val
  })
  watch(user, (val) => {
    userCookie.value = val
  })

  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase as string

  async function register(name: string, username: string, email: string, password: string) {
    const res = await $fetch<{
      message: string
      data: {
        token: string
        token_type: string
        user: AuthUser
      }
    }>(`${apiBase}/auth/register`, {
      method: 'POST',
      body: { name, username, email, password },
      headers: { Accept: 'application/json' },
    })

    token.value = res.data.token
    user.value = res.data.user
    tokenCookie.value = res.data.token
    userCookie.value = res.data.user

    return res
  }

  async function login(email: string, password: string) {
    const res = await $fetch<{
      message: string
      data: {
        token: string
        token_type: string
        user: AuthUser
      }
    }>(`${apiBase}/auth/login`, {
      method: 'POST',
      body: { email, password },
      headers: { Accept: 'application/json' },
    })

    token.value = res.data.token
    user.value = res.data.user
    tokenCookie.value = res.data.token
    userCookie.value = res.data.user

    return res
  }

  async function fetchMe() {
    if (!token.value) return null
    try {
      const res = await $fetch<{ data: AuthUser }>(`${apiBase}/auth/me`, {
        headers: {
          Authorization: `Bearer ${token.value}`,
          Accept: 'application/json',
        },
      })
      user.value = res.data
      userCookie.value = res.data
      return res.data
    } catch {
      logout()
      return null
    }
  }

  async function updateProfil(payload: { name: string; username: string; bio?: string; angkatan?: string }) {
    const res = await $fetch<{ message: string; data: AuthUser }>(`${apiBase}/auth/profil`, {
      method: 'PUT',
      body: payload,
      headers: {
        Authorization: `Bearer ${token.value}`,
        Accept: 'application/json',
      },
    })
    user.value = res.data
    userCookie.value = res.data
    return res
  }

  async function gantiPassword(payload: { password_lama: string; password_baru: string; password_baru_confirmation: string }) {
    const res = await $fetch<{ message: string; data: { token: string } }>(`${apiBase}/auth/ganti-password`, {
      method: 'PUT',
      body: payload,
      headers: {
        Authorization: `Bearer ${token.value}`,
        Accept: 'application/json',
      },
    })
    // Update dengan token baru (token lama dihapus oleh server)
    token.value = res.data.token
    tokenCookie.value = res.data.token
    return res
  }

  function logout() {
    if (token.value) {
      $fetch(`${apiBase}/auth/logout`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${token.value}`,
          Accept: 'application/json',
        },
      }).catch(() => {})
    }

    token.value = null
    user.value = null
    tokenCookie.value = null
    userCookie.value = null

    navigateTo('/login')
  }

  return {
    token,
    user,
    isLoggedIn,
    isMahasiswa,
    isDosen,
    register,
    login,
    logout,
    fetchMe,
    updateProfil,
    gantiPassword,
  }
})
