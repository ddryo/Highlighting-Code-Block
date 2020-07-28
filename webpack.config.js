const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');
const glob = require('glob');

// ブロック自動取得

module.exports = {
	...defaultConfig, //@wordpress/scriptを引き継ぐ

	mode: 'production', // npm start でも圧縮させる

	//エントリーポイント
	entry: {
		hcb_script: './src/js/hcb_script.js',
		hcb_blocks: './src/js/hcb_blocks.js',
		['blocks/code-block/index']: './src/js/blocks/code-block/index.js',
	},

	//アウトプット先
	output: {
		filename: '[name].js',
		// path は Gulpで 指定
	},

	module: {
		...defaultConfig.module,
		rules: [
			...defaultConfig.module.rules,
			{
				test: /\.scss/,
				use: [
					// linkタグに出力する機能
					'style-loader',
					{
						// CSSをバンドルするための機能
						loader: 'css-loader',
						options: { url: false }, // CSS内のurl()メソッドの取り込みを禁止する
						// sourceMap: false, // ソースマップの利用有無
						// importLoaders: 2, //sass-loaderの読み込みに必要?
					},
					{
						loader: 'sass-loader',
						options: {
							// ソースマップの利用有無
							// sourceMap: false,
						},
					},
				],
			},
		],
	},

	resolve: {
		alias: {
			'@blocks': path.resolve(__dirname, 'src/blocks/'),
		},
	},
};
