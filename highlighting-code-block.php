<?php
/**
 * Plugin Name: Highlighting Code Block
 * Plugin URI: https://wordpress.org/plugins/highlighting-code-block/
 * Description: Add code block with syntax highlighting using prism.js. (Available for Gutenberg and Classic Editor)
 * Version: 1.2.7
 * Author: LOOS, Inc.
 * Author URI: https://loos-web-studio.com/
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: loos-hcb
 */
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! function_exists( 'register_block_type' ) ) return;

/**
 * Defined HCB const.
 */
define( 'LOOS_HCB_VERSION', ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? date('mdGis') : '1.2.7');
define( 'LOOS_HCB_PATH', plugin_dir_path( __FILE__ ) );
define( 'LOOS_HCB_BASENAME', plugin_basename( __FILE__ ) );
define( 'LOOS_HCB_URL', plugins_url( '/', __FILE__ ) );

/**
 * Autoload Class files.
 */
spl_autoload_register( function( $classname ) {

	if ( false === strpos( $classname, 'LOOS_HCB' ) ) return;
	$file = LOOS_HCB_PATH .'class/'. mb_strtolower( $classname ) .'.php';
	if ( file_exists( $file ) ) require $file;
});

/**
 * Activation hooks.
 */
register_activation_hook( __FILE__, ['LOOS_HCB_Activation', 'plugin_activate'] );
register_uninstall_hook( __FILE__, ['LOOS_HCB_Activation', 'plugin_uninstall'] );

/**
 * Start
 */
add_action( 'plugins_loaded', function() {
	// 翻訳
	$locale = apply_filters( 'plugin_locale', determine_locale(), 'loos-hcb' );
	load_textdomain( 'loos-hcb', LOOS_HCB_PATH . 'languages/loos-hcb-' . $locale . '.mo' );

	// 実行
	new LOOS_HCB();
});
