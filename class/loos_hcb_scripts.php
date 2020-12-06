<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class LOOS_HCB_Scripts {

	/**
	 * The constructor
	 */
	public function __construct() {
		add_action( 'init', ['LOOS_HCB_Scripts', 'hook_init'] );
		add_action( 'wp_enqueue_scripts', ['LOOS_HCB_Scripts', 'hook_wp_enqueue_scripts'], 20 );
		add_action( 'admin_enqueue_scripts', ['LOOS_HCB_Scripts', 'hook_admin_enqueue_scripts'] );
		add_action( 'enqueue_block_editor_assets', ['LOOS_HCB_Scripts', 'hook_enqueue_block_editor_assets'] );
		add_action( 'admin_head', ['LOOS_HCB_Scripts', 'hook_admin_head'], 1 );
	}

	/**
	 * Register Block
	 */
	public static function hook_init() {

		// ブロックのスクリプト登録
		$asset = include( LOOS_HCB_PATH. 'build/js/code-block/index.asset.php' );
		wp_register_script(
			'hcb-code-block',
			LOOS_HCB_URL .'build/js/code-block/index.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		// ブロックの登録
		$metadata = json_decode( file_get_contents( LOOS_HCB_PATH . 'src/js/code-block/block.json' ), true );
		$metadata = array_merge( $metadata, [ 'editor_script' => 'hcb-code-block' ] );
		register_block_type( 'loos-hcb/code-block', $metadata );
	}


	/**
	 * Front Scripts
	 */
	public static function hook_wp_enqueue_scripts() {

		// HCB style
		wp_enqueue_style( 'hcb-style', LOOS_HCB_URL .'build/css/hcb_style.css', [], LOOS_HCB_VERSION );

		// Coloring style
		wp_enqueue_style( 'hcb-coloring', LOOS_HCB::$coloring_css_url, ['hcb-style'], LOOS_HCB_VERSION );

		// Inline Style
		wp_add_inline_style( 'hcb-style', LOOS_HCB_Scripts::get_inline_style() );

		// clipboard.js
		$is_show_copy = LOOS_HCB::$settings['show_copy'];
		if ( $is_show_copy ) {
			wp_enqueue_script( 'clipboard' );
		}

		// Prism.js
		wp_enqueue_script( 'hcb-prism', LOOS_HCB::$prism_js_url, [], LOOS_HCB_VERSION, true );
		// wp_add_inline_script( 'hcb-prism', 'window.Prism = window.Prism || {}; Prism.manual = true;', 'before' );

		// HCB script
		wp_enqueue_script( 'hcb-script', LOOS_HCB_URL .'build/js/hcb_script.js', ['hcb-prism'], LOOS_HCB_VERSION, true );

		// スクリプトに渡すグローバル変数
		wp_localize_script( 'hcb-script', 'hcbVars', [
			'showCopy' => $is_show_copy,
		] );

	}


	/**
	 * Admin Scripts
	 */
	public static function hook_admin_enqueue_scripts( $hook_suffix ) {
		if ( 'settings_page_hcb_settings' === $hook_suffix ) {
			wp_enqueue_style( 'hcb-admin', LOOS_HCB_URL .'build/css/hcb_admin.css', [], LOOS_HCB_VERSION );
		}
	}


	/**
	 * Block Scripts
	 */
	public static function hook_enqueue_block_editor_assets() {

		// Editor Style
		wp_enqueue_style( 
			'hcb-editor-style',
			LOOS_HCB_URL .'build/css/hcb_editor.css',
			[],
			LOOS_HCB_VERSION
		);

		// Editor Coloring
		wp_enqueue_style( 
			'hcb-gutenberg-style',
			LOOS_HCB::$editor_coloring_css_url,
			['hcb-editor-style'],
			LOOS_HCB_VERSION
		);

		// Inline Style
		// wp_add_inline_style( 'hcb-gutenberg-style', LOOS_HCB_Scripts::get_inline_style( 'block' ) );

		// 翻訳登録用の空ファイル
		wp_enqueue_script(
			'hcb-blocks',
			LOOS_HCB_URL .'assets/js/hcb.js',
			[],
			LOOS_HCB_VERSION,
			false
		);

		// 翻訳ファイルの読み込み
		wp_set_script_translations(
			'hcb-blocks',
			'loos-hcb',
			LOOS_HCB_PATH . 'languages'
		);

		// 管理画面側に渡すグローバル変数
		wp_localize_script( 'hcb-blocks', 'hcbVars', [
			'showLang'    => LOOS_HCB::$settings[ 'show_lang' ],
			'showLinenum' => LOOS_HCB::$settings[ 'show_linenum' ],
		] );
	}


	/**
	 * インラインスタイルの生成
	 */
	public static function get_inline_style() {

		$inline_css = '';
		$hcb = LOOS_HCB::$settings;

		// Font size
		$inline_css .= '.hcb_wrap pre.prism{font-size: '. $hcb['fontsize_pc'] .'}'.
			'@media screen and (max-width: 599px){.hcb_wrap pre.prism{font-size: '. $hcb['fontsize_sp'] .'}}';
		// Code Lang
		if ( 'off' === $hcb[ 'show_lang' ] ) {
			$inline_css .= '.hcb_wrap pre:not([data-file]):not([data-show-lang])::before{ content: none;}';
		}
		// Font smoothing
		if ( 'on' === $hcb[ 'font_smoothing' ] ) {
			$inline_css .= '.hcb_wrap pre{-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;}';
		}
		// Font family
		if ( $hcb[ 'font_family' ] ) {
			$inline_css .= '.hcb_wrap pre{font-family:'. $hcb[ 'font_family' ] .'}';
		}
		
		return $inline_css;
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

		echo '<script id="hcb-langs">var hcbLangs = {'. trim( $langs ) .'};</script>' . PHP_EOL;
	}
}
