"use strict";
// Gulpfile Taskrunner | by SecondSite
// Read the README.md for a list of the functions available
// Load plugins
const Fiber = require('fibers'),
    autoprefixer = require("autoprefixer"),
    cleanCSS = require("gulp-clean-css"), // CSSNANO tested faster, not in use
    del = require("del"),
    gulp = require("gulp"),
    newer = require('gulp-newer'),
    cache = require('gulp-cache'),
    imagemin = require('gulp-imagemin'),
    pngquant = require('imagemin-pngquant'),
    Zopfli = require('imagemin-zopfli'),
    Mozjpeg = require('imagemin-mozjpeg'),
    Giflossy = require('imagemin-giflossy'),
    header = require("gulp-header"),
    merge = require("merge-stream"),
    plumber = require("gulp-plumber"),
    rename = require("gulp-rename"),
    sass = require("gulp-sass"),
    cssnano = require('cssnano'),
    postcss = require('gulp-postcss'),
    terser = require('gulp-terser-js'), // in use, tested better compression than Uglify
    uglify = require("gulp-uglify"), // Error rates higher than Terser
    concat = require('gulp-concat'),
    deporder = require('gulp-deporder'),
    stripdebug = require('gulp-strip-debug'),
    sourcemaps = require('gulp-sourcemaps'),
    strip = require('gulp-strip-comments'),
    gulp_bootstrap_email = require('gulp-bootstrap-email'),
    zip = require('gulp-zip'),
    pkg = require('./package.json'),
    pipeline = require('readable-stream').pipeline;
    sass.compiler = require('node-sass'); // node-sass compiler
// Javascript files to be concatenated and minified into admin.js, admin.min,js and map for backend facing pages
const adminScripts = [
    "./vendor/jquery/jquery.js",
    "./vendor/jquery-validation/jquery.validate.js",
    "./vendor/bootstrap/js/bootstrap.bundle.js",
    "./src/js-compile/**/*"
];
// Set the banner content for js and css files
const banner = ['/*!\n',
  ' * Dash PHP Template - <%= pkg.title %> v<%= pkg.version %> ((https://github.com/SecondSite-web/<%= pkg.name %>)\n',
  ' * Copyright ' + (new Date()).getFullYear(), ' <%= pkg.author %>\n',
  ' * Licensed under <%= pkg.license %> (https://github.com/SecondSite-web/<%= pkg.name %>/blob/master/LICENSE)\n',
  ' */\n',
  '\n'
].join('');

function clean()
{
    return del([
        './css/',
        './js/',
        './src/img/**/*'
    ]);
}

