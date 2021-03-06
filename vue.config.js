const WebpackCdnPlugin = require('webpack-cdn-plugin')
module.exports = {
  publicPath: './',
  pluginOptions: {
    cordovaPath: 'src-cordova',
    i18n: {
      locale: 'en',
      fallbackLocale: 'en',
      localeDir: 'locales',
      enableInSFC: false
    }
  },
  pwa: {
    workboxOptions: {
      skipWaiting: true,
        runtimeCaching: [
          {
            urlPattern: new RegExp('https://cdn\\.jsdelivr\\.net/.*'),
            handler: 'NetworkFirst',
            options: {
              networkTimeoutSeconds: 20,
              cacheName: 'cdn-cache',
              cacheableResponse: {
                statuses: [200]
              }
            }
          }
        ]
	}
  },
  configureWebpack: {
    plugins: [
      ...(process.env.CDN == 'yes' ? [new WebpackCdnPlugin({
        modules: [
          { name: 'vue', var: 'Vue', path: 'dist/vue.runtime.min.js' },
          { name: 'vue-router', var: 'VueRouter', path: 'dist/vue-router.min.js' },
          //{ name: '@fortawesome/fontawesome-free', style: 'css/all.min.css', cssOnly: true },
          { name: 'chemicaltools', var: 'chemicaltools', path: 'dist/main.js' },
          { name: 'string-format', var: 'format', path: 'index.min.js' },
          { name: 'vue-i18n', var:'VueI18n', path: 'dist/vue-i18n.min.js' },
          { name: 'vue2-storage', var: 'Vue2Storage', path: 'dist/vue2-storage.min.js' },
        ],
        prodUrl: "//cdn.jsdelivr.net/npm/:name@:version/:path"
        // publicPath: './node_modules'
      })]:[])
    ]
  }
}
