<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class LOOS_HCB_Mce {

	/**
	 * The constructor
	 */
	public function __construct() {
		add_action( 'mce_css', ['LOOS_HCB_Mce', 'hook_mce_css'] );
		add_action( 'mce_external_plugins', ['LOOS_HCB_Mce', 'hook_mce_external_plugins'], 20 );
		add_action( 'tiny_mce_before_init', ['LOOS_HCB_Mce', 'hook_tiny_mce_before_init'] );
		add_action( 'mce_buttons_2', ['LOOS_HCB_Mce', 'hook_mce_buttons_2'] );
	}

	/**
	 * Mce style
	 */
	public static function hook_mce_css( $mce_css ) {

		if ( ! empty( $mce_css ) ) $mce_css .= ',';

		$mce_css .= LOOS_HCB_URL .'build/css/hcb_editor.css';
		$mce_css .= ',';
		$mce_css .= LOOS_HCB::$editor_coloring_css_url;
		return $mce_css;
	}

	/**
	 * Set script to Add Tinymce Button
	 */
	public static function hook_mce_external_plugins( $plugins ) {
		$plugins[ 'hcb_external_script' ] = LOOS_HCB_URL . 'assets/js/hcb_mce_button.js';
		return $plugins;
	}

	/**
	 * Set Tinymce setting
	 */
	public static function hook_tiny_mce_before_init( $init ) {
		// Don't delete id & empty tags & etc...
		$init[ 'valid_elements' ]          = '*[*]';
		$init[ 'extended_valid_elements' ] = '*[*]';
		$init[ 'verify_html' ]             = false;

		// Text editor indent
		$init[ 'indent' ] = true;

		return $init;
	}

	/**
	 * Add HCB Button
	 */
	public static function hook_mce_buttons_2( $buttons ) {
		$buttons[] = 'hcb_select';
			return $buttons;
	}
}
