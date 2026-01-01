export default {
  content: [
    "./*.php",
    "./**/*.php",
    "./assets/**/*.js",
    "./assets/**/*.jsx",
    "./assets/**/*.ts",
    "./assets/**/*.tsx",
  ],
  theme: {
    extend: {
      fontFamily: {
        'sans': ['Roboto', 'Abel', 'Helvetica', 'Arial', 'sans-serif']
      }
    }
  },
  plugins: [],
    safelist: [
    'hidden',
    'block',
    'p-4',
    'text-green-500'
  ],
};
