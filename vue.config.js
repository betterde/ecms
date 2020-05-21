// const fs = require('fs');
// const CompressionPlugin = require("compression-webpack-plugin");

module.exports = {
  pages: {
    index: {
      entry: "spa/src/entries/index.js",
      template: 'spa/public/index.html',
      filename: 'index.html',
      title: 'ECMS',
      chunks: ['chunk-vendors', 'chunk-common', 'index']
    }
  },
  outputDir: 'public',
  indexPath: process.env.NODE_ENV === 'production' ? '../resources/views/index.blade.php' : 'index.html',
  // configureWebpack: {
  //
  // },
  devServer: {
    // host: 'ecms.betterde.com',
    // port: 443,
    // http2: true,
    // https: {
    //   key: fs.readFileSync('./cert/betterde.com.key'),
    //   cert: fs.readFileSync('./cert/fullchain.cer'),
    //   ca: fs.readFileSync('./cert/ca-bundle.trust.crt'),
    // },
    proxy: {
      '/api': {
        target: 'http://ecms.it/',
        ws: true,
        changeOrigin: true,
        secure: false,
        pathRewrite: {
          '^/api': '/api'
        }
      }
    },
    compress: true,
    open: 'Google Chrome'
  },
  // configureWebpack: config => {
  //   if (process.env.NODE_ENV === 'production') {
  //     config.plugins.push(new CompressionPlugin({
  //         algorithm: 'gzip',
  //         test: /\.js$|\.css/,
  //         threshold: 10240,
  //         minRatio: 0.8,
  //         deleteOriginalAssets: true
  //       })
  //     )
  //   }
  // }
};
