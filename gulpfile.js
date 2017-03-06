var elixir = require('laravel-elixir');

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


// no need to create Source Map files
elixir.config.sourcemaps = false;



// generic path to the bower-installed packages
var bower_path = 'resources/assets/vendor';

// paths to individual packages
var path = {
    'font_awesome'  : bower_path + '/font-awesome',
    'jquery'        : bower_path + '/jquery',
    'tether'        : bower_path + '/tether',
    'jquery_ui'     : bower_path + '/jquery-ui',
    'bootstrap'     : bower_path + '/bootstrap',
    'moment'        : bower_path + '/moment',
    'jeditable'     : bower_path + '/jeditable',
    'spectrum'      : bower_path + '/spectrum',
    'file_upload'   : bower_path + '/blueimp-file-upload',
    'timepicker'    : bower_path + '/timepicker',
    'c_spot'        : bower_path + '/c-spot',
};

/* 
    Themes for jQuery-UI
    other good ones: base, pepper-grinder, smoothness, redmond, overcast, sunny, flick, cuppertino, ui-lightness
    see: http://jqueryui.com/themeroller/
*/
var ui_theme = 'redmond'; 



elixir(function(mix) {


    /* mix styles */
    mix.styles([
        path.bootstrap      + '/dist/css/bootstrap.css',
        path.font_awesome   + '/css/font-awesome.css',
        path.jquery_ui      + '/themes/base/core.css',
        path.jquery_ui      + '/themes/base/datepicker.css',
        path.jquery_ui      + '/themes/base/slider.css',
        path.jquery_ui      + '/themes/base/tabs.css',
        path.jquery_ui      + '/themes/'+ui_theme+'/theme.css',
        path.spectrum       + '/spectrum.css',
        path.timepicker     + '/jquery-ui-timepicker-addon.css',

        path.c_spot         + '/css/style.css',
        path.c_spot         + '/css/signin.css',
        
    ], 'public/css/all.css', './');


    /* mix JS */
    mix.scripts([
        path.tether         + '/dist/js/tether.js',
        path.jquery         + '/dist/jquery.js',
        path.jquery_ui      + '/jquery-ui.js',
        path.jquery_ui      + '/ui/widgets/datepicker.js',
        path.jquery_ui      + '/ui/widgets/slider.js',
        path.bootstrap      + '/dist/js/bootstrap.js',
        path.moment         + '/moment.js',
        path.jeditable      + '/jquery.jeditable.js',
        path.spectrum       + '/spectrum.js',
        // path.file_upload    + '/js/vendor/jquery.ui.widget.js',
        path.file_upload    + '/js/jquery.iframe-transport.js',
        path.file_upload    + '/js/jquery.fileupload.js',
        path.timepicker     + '/jquery-ui-timepicker-addon.js',

        path.c_spot         + '/js/_main.js',
        path.c_spot         + '/js/document.ready.js',
        path.c_spot         + '/js/spa.utilities.js',
        path.c_spot         + '/js/presentation.js',

    ], 'public/js/all.js', './');
    

    // allow for versioning
    mix.version(['css/all.css', 'js/all.js']);


    /* 
        copy static files 

        1. FONTS
    */
    mix.copy([
        path.font_awesome   + '/fonts',
    ], 'public/build/fonts');

    // 2. IMAGES
    mix.copy([
        path.jquery_ui      + '/themes/'+ui_theme+'/images',
    ], 'public/build/css/images');


    // Browser Sync
    // mix.browserSync({        proxy: 'c-spot.app'    });

});
