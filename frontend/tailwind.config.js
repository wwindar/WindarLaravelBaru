/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
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
        canvas: '#F7F7FB',
        ink: {
          DEFAULT: '#16162B',
          muted: '#63627A',
          subtle: '#9190A5',
          light: '#D3D3E0',
        },
        indigo: {
          brand: '#5646E0',
          hover: '#4535C8',
          dark: '#1E1650',
          soft: '#EEECFC',
          light: '#F4F3FD',
        },
        card: {
          DEFAULT: '#FFFFFF',
          border: '#E3E3EE',
        },
        status: {
          draf: '#6B7280',
          menunggu: '#D97706',
          terbit: '#059669',
          revisi: '#D946EF',
          ditolak: '#DC2626',
        },
      },
      borderRadius: {
        '2xl': '16px',
      },
      boxShadow: {
        subtle: '0 2px 8px -2px rgba(22, 22, 43, 0.05), 0 4px 16px -4px rgba(22, 22, 43, 0.08)',
        card: '0 1px 3px 0 rgba(22, 22, 43, 0.04), 0 6px 20px -4px rgba(86, 70, 224, 0.06)',
        lift: '0 12px 28px -6px rgba(86, 70, 224, 0.12), 0 4px 12px -2px rgba(22, 22, 43, 0.04)',
      },
    },
  },
  plugins: [],
}
