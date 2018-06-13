const { mix } = require('laravel-mix');

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

mix.js('resources/assets/js/src/main.js', 'public/js').extract(['vue', 'vuex', 'vue-echarts', 'element-ui', 'jquery', 'echarts'])
   .sass('resources/assets/scss/style.scss', 'public/css');


//生产环境下才用 hash码生成编辑文件
// if (mix.config.inProduction) {
//     mix.version();
// }
