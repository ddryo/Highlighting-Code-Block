<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add HCB setting page.
 */
add_action( 'admin_menu', function() {
	$pagename = __( '[HCB] Settings', 'highlighting-code-block' );
	add_options_page(
		$pagename,
		$pagename,
		'manage_options',
		LOOS_HCB::MENU_SLUG,
		[ 'LOOS_HCB_Menu', 'hcb_settings_cb' ]
	);
});

/**
 * 設定項目フィールドの登録
 */
add_action( 'admin_init', function() {
	// データベースに保存されるオプション名を登録
	register_setting(
		LOOS_HCB::MENU_SLUG,
		LOOS_HCB::DB_NAME['settings'],
		[
			'type'              => 'array',
			'sanitize_callback' => [ 'LOOS_HCB_Menu', 'sanitize_settings' ],
		]
	);

	//「基本設定」セクション
	add_settings_section(
		'hcb_setting_section',
		__( 'Basic settings', 'highlighting-code-block' ),
		'',
		LOOS_HCB::MENU_SLUG
	);

	$basic_sections = [
		'show_lang'       => [
			'title' => __( 'Display language name', 'highlighting-code-block' ),
			'args'  => [
				'type'  => 'checkbox',
				'label' => __( 'Display language name in code block', 'highlighting-code-block' ),
				'desc'  => __( 'If checked, the language type is displayed in the code on the site display side.', 'highlighting-code-block' ),
			],
		],
		'show_linenum'    => [
			'title' => __( 'Display settings for the number of rows', 'highlighting-code-block' ),
			'args'  => [
				'type'  => 'checkbox',
				'label' => __( 'Show line count in code block', 'highlighting-code-block' ),
				'desc'  => __( 'If checked, the number of lines will be displayed on the left end of the code on the site display side.', 'highlighting-code-block' ),
			],
		],
		'show_copy'       => [
			'title' => __( 'Copy button', 'highlighting-code-block' ),
			'args'  => [
				'type'  => 'checkbox',
				'label' => __( 'Show copy button in code block', 'highlighting-code-block' ),
				'desc'  => '',
			],
		],
		'font_smoothing'  => [
			'title' => __( 'Font smoothing', 'highlighting-code-block' ),
			'args'  => [
				'type'  => 'checkbox',
				'label' => __( 'Turn on font smoothing', 'highlighting-code-block' ),
				'desc'  => sprintf(
					/* translators: %1$s and %2$s are CSS property declarations wrapped in <code> tags. */
					__( 'Add %1$s and %2$s to the code block.', 'highlighting-code-block' ),
					'<code>-webkit-font-smoothing: antialiased;</code>',
					'<code>-moz-osx-font-smoothing: grayscale;</code>'
				),
			],
		],
		'front_coloring'  => [
			'title' => __( 'Cord coloring (front side)', 'highlighting-code-block' ),
			'args'  => [
				'type'    => 'radio',
				'choices' => [
					'Light' => 'light',
					'Dark'  => 'dark',
				],
			],
		],
		'editor_coloring' => [
			'title' => __( 'Code coloring (editor side)', 'highlighting-code-block' ),
			'args'  => [
				'type'    => 'radio',
				'choices' => [
					'Light' => 'light',
					'Dark'  => 'dark',
				],
			],
		],
		'fontsize_pc'     => [
			'title' => __( 'Font Size', 'highlighting-code-block' ) . '(PC)',
			'args'  => [
				'before' => 'font-size: ',
			],
		],
		'fontsize_sp'     => [
			'title' => __( 'Font Size', 'highlighting-code-block' ) . '(SP)',
			'args'  => [
				'before' => 'font-size: ',
			],
		],
		'font_family'     => [
			'title' => __( '"Font-family" in code', 'highlighting-code-block' ),
			'args'  => [
				'type' => 'textarea',
				'rows' => 2,
				'desc' => 'Default: <code>"Menlo", "Consolas", "Hiragino Kaku Gothic ProN", "Hiragino Sans", "Meiryo", sans-serif</code>',
			],
		],
	];

	foreach ( $basic_sections as $id => $data ) {
		$args       = $data['args'];
		$args['id'] = $id;

		add_settings_field(
			$id,
			$data['title'],
			[ 'LOOS_HCB_Menu', 'settings_field_cb' ],
			LOOS_HCB::MENU_SLUG,
			'hcb_setting_section',
			$args
		);
	}

	/**
	 * 「高度な設定設定」セクション
	 */
	add_settings_section(
		'hcb_setting_advanced',
		__( 'Advanced settings', 'highlighting-code-block' ),
		'',
		LOOS_HCB::MENU_SLUG
	);

	$help_desc = __( 'When you use each original file, please upload it in the theme folder.', 'highlighting-code-block' ) . '<br>' .
		__( 'If you set the path to your own file, the default coloring file and prism.js file will not be loaded..', 'highlighting-code-block' ) .
		'<br>' . sprintf(
			/* translators: %s is a link to the prism.js download page. */
			__( '* The currently loaded prism.js file can be downloaded at %s.', 'highlighting-code-block' ),
			'<a href="https://prismjs.com/download.html#themes=prism&languages=markup+css+clike+javascript+c+csharp+bash+cpp+ruby+markup-templating+git+java+json+objectivec+php+sql+scss+python+typescript+swift&plugins=line-highlight+line-numbers" target="_blank">' . __( 'Here', 'highlighting-code-block' ) . '</a>'
		);

	$advanced_sections = [
		'support_langs'  => [
			'title' => __( 'Language set to use', 'highlighting-code-block' ),
			'args'  => [
				'type'  => 'textarea',
				'rows'  => 16,
				'desc'  => sprintf(
					/* translators: %s is the input format example wrapped in a <code> tag. */
					__( 'Write in the format of %s, separated by "," (comma).', 'highlighting-code-block' ),
					'<code>' . __( 'class-key:"language-name"', 'highlighting-code-block' ) . '</code>'
				) . '<br>&emsp;- ' .
					__( '"class-key" is the class name used in prism.js (the part corresponding to "◯◯" in "lang- ◯◯")', 'highlighting-code-block' ) .
					'<br> ' . __( '* If you use a language that is not supported by default, please use it together with "Original prism.js" setting.', 'highlighting-code-block' ),
				'after' => '<pre class="default_support_langs"><code>' . LOOS_HCB::DEFAULT_LANGS . '</code></pre>',
			],
		],
		'prism_css_path' => [
			'title' => __( 'Original coloring file', 'highlighting-code-block' ),
			'args'  => [
				'before' => get_stylesheet_directory_uri() . '/ ',
				'desc'   => __( 'Load your own CSS file for code coloring.', 'highlighting-code-block' ),
			],
		],
		'prism_js_path'  => [
			'title' => __( 'Original prism.js', 'highlighting-code-block' ),
			'args'  => [
				'before' => get_stylesheet_directory_uri() . '/ ',
				'desc'   => __( 'You can use the prism.js file corresponding to your own language set.', 'highlighting-code-block' ),
			],
		],
		'hcb_help'       => [
			'title' => __( 'help', 'highlighting-code-block' ),
			'args'  => [
				'type' => '',
				'desc' => $help_desc,
			],
		],
	];

	foreach ( $advanced_sections as $id => $data ) {
		$args       = $data['args'];
		$args['id'] = $id;

		add_settings_field(
			$id,
			$data['title'],
			[ 'LOOS_HCB_Menu', 'settings_field_cb' ],
			LOOS_HCB::MENU_SLUG,
			'hcb_setting_advanced',
			$args
		);
	}
} );


class LOOS_HCB_Menu {

	/**
	 * 設定値のサニタイズ（register_setting の sanitize_callback）
	 *
	 * 保存される各値をサーバ側で型・許容値ごとに検証する。
	 * 想定キーのみをホワイトリストとして保存し、未知のキーは破棄する。
	 */
	public static function sanitize_settings( $input ) {

		$input  = is_array( $input ) ? $input : [];
		$output = [];

		// on / off のチェックボックス値.
		foreach ( [ 'show_lang', 'show_linenum', 'show_copy', 'font_smoothing' ] as $key ) {
			$output[ $key ] = ( isset( $input[ $key ] ) && 'on' === $input[ $key ] ) ? 'on' : 'off';
		}

		// light / dark のカラーリング値.
		foreach ( [ 'front_coloring', 'editor_coloring' ] as $key ) {
			$output[ $key ] = ( isset( $input[ $key ] ) && 'dark' === $input[ $key ] ) ? 'dark' : 'light';
		}

		// CSSコンテキストに出力される値（font-size / font-family）.
		foreach ( [ 'fontsize_pc', 'fontsize_sp', 'font_family' ] as $key ) {
			$output[ $key ] = isset( $input[ $key ] ) ? LOOS_HCB::sanitize_css_value( $input[ $key ] ) : '';
		}

		// テーマ内の相対ファイルパス.
		foreach ( [ 'prism_css_path', 'prism_js_path' ] as $key ) {
			$output[ $key ] = isset( $input[ $key ] ) ? LOOS_HCB::sanitize_theme_path( $input[ $key ] ) : '';
		}

		// 言語設定テキスト.
		if ( isset( $input['support_langs'] ) ) {
			$output['support_langs'] = LOOS_HCB::sanitize_langs( $input['support_langs'] );
		}

		return $output;
	}

