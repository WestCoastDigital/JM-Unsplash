<?php
/**
 * Plugin Name: JM Unsplash Imagery
 * Plugin URI: https://example.com/plugins/jm-unsplash-imagery
 * Description: This plugin allows you to easily add Unsplash images to your website.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define constants
define( 'JM_UNSPLASH_IMAGERY_VERSION', '1.0.0' );
define( 'JM_UNSPLASH_IMAGERY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'JM_UNSPLASH_IMAGERY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include all php files in inc folder
foreach ( glob( JM_UNSPLASH_IMAGERY_PLUGIN_DIR . 'inc/*.php' ) as $file ) {
    include_once $file;
}