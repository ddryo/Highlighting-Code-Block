<?php

class LOOS_HCB_Menu {

    /**
     * Set const
     */
    const PAGE_NAME     = 'hcb_settings';
    const SECTION_NAME  = 'hcb_setting_section';
    const SECTION_NAME2 = 'hcb_setting_section2';


    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_hcb_page' ) );
        add_action( 'admin_init', array( $this, 'add_hcb_fields' ) );
    }


    /**
     * Add HCB setting page.
     */
    public function add_hcb_page() {
        add_options_page(
            'CODE BLOCK',  // ページのタイトル
            'CODE BLOCK',  // メニューのタイトル
            'manage_options', // このページを操作する権限
            self::PAGE_NAME,   // ページ名
            array($this, 'hcb_settings_cb') // コールバック関数。この関数の実行結果が出力される
        );
    }


    /**
     * 設定ページ表示関数
     */
    public function hcb_settings_cb() {
        echo '<div class="wrap hcb_setting">'.
            '<h1>'. __( 'Highlighting Code Block settings', LOOS_HCB_DOMAIN ) .'</h1>'.
            '<form action="options.php" method="post">';
            do_settings_sections( self::PAGE_NAME ); // このページに登録されたセクションの表示
            settings_fields( self::PAGE_NAME );      // register_setting() で使用されるグループ名に一致する必要がある。
            submit_button();
        echo '</form></div>';
    }


    /**
     * 設定項目フィールドの登録
     */
    public function add_hcb_fields() {

        /**
         * データベースに保存されるオプション名を登録
         * 同じオプションに配列で値を保存するので、register_setting()は１つだけ
         *   @param : グループ名・データベースのオプション名・(サニタイズ関数)
         */
        register_setting( self::PAGE_NAME, LOOS_HCB::DB_NAME['settings'] );

        /**
         * 「基本設定」セクション
         */
        add_settings_section(
            self::SECTION_NAME,  //ID名
            __( 'Basic settings', LOOS_HCB_DOMAIN ), //セクションのタイトル
            '', //セクションを出力する関数名
            self::PAGE_NAME //セクションを表示する設定ページ
        );
        // 言語名を表示するかどうか
        add_settings_field(
            'show_lang',                         //ID名
            __( 'Display language name', LOOS_HCB_DOMAIN ), //フィールドのタイトル
            array( $this, 'settings_field_cb'),  //フィールドを出力する関数。引数として配列を一つ（$args）渡す
            self::PAGE_NAME,                     //フィールドを表示する設定ページ
            self::SECTION_NAME,                  //フィールドを表示する設定ページのセクション
            array(
                'id' => 'show_lang',
                'type' => 'checkbox',
                'label' => __( 'Display language name in code block', LOOS_HCB_DOMAIN ),
                'desc' => __( 'If checked, the language type is displayed in the code on the site display side.', LOOS_HCB_DOMAIN )
            )
        );

        // 行数を表示するかどうか
        add_settings_field(
            'show_linenum',
             __( 'Display settings for the number of rows', LOOS_HCB_DOMAIN ),
            array( $this, 'settings_field_cb'),
            self::PAGE_NAME,
            self::SECTION_NAME,
            array(
                'id' => 'show_linenum',
                'type' => 'checkbox',
                'label' => __( 'Show line count in code block', LOOS_HCB_DOMAIN ),
                'desc' => __( 'If checked, the number of lines will be displayed on the left end of the code on the site display side.', LOOS_HCB_DOMAIN ), 
            )
        );
        // font-smoothing設定
        add_settings_field(
            'font_smoothing',
            __( 'Font smoothing', LOOS_HCB_DOMAIN ),
            array( $this, 'settings_field_cb'),
            self::PAGE_NAME,
            self::SECTION_NAME,
            array(
                'id' => 'font_smoothing',
                'type' => 'checkbox',
                'label' => __( 'Turn on font smoothing', LOOS_HCB_DOMAIN ),
                'desc' => sprintf(
                    __( 'Add %s and %s to the code block.', LOOS_HCB_DOMAIN ),
                    '<code>-webkit-font-smoothing: antialiased;</code>',
                    '<code>-moz-osx-font-smoothing: grayscale;</code>'
                ),
            )
        );
        // コードカラーリング（フロント）
        add_settings_field(
            'front_coloring',
             __( 'Cord coloring (front side)', LOOS_HCB_DOMAIN ),
            array($this, 'settings_field_cb'),
            self::PAGE_NAME,   
            self::SECTION_NAME,
            array(
                'id' => 'front_coloring',
                'type' => 'radio',
                'choices' => array(
                    'Light' => 'light',
                    'Dark' => 'dark',
                )
            )
        );
        // コードカラーリング（エディタ）
        add_settings_field(
            'editor_coloring',
            __( 'Code coloring (editor side)', LOOS_HCB_DOMAIN ),
            array($this, 'settings_field_cb'),
            self::PAGE_NAME,   
            self::SECTION_NAME,
            array(
                'id' => 'editor_coloring',
                'type' => 'radio',
                'choices' => array(
                    'Light' => 'light',
                    'Dark' => 'dark',
                )
            )
        );
        // フォントサイズ（PC）
        add_settings_field(
            'fontsize_pc',
            __( 'Font Size', LOOS_HCB_DOMAIN ). '(PC)',
            array($this, 'settings_field_cb'),
            self::PAGE_NAME,   
            self::SECTION_NAME,
            array(
                'id' => 'fontsize_pc',
                'type' => 'input',
                'input_type' => 'text',
                'before' => 'font-size: ',
                'after' => '',
                'desc' => ''
            )
        );
        // フォントサイズ（SP）
        add_settings_field(
            'fontsize_sp',
            __( 'Font Size', LOOS_HCB_DOMAIN ). '(SP)',
            array($this, 'settings_field_cb'),
            self::PAGE_NAME,
            self::SECTION_NAME,
            array(
                'id' => 'fontsize_sp',
                'type' => 'input',
                'input_type' => 'text',
                'before' => 'font-size: ',
                'after' => '',
                'desc' => ''
            )
        );

        // フォントファミリー
        add_settings_field(
            'font_family',
            __( '"Font-family" in code', LOOS_HCB_DOMAIN ),
            array($this, 'settings_field_cb'),
            self::PAGE_NAME,
            self::SECTION_NAME,
            array(
                'id' => 'font_family',
                'type' => 'textarea',
                'rows' => 2,
                'before' => '',
                'after' => '',
                'desc' => __( 'Default' ). ' : <code>Menlo, Consolas, "メイリオ", sans-serif;</code>'
            )
        );
        
        // ブロックエディタのコンテンツ幅
        add_settings_field(
            'block_width',
            __( 'Block editor maximum width', LOOS_HCB_DOMAIN ),
            array($this, 'settings_field_cb'),
            self::PAGE_NAME,
            self::SECTION_NAME,
            array(
                'id' => 'block_width',
                'type' => 'input',
                'input_type' => 'number',
                'before' => '',
                'after' => ' px',
                // 'desc' => ''
            )
        );


        /**
         * 「高度な設定設定」セクション
         */
        add_settings_section(
            self::SECTION_NAME2,
            __( 'Advanced settings', LOOS_HCB_DOMAIN ),
            '',
            self::PAGE_NAME
        );


        

        
        // 使用する言語設定
        add_settings_field(
            'support_langs',
            __( 'Language set to use', LOOS_HCB_DOMAIN ),
            array($this, 'settings_field_cb'),
            self::PAGE_NAME,
            self::SECTION_NAME2,
            array(
                'id' => 'support_langs',
                'type' => 'textarea',
                'rows' => 16,
                'desc' => sprintf(
                    __( 'Write in the format of %s, separated by "," (comma).', LOOS_HCB_DOMAIN ),
                    '<code>'. __('class-key:"language-name"', LOOS_HCB_DOMAIN ) .'</code>'
                ).
                '<br>&emsp;- '.__('"class-key" is the class name used in prism.js (the part corresponding to "◯◯" in "lang- ◯◯")', LOOS_HCB_DOMAIN ). 
                '<br> '.__('* If you use a language that is not supported by default, please use it together with "Original prism.js" setting.', LOOS_HCB_DOMAIN ).
                '',

                'after' => '<pre class="default_support_langs"><code>html:"HTML",
css:"CSS",
scss:"SCSS",
js:"JavaScript",
ts:"TypeScript",
php:"PHP",
ruby:"Ruby",
python:"Python",
swift:"Swift",
c:"C",
csharp:"C#",
cpp:"C++",
objectivec:"Objective-C",
sql:"SQL",
json:"JSON",
bash:"Bash",
git:"Git",</code></pre>',

            )
        );


        // 独自カラーリングファイル
        add_settings_field(
            'prism_css_path',
            __('Original coloring file', LOOS_HCB_DOMAIN ), 
            array($this, 'settings_field_cb'),
            self::PAGE_NAME,
            self::SECTION_NAME2,
            array(
                'id' => 'prism_css_path',
                'type' => 'input',
                'input_type' => 'text',
                'before' => get_stylesheet_directory_uri()."/ ",
                'after' => '',
                'desc' => __('Load your own CSS file for code coloring.', LOOS_HCB_DOMAIN ),
            )
        );
        // 独自prism
        add_settings_field(
            'prism_js_path',
            __('Original prism.js', LOOS_HCB_DOMAIN ),
            array($this, 'settings_field_cb'),
            self::PAGE_NAME,
            self::SECTION_NAME2,
            array(
                'id' => 'prism_js_path',
                'type' => 'input',
                'input_type' => 'text',
                'before' => get_stylesheet_directory_uri()."/ ",
                'after' => '',
                'desc' => __('You can use the prism.js file corresponding to your own language set.', LOOS_HCB_DOMAIN ),
            )
        );
        // ヘルプ
        add_settings_field(
            'hcb_help',
            __('help', LOOS_HCB_DOMAIN ), //'ヘルプ',
            array($this, 'settings_field_cb'),
            self::PAGE_NAME,
            self::SECTION_NAME2,
            array(
                'type' => '',
                'desc' => __('When you use each original file, please upload it in the theme folder.', LOOS_HCB_DOMAIN ).
                '<br>'.
                __('If you set the path to your own file, the default coloring file and prism.js file will not be loaded..',LOOS_HCB_DOMAIN ).
                '<br><br>'.
                sprintf(
                    __('* The currently loaded prism.js file can be downloaded at %s.', LOOS_HCB_DOMAIN ),
                    '<a href="https://prismjs.com/download.html#themes=prism&languages=markup+css+clike+javascript+c+csharp+bash+cpp+ruby+markup-templating+git+java+json+objectivec+php+sql+scss+python+typescript+swift&plugins=line-highlight+line-numbers" target="_blank">'.__( 'Here', LOOS_HCB_DOMAIN ) .'</a>'
                )
            )
        );
    }


    /**
     * 設定項目フィールド表示関数
     */
    public function settings_field_cb( $args ) {

        if ( $args['type'] === 'input' ) {
            //<input:"text"|"number"|"email"|...]>
            self::set_field_input( $args['id'], $args['input_type'], $args['before'], $args['after'] );

        } elseif( $args['type'] === 'radio' ) {

            //<input:"radio">
            self::set_field_radio( $args['id'], $args['choices']);

        } elseif( $args['type'] === 'checkbox' ) {
            //<input:"checkbox">
            self::set_field_checkbox( $args['id'], $args['label'] );

        } elseif( $args['type'] === 'textarea' ) {
            //<textarea>
            self::set_field_textarea( $args['id'], $args['rows'], $args['after'] );

        }

        if ( isset( $args['desc'] ) ) {
            // Field description
            echo '<p class="description">', $args['desc'], '</p>';
        }

    }


    /**
     * <input:"text"|"number"|"email"|...]>
     */
    private static function set_field_input( $field_id, $type, $before_text, $after_text ) {

        $setting_name = LOOS_HCB::DB_NAME['settings'];
        //$setting_data = get_option( $setting_name );
        $setting_data = LOOS_HCB::$settings;

        echo $before_text, '<input id="',$field_id,'" name="',$setting_name."[$field_id]" ,'" type="', $type, '" value="', $setting_data[$field_id] ,'" />', $after_text;

    }

    /**
     * <textarea>
     */
    private static function set_field_textarea( $field_id, $rows, $after = '' ) {

        $setting_name = LOOS_HCB::DB_NAME['settings'];
        //$setting_data = get_option( $setting_name );
        $setting_data = LOOS_HCB::$settings;

        if ( $after ) {

            echo '<div class="hcb_col2_wrap">',
                '<div class="hcb_col_item"><textarea id="'. $field_id .'" name="'. $setting_name . "[$field_id]" . '" type="text" class="regular-text" rows="'. $rows . '" >'. $setting_data[$field_id] .'</textarea></div>',
                '<div class="hcb_col_item">'. $after .'</div>',
                '</div>';
        } else {
            echo '<textarea id="',$field_id,'" name="',$setting_name."[$field_id]" ,'" type="text" class="regular-text" rows="', $rows, '" >', $setting_data[$field_id] ,'</textarea>';
        }
    }

    /**
     * <input:"radio">
     */
    private static function set_field_radio( $field_id, $choices ) {

        $setting_name = LOOS_HCB::DB_NAME['settings'];
        //$setting_data = get_option( $setting_name );
        $setting_data = LOOS_HCB::$settings;

        echo '<fieldset>';
        foreach ($choices as $key => $val) {
            $radio_id = $field_id.'_'.$val;
            $checked = checked( $setting_data[$field_id], $val, false );
            $attr = 'id="'.$radio_id.'" name="'.$setting_name."[$field_id]".'" value="'.$val.'" '.$checked.'"';
            echo '<label for="', $radio_id, '">',
                    '<input type="radio" ', $attr, ' >',
                    '<span>', $key, '</span>',
                '</label><br>';
        }
        echo '</fieldset>';

    }

    /**
     * <input:"checkbox">
     */
    private static function set_field_checkbox( $field_id, $label ) {

        $setting_name = LOOS_HCB::DB_NAME['settings'];
        //$setting_data = get_option( $setting_name );
        $setting_data = LOOS_HCB::$settings;

        $checked = checked( $setting_data[$field_id], 'on', false );
        echo '<input type="hidden" name="', $setting_name."[$field_id]", '" value="off">';
        echo '<input type="checkbox" id="', $field_id, '" name="', $setting_name."[$field_id]", '" value="on" ', $checked, ' />';
        echo '<label for="', $field_id, '">', $label, '</label>';

    }

}

