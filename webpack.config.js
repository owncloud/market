module.exports = {
	devtool: 'cheap-eval-source-map',
	entry: './src/default.js',
	output : {
		filename : './js/market.bundle.js'
	},
	module: {
		loaders: [{
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
			test: /\.vue$/,
			loader: 'vue-loader',
			options: {
				loaders: {
					scss: 'vue-style-loader!css-loader!sass-loader',
					less: 'vue-style-loader!css-loader!less-loader'
				}
			}
		}]
	}
}
