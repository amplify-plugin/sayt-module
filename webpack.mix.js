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
    .vue()
    .copy('resources/js/admin.js', 'public/js/admin.js')
    .copy('node_modules/lightbox2/dist', 'public/packages/lightbox2')
    .sass('resources/frontend/global.scss', 'public/css/global.css')
    .sass('resources/scss/app.scss', 'public/css/app.css')
    .copyDirectory('resources/backpack', 'public/packages/backpack/crud')
    .version();

// Extend Webpack configuration
mix.webpackConfig({
    resolve: {
        fallback: {
            buffer: require.resolve('buffer/'), // Polyfill for buffer
        },
    },
    plugins: [
        new webpack.ProvidePlugin({
            Buffer: ['buffer', 'Buffer'], // Provide Buffer globally
        }),
    ],
});
