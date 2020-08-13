<?php
/**
 * Plugin Name: Highlighting Code Block
 * Plugin URI: https://wordpress.org/plugins/highlighting-code-block/
 * Description: Add code block with syntax highlighting using prism.js. (Available for Gutenberg and Classic Editor)
 * Version: 1.2.3
 * Author: LOOS, Inc.
 * Author URI: https://loos-web-studio.com/
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: loos-hcb
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Be disabled ~PHP5.6.
 */
$phpver = phpversion();
if ( (double) $phpver < 5.6 ) {
	add_action( 'admin_notices', function() { ?>
		<div class="notice notice-error is-dismissible">
			<p>
				<b>[ Highlighting Code Block ]</b><br>
				This Plugin is available in PHP since version 5.6 ! <br> (Your PHP is ver. <?php echo phpversion(); ?> )
			</p>
		</div> <?php
	} );
	return;
}

/**
 * 翻訳用のテキストドメインを定義
 */
if ( ! defined( 'LOOS_HCB_DOMAIN' ) ) {
	define( 'LOOS_HCB_DOMAIN', 'loos-hcb' );
}

/**
 * Defined HCB const.
 */
if ( ! defined( 'LOOS_HCB_VERSION' ) ) {
	define( 'LOOS_HCB_VERSION', ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? date('mdGis') : '1.2.3');
}
if ( ! defined( 'LOOS_HCB_FILE' ) ) {
	define( 'LOOS_HCB_FILE', __FILE__ );
}
if ( ! defined( 'LOOS_HCB_PATH' ) ) {
	define( 'LOOS_HCB_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'LOOS_HCB_BASENAME' ) ) {
	define( 'LOOS_HCB_BASENAME', plugin_basename( LOOS_HCB_FILE ) );
}
if ( ! defined( 'LOOS_HCB_URL' ) ) {
	define( 'LOOS_HCB_URL', plugins_url( '/', __FILE__ ) );
}

/**
 * Autoload Class files.
 */
spl_autoload_register( function( $classname ) {

	// LOOS_HCB の付いたクラスだけを対象にする。
	if ( strpos( $classname, 'LOOS_HCB' ) === false ) return;

	$file = LOOS_HCB_PATH .'class/'. mb_strtolower( $classname ) .'.php';
	if ( file_exists( $file ) ) {
		require $file;
	}
});


/**
 * Activation hooks.
 */
register_activation_hook( LOOS_HCB_FILE, ['LOOS_HCB_Activation', 'plugin_activate'] );
register_deactivation_hook( LOOS_HCB_FILE, ['LOOS_HCB_Activation', 'plugin_deactivate'] );
register_uninstall_hook( LOOS_HCB_FILE, ['LOOS_HCB_Activation', 'plugin_uninstall'] );


/**
 * Start
 */
add_action( 'plugins_loaded', function() {

	// 翻訳ファイルを登録
	$locale = apply_filters( 'plugin_locale', determine_locale(), LOOS_HCB_DOMAIN );
	load_textdomain( LOOS_HCB_DOMAIN, LOOS_HCB_PATH . 'languages/loos-hcb-' . $locale . '.mo' );

	// プラグイン実行
	new LOOS_HCB();
});
