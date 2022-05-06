module.exports = {
  enabled: process.env.NODE_ENV === 'production',
  purge: [
    './resources/js/**/*.js',
    './resources/js/**/**/*.js',
    './resources/js/**/**/**/**/*.js',
  ],
  theme: {},
  variants: {},
  plugins: [],
}