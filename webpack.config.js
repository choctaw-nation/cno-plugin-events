const defaultConfig = require('@wordpress/scripts/config/webpack.config.js');

module.exports = {
	...defaultConfig,
	...{
		entry: {
			'choctaw-events': __dirname + `/src/js/index.ts`,
			'choctaw-events-search': __dirname + `/src/js/search/App.tsx`,
		},
		resolve: {
			...defaultConfig.resolve,
			extensions: ['.js', '.jsx', '.ts', '.tsx'],
		},
		output: {
			path: __dirname + `/dist`,
			filename: `[name].js`,
		},
	},
};
