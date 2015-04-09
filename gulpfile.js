var elixir = require('laravel-elixir');
var gulp = require('gulp');
var concat = require('gulp-concat');
var paths = require('./gulp.config.json');
var rename = require('gulp-rename');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.less([
      'app.less',
      'styles.less'
    ]);
});

gulp.task('default', ['js', 'copyindex', 'copytemplates'],function() {

});

gulp.task('js', function() {
  return gulp.src([].concat(paths.js))
    .pipe(concat('app.js'))
    .pipe(gulp.dest('./public/build/js/'));
});

gulp.task('copyindex', function() {
  return gulp.src('./resources/assets/app/index.html')
    .pipe(rename('layout.blade.php'))
    .pipe(gulp.dest(paths.build.index));
});

gulp.task('copytemplates', function() {
  return gulp.src(['./resources/assets/app/**/*.html', '!./resources/assets/app/index.html'])
    .pipe(gulp.dest(paths.build.views));
});
