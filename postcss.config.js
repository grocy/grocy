/* eslint-disable global-require, import/no-extraneous-dependencies */
const postcssConfig = {
	plugins: [require('autoprefixer')], // handle browser-needed prefixes
};

// If we are in production mode, then add cssnano
if (process.env.NODE_ENV === 'production')
{
	postcssConfig.plugins.push(
		require('cssnano')({
			// use the safe preset so that it doesn't
			// mutate or remove code from our css
			preset: 'default',
		})
	);
}

module.exports = postcssConfig;