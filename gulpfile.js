const { src, dest, watch, series, parallel, lastRun } = require('gulp');

// エラー時処理
const plumber = require('gulp-plumber'); // 続行
const notify = require('gulp-notify'); // 通知

// sass・css系
const sass = require('gulp-sass'); // sassコンパイル
const sassGlob = require('gulp-sass-glob'); // glob (@importの/*を可能に)
const autoprefixer = require('gulp-autoprefixer'); // プレフィックス付与
const gcmq = require('gulp-group-css-media-queries'); // media query整理
const cleanCSS = require('gulp-clean-css');

// webpack
const webpack = require('webpack');
const webpackStream = require('webpack-stream');
const webpackConfig = require('./webpack.config');

/**
 * パス
 */
const path = {
	watch: {
		scss: 'src/**/*.scss',
		js: 'src/**/*.js',
		inlinestyle: 'src/blocks/**/*_inline.scss',
	},
	src: {
		scss: 'src/scss/**/*.scss',
		// js: 'src/**/*.js',
	},
	dest: {
		css: 'build/css',
		js: 'build/js',
	},
};

/**
 * SCSSコンパイル
 */
const compileScss = () => {
	return (
		src(path.src.scss)
			.pipe(
				plumber({
					errorHandler: notify.onError('<%= error.message %>'),
				})
			)
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

/**
 * Webpack
 */
const doWebpack = () => {
	return webpackStream(webpackConfig, webpack)
		.on('error', function () {
			this.emit('end'); // webpack-streamでエラー発生時に処理を終了させずに監視を続ける
		})
		.pipe(dest(path.dest.js));
};

/**
 * watch
 */
const watchFiles = (cb) => {
	watch(path.watch.scss, compileScss);
	watch(path.watch.js, doWebpack);
	watch(path.watch.inlinestyle, doWebpack);
	cb();
};

//exports.default = series( myBrowserInit, parallel( watchFiles ) );
exports.default = watchFiles;
exports.webpack = doWebpack;
exports.scss = compileScss;
