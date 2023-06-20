const defaultConfig = require( '@wordpress/scripts/config/webpack.config.js' );

module.exports = {
	...defaultConfig,
	...{
		entry: {
			global: __dirname + `/src/index.js`,
			search: __dirname + `/src/js/search/App.tsx`,
		},
		output: {
			path: __dirname + `/build`,
			filename: `[name].js`,
		},
	},
};
