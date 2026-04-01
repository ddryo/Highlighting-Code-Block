/**
 * block.json のコピー先を JS の出力先と一致させるため、基準ディレクトリは src にする。
 */
process.env.WP_SRC_DIRECTORY = 'src';

const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

/**
 * CleanWebpackPlugin （ビルド先のほかのファイルを勝手に削除するやつ） はオフに。
 */
// defaultConfig.plugins.shift();

/**
 * exports
 *  path.path/{WP_SRC_DIRECTORYからの相対パス} へ block.json が吐き出されるので、JS の entry 名と揃える。
 */
module.exports = {
	...defaultConfig, //@wordpress/scriptを引き継ぐ

	mode: 'production', // npm start でも圧縮させる

	//エントリーポイント
	entry: {
		// hcb: './src/js/hcb.js',
		'js/hcb_script': './src/js/hcb_script.js',
		'js/code-block/index': './src/js/code-block/index.js',
	},

	//アウトプット先
	output: {
		path: path.resolve(__dirname, 'build'),
		filename: '[name].js',
	},
	resolve: {
		alias: {
			'@blocks': path.resolve(__dirname, 'src/blocks/'),
		},
	},
	performance: { hints: false },
};
