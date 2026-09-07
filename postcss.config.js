module.exports = {
	plugins: [
		require('cssnano')({
			preset: ['default', {
				discardComments: {
					removeAll: true,
				},
			}]
		}),
        require('postcss-sort-media-queries')({
            sort: 'mobile-first',
        }),
		require('postcss-preset-env')({
            stage: 1,
            // Cascade Layers (@layer, see src/sass/themes/_default.scss)
            // used to get polyfilled via a specificity-boost hack applied
            // per PostCSS build pass. That silently broke once each block
            // became its own separate Vite/PostCSS entry - a rule compiled
            // in one pass has no way to know about @layer usage in another,
            // so boosting became inconsistent across chunks (see
            // AUDIT_perf_seo_a11y.md). Every currently-supported browser
            // (browserslist: last 2 versions, > 0.5%, not dead) has native
            // @layer support, so let the browser resolve it for real
            // instead - that works correctly across any number of
            // separately-loaded stylesheets, which the polyfill never did.
            features: {
                'cascade-layers': false,
            },
        }),
		require('autoprefixer')
	],
};
