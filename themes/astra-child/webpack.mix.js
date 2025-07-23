/*
 *--------------------------------------------------------------------------
 * Mix Asset Management
 *--------------------------------------------------------------------------
 *
 * Mix provides a clean, fluent API for defining some Webpack build steps
 * for Laravel applications. Instead of Laravel, we are using it for WordPress
 * theme and plugin development. By default, we are compiling the Sass file for
 * the theme or plugin as well as bundling up all the JS files.
 *
 */

// Require laravel mix.
let mix = require( 'laravel-mix' );

// Compile assets.
mix
    .sass( 'scss/main.scss', 'main.css' )
    .sass( 'scss/blocks.scss', 'blocks.min.css' )
    .js( 'js/grillseeker-blocks.js', 'grillseeker-blocks.min.js' )
    .disableNotifications()
    .options( { manifest: false } );
