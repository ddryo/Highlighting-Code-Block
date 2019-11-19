<?php
/*
Plugin Name: Highlighting Code Block
Plugin URI: https://wemo.tech/
Description: シンタックスハイライト機能を持つコードブロックを簡単に追加できます。新旧エディタ対応。
Version: 1.0.6
Author: LOOS WEB STUDIO
Author URI: https://loos-web-studio.com/
License: GPL2

/*  Copyright 2018 LOOS WEB STUDIO (email : info@loos-web-studio.com)
 
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
     published by the Free Software Foundation.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/**
 * Return 403 err if not 'add_filter'.
 */ 
if ( ! function_exists( 'add_filter' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}


/**
 * Be disabled ~PHP5.6.
 */
$phpver = phpversion();
if ( (double) $phpver < 5.6 ) {
    add_action( 'admin_notices', function() { ?>
        <div class="notice notice-error is-dismissible">
            <p>
                <b>[ SEO SIMPLE PACK ]</b><br>
                This Plugin is available in PHP since version 5.6 ! <br> (Your PHP is ver. <?php echo phpversion(); ?> )
            </p>
        </div> <?php
    } );
    return;
}


/**
 * Defined HCB const.
 */
if ( ! defined( 'LOOS_HCB_VERSION' ) ) {
    // define( 'LOOS_HCB_VERSION', '1.0.6' );
    define( 'LOOS_HCB_VERSION', date('Ymdgis') ); //開発用
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
    define( 'LOOS_HCB_URL', plugins_url( '', __FILE__ ) );
}


/**
 * Autoload Class files.
 */
spl_autoload_register( function( $classname ) {
    $file = LOOS_HCB_PATH . 'class/'. mb_strtolower( $classname ) . '.php';
    if ( file_exists( $file ) ) {
        require $file;
    }
});


/**
 * Activation hooks.
 */
register_activation_hook( LOOS_HCB_FILE,   array('LOOS_HCB_Activation', 'plugin_activate') );
register_deactivation_hook( LOOS_HCB_FILE, array('LOOS_HCB_Activation', 'plugin_deactivate') );
register_uninstall_hook( LOOS_HCB_FILE,    array('LOOS_HCB_Activation', 'plugin_uninstall') );


/**
 * WP_Filesystem
 */
require_once(ABSPATH.'wp-admin/includes/file.php');


/**
 * Init
 */
new LOOS_HCB();
