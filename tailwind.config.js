// tailwind.config.js
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        // Themabinti Custom Palette
        primary: { // A vibrant purple
          DEFAULT: '#8A2BE2', // Web Purple
          50: '#F5F0FF',
          100: '#E6D9FF',
          200: '#CDBFFF',
          300: '#B0A2FF',
          400: '#957AFF',
          500: '#8A2BE2', // Original purple
          600: '#7B20D2',
          700: '#6C1ABF',
          800: '#5D14AE',
          900: '#4F0F9C',
          950: '#3F097A',
        },
        accent: { // A complementary pink
          DEFAULT: '#FF69B4', // Hot Pink
          50: '#FFF0F7',
          100: '#FFD9ED',
          200: '#FFB8DB',
          300: '#FF8CC6',
          400: '#FF64B2',
          500: '#FF69B4', // Original pink
          600: '#F058A8',
          700: '#DC4A98',
          800: '#C83C88',
          900: '#B42E78',
          950: '#8F205F',
        },
        neutral: { // Whites, grays for backgrounds and text
          white: '#FFFFFF',
          light: '#F8F8F8', // Very light gray, almost white
          DEFAULT: '#EAEAEA', // Light gray for subtle differences
          dark: '#333333', // Dark gray for text
          black: '#000000',
        },
      },
      fontFamily: {
        // Optional: Add custom fonts if using Google Fonts etc.
        // sans: ['Inter', 'sans-serif'],
      },
    },
  },
  plugins: [],
}