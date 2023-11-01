const VueLoaderPlugin = require('vue-loader/lib/plugin');

module.exports = {
	devtool: 'cheap-eval-source-map',
	entry: './src/default.js',
	output : {
		path: require('path').resolve(__dirname, 'js'),
		filename : 'market.bundle.js',
		publicPath: '/'
	},
	module: {
		rules: [{
			test: require.resolve('uikit'),
			loader: 'expose-loader?UIkit'
		}, {
			test: /\.js?$/,
			exclude: /node_modules/,
			loader: 'babel-loader',
		}, {
			test: /\.scss?$/,
			loader: 'style-loader!css-loader!sass-loader'
		}, {
			test: /\.pug$/,
			loader: 'vue-pug-loader'
		}, {
			test: /\.vue$/,
			loader: 'vue-loader',
			options: {
				loaders: {
					scss: 'vue-style-loader!css-loader!sass-loader',
					less: 'vue-style-loader!css-loader!less-loader'
				}
			}
		}]
	},
	plugins: [
		new VueLoaderPlugin()
	]
}
