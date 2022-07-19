var gulp = require('gulp');
var plumber = require('gulp-plumber');
var stripDebug = require('gulp-strip-debug');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');

gulp.task('js', function () {
  return gulp.src('Resources/Private/JavaScript/**/*.js')
    .pipe(gulp.dest('Resources/Public/JavaScript'))
    .pipe(stripDebug())
    .pipe(uglify())
    .pipe(rename({ suffix: ".min" }))
    .pipe(gulp.dest('Resources/Public/JavaScript'));
});

gulp.task('watch', function () {
  gulp.watch('Resources/Private/JavaScript/**/*.js', gulp.parallel('js'));
});

gulp.task('build', gulp.parallel('js'));

gulp.task('default', gulp.parallel('build', 'watch'));
