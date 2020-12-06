<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add HCB setting page.
 */
add_action( 'admin_menu', function() {
	$pagename = __( '[HCB] Settings', 'loos-hcb' );
	add_options_page(
		$pagename,
		$pagename,
		'manage_options',
		LOOS_HCB::MENU_SLUG,
		['LOOS_HCB_Menu', 'hcb_settings_cb']
	);
});

/**
 * 設定項目フィールドの登録
 */
add_action( 'admin_init', function() {
	// データベースに保存されるオプション名を登録
	register_setting( LOOS_HCB::MENU_SLUG, LOOS_HCB::DB_NAME['settings'] );

	//「基本設定」セクション
	add_settings_section(
		'hcb_setting_section',
		__( 'Basic settings', 'loos-hcb' ),
		'',
		LOOS_HCB::MENU_SLUG
	);

	$basic_sections = [
		'show_lang' => [
			'title' => __( 'Display language name', 'loos-hcb' ),
			'args' => [
				'type' => 'checkbox',
				'label' => __( 'Display language name in code block', 'loos-hcb' ),
				'desc' => __( 'If checked, the language type is displayed in the code on the site display side.', 'loos-hcb' )
			]
		],
		'show_linenum' => [
			'title' => __( 'Display settings for the number of rows', 'loos-hcb' ),
			'args' => [
				'type' => 'checkbox',
				'label' => __( 'Show line count in code block', 'loos-hcb' ),
				'desc' => __( 'If checked, the number of lines will be displayed on the left end of the code on the site display side.', 'loos-hcb' ),
			]
		],
		'show_copy' => [
			'title' => __( 'Copy button', 'loos-hcb' ),
			'args' => [
				'type' => 'checkbox',
				'label' => __( 'Show copy button in code block', 'loos-hcb' ),
				'desc' => '', 
			]
		],
		'font_smoothing' => [
			'title' => __( 'Font smoothing', 'loos-hcb' ),
			'args' => [
				'type' => 'checkbox',
				'label' => __( 'Turn on font smoothing', 'loos-hcb' ),
				'desc' => sprintf(
					__( 'Add %s and %s to the code block.', 'loos-hcb' ),
					'<code>-webkit-font-smoothing: antialiased;</code>',
					'<code>-moz-osx-font-smoothing: grayscale;</code>'
				),
			]
		],
		'front_coloring' => [
			'title' => __( 'Cord coloring (front side)', 'loos-hcb' ),
			'args' => [
				'type' => 'radio',
				'choices' => [
					'Light' => 'light',
					'Dark' => 'dark',
				]
			]
		],
		'editor_coloring' => [
			'title' => __( 'Code coloring (editor side)', 'loos-hcb' ),
			'args' => [
				'type' => 'radio',
				'choices' => [
					'Light' => 'light',
					'Dark' => 'dark',
				]
			]
		],
		'fontsize_pc' => [
			'title' => __( 'Font Size', 'loos-hcb' ) . '(PC)',
			'args' => [
				'before' => 'font-size: ',
			]
		],
		'fontsize_sp' => [
			'title' => __( 'Font Size', 'loos-hcb' ) . '(SP)',
			'args' => [
				'before' => 'font-size: ',
			]
		],
		'font_family' => [
			'title' => __( '"Font-family" in code', 'loos-hcb' ),
			'args' => [
				'type' => 'textarea',
				'rows' => 2,
			]
		],
	];

	foreach ( $basic_sections as $id => $data ) {
		$args = $data['args'];
		$args['id'] = $id;

		add_settings_field(
			$id,
			$data['title'],
			['LOOS_HCB_Menu', 'settings_field_cb'],
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
		__( 'Advanced settings', 'loos-hcb' ),
		'',
		LOOS_HCB::MENU_SLUG
	);

	$help_desc = __('When you use each original file, please upload it in the theme folder.', 'loos-hcb' ) . '<br>' .
		__('If you set the path to your own file, the default coloring file and prism.js file will not be loaded..','loos-hcb' ) . 
		'<br>'. sprintf(
			__('* The currently loaded prism.js file can be downloaded at %s.', 'loos-hcb' ),
			'<a href="https://prismjs.com/download.html#themes=prism&languages=markup+css+clike+javascript+c+csharp+bash+cpp+ruby+markup-templating+git+java+json+objectivec+php+sql+scss+python+typescript+swift&plugins=line-highlight+line-numbers" target="_blank">'. __( 'Here', 'loos-hcb' ) .'</a>'
		);

	$advanced_sections = [
		'support_langs' => [
			'title' => __( 'Language set to use', 'loos-hcb' ),
			'args' => [
				'type' => 'textarea',
				'rows' => 16,
				'desc' => sprintf(
						__( 'Write in the format of %s, separated by "," (comma).', 'loos-hcb' ),
						'<code>'. __('class-key:"language-name"', 'loos-hcb' ) .'</code>'
					). '<br>&emsp;- '.
					__('"class-key" is the class name used in prism.js (the part corresponding to "◯◯" in "lang- ◯◯")', 'loos-hcb' ). 
					'<br> '.__('* If you use a language that is not supported by default, please use it together with "Original prism.js" setting.', 'loos-hcb' ),
				'after' => '<pre class="default_support_langs"><code>'. LOOS_HCB::DEFAULT_LANGS . '</code></pre>',
			]
		],
		'prism_css_path' => [
			'title' => __('Original coloring file', 'loos-hcb' ),
			'args' => [
				'before' => get_stylesheet_directory_uri() . '/ ',
				'desc' => __('Load your own CSS file for code coloring.', 'loos-hcb' ),
			]
		],
		'prism_js_path' => [
			'title' => __('Original prism.js', 'loos-hcb' ),
			'args' => [
				'before' => get_stylesheet_directory_uri() . '/ ',
				'desc' => __('You can use the prism.js file corresponding to your own language set.', 'loos-hcb' ),
			]
		],
		'hcb_help' => [
			'title' => __('help', 'loos-hcb' ),
			'args' => [
				'type' => '',
				'desc' => $help_desc
			]
		],
	];

	foreach ( $advanced_sections as $id => $data ) {
		$args = $data['args'];
		$args['id'] = $id;

		add_settings_field(
			$id,
			$data['title'],
			['LOOS_HCB_Menu', 'settings_field_cb'],
			LOOS_HCB::MENU_SLUG,
			'hcb_setting_advanced',
			$args
		);
	}
} );


class LOOS_HCB_Menu {

	/**
	 * hcb_settings_cb
	 */
	public static function hcb_settings_cb() {
		echo '<div class="wrap hcb_setting">'.
		'<h1>'. __( 'Highlighting Code Block settings', 'loos-hcb' ) .'</h1>'.
		'<form action="options.php" method="post">';
		do_settings_sections( LOOS_HCB::MENU_SLUG );
		settings_fields( LOOS_HCB::MENU_SLUG ); // register_setting() の グループ名に一致させる
		submit_button();
		echo '</form></div>';
	}

	/**
	 * 設定項目フィールド表示関数
	 */
	public static function settings_field_cb( $args = [] ) {

		$default = [
			'id'    => '',
			'type'   => 'input',
			'input_type'   => 'text',
			'choices' => [],
			'label' => '',
			'rows' => '',
			'before' => '',
			'after' => '',
			'desc' => '',
		];
		$args = array_merge( $default, $args );

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

		if ( $args['desc'] ) echo '<p class="description">'. $args['desc'] .'</p>';
	}

	/**
	 * input
	 */
	private static function field_input( $args ) {

		$id = $args['id'];
		$name = LOOS_HCB::DB_NAME['settings'] . '['. $id . ']';
		$value = LOOS_HCB::$settings[$id];

		echo $args['before']. '<input id="' . $id . '" name="' . $name . '" type="'. $args['input_type']. '" value="'. $value .'" />'. $args['after'];

	}

	/**
	 * textarea
	 */
	private static function field_textarea( $args ) {

		$id = $args['id'];
		$name = LOOS_HCB::DB_NAME['settings'] . '['. $id . ']';
		$value = LOOS_HCB::$settings[$id];

		echo '<div class="hcb_field_textarea '. $id .'">' .
			'<textarea id="'. $id .'" name="'. $name . '" type="text" class="regular-text" rows="'. $args['rows'] . '" >'.
			$value .'</textarea>'. $args['after'].
		'</div>';
	}

	/**
	 * radio
	 */
	private static function field_radio( $args ) {

		$id = $args['id'];
		$name = LOOS_HCB::DB_NAME['settings'] . '['. $id . ']';
		$value = LOOS_HCB::$settings[$id];

		$fields = '';
		foreach ( $args['choices'] as $key => $val ) {
			$radio_id = $id .'_'. $val;
			$checked = checked( $value, $val, false );
			$props = 'name="'. $name .'" value="'. $val .'" '. $checked;

			$fields .= '<label for="'. $radio_id. '">'.
				'<input id="'. $radio_id .'" type="radio" '. $props . ' >'.
				'<span>'. $key. '</span>'.
			'</label><br>';
		}
		echo '<fieldset>'. $fields .'</fieldset>';
	}

	/**
	 * checkbox
	 */
	private static function field_checkbox( $args ) {

		$id = $args['id'];
		$name = LOOS_HCB::DB_NAME['settings'] . '['. $id . ']';
		$value = LOOS_HCB::$settings[$id];

		$checked = checked( $value, 'on', false );
		echo '<input type="hidden" name="'. $name.  '" value="off">'.
		'<input type="checkbox" id="'. $id. '" name="'. $name . '" value="on" '. $checked. ' />'.
		'<label for="'. $id. '">'. $args['label'] . '</label>';
	}
}

