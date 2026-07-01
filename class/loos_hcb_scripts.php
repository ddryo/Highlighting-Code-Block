<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class LOOS_HCB_Scripts {

	/**
	 * The constructor
	 */
	public function __construct() {
		add_action( 'init', [ 'LOOS_HCB_Scripts', 'register_hcb_block' ] );
		add_action( 'wp_enqueue_scripts', [ 'LOOS_HCB_Scripts', 'hook_wp_enqueue_scripts' ], 20 );
		add_action( 'admin_enqueue_scripts', [ 'LOOS_HCB_Scripts', 'hook_admin_enqueue_scripts' ] );
		add_action( 'enqueue_block_editor_assets', [ 'LOOS_HCB_Scripts', 'hook_enqueue_block_editor_assets' ] );
		add_action( 'admin_head', [ 'LOOS_HCB_Scripts', 'hook_admin_head' ], 1 );
	}


	/**
	 * Register Block
	 */
	public static function register_hcb_block() {

		// ブロックのスクリプト登録 ( memo: wp_add_inline_script() でグローバル変数を紐付けるために wp_register_script() で登録している )
		$asset = include LOOS_HCB_PATH . '/build/js/code-block/index.asset.php';
		wp_register_script(
			'hcb-code-block',
			LOOS_HCB_URL . '/build/js/code-block/index.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		// ブロックの登録
		register_block_type_from_metadata( LOOS_HCB_PATH . '/build/js/code-block' );

		// エディタに渡すグローバル変数
		wp_add_inline_script(
			'hcb-code-block',
			'var hcbLangs = ' . self::get_lang_obj_str() . ';',
			'before'
		);
		wp_localize_script( 'hcb-code-block', 'hcbVars', [
			'showLang'    => LOOS_HCB::$settings['show_lang'],
			'showLinenum' => LOOS_HCB::$settings['show_linenum'],
		] );
	}


	/**
	 * Front Scripts
	 */
	public static function hook_wp_enqueue_scripts() {

		// ファイルに付与するクエリ
		$ver = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? wp_date( 'mdGis' ) : LOOS_HCB_VER;

		// HCB スタイルの読み込み.
		if ( LOOS_HCB::$custom_coloring_path ) {
			// ユーザーに指定されたCSSがあれば、baseスタイル + そのファイルを読み込む
			wp_enqueue_style( 'hcb-style', LOOS_HCB_URL . '/build/css/hcb.css', [], $ver );
			wp_enqueue_style( 'hcb-coloring', LOOS_HCB::$custom_coloring_path, [ 'hcb-style' ], $ver );

		} else {
			// ユーザー指定がなければ、カラーリングを含んだデフォルトCSSを読み込む
			wp_enqueue_style( 'hcb-style', LOOS_HCB::$front_css_path, [], $ver );
		}

		// Inline Style
		wp_add_inline_style( 'hcb-style', LOOS_HCB::get_inline_style() );

		// Prism.js
		if ( LOOS_HCB::$custom_prism_path ) {
			wp_enqueue_script( 'hcb-prism', LOOS_HCB::$custom_prism_path, [], $ver, true );
		} else {
			wp_enqueue_script( 'hcb-prism', LOOS_HCB_URL . '/assets/js/prism.js', [], $ver, true );
		}

		// hcb-scriptの依存関係
		$js_deps = [ 'hcb-prism' ];
		$js_vars = [
			'showCopyBtn'  => '',
			'copyBtnLabel' => __( 'Copy code to clipboard', 'highlighting-code-block' ),
		];

		if ( 'on' === LOOS_HCB::$settings['show_copy'] ) {
			$js_deps[]              = 'clipboard';
			$js_vars['showCopyBtn'] = '1';
		}

		// HCB script
		wp_enqueue_script( 'hcb-script', LOOS_HCB_URL . '/build/js/hcb_script.js', $js_deps, $ver, true );
		wp_localize_script( 'hcb-script', 'hcbVars', $js_vars );
	}


	/**
	 * Admin Scripts
	 */
	public static function hook_admin_enqueue_scripts( $hook_suffix ) {

		// ファイルに付与するクエリ
		$ver = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? wp_date( 'mdGis' ) : LOOS_HCB_VER;

		if ( 'settings_page_hcb_settings' === $hook_suffix ) {
			wp_enqueue_style( 'hcb-admin', LOOS_HCB_URL . '/build/css/hcb-admin.css', [], $ver );
		}
	}


	/**
	 * Block Scripts
	 */
	public static function hook_enqueue_block_editor_assets() {

		// ファイルに付与するクエリ
		$ver = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? wp_date( 'mdGis' ) : LOOS_HCB_VER;

		// Editor Style
		wp_enqueue_style( 'hcb-editor-style', LOOS_HCB::$editor_css_path, [], $ver );
		wp_add_inline_style( 'hcb-editor-style', LOOS_HCB::get_inline_style() );

		// 翻訳登録用の空ファイル
		wp_enqueue_script( 'hcb-blocks', LOOS_HCB_URL . '/assets/js/hcb.js', [], $ver, false );

		// 翻訳jsonファイルの読み込み
		wp_set_script_translations( 'hcb-blocks', 'highlighting-code-block', LOOS_HCB_PATH . '/languages' );
	}


	/**
	 * 言語設定テキストをパースし、JSへ安全に渡せるJSON文字列を返す。
	 *
	 * ユーザーが入力する `class-key:"language-name"` 形式をサーバ側で連想配列へ変換し、
	 * キーは [A-Za-z0-9_-] に限定、ラベルはテキストとしてサニタイズする。
	 * 出力は wp_json_encode( JSON_HEX_* ) を用いて <script> コンテキストで安全な形にし、
	 * 任意のJS文を混入できないようにする（Stored XSS対策）。
	 */
	public static function get_lang_obj_str() {

		$raw = (string) LOOS_HCB::$settings['support_langs'];

		// 全角の記号・スペースを半角へ正規化（ユーザー入力の揺れを吸収）.
		$raw = str_replace(
			[ '　', '＂', '＇', '：', '；', '，' ],
			[ ' ', '"', "'", ':', ';', ',' ],
			$raw
		);

		// `key:"label"` 形式のみを抽出する。キーは英数字・ハイフン・アンダースコアに限定.
		$langs = [];
		if ( preg_match_all( '/([A-Za-z0-9_-]+)\s*:\s*"([^"]*)"/', $raw, $matches, PREG_SET_ORDER ) ) {
			foreach ( $matches as $match ) {
				$key = $match[1];
				if ( '' === $key ) {
					continue;
				}
				$langs[ $key ] = sanitize_text_field( $match[2] );
			}
		}

		// JS(<script>)コンテキストで安全なJSONオブジェクトとして出力する.
		$json = wp_json_encode( (object) $langs, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );

		// エンコード失敗時も壊れたJSを出力しないよう空オブジェクトにフォールバック.
		return false === $json ? '{}' : $json;
	}

	/**
	 * Add code to Admin Head. (for TinyMCE)
	 */
	public static function hook_admin_head() {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_lang_obj_str() returns JSON encoded with wp_json_encode( JSON_HEX_* ).
		echo '<script id="hcb-langs">var hcbLangs = ' . self::get_lang_obj_str() . ';</script>' . PHP_EOL;
	}
}