	/**
	 * hcb_settings_cb
	 */
	public static function hcb_settings_cb() {
		echo '<div class="wrap hcb_setting">';
		echo '<h1>' . esc_html__( 'Highlighting Code Block settings', 'highlighting-code-block' ) . '</h1>';
		echo '<form action="options.php" method="post">';
		settings_fields( LOOS_HCB::MENU_SLUG ); // register_setting() の グループ名に一致させる.
		do_settings_sections( LOOS_HCB::MENU_SLUG );
		submit_button();
		echo '</form></div>';
	}

	/**
	 * 設定項目フィールド表示関数
	 */
	public static function settings_field_cb( $args = [] ) {

		$default = [
			'id'         => '',
			'type'       => 'input',
			'input_type' => 'text',
			'choices'    => [],
			'label'      => '',
			'rows'       => '',
			'before'     => '',
			'after'      => '',
			'desc'       => '',
		];
		$args    = array_merge( $default, $args );

		$type = $args['type'];
		if ( 'input' === $type ) {
			self::field_input( $args );
		} elseif ( 'radio' === $type ) {
			self::field_radio( $args );
		} elseif ( 'checkbox' === $type ) {
			self::field_checkbox( $args );
		} elseif ( 'textarea' === $type ) {
			self::field_textarea( $args );
		}

		if ( $args['desc'] ) {
			echo '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';
		}
	}

	/**
	 * input
	 */
	private static function field_input( $args ) {

		$id    = $args['id'];
		$name  = LOOS_HCB::DB_NAME['settings'] . '[' . $id . ']';
		$value = LOOS_HCB::$settings[ $id ];

		echo esc_html( $args['before'] );
		printf(
			'<input id="%1$s" name="%2$s" type="%3$s" value="%4$s" />',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $args['input_type'] ),
			esc_attr( $value )
		);
		echo wp_kses_post( $args['after'] );
	}

	/**
	 * textarea
	 */
	private static function field_textarea( $args ) {

		$id    = $args['id'];
		$name  = LOOS_HCB::DB_NAME['settings'] . '[' . $id . ']';
		$value = LOOS_HCB::$settings[ $id ];

		echo '<div class="hcb_field_textarea ' . esc_attr( $id ) . '">';
		printf(
			'<textarea id="%1$s" name="%2$s" class="regular-text" rows="%3$s">%4$s</textarea>',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $args['rows'] ),
			esc_textarea( $value )
		);
		echo wp_kses_post( $args['after'] );
		echo '</div>';
	}

	/**
	 * radio
	 */
	private static function field_radio( $args ) {

		$id    = $args['id'];
		$name  = LOOS_HCB::DB_NAME['settings'] . '[' . $id . ']';
		$value = LOOS_HCB::$settings[ $id ];

		echo '<fieldset>';
		foreach ( $args['choices'] as $label => $val ) {
			$radio_id = $id . '_' . $val;
			printf(
				'<label for="%1$s"><input id="%1$s" type="radio" name="%2$s" value="%3$s"%4$s><span>%5$s</span></label><br>',
				esc_attr( $radio_id ),
				esc_attr( $name ),
				esc_attr( $val ),
				checked( $value, $val, false ),
				esc_html( $label )
			);
		}
		echo '</fieldset>';
	}

	/**
	 * checkbox
	 */
	private static function field_checkbox( $args ) {

		$id    = $args['id'];
		$name  = LOOS_HCB::DB_NAME['settings'] . '[' . $id . ']';
		$value = LOOS_HCB::$settings[ $id ];

		printf(
			'<input type="hidden" name="%1$s" value="off">' .
			'<input type="checkbox" id="%2$s" name="%1$s" value="on"%3$s />' .
			'<label for="%2$s">%4$s</label>',
			esc_attr( $name ),
			esc_attr( $id ),
			checked( $value, 'on', false ),
			esc_html( $args['label'] )
		);
	}
}
