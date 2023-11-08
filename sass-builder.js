/* eslint no-console: 0 */
// console.log('start sass-builder.js ...');

const path = require('path');
const fs = require('fs');
const glob = require('glob');

// node-sass
const sass = require('node-sass');
const globImporter = require('node-sass-glob-importer');

// postcss
const postcss = require('postcss');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const mqpacker = require('css-mqpacker');

// consoleの色付け
const red = '\u001b[31m';
const green = '\u001b[32m';

const writeCSS = (filePath, css) => {
	const dir = path.dirname(filePath);

	// ディレクトリがなければ作成
	if (!fs.existsSync(dir)) {
		fs.mkdirSync(dir, { recursive: true });
	}

	// css書き出し
	fs.writeFileSync(filePath, css);
};

// パス

// パス
const src = 'src/scss';
const dist = 'build/css';
// const files = [
// 	'hcb',
// 	'hcb--light',
// 	'hcb--dark',
// 	'hcb-editor--light',
// 	'hcb-editor--dark',
// 	'hcb-admin',
// ];
const ignore = ['**/_*.scss'];
const files = glob.sync(src + '/**/*.scss', { ignore });
// console.log('files', files);

files.forEach((filePath) => {
	const fileName = filePath.replace(src + '/', '');
	const srcPath = path.resolve(__dirname, src, fileName);
	const distPath = path.resolve(__dirname, dist, fileName).replace('.scss', '.css');

	// console.log(filePath, srcPath, distPath);

	// renderSyncだとimporter使えない
	sass.render(
		{
			file: srcPath,
			outputStyle: 'compressed',
			importer: globImporter(),
		},
		function (err, sassResult) {
			if (err) {
				console.error(red + err);
			} else {
				const css = sassResult.css.toString();

				// postcss実行
				postcss([autoprefixer, mqpacker, cssnano])
					.process(css, { from: undefined })
					.then((postcssResult) => {
						console.log(green + 'Wrote CSS to ' + distPath);
						writeCSS(distPath, postcssResult.css);

						// if (postcssResult.map) {fs.writeFile('dest/app.css.map', postcssResult.map.toString(), () => true);}
					});
			}
		}
	);
});
