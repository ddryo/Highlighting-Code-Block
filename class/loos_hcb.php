<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class LOOS_HCB {

	/**
	 * DB Names
	 */
	const DB_NAME = [
		'installed' => 'loos_hcb_installed',
		'settings'  => 'loos_hcb_settings',
	];

	/**
	 * 設定ページのスラッグ
	 */
	const MENU_SLUG = 'hcb_settings';

	/**
	 * Default Settings (インストール時にDBへ保存)
	 */
	const DEFAULT_SETTINGS = [
		'show_lang'       => 'on',
		'show_linenum'    => 'on',
		'show_copy'       => 'on',
		'font_smoothing'  => 'off',
		'front_coloring'  => 'light',
		'editor_coloring' => 'light',
		'fontsize_pc'     => '14px',
		'fontsize_sp'     => '13px',
		'font_family'     => 'Menlo, Consolas, "Hiragino Kaku Gothic ProN", "Hiragino Sans", Meiryo, sans-serif;',
		'prism_css_path'  => '',
		'prism_js_path'   => '',
	];

	/**
	 * Default Languages
	 */
	const DEFAULT_LANGS = '' .
		'html:"HTML",' . "\n" .
		'css:"CSS",' . "\n" .
		'scss:"SCSS",' . "\n" .
		'js:"JavaScript",' . "\n" .
		'ts:"TypeScript",' . "\n" .
		'php:"PHP",' . "\n" .
		'ruby:"Ruby",' . "\n" .
		'python:"Python",' . "\n" .
		'swift:"Swift",' . "\n" .
		'c:"C",' . "\n" .
		'csharp:"C#",' . "\n" .
		'cpp:"C++",' . "\n" .
		'objectivec:"Objective-C",' . "\n" .
		'sql:"SQL",' . "\n" .
		'json:"JSON",' . "\n" .
		'bash:"Bash",' . "\n" .
		'git:"Git",';

	/**
	 * variables
	 */
	public static $settings             = '';
	public static $custom_prism_path    = '';
	public static $custom_coloring_path = '';
	public static $front_css_path       = '';
	public static $editor_css_path      = '';

	/**
	 * The constructor
	 */
	public function __construct() {
		$this->init();
		$this->set_path();
		new LOOS_HCB_Scripts();
		new LOOS_HCB_Mce();
		new LOOS_HCB_Menu();

		// Set linenum
		add_filter( 'the_content', function( $content ) {
			$content = str_replace( 'prism on-numbers', 'prism line-numbers', $content );
			// $content = preg_replace( '/class="prism([^"]*)on-numbers"/', 'class="prism$1line-numbers"', $content );

			//個別設定が未定義のブロックはベース設定に依存
			if ( 'on' === LOOS_HCB::$settings['show_linenum'] ) {
				$content = str_replace( 'prism undefined-numbers', 'prism line-numbers', $content );
				// $content = preg_replace( '/class="prism([^"]*)undefined-numbers"/', 'class="prism$1line-numbers"', $content );
			}
			return $content;

		}, 99);
	}

	/**
	 * Set HCB Settings
	 */
	private function init() {

		// Get Option for HCB Setiings
		$option = get_option( self::DB_NAME['settings'] ) ?: [];

		// v1.2.2での変更
		if ( is_admin() && isset( $option['support_langs'] ) ) {

			$support_langs = str_replace( ["\r\n", "\r", "\n" ], '', $option['support_langs'] );
			$default_langs = str_replace( ["\n" ], '', self::DEFAULT_LANGS );

			if ( $default_langs === $support_langs ) {
				unset( $option['support_langs'] );

				// DB更新
				update_option( self::DB_NAME['settings'], $option );
			}
		}

		// Get default settings
		$default                  = self::DEFAULT_SETTINGS;
		$default['support_langs'] = self::DEFAULT_LANGS;

		// Merge to default
		self::$settings = array_merge( $default, $option );

	}


	/**
	 * Set file path
	 */
	private function set_path() {

		// Set Prism.js file url
		if ( self::$settings['prism_js_path'] ) {
			self::$custom_prism_path = get_stylesheet_directory_uri() . '/' . self::$settings['prism_js_path'];
		}

		// Set front coloring file url
		if ( self::$settings['prism_css_path'] ) {
			self::$custom_coloring_path = get_stylesheet_directory_uri() . '/' . self::$settings['prism_css_path'];
		}

		self::$front_css_path  = LOOS_HCB_URL . '/build/css/hcb--' . self::$settings['front_coloring'] . '.css';
		self::$editor_css_path = LOOS_HCB_URL . '/build/css/hcb-editor--' . self::$settings['editor_coloring'] . '.css';
	}


	/**
	 * インラインスタイルの生成
	 */
	public static function get_inline_style() {

		$inline_css = '';
		$HCB        = self::$settings;

		// Font size
		$inline_css .= ':root{--hcb--fz--base: ' . $HCB['fontsize_pc'] . '}';
		$inline_css .= ':root{--hcb--fz--mobile: ' . $HCB['fontsize_sp'] . '}';

		// Font family
		if ( $HCB['font_family'] ) {
			$inline_css .= ':root{--hcb--ff:' . $HCB['font_family'] . '}';
		}

		// Code Lang (Default)
		if ( 'off' === $HCB['show_lang'] ) {
			$inline_css .= '.hcb_wrap{--hcb--data-label: none;--hcb--btn-offset: 0px;}';
		}

		// Font smoothing
		if ( 'on' === $HCB['font_smoothing'] ) {
			$inline_css .= '.hcb_wrap pre{-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;}';
		}

		return $inline_css;
	}
}
