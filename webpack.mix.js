const mix = require('laravel-mix');
const webpack = require('webpack');

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


mix.sourceMaps(true, 'source-map')
    .options({
        processCssUrls: false,
        notifications : {
            onSuccess: false
        },
        clearConsole: true
    })
    .js('resources/js/app.js', 'public/js')
    .version();
