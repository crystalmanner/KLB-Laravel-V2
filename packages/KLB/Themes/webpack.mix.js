const mix = require("laravel-mix");

if (mix == 'undefined') {
    const { mix } = require("laravel-mix");
}

require("laravel-mix-merge-manifest");

if (mix.inProduction()) {
    var publicPath = 'publishable/assets';
} else {
    var publicPath = '../../../public/themes/KLB-theme/assets';
}

mix.setPublicPath(publicPath).mergeManifest();
mix.disableNotifications();
mix
    .js([
        __dirname + "/src/Resources/assets/js/app.js",
        // jQuery ElevateZoom Plus Plugin (EZ-Plus)
        // https://github.com/igorlino/elevatezoom-plus
        // Required for packages/KLB/Themes/src/Resources/assets/js/UI/components/image-magnifier.vue
        // __dirname + "/src/Resources/assets/js/jquery.ez-plus.js"
        '../../Webkul/Velocity/publishable/assets/js/jquery.ez-plus.js'
    ],
        "js/KLB.js"
    )
    .copyDirectory(__dirname + '/src/Resources/assets/images', publicPath + "/images")
    .sass(
        __dirname + '/src/Resources/assets/sass/admin.scss',
        __dirname + '/' + publicPath + '/css/KLB-admin.css'
    )
    .sass(
        __dirname + '/src/Resources/assets/sass/app.scss',
        __dirname + '/' + publicPath + '/css/KLB.css'
    )
    .options({
        processCssUrls: false
    });


if (! mix.inProduction()) {
    mix.sourceMaps();
}

if (mix.inProduction()) {
    mix.version();
}
