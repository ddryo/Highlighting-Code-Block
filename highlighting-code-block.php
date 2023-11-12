<?php
/**
 * Plugin Name: Highlighting Code Block
 * Plugin URI: https://wordpress.org/plugins/highlighting-code-block/
 * Description: Add code block with syntax highlighting using prism.js. (Available for Gutenberg and Classic Editor)
 * Version: 2.0.1
 * Requires at least: 5.6
 * Author: LOOS, Inc.
 * Author URI: https://loos-web-studio.com/
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: loos-hcb
 * Domain Path: /languages
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Defined HCB const.
 */
define( 'LOOS_HCB_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'LOOS_HCB_URL', plugins_url( '', __FILE__ ) );

// プラグインのバージョン
$file_data = get_file_data( __FILE__, [ 'version' => 'Version' ] );
if ( ! defined( 'LOOS_HCB_VER' ) ) {
	define( 'LOOS_HCB_VER', $file_data['version'] );
}

/**
 * Autoload Class files.
 */
spl_autoload_register( function( $classname ) {
	if ( false === strpos( $classname, 'LOOS_HCB' ) ) return;
	$file = LOOS_HCB_PATH . '/class/' . mb_strtolower( $classname ) . '.php';
	if ( file_exists( $file ) ) require $file;
});

/**
 * Activation hooks.
 */
register_activation_hook( __FILE__, ['LOOS_HCB_Activation', 'plugin_activate' ] );
register_uninstall_hook( __FILE__, ['LOOS_HCB_Activation', 'plugin_uninstall' ] );

/**
 * Start
 */
add_action( 'plugins_loaded', function() {
	// 翻訳ファイルの読み込み
	load_plugin_textdomain( 'loos-hcb', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	// 実行
	new LOOS_HCB();
});
