// Programmatic webpack build runner.
//
// Why this exists instead of calling the `webpack` CLI directly:
// vue-template-compiler (pulled in by vue-loader) runs Vue 2's nextTick setup
// at require() time, which creates a native MessageChannel and registers
// `channel.port1.onmessage`. On Node >=12 that ref'd MessagePort keeps the
// event loop alive indefinitely, so the process never exits after the bundle
// has compiled. In CI the "Build dist" job then runs until the 6h ceiling and
// is cancelled. Running webpack programmatically lets us call process.exit()
// once the build is done, so the lingering handle can no longer block exit.

process.env.NODE_ENV = process.env.NODE_ENV || 'production';

const webpack = require('webpack');
const config = require('./webpack.config.js');

webpack(config).run((err, stats) => {
	if (err) {
		console.error(err.stack || err);
		if (err.details) {
			console.error(err.details);
		}
		process.exit(1);
	}

	console.log(stats.toString({ colors: true }));

	process.exit(stats.hasErrors() ? 1 : 0);
});
