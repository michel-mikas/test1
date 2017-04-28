const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(mix => {
    /*mix.sass('app.scss')
       .webpack('app.js');*/
    mix.styles([
        'AdminLTE.min.css',
        'skin-black.min.css',
        'styles.css'
    ], 'public/css/admin_panel.css');
    mix.scripts([
        'app.min.js',
        'jquery.slimscroll.min.js',
        'fastclick.min.js',
        'func_datatables_server.js',
        'func_datatables.js',
        'actions.js',
        'custom.js'
    ], 'public/js/admin_panel.js');
});
