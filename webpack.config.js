const defaultConfig = require( '@wordpress/scripts/config/webpack.config.js' );

module.exports = {
	...defaultConfig,
	...{
		entry: {
			'choctaw-events': __dirname + `/src/js/index.ts`,
		},
	},
};
