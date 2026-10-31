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
        canvas: 'rgba(var(--bg-rgb), <alpha-value>)',
        ink: {
          DEFAULT: 'rgba(var(--text-rgb), <alpha-value>)',
          muted: 'rgba(var(--text-muted-rgb), <alpha-value>)',
          subtle: 'rgba(var(--text-subtle-rgb), <alpha-value>)',
          light: 'rgba(var(--border-rgb), <alpha-value>)',
        },
        indigo: {
          brand: 'rgba(var(--accent-rgb), <alpha-value>)',
          hover: 'rgba(var(--accent-hover-rgb), <alpha-value>)',
          dark: 'rgba(var(--brand-dark-rgb), <alpha-value>)',
          soft: 'rgba(var(--accent-soft-rgb), <alpha-value>)',
          light: 'rgba(var(--accent-light-rgb), <alpha-value>)',
        },
        card: {
          DEFAULT: 'rgba(var(--surface-rgb), <alpha-value>)',
          border: 'rgba(var(--border-rgb), <alpha-value>)',
        },
        status: {
          draf: '#78716C',
          menunggu: '#D97706',
          terbit: '#10B981',
          revisi: '#EA580C',
          ditolak: '#EF4444',
        },
      },
      borderRadius: {
        '2xl': '16px',
      },
      boxShadow: {
        subtle: '0 2px 8px -2px rgba(28, 25, 23, 0.05), 0 4px 16px -4px rgba(28, 25, 23, 0.08)',
        card: 'var(--card-shadow)',
        lift: '0 12px 28px -6px rgba(234, 88, 12, 0.16), 0 4px 12px -2px rgba(28, 25, 23, 0.06)',
      },
    },
  },
  plugins: [],
}
