const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

if(elixir.config.production == true){
    process.env.NODE_ENV = 'production';
}

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application as well as publishing vendor resources.
 |
 */

elixir(mix => {
    mix.sass('app.scss')
       .webpack('main.js');
});
