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
        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
        serif: ['"Playfair Display"', 'Georgia', 'serif'],
        mono: ['"JetBrains Mono"', 'monospace'],
      },
      colors: {
        parchment: {
          50:  '#fdfaf6',
          100: '#fbf4eb',
          200: '#f6e9d8',
          300: '#FFEAD3', // Warna 1: Cream/Parchment
          400: '#edd0b2',
          500: '#d9b18c',
          600: '#b88d67',
          700: '#8e6948',
          800: '#5c422c',
          900: '#382618',
        },
        sage: {
          50:  '#f3fafb',
          100: '#e5f6f7',
          200: '#d0eff2',
          300: '#BFE7EA', // Warna 2: Soft Mint / Mist
          400: '#95d2d7',
          500: '#67b3ba',
          600: '#488e95',
          700: '#376f75',
          800: '#2b5459',
          900: '#1d3b3f',
        },
        ochre: {
          50:  '#fef8ee',
          100: '#fdf0db',
          200: '#f9deaf',
          300: '#f4c680',
          400: '#E89B5B', // Warna 3: Warm Ochre
          500: '#d8833e',
          600: '#bd672e',
          700: '#974c25',
          800: '#753920',
          900: '#562a19',
        },
        terracotta: {
          50:  '#fdf4f0',
          100: '#fae6dd',
          200: '#f4cebc',
          300: '#eab095',
          400: '#df8768',
          500: '#D46743', // Warna 4: Deep Rust / Terracotta
          600: '#b84e2c',
          700: '#943c22',
          800: '#73311e',
          900: '#552417',
        },
        ink: {
          50:  '#f8f9fa',
          100: '#f1f3f5',
          200: '#e9ecef',
          700: '#382f2c',
          800: '#2c2523',
          850: '#221c1a',
          900: '#1a1514', // Warm Charcoal / Deep Ink
          950: '#120e0d', // Canvas dark background
        },
      },
    },
  },
  plugins: [],
}
