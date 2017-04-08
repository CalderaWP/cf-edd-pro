var gulp = require('gulp');
var concat = require('gulp-concat');
var watch = require('gulp-watch');
var minify = require( 'gulp-minify' );

gulp.task('js', function() {
    gulp.src('assets/*.js')
        .pipe(minify({
            ext:'.min.js',
            noSource: true,
            mangle: true,
            compress: true
        }))
        .pipe(gulp.dest('assets/build'))
});

gulp.task('watch', function(){
    gulp.watch('assets/*.js', ['js']);
});

gulp.task('default', ['js']);