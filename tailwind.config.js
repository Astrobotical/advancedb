/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: ["./**/*.php"],
  theme: {
    extend: {
      textColor:{
        primaryTextColor:'var(--primaryTextColor)',
        bgColor: 'var(--backgroundColor)', 
        btnPrimary:'var(--primaryColor)',
      },
      backgroundColor:{
        primaryTextColor:'var(--primaryTextColor)',
        bgColor: 'var(--backgroundColor)', 
        btnPrimary:'var(--primaryColor)',
      }
    },
  },
  plugins: [require('daisyui')],
}

