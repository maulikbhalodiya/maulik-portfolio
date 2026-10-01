/**
 * ESLint flat config.
 *
 * ESLint 9 dropped .eslintrc support, so the theme ships a flat config rather
 * than relying on whatever wp-scripts would scaffold. The scope is deliberately
 * narrow: the only JavaScript in this repository is the SCSS token generator.
 * There is no front end JavaScript and there is no jQuery.
 */

import js from '@eslint/js';
import wordpress from '@wordpress/eslint-plugin';

export default [
	{
		ignores: [
			'build/**',
			'node_modules/**',
			'vendor/**',
			'assets/styles/generated/**',
			'style.css',
			'style.min.css',
		],
	},
	js.configs.recommended,
	...wordpress.configs.recommended,
	...wordpress.configs.i18n,
	...wordpress.configs.jsdoc,
	{
		languageOptions: {
			ecmaVersion: 2024,
			sourceType: 'module',
			globals: {
				process: 'readonly',
			},
		},
		rules: {
			'no-console': 'warn',
			eqeqeq: [ 'error', 'always' ],
			'prefer-const': 'error',
		},
	},
];