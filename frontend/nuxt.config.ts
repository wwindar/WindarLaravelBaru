// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-05-15',
  devtools: { enabled: false },

  ssr: false, // SPA Mode untuk static hosting / InfinityFree

  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8001/api',
    },
  },

  app: {
    head: {
      title: 'Aksara Kampus — Ruang Dialektika & Sastra Mahasiswa',
      meta: [
        { charset: 'utf-8' },
        { name: 'description', content: 'Portal Sastra dan Artikel Kampus — Puisi, Cerpen, Esai, dan Berita' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      ],
      link: [
        { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
        { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
        {
          rel: 'stylesheet',
          href: 'https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Sora:wght@400;600;700;800&display=swap',
        },
      ],
    },
    pageTransition: { name: 'page', mode: 'out-in' },
  },

  srcDir: '.',
  css: ['~/assets/css/main.css'],

  modules: ['@nuxtjs/tailwindcss', '@pinia/nuxt'],

  tailwindcss: {
    cssPath: '~/assets/css/main.css',
    configPath: 'tailwind.config.js',
  },
})
