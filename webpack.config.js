const VueLoaderPlugin = require('vue-loader/lib/plugin');

module.exports = {
	mode: 'production',
	entry: './src/default.js',
	output : {
		path: require('path').resolve(__dirname, 'js'),
		filename : 'market.bundle.js',
		publicPath: '/'
	},
	module: {
		rules: [{
			test: require.resolve('uikit'),
			loader: 'expose-loader',
			options: {
				exposes: ["UIkit"],
			},
		}, {
			test: /\.js?$/,
			exclude: /node_modules/,
			use: 'babel-loader',
		}, {
			test: /\.scss?$/,
			use: ['style-loader', 'css-loader', 'sass-loader']
		}, {
			test: /\.pug$/,
			use: 'vue-pug-loader'
		}, {
			test: /\.vue$/,
			loader: 'vue-loader',
		}]
	},
	plugins: [
		new VueLoaderPlugin()
	]
}
