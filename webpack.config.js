const webpack = require('webpack');
const VueLoaderPlugin = require('vue-loader/lib/plugin');

module.exports = {
    mode: 'production',

    // メインとなるJavaScriptファイル（エントリーポイント）
    entry: {
        hcb_script: './src/js/hcb_script.js',
        hcb_block: './src/js/hcb_block.js',
    },

    // ファイルの出力設定
    output: {
        // 出力ファイル名
        filename: '[name].js',

        //pathはgulp側で。
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: [
                    {
                        // Babel を利用する
                        loader: 'babel-loader',
                        // Babel のオプションを指定する
                        options: {
                            presets: ['@babel/preset-env'],
                            plugins: [
                                [
                                    'transform-react-jsx',
                                    {
                                        pragma: 'wp.element.createElement',
                                    },
                                ],
                            ],
                        },
                    },
                ],
            },
        ],
    },
    //importするときにいちいち.jsを書かなくてもすむ
    resolve: {
        extensions: ['.js'],
    },
    performance: { hints: false },
};
