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
        echo '<div class="wrap hcb_setting"><h1>Highlighting Code Block 設定</h1><form action="options.php" method="post">';
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
            '基本設定',            //セクションのタイトル
            '',                  //セクションを出力する関数名
            self::PAGE_NAME      //セクションを表示する設定ページ
        );
        // 言語名を表示するかどうか
        add_settings_field(
            'show_lang',                         //ID名
            '言語名の表示',                         //フィールドのタイトル
            array( $this, 'settings_field_cb'),  //フィールドを出力する関数。引数として配列を一つ（$args）渡す
            self::PAGE_NAME,                     //フィールドを表示する設定ページ
            self::SECTION_NAME,                  //フィールドを表示する設定ページのセクション
            array(
                'id' => 'show_lang',
                'type' => 'checkbox',
                'label' => 'コードブロックに言語名を表示する',
                'desc' => 'チェックすると、サイト表示側のコードに言語の種類が表示されます。'
            )
        );
        // 行数を表示するかどうか
        add_settings_field(
            'show_linenum',
            '行数の表示',
            array( $this, 'settings_field_cb'),
            self::PAGE_NAME,
            self::SECTION_NAME,
            array(
                'id' => 'show_linenum',
                'type' => 'checkbox',
                'label' => 'コードブロックに行数を表示する',
                'desc' => 'チェックすると、サイト表示側のコードの左端に行数が表示されます。'
            )
        );
        // font-smoothing設定
        add_settings_field(
            'font_smoothing',
            'フォントスムージング',
            array( $this, 'settings_field_cb'),
            self::PAGE_NAME,
            self::SECTION_NAME,
            array(
                'id' => 'font_smoothing',
                'type' => 'checkbox',
                'label' => 'フォントスムージングをオンにする',
                'desc' => 'コードブロックに<code>-webkit-font-smoothing: antialiased;</code>と<code>-moz-osx-font-smoothing: grayscale;</code>が追加されます。'
            )
        );
        // コードカラーリング（フロント）
        add_settings_field(
            'front_coloring',
            'コードカラーリング（サイト表示）',
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
            'コードカラーリング（エディタ内）',
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
            'フォントサイズ（PC）',
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
            'フォントサイズ（SP）',
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
            'fontsize_sp',
            'font-family: <br>コードのフォント',
            array($this, 'settings_field_cb'),
            self::PAGE_NAME,   
            self::SECTION_NAME,
            array(
                'id' => 'font_family',
                'type' => 'textarea',
                'rows' => 2,
                'before' => '',
                'after' => '',
                'desc' => 'デフォルト：<code>Menlo, Consolas, "メイリオ", sans-serif;</code>'
            )
        );
        // ブロックエディタのコンテンツ幅
        add_settings_field(
            'block_width',
            'ブロックエディタのコンテンツ幅',
            array($this, 'settings_field_cb'),
            self::PAGE_NAME,   
            self::SECTION_NAME,
            array(
                'id' => 'block_width',
                'type' => 'input',
                'input_type' => 'number',
                'before' => '',
                'after' => ' px',
                'desc' => 'コードの執筆に適した幅を設定できます。( WordPressのデフォルトは<code>610px</code>です)',
            )
        );

        /**
         * 「高度な設定設定」セクション
         */
        add_settings_section( self::SECTION_NAME2, '高度な設定', '', self::PAGE_NAME);
        // 使用する言語設定
        add_settings_field(
            'support_langs',
            '使用する言語設定',
            array($this, 'settings_field_cb'),
            self::PAGE_NAME,
            self::SECTION_NAME2,
            array(
                'id' => 'support_langs',
                'type' => 'textarea',
                'rows' => 16,
                'desc' => '「<code>クラスキー:"言語名"</code>」の形式で「<code>,</code>」（カンマ）区切りで記述してください。（改行の有無はどちらでも可。）
                        <br>&emsp;&emsp;・「クラスキー」は、prism.jsで使用されるクラス名、「lang-◯◯」の「◯◯」に該当する部分です
                        <br>&emsp;&emsp;・「言語名」は、セレクトボックスおよびコードブロック上に表示されます
                        <br>※ デフォルトでサポートされていない言語を使用する場合、「独自prism.js」の設定と併用してください。',
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
            '独自カラーリングファイル',
            array($this, 'settings_field_cb'),
            self::PAGE_NAME,
            self::SECTION_NAME2,
            array(
                'id' => 'prism_css_path',
                'type' => 'input',
                'input_type' => 'text',
                'before' => get_stylesheet_directory_uri()."/ ",
                'after' => '',
                'desc' => 'あなたが用意したコードカラーリング用のCSSファイルを読み込みます。'
            )
        );
        // 独自prism
        add_settings_field(
            'prism_js_path',
            '独自prism.js',
            array($this, 'settings_field_cb'),
            self::PAGE_NAME,
            self::SECTION_NAME2,
            array(
                'id' => 'prism_js_path',
                'type' => 'input',
                'input_type' => 'text',
                'before' => get_stylesheet_directory_uri()."/ ",
                'after' => '',
                'desc' => '自分専用の言語セットに対応したprism.jsファイルを使用できます。'
            )
        );
        // ヘルプ
        add_settings_field(
            'hcb_help',
            'ヘルプ',
            array($this, 'settings_field_cb'),
            self::PAGE_NAME,
            self::SECTION_NAME2,
            array(
                'type' => '',
                'desc' => '
                ファイルはテーマファイル内にアップロードしてください。
                <br>
                ファイルのパスが指定された場合、既存のカラーリングファイルやprism.jsファイルは読み込まれません。
                <br><br>
                ※ 現在読み込んでいるprism.jsファイルは<a href="https://prismjs.com/download.html#themes=prism&languages=markup+css+clike+javascript+c+csharp+bash+cpp+ruby+markup-templating+git+java+json+objectivec+php+sql+scss+python+typescript+swift&plugins=line-highlight+line-numbers" target="_blank"> こちら </a>でダウンロードできるJSファイルです。
                '
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

