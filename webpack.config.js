module.exports = {
	entry: './src/default.js',
	output : {
		filename : './js/market.bundle.js'
	},
	module: {
		loaders: [{
			test: /\.js?$/,
			exclude: /node_modules/,
			loader: 'babel-loader',
		}, {
			test: /\.vue$/,
			loader: 'vue-loader',
			options: {
				loaders: {
					scss: 'vue-style-loader!css-loader!sass-loader', // <style lang="scss">
					less: 'vue-style-loader!css-loader!less-loader' // <style lang="less">
				}
			}
		}]
	}
}