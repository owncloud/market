const VueLoaderPlugin = require('vue-loader/lib/plugin');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = {
	mode: 'production',
	entry: './src/default.js',
	output : {
		path: require('path').resolve(__dirname, 'js'),
		filename : 'market.bundle.js',
		publicPath: '/'
	},
	optimization: {
		// Disable terser's parallel worker pool: in CI the jest-worker child
		// processes can fail to terminate, leaving webpack hanging after the
		// build completes until the job hits the 6h timeout. Building
		// single-threaded is negligibly slower for this bundle.
		minimizer: [
			new TerserPlugin({ parallel: false })
		]
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
			use: ['style-loader', 'css-loader', {
				loader: 'sass-loader',
				options: {
					implementation: require('sass'),
				}
			}]
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
