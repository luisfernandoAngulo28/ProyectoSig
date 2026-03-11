var elixir = require('laravel-elixir');
var htmlmin = require('gulp-htmlmin');
var gulp = require('gulp');

require('laravel-elixir-sass-compass');

var paths = {
    'jquery': '../../bower_components/jquery/',
    'bootstrap': '../../bower_components/bootstrap-sass-official/assets/',
    'fontawesome': '../../bower_components/fontawesome/',
    'perfectscrollbar': '../../bower_components/perfect-scrollbar/min/',
    'animatecss': '../../bower_components/animate.css/',
    'preventdoublesubmit': '../../bower_components/jquery-prevent-double-submit/',
    'wysiwygeditor': '../../bower_components/wysiwyg-editor/',
    'navbarmenu': '../../bower_plugins/MegaNavbar/assets/',
    'calendar': '../../bower_plugins/calendar-zabuto/',
    'flexslider': '../../bower_components/FlexSlider/',
    'featherlight': '../../bower_plugins/featherlight/release/',
    'wow': '../../bower_components/wow/dist/',
    'owl': '../../bower_components/OwlCarousel2/dist/',
    'isotope': '../../bower_components/isotope/dist/',
    'imagesloaded': '../../bower_components/imagesloaded/',
    'pickadate': '../../bower_components/pickadate/lib/compressed/',
    'excanvas': '../../bower_components/ExplorerCanvas/',
    'respondjs': '../../node_modules/respond.js/dest/',
    'angularcode': 'resources/assets/ng/',
    'legacy': 'resources/assets/legacy/',
}
var public_directory = 'public';

elixir.config.publicDir = public_directory;
elixir.config.publicPath  = public_directory;
elixir(function(mix) {
    mix.cssOuput = public_directory + '/assets/css';
    mix.jsOuput = public_directory + '/assets/js';
    mix
        /*.copy(paths.bootstrap + 'stylesheets/**', 'resources/assets/sass/bootstrap')
        .copy(paths.bootstrap + 'fonts/bootstrap/**', public_directory + '/assets/fonts/bootstrap')
        .copy(paths.fontawesome + 'scss/**', 'resources/assets/sass/fontawesome')
        .copy(paths.fontawesome + 'fonts/**', public_directory + '/assets/fonts/fontawesome')
        .sass("resources/assets/sass/vendor.scss", public_directory + '/assets/css/vendor.css')*/
        .compass("main.scss", public_directory + '/assets/css', {
            config_file: "config/compass.rb",
            style: "nested",
            comments: false,
            sass: "resources/assets/main"
        })
        .styles([
            paths.wysiwygeditor + "css/froala_style.min.css",
            paths.animatecss + "animate.min.css",
            paths.calendar + "zabuto_calendar.min.css",
            paths.owl + "assets/owl.carousel.min.css",
            paths.owl + "assets/owl.theme.default.min.css"
        ], public_directory + '/assets/css/plugins.css', './')
        .styles([
            paths.legacy + "css/vendor.css",
            paths.legacy + "css/style-4.css",
            // Autocomplete
            paths.legacy + "autocomplete/easy-autocomplete.min.css",
            paths.legacy + "autocomplete/easy-autocomplete.themes.min.css"
        ], public_directory + '/assets/css/template.css', './')
        .scripts([
            paths.jquery + "dist/jquery.js",
            paths.bootstrap + "javascripts/bootstrap.js"
        ], public_directory + '/assets/js/vendor.js', './')
        .scripts([
            paths.isotope + "isotope.pkgd.min.js",
            paths.imagesloaded + "imagesloaded.pkgd.min.js",
            paths.calendar + "zabuto_calendar.min.js",
            paths.owl + "owl.carousel.min.js",
            paths.wow + "wow.min.js"
        ], public_directory + '/assets/js/plugins.js', './')
        .scripts([
            paths.legacy + "js/vendor.js",
            paths.legacy + "js/active.js",
            // Autocomplete
            paths.legacy + "autocomplete/jquery.easy-autocomplete.min.js"
        ], public_directory + '/assets/js/template.js', './')
        .scripts([
            paths.respondjs + "respond.min.js",
            paths.excanvas + "excanvas.js"
        ], public_directory + '/assets/js/ie8.js', './')
        .version([
            'assets/css/vendor.css',
            'assets/css/plugins.css',
            'assets/css/template.css',
            'assets/css/main.css',
            'assets/js/vendor.js',
            'assets/js/plugins.js',
            'assets/js/template.js',
            'assets/js/ie8.js'
        ]);
});

gulp.task('compress', function() {
    var opts = {
        collapseWhitespace:    true,
        removeAttributeQuotes: true,
        removeComments:        true,
        minifyJS:              true
    };

    return gulp.src('./storage/framework/views/**/*')
        .pipe(htmlmin(opts))
        .pipe(gulp.dest('./storage/framework/views/'));
});