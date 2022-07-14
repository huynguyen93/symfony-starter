const path = require('path');
const { WebpackManifestPlugin } = require('webpack-manifest-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
  entry: {
    cssLight: './scss/front-light.scss',
    cssDark: './scss/front-dark.scss',
    front: './js/front.js',
    react: [
      'react',
      'react-dom',
      'redux',
      'react-redux',
      'redux-thunk',
      'redux-logger',
      'react-bootstrap',
    ],
  },
  output: {
    filename: '[name].[chunkhash].js',
    path: path.resolve(__dirname, '../public/assets'),
    clean: true,
    chunkFilename: '[name].[chunkhash].js',
    publicPath: '/assets/',
  },
  resolve: {
    extensions: ['.js', '.jsx'],
  },
  module: {
    rules: [
      {
        test: /\.(js|jsx)$/,
        exclude: /node_modules/,
        use: [
          'babel-loader'
        ],
      },
      {
        test: /\.(scss|css)$/,
        use: [
          "style-loader",
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              esModule: false,
            },
          },
          // Translates CSS into CommonJS
          "css-loader",
          // Compiles Sass to CSS
          "sass-loader",
        ],
      },
    ],
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: '[name].[chunkhash].css',
    }),
    new WebpackManifestPlugin({
      writeToFileEmit: true,
      basePath: 'assets/',
    }),
  ]
};
