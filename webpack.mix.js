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
    .sourceMaps(true, 'source-map')
    .copyDirectory('resources/css/images', 'public/css/images')
    .styles([
        'resources/css/autocomplete.css',
        'resources/css/sayt.css',
        'resources/css/jquery-ui.css',
    ], 'public/css/sayt.css')
    .copyDirectory('resources/js/templates', 'public/js/templates')
    .copyDirectory('resources/js/scripts', 'public/js/scripts')
    .copyDirectory('resources/js/sayt', 'public/js/sayt')
    .version();
