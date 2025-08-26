// postcss.config.js
export default {
  plugins: {
    'postcss-nesting': {}, // Add this line FIRST
    tailwindcss: {},
    autoprefixer: {},
  },
};