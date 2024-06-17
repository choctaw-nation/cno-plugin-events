const defaultConfig = require( '@wordpress/scripts/config/webpack.config.js' );

module.exports = {
	...defaultConfig,
	...{
		entry: {
			'choctaw-events': __dirname + `/src/js/index.ts`,
		},
		output: {
			path: __dirname + `/dist`,
			filename: `[name].js`,
		},
	},
};
