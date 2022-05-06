const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.react('resources/js/ReactApp.js', 'public/js')
   .sass('resources/sass/ReactApp.scss', 'public/css')
   .options({
        processCssUrls: false,
        postCss: [
        require('autoprefixer')({
            browsers: ['cover 99.5%'],
                grid: true
            }),
            tailwindcss('./tailwind.config.js') 
        ],
        
    });
// mix.browserSync('localhost:8000');
   //Add your own php artisan serve's url and port
   //For me url is localhost or 127.0.0.1 and port is 8000