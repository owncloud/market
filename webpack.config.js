// webpack.config.js
// https://medium.com/js-dojo/how-to-configure-webpack-4-with-vuejs-a-complete-guide-209e943c4772

const { VueLoaderPlugin } = require('vue-loader')

module.exports = {
	devtool: 'eval-cheap-source-map',
	entry: './src/default.js',
	output : {
		filename : './js/market.bundle.js'
	},
	module: {
		rules: [{
			test: require.resolve('uikit'),
			use: [{
				loader: 'expose-loader?UIkit',
				options: {
					exposes: [{
						globalName: 'Promise',
						override: true,
					}],
				}
			}],
		}, {
			test: /\.js?$/,
			exclude: /node_modules/,
			use: ['babel-loader'],
		}, {
			test: /\.(sa|sc|c)ss$/,
			use: ['style-loader', 'css-loader', 'sass-loader'],
		}, {
			test: /\.vue$/,
			loader: 'vue-loader',
				options: {
					loaders: {
						scss: [
							{loader: 'vue-style-loader'},
							{loader: 'css-loader'},
							{loader: 'sass-loader'},
						],
						less: [
							{loader: 'vue-style-loader'},
							{loader: 'css-loader'},
							{loader: 'less-loader'},
						],
					}
				}
		}]
	},
	// make sure to include the plugin!
	plugins: [
		new VueLoaderPlugin()
	]
}
