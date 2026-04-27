const mix = require('laravel-mix');
const path = require("path");
const fs = require("fs");
const {exec} = require("child_process");
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

class PublishSaytAssets {
    apply(compiler) {
        compiler.hooks.done.tap('RunCommandIfArtisanExists', (stats) => {
            if (stats.hasErrors()) return;

            // adjust path relative to your webpack.mix.js
            const artisanPath = path.resolve(__dirname, '../../artisan');

            if (fs.existsSync(artisanPath)) {
                exec('php ../../artisan vendor:publish --tag=sayt-asset --ansi --force', (err, stdout, stderr) => {
                    if (err) {
                        console.error(err);
                        return;
                    }
                    console.log(stdout);
                });
            } else {
                console.log('artisan file not found, skipping...');
            }
        });
    }
}


mix.setResourceRoot('resources')
    .setPublicPath('public')
    .sass('resources/scss/sayt.scss', 'public/css/sayt.css')
    .copyDirectory('resources/js', 'public/js')
    // .js('resources/vue/app.js', 'public/js/app.js')
    // .vue({version: 3})
    .copy('resources/scss/images/no-image-placeholder.png', 'public/images/no-image-placeholder.png')
    .webpackConfig({
        plugins: [
            new PublishSaytAssets(),
        ]
    })
    .version();
