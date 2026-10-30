/**
 * Composable useApi — wrapper $fetch terpusat untuk semua request ke backend
 */
export const useApi = () => {
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase as string

  /**
   * Buat request ke API.
   * @param path  - path relatif, misal '/artikel' atau '/auth/login'
   * @param opts  - opsi $fetch standar (method, body, headers, dll)
   */
  const request = <T = unknown>(path: string, opts: Parameters<typeof $fetch>[1] = {}) => {
    const auth = useAuthStore()
    const headers: Record<string, string> = {
      Accept: 'application/json',
      ...(opts.headers as Record<string, string> || {}),
    }

    if (auth.token) {
      headers['Authorization'] = `Bearer ${auth.token}`
    }

    return $fetch<T>(`${apiBase}${path}`, {
      ...opts,
      headers,
    })
  }

  return { request }
}

