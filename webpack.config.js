/**
 * WP_SRC_DIRECTORY: src/js にセットしてることに注意。
 */
process.env.WP_SRC_DIRECTORY = 'src/js';

const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

/**
 * CleanWebpackPlugin （ビルド先のほかのファイルを勝手に削除するやつ） はオフに。
 */
// defaultConfig.plugins.shift();

/**
 * exports
 *  path.path/{WP_SRC_DIRECTORYからの相対パス} へ、cssやblock.jsonが吐き出されるので、関係性に注意。（@v.26時点）
 */
module.exports = {
	...defaultConfig, //@wordpress/scriptを引き継ぐ

	mode: 'production', // npm start でも圧縮させる

	//エントリーポイント
	entry: {
		// hcb: './src/js/hcb.js',
		hcb_script: './src/js/hcb_script.js',
		'/code-block/index': './src/js/code-block/index.js',
	},

	//アウトプット先
	output: {
		path: path.resolve(__dirname, 'build/js'),
		filename: '[name].js',
	},
	resolve: {
		alias: {
			'@blocks': path.resolve(__dirname, 'src/blocks/'),
		},
	},
	performance: { hints: false },
};
