module.exports = {
	devtool: 'cheap-eval-source-map',
	entry: './src/default.js',
	output : {
		filename : './js/market.bundle.js'
	},
	module: {
		rules: [{
			test: require.resolve('uikit'),
			use: 'expose-loader?UIkit'
		}, {
			test: /\.js?$/,
			exclude: /node_modules/,
			use: 'babel-loader',
		}, {
			test: /\.scss?$/,
			use: ['style-loader', 'css-loader', 'sass-loader']
		}, {
			test: /\.vue$/,
			loader: 'vue-loader',
			options: {
				use: {
					scss: ['vue-style-loader', 'css-loader', 'sass-loader'],
					less: ['vue-style-loader', 'css-loader', 'less-loader']
				}
			}
		}]
	}
}
