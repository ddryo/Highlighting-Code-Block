const webpack = require('webpack');
const VueLoaderPlugin = require('vue-loader/lib/plugin');

module.exports = {
  mode: "production",
  
  // メインとなるJavaScriptファイル（エントリーポイント）
  entry: {
    hcb_script: './src/js/hcb_script.js',
    hcb_gutenberg_script: './src/js/hcb_gutenberg_script.js',
  },
 
  // ファイルの出力設定
  output: {
    // 出力ファイル名
    //filename: 'main.js',
    filename: '[name].js',
    
    //pathはgulp側で。
  },
  module: {

    rules: [
      {
        test: /\.vue$/,
        loader: 'vue-loader',
      },
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
              "plugins": [
                [
                  "transform-react-jsx",
                  {
                    "pragma": "wp.element.createElement"
                  }
                ]
              ]
            }
          }
        ]
      }
    ]
  },
  //importするときにいちいち.vueを書かなくてもすむ
  resolve: {
    extensions: ['.js', '.vue'],
    alias: {
      vue$: 'vue/dist/vue.esm.js',
    },
  },

  plugins: [
    new VueLoaderPlugin(),
    // new webpack.ProvidePlugin({
    //   $: 'jquery',
    //   jQuery: 'jquery'
    // })
  ],
  performance: { hints: false }
};