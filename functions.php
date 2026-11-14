<?php
/**
 * KÜS Prüfstelle Blatter – Theme-Bootstrap.
 */

defined( 'ABSPATH' ) || exit;

define( 'BLATTER_VERSION', '1.0.0' );
define( 'BLATTER_DIR', get_template_directory() );
define( 'BLATTER_URI', get_template_directory_uri() );

require BLATTER_DIR . '/inc/helpers.php';
require BLATTER_DIR . '/inc/setup.php';
require BLATTER_DIR . '/inc/content-types.php';
require BLATTER_DIR . '/inc/settings.php';
require BLATTER_DIR . '/inc/shortcodes.php';
require BLATTER_DIR . '/inc/seo.php';
