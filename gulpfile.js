// const gulp   = require("gulp");
const rimraf = require('rimraf');
const { src, dest, watch, series, parallel, lastRun } = require('gulp');

//エラー時処理
const plumber = require('gulp-plumber'); //続行
const notify = require('gulp-notify'); //通知

//browserSync
const browserSync = require('browser-sync').create();

//sass・css系
const sass = require('gulp-sass'); //sassコンパイル
const sassGlob = require('gulp-sass-glob'); //glob (@importの/*を可能に)
const autoprefixer = require('gulp-autoprefixer'); //プレフィックス付与
const gcmq = require('gulp-group-css-media-queries'); //media query整理
const cleanCSS = require('gulp-clean-css');

//webpack
const webpack = require('webpack');
const webpackStream = require('webpack-stream');
const webpackConfig = require('./webpack.config');

//JS Concat
// const babel  = require('gulp-babel');
// const uglify = require('gulp-uglify');
// const rename = require('gulp-rename');
const concat = require('gulp-concat');

//画像圧縮
const imagemin = require('gulp-imagemin'); //img圧縮
const mozjpeg = require('imagemin-mozjpeg'); //jpeg圧縮
const pngquant = require('imagemin-pngquant'); //png圧縮
const changed = require('gulp-changed');

/**
 * パス
 */
const path = {
    src: {
        scss: 'src/scss/**/*.scss',
        jsPlugin: 'src/js/plugin/*.js',
        js: 'src/js/**/*.js',
        img: 'src/img/**/*.{png,jpg,gif,svg}',
    },
    dest: {
        css: 'assets/css',
        js: 'assets/js',
        img: 'assets/img',
    },
};

/**
 * ブラウザシンク
 */
const myBrowserInit = (cb) => {
    browserSync.init({
        proxy: 'http://themes.wp/',
        open: true,
        watchOptions: {
            debounceDelay: 500, //0.5秒間、タスクの再実行を抑制
        },
    });
    cb(); // 明示的に終了を通知
};

const browserReload = (cb) => {
    browserSync.reload();
    cb();
};

/**
 * SASSコンパイル
 */
const compileSass = (cb) => {
    return (
        src(path.src.scss)
            .pipe(plumber({ errorHandler: notify.onError('<%= error.message %>') }))
            .pipe(sassGlob())
            .pipe(sass())
            .pipe(
                autoprefixer({
                    cascade: false,
                })
            )
            .pipe(gcmq())
            // .pipe(sass({ outputStyle: 'compressed' }))  //gcmqでnestedスタイルに展開されてしまうので再度compact化。
            .pipe(cleanCSS())
            .pipe(dest(path.dest.css))
    );
};

/*
 * プラグインスクリプトをまとめる
 */
const concatPluginScripts = (cb) => {
    return src(path.src.jsPlugin)
        .pipe(plumber({ errorHandler: notify.onError('<%= error.message %>') }))
        .pipe(concat('plugins.js'))
        .pipe(dest(path.dest.js));
};

/**
 * Webpack
 */
const doWebpack = (cb) => {
    return webpackStream(webpackConfig, webpack)
        .on('error', function(e) {
            this.emit('end');
        })
        .pipe(dest(path.dest.js));
};

/**
 * 画像圧縮
 */
const doImgMin = (cb) => {
    return src(path.src.img, {
        since: lastRun(doImgMin), // changed の 4版
    })
        .pipe(
            imagemin([
                pngquant({
                    quality: [0.7, 0.8], // 画質
                    speed: 1, // 最低のスピード
                    floyd: 0, // ディザリングなし
                }),
                mozjpeg({
                    progressive: true,
                }),
                imagemin.svgo(),
                imagemin.optipng(),
                imagemin.gifsicle(),
            ])
        )
        .pipe(dest(path.dest.img)); // 保存
    //.pipe(notify('&#x1f363; images task finished &#x1f363;'));
};

/**
 * watch
 */
const watchFiles = (cb) => {
    watch(path.src.scss, compileSass);
    watch(path.src.jsPlugin, concatPluginScripts);
    watch(path.src.js, doWebpack);
    watch(path.src.img, doImgMin);
    watch('**/*.{php,css}', browserReload);
    watch(path.dest.js + '/**/*.js', browserReload);

    cb();
};

exports.default = series(
    // myBrowserInit,
    parallel(watchFiles)
);
