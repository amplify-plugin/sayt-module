const mix = require('laravel-mix');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */


mix.setResourceRoot('resources')
    .setPublicPath('public')
    .sass('resources/scss/sayt.scss', 'public/css/sayt.css')
    .copyDirectory('resources/js', 'public/js')
    .js('resources/vue/app.js', 'public/js/app.js')
    .vue({version: 3})
    .copy('resources/scss/images/no-image-placeholder.png', 'public/images/no-image-placeholder.png')
    .version();
