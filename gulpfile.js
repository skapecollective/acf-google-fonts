const { src, dest } = require( 'gulp' );
const sass = require( 'gulp-sass' );
const autoprefixer = require( 'gulp-autoprefixer' );
const babel = require( 'gulp-babel' );
const minify = require( 'gulp-babel-minify' );

function buildTask( callback ) {

    // Process stylesheets
    src( 'assets/sass/field.scss' )
        .pipe( sass( {
            outputStyle: 'compressed'
        } ).on( 'error', sass.logError ) )
        .pipe( autoprefixer() )
        .pipe( dest( 'build/css' ) );

    // Process javascripts
    src( 'assets/js/field.js' )
        .pipe( babel({
            presets: [ '@babel/env' ]
        } ) ).on( 'error', function( error ) {
        console.error( error.toString(), '\n\b', error.codeFrame );
        this.emit( 'end' );
    } )
        .pipe( minify() )
        .pipe( dest( 'build/js' ) );

    callback();
}


exports.default = buildTask;
