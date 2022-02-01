<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class LOOS_HCB_Scripts {

	/**
	 * The constructor
	 */
	public function __construct() {
		add_action( 'init', ['LOOS_HCB_Scripts', 'register_hcb_block'] );
		add_action( 'wp_enqueue_scripts', ['LOOS_HCB_Scripts', 'hook_wp_enqueue_scripts'], 20 );
		add_action( 'admin_enqueue_scripts', ['LOOS_HCB_Scripts', 'hook_admin_enqueue_scripts'] );
		add_action( 'enqueue_block_editor_assets', ['LOOS_HCB_Scripts', 'hook_enqueue_block_editor_assets'] );
		add_action( 'admin_head', ['LOOS_HCB_Scripts', 'hook_admin_head'], 1 );
	}


	/**
	 * Register Block
	 */
	public static function register_hcb_block() {

		// ブロックのスクリプト登録 ( memo: wp_add_inline_script() で紐付けるために wp_register_script 必要 )
		$asset = include( LOOS_HCB_PATH . '/build/js/code-block/index.asset.php' );
		wp_register_script(
			'hcb-code-block',
			LOOS_HCB_URL . '/build/js/code-block/index.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);
		
		// ブロックの登録
		register_block_type_from_metadata( LOOS_HCB_PATH . '/src/js/code-block' );
	}


	/**
	 * Front Scripts
	 */
	public static function hook_wp_enqueue_scripts() {

		// ファイルに付与するクエリ
		$ver = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? wp_date( 'mdGis' ) : LOOS_HCB_VER;

		// HCB style
		wp_enqueue_style( 'hcb-style', LOOS_HCB_URL . '/build/css/hcb_style.css', [], $ver );

		// Coloring style
		wp_enqueue_style( 'hcb-coloring', LOOS_HCB::$coloring_css_url, [ 'hcb-style' ], $ver );

		// Inline Style
		wp_add_inline_style( 'hcb-style', LOOS_HCB::get_inline_style() );

		// clipboard.js
		$is_show_copy = LOOS_HCB::$settings['show_copy'];
		if ( 'on' === $is_show_copy ) {
			wp_enqueue_script( 'clipboard' );
		}

		// Prism.js
		wp_enqueue_script( 'hcb-prism', LOOS_HCB::$prism_js_url, [], $ver, true );

		// HCB script
		wp_enqueue_script( 'hcb-script', LOOS_HCB_URL . '/build/js/hcb_script.js', [ 'hcb-prism' ], $ver, true );

	}


	/**
	 * Admin Scripts
	 */
	public static function hook_admin_enqueue_scripts( $hook_suffix ) {

		// ファイルに付与するクエリ
		$ver = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? wp_date( 'mdGis' ) : LOOS_HCB_VER;

		if ( 'settings_page_hcb_settings' === $hook_suffix ) {
			wp_enqueue_style( 'hcb-admin', LOOS_HCB_URL . '/build/css/hcb_admin.css', [], $ver );
		}
	}


	/**
	 * Block Scripts
	 */
	public static function hook_enqueue_block_editor_assets() {

		// ファイルに付与するクエリ
		$ver = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? wp_date( 'mdGis' ) : LOOS_HCB_VER;

		// Editor Coloring
		wp_enqueue_style( 'hcb-editor-coloring', LOOS_HCB::$editor_coloring_css_url, [], $ver );

		// Editor Style
		wp_enqueue_style( 'hcb-editor-style', LOOS_HCB_URL . '/build/css/hcb_editor.css', [ 'hcb-editor-coloring' ], $ver );
		wp_add_inline_style( 'hcb-editor-style', LOOS_HCB::get_inline_style() );

		// 翻訳登録用の空ファイル
		wp_enqueue_script( 'hcb-blocks', LOOS_HCB_URL . '/assets/js/hcb.js', [], $ver, false );

		// 翻訳ファイルの読み込み
		wp_set_script_translations( 'hcb-blocks', 'loos-hcb', LOOS_HCB_PATH . '/languages' );

		// 管理画面側に渡すグローバル変数
		wp_localize_script( 'hcb-blocks', 'hcbVars', [
			'showLang'    => LOOS_HCB::$settings[ 'show_lang' ],
			'showLinenum' => LOOS_HCB::$settings[ 'show_linenum' ],
		] );
	}


	/**
	 * Add code to Admin Head.
	 * TinyMCEでも必要なので admin_head にフックさせている。
	 */
	public static function hook_admin_head() {

		$langs = LOOS_HCB::$settings[ 'support_langs' ];
		$langs = mb_convert_kana( $langs, 'as'); //全角の文字やスペースがあれば半角に直す
		$langs = str_replace(["\r\n", "\r", "\n"], '', $langs);
		$langs = trim( $langs, ',' );

		$code = 'var hcbLangs = {'. trim( $langs ) .'};';

		// for tinyMCE
		echo '<script id="hcb-langs">' . $code . '</script>' . PHP_EOL;

		// for Gutenberg
		wp_add_inline_script( 'hcb-code-block', $code, 'before' );
	}
}
