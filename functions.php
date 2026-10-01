<?php
/**
 * Maulik Dev functions and definitions.
 *
 * This file is intentionally a thin loader and nothing else. Every piece of
 * behaviour lives in a file under inc/ so that a child theme can replace a
 * single concern without having to fork this file, and so that the load order
 * stays obvious at a glance.
 *
 * @package Maulik_Dev
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme version.
 *
 * Used to bust asset caches. Kept in sync with the Version header in
 * style.css by hand, because Core does not read style.css at runtime for this.
 */
define( 'MAULIK_DEV_VERSION', '0.1.0' );

/**
 * Absolute path to the theme directory, with a trailing slash.
 */
define( 'MAULIK_DEV_DIR', trailingslashit( get_parent_theme_file_path() ) );

/**
 * URL of the theme directory, with a trailing slash.
 */
define( 'MAULIK_DEV_URI', trailingslashit( get_parent_theme_file_uri() ) );

require_once MAULIK_DEV_DIR . 'inc/helpers.php';
require_once MAULIK_DEV_DIR . 'inc/setup.php';
require_once MAULIK_DEV_DIR . 'inc/assets.php';
require_once MAULIK_DEV_DIR . 'inc/seo.php';