// Bring third party dependencies from node_modules into vendor directory - part of 'gulp vendor' command
function modules()
{
    var bootstrapJS = gulp.src('./node_modules/bootstrap/dist/js/**/*')
            .pipe(gulp.dest('./vendor/bootstrap/js')),
        bootstrapSCSS = gulp.src('./node_modules/bootstrap/scss/**/*')
            .pipe(gulp.dest('./vendor/bootstrap/scss')),
        jquery = gulp.src('./node_modules/jquery/dist/*')
            .pipe(gulp.dest('./vendor/jquery')),
        jqueryValidation = gulp.src('./node_modules/jquery-validation/dist/**/*')
            .pipe(gulp.dest('./vendor/jquery-validation')),
        fa_fonts = gulp.src('./node_modules/@fortawesome/fontawesome-free/webfonts/**/*')
            .pipe(gulp.dest('./webfonts')),
        fa_elements = gulp.src('./node_modules/@fortawesome/fontawesome-free/scss/*')
            .pipe(gulp.dest('./vendor/fontawesome/scss')),
        froala = gulp.src('./node_modules/froala-editor/**/*')
            .pipe(gulp.dest('./vendor/froala')),
        dataTables = gulp.src([
            './node_modules/datatables.net/js/*.js',
            './node_modules/datatables.net-bs4/js/*.js',
            './node_modules/datatables.net-bs4/css/*.css',
            './node_modules/datatables.net-buttons/js/*.js'
        ])
        .pipe(gulp.dest('./vendor/datatables')),
        pdfMake = gulp.src([
            './node_modules/pdfmake/build/*.js',
        ])
        .pipe(gulp.dest('./vendor/pdfmake')),
        jsZip = gulp.src([
            './node_modules/jszip/dist/*.js',
        ])
            .pipe(gulp.dest('./vendor/jszip'))
    return merge(bootstrapJS, bootstrapSCSS, jquery, jqueryValidation, fa_fonts, fa_elements, froala, dataTables, pdfMake, jsZip);
}
// writes font end css from scss - `gulp watch`
function compileCss()
{
    return mincss('./src/scss/styles.scss', './css/')
}
function adminCss()
{
    return mincss('./src/scss/admin/admin.scss', './css/')
}
// minifies individual js files from ./src/js-single to ./js - `gulp watch`
function jsSingle()
{
    return pagejs('./src/js-single/*.js','./js/');
}
// concatenates and minifies backend JS - `gulp watch`
function jsCompile()
{
    return minjs(adminScripts, './admin.js', './js/');
}
// edit this function to change the way SCSS is minified into css - very important function
function mincss(source, destination)
{
    return gulp
        .src(source)
        .pipe(
            plumber({
                errorHandler: function (err) {
                    console.log(err);
                    this.emit('end');
                }
            })
        )
        .pipe(sourcemaps.init()) // begins the sourcemap recording
        // records errors in the process and provides feedback
        .pipe(sass.sync({
            fiber: Fiber,
            outputStyle: "expanded",
            includePaths: "./node_modules"
        }).on('error', sass.logError))
        // autoprefixer for backward compatibility
        .pipe(postcss([autoprefixer({
            overrideBrowserslist: ['last 2 versions'],
            cascade: false
        })]))
        // https://github.com/cssnano/cssnano - tested better than css-minify and clean-css and gulp-cssnano
        // tested far faster than gulp-cssnano
        .pipe(postcss([cssnano])) // cssnano minifier settings in package.json
        .pipe(header(banner, {pkg: pkg})) // writes the banner from 'gulfile.js' to the head of minified CSS files
        .pipe(rename({suffix: '.min'})) // adds .min. to minifed files
        .pipe(sourcemaps.write('/')) // Output source maps.
        .pipe(gulp.dest(destination)) // writes to destination

}
// child function to minify js files
function minjs(input, filename, outputdir)
{
    return pipeline(
        gulp.src(input),
        sourcemaps.init(), // Begins sourcemap capture
        concat(filename), // Concatenates js files in input list
        // stripdebug(), // Strips all debug rules from js files
        // strip(), // Removes all comments from js files
        // terser(), // Supported fork of uglify.js that minifies js files - https://www.npmjs.com/package/terser
        header(banner, {pkg: pkg}), // writes the banner from 'gulfile.js' to the head of minified JS files
        rename({suffix:'.min'}),
        sourcemaps.write('./'), // outputs the sourcemap
        gulp.dest(outputdir) // writes the files to destination
    );
}
// As above but writes each files in ./twig/src/pagejs to ./twig/js
function pagejs(input, outputdir)
{
    return pipeline(
        gulp.src(input),
        sourcemaps.init(),
        // concat(filename),
        // stripdebug(),
        // strip(),
        // terser(),
        rename({suffix:'.min'}),
        header(banner, {pkg: pkg}), // writes the banner from 'gulfile.js' to the head of minified JS files
        sourcemaps.write('./'),
        gulp.dest(outputdir)
    );
}
// exports the full app into a zip file
function dist()
{
    return gulp.src([
        '**',
        '!**/node_modules/**',
        '!**/tools/**',
        '!**/src/**',
        '!**/include/tests/**',
        '!./gitignore',
        '!./composer.json',
        '!./composer.lock',
        '!./gulpfile.js',
        '!./package.json',
        '!./package-lock.json',
        '!./phpstan.neon',
        '!./php_cs.cache'
    ])
        .pipe(zip('dash-dist.zip'))
        .pipe(gulp.dest('./dist'))
}
function zipFile()
{
    return gulp.src(['**',])
        // .pipe(zip('dash-master.zip'))
        .pipe(gulp.dest('../financefusion'))
}
// writes email templates from ./src/email-templates to ./email-templates (compiles with `gulp watch` or `gulp email` command)
function emails()
{
    return gulp.src('./src/email-templates/**/*.html')
        .pipe(gulp_bootstrap_email())
        .pipe(gulp.dest('./email-templates/'))
}
// removes compressed images from ./src (part of `gulp watch` or `gulp images` command
function img_cleanup()
{
    return del('./src/img/**/*')
}
// function that controls image compression - requires research to optimise
// imagemin is an insecure lib and should not be used in a production environment
function img_compress()
{
    return gulp.src(['./src/img/**/*.{gif,png,jpg,svg}'])
    // https://www.npmjs.com/package/imagemin
        .pipe(cache(imagemin({ optimizationLevel: 5, verbose:true, progressive:true, use:[
                //png
                pngquant({
                    speed: 1,
                    quality: [0.95, 1] //lossy settings
                }),
                Zopfli({
                    more: true
                    // iterations: 50 // very slow but more effective
                }),
                // gif very light lossy, use only one of gifsicle or Giflossy
                // imagemin.gifsicle({
                //     interlaced: true,
                //     optimizationLevel: 3
                // }),
                Giflossy({
                    optimizationLevel: 3,
                    optimize: 3, //keep-empty: Preserve empty transparent frames
                    lossy: 2
                }),
                //svg
                imagemin.svgo({
                    plugins: [{
                        removeViewBox: false
                    }]
                }),
                //jpg lossless
                imagemin.mozjpeg({
                    progressive: true
                }),
                //jpg very light lossy, use vs jpegtran
                Mozjpeg({
                    quality: 50
                })
            ]})))
        .pipe(gulp.dest('./img'));
}
// Cleans working folders
// Watch files - any file change in a watched folder triggers the related compile function
function watchFiles()
{
      gulp.watch("./src/scss/admin/**/*", adminCss);
      gulp.watch("./src/email-templates/**/*", emails);
      gulp.watch(["./src/js-single/**/*", "!./src/js-single/**/*.min.js"], jsSingle);
      gulp.watch(["./src/js-compile/**/*", "!./src/js-compile/**/*.min.js"], jsCompile);
      gulp.watch("./src/img/**.*", images);
}

// Define complex tasks
const vendor = gulp.series(clean, modules);
const images = gulp.series(img_compress, img_cleanup);
const build = gulp.series(vendor, images, emails, adminCss, jsCompile, jsSingle);
const watch = gulp.parallel(watchFiles);

// Export tasks - tasks that can be run by 'gulp ' - eg. 'gulp images'
exports.images = images; // compresses images
exports.adminCss = adminCss;
exports.compileCss = compileCss; // writes client css
exports.jsCompile = jsCompile; // writes admin JS
exports.jsSingle = jsSingle; // writes individual admin JS files
exports.clean = clean; // deletes folders when doing a fresh compile or update
exports.emails = emails; // writes template emails
exports.vendor = vendor; // re-writes the ./vendor folder
exports.build = build; // does a full write of all css and js and images and email templates - A full 'gulp watch' cycle
exports.watch = watch; // Watches ./src and ./twig/src for all saved changes and auto compiles css, js, email templates, and images
exports.default = build; // sets - 'gulp' command to run the 'build' function
exports.zipFile = zipFile; // Exports the full file structure to a zip file in ./dist
exports.dist = dist;