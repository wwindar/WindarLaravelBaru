/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: ['class', '[data-theme="dark"]'],
  content: [
    './components/**/*.{js,vue,ts}',
    './layouts/**/*.vue',
    './pages/**/*.vue',
    './plugins/**/*.{js,ts}',
    './app.vue',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['"DM Sans"', 'system-ui', 'sans-serif'],
        heading: ['"Sora"', 'system-ui', 'sans-serif'],
      },
      colors: {
        canvas: 'rgb(var(--bg-rgb) / <alpha-value>)',
        ink: {
          DEFAULT: 'rgb(var(--text-rgb) / <alpha-value>)',
          muted: 'rgb(var(--text-muted-rgb) / <alpha-value>)',
          subtle: 'rgb(var(--text-subtle-rgb) / <alpha-value>)',
          light: 'rgb(var(--border-rgb) / <alpha-value>)',
        },
        indigo: {
          brand: 'rgb(var(--accent-rgb) / <alpha-value>)',
          hover: 'rgb(var(--accent-hover-rgb) / <alpha-value>)',
          dark: 'rgb(var(--brand-dark-rgb) / <alpha-value>)',
          soft: 'rgb(var(--accent-soft-rgb) / <alpha-value>)',
          light: 'rgb(var(--accent-light-rgb) / <alpha-value>)',
        },
        card: {
          DEFAULT: 'rgb(var(--surface-rgb) / <alpha-value>)',
          border: 'rgb(var(--border-rgb) / <alpha-value>)',
        },
        status: {
          draf: '#7E7192',
          menunggu: '#B7791F',
          terbit: '#10B981',
          revisi: '#BA43F6',
          ditolak: '#EF4444',
        },
      },
      borderRadius: {
        '2xl': '16px',
      },
      boxShadow: {
        subtle: '0 2px 8px -2px rgba(38, 28, 51, 0.05), 0 4px 16px -4px rgba(38, 28, 51, 0.08)',
        card: 'var(--card-shadow)',
        lift: '0 12px 28px -6px rgba(186, 67, 246, 0.16), 0 4px 12px -2px rgba(38, 28, 51, 0.06)',
      },
    },
  },
  plugins: [],
}
