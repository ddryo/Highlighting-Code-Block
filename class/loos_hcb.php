<?php

class LOOS_HCB {

    /**
     * DB Names
     */
    const DB_NAME = [
        'installed' => 'loos_hcb_installed',
        'settings'  => 'loos_hcb_settings',
    ];


    /**
     * NOUNCE
     */
    const NOUNCE_ACTION = 'loos_hcb_post';
    const NOUNCE_NAME   = 'loos_hcb_nounce';


    /**
     * Default Settings
     */
    const DEFAULT_SETTINGS = [
        'show_lang'       => 'on',
        'show_linenum'    => 'on',
        'font_smoothing'  => 'off',
        'front_coloring'  => 'light',
        'editor_coloring' => 'light',
        'fontsize_pc'     => '14px',
        'fontsize_sp'     => '13px',
        'font_family'     => 'Menlo, Consolas, "メイリオ", sans-serif;',
        'block_width'     => '',
        'prism_css_path'  => '',
        'prism_js_path'   => '',
        'support_langs'   => 'html:"HTML",'."\n"
                            .'css:"CSS",'."\n"
                            .'scss:"SCSS",'."\n"
                            .'js:"JavaScript",'."\n"
                            .'ts:"TypeScript",'."\n"
                            .'php:"PHP",'."\n"
                            .'ruby:"Ruby",'."\n"
                            .'python:"Python",'."\n"
                            .'swift:"Swift",'."\n"
                            .'c:"C",'."\n"
                            .'csharp:"C#",'."\n"
                            .'cpp:"C++",'."\n"
                            .'objectivec:"Objective-C",'."\n"
                            .'sql:"SQL",'."\n"
                            .'json:"JSON",'."\n"
                            .'bash:"Bash",'."\n"
                            .'git:"Git",'
    ];


    /**
     * $val for DataBase Setiing
     */
    public static $settings = "";


    /**
     * $val for Prism files
     */
    public $prism_js_path  = "";
    public static $prism_css_path = "";


    /**
     * The constructor
     */
    public function __construct() {

        $this->init();
        $this->load_files();
        $this->add_hooks();
        new LOOS_HCB_Menu();

    }


    /**
     * Set HCB Settings
     */
    private function init() {

        /* Get Option for HCB Setiings */
        $option = get_option( LOOS_HCB::DB_NAME[ 'settings' ] );

        if ( empty( $option ) ) {

            $option = LOOS_HCB::DEFAULT_SETTINGS;

        } else {

            //self::$settings = get_option( LOOS_HCB::DB_NAME[ 'settings' ] );

        }

        //マージする
        self::$settings = array_merge( LOOS_HCB::DEFAULT_SETTINGS, $option );

        /* Set Prism.js file path */
        if ( self::$settings[ 'prism_js_path' ] ) {

            $this->prism_js_path = get_stylesheet_directory_uri() ."/". self::$settings[ 'prism_js_path' ];

        } else {

            $this->prism_js_path = LOOS_HCB_URL.'/assets/js/prism.js';

        }

        /* Set Coloring file path */
        if ( self::$settings[ 'prism_css_path' ] ) {

            // $this->prism_css_path = get_stylesheet_directory_uri() ."/". self::$settings[ 'prism_css_path' ];
            self::$prism_css_path = get_stylesheet_directory() ."/". self::$settings[ 'prism_css_path' ];

        } else {

            // $this->prism_css_path = LOOS_HCB_URL.'/assets/css/cloring_'. self::$settings[ 'front_coloring' ] .'.css';
            self::$prism_css_path = LOOS_HCB_PATH.'/assets/css/cloring_'. self::$settings[ 'front_coloring' ] .'.css';

        }

    }

    /**
     * WP_Filesystemでのファイル読み込み
     */
    public static function get_file_contents( $file ) {

        if ( file_exists( $file ) && WP_Filesystem() ) {
            global $wp_filesystem;
            $file_content = $wp_filesystem->get_contents( $file );
            return $file_content;
        }
        return false;
    }


    /**
     * Load Files
     */
    private function load_files() {

        /**
         * Admin Scripts
         */
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_style( 'hcb_admin', LOOS_HCB_URL. '/assets/css/hcb_admin.css', [], LOOS_HCB_VERSION );
        });


        /**
         * Front Scripts
         */
        add_action( 'wp_enqueue_scripts', function() {

            /** Prism.js */
            wp_enqueue_script( 'hcb_prism_script', $this->prism_js_path, [], LOOS_HCB_VERSION, true );

            /** Coloring File */
            // wp_enqueue_style( 'hcb_prism_style', $this->prism_css_path, [], LOOS_HCB_VERSION );

            /** script */
            wp_enqueue_script( 'hcb_script', LOOS_HCB_URL. '/assets/js/hcb_script.js', ['hcb_prism_script'], LOOS_HCB_VERSION, true );


        }, 20 );


        /**
         * Stylesheet to Classic Editor
         */
        add_filter( 'mce_css', function( $mce_css ) {

            $editor_style_path = plugins_url( 'assets/css/editor_'. LOOS_HCB::$settings[ 'editor_coloring' ] .'.css', LOOS_HCB_FILE )."?v=".LOOS_HCB_VERSION;

            if ( ! empty( $mce_css ) ) {
                $mce_css .= ',';
            }

            $mce_css .= $editor_style_path;

            return $mce_css;
        } );


        /**
         * Script to Add Tinymce Button
         */
        add_filter( 'mce_external_plugins', function( $plugin_array ) {

            $plugin_array[ 'hcb_external_script' ] = plugins_url( 'assets/js/hcb_mce_button.js', LOOS_HCB_FILE );
            return $plugin_array;

        } );


        /**
         * Scripts to Block Editor
         */
        add_action( 'enqueue_block_editor_assets', function() {

            /* Stylesheet to Block Editor */
            wp_enqueue_style( 
                'hcb-gutenberg-style',
                plugins_url( 'assets/css/editor_'. LOOS_HCB::$settings[ 'editor_coloring' ] .'.css', LOOS_HCB_FILE ),
                [],
                LOOS_HCB_VERSION
            );

        } );


        /**
         * カスタムブロック用のスクリプトを追加
         */
        add_action( 'init', function() {

            // $asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');
            
            wp_register_script(
                'loos-hcb-script',
                LOOS_HCB_URL.'/assets/js/hcb_block.js',
                ['wp-blocks', 'wp-element', 'wp-polyfill'], //$asset_file['dependencies'],
                LOOS_HCB_VERSION, //$asset_file['version']
                true
            );

            register_block_type(
                'loos-hcb/code-block', [
                    'editor_script' => 'loos-hcb-script',
                ]
            );

            // JS用翻訳ファイルの読み込み
            if ( function_exists( 'wp_set_script_translations' ) ) {
                wp_set_script_translations(
                    'loos-hcb-script',
                    LOOS_HCB_DOMAIN,
                    LOOS_HCB_PATH . 'languages'
                );
            }

        } );


    }


    /**
     * Add Other hooks
     */
    private function add_hooks() {

        /* Tinymce setting */
        add_filter( 'tiny_mce_before_init', function( $in ) {

            /* Don't delete id & empty tags & etc... */
            $in[ 'valid_elements' ]          = '*[*]';
            $in[ 'extended_valid_elements' ] = '*[*]';
            $in[ 'verify_html' ]             = false;

            /* Text editor indent */
            $in[ 'indent' ] = true;

            return $in;

        } );


        /* Add HCB Button */
        add_filter( 'mce_buttons_2', function( $buttons ) {
            $buttons[] = 'hcb_select';
            return $buttons;
        } );


        /* Delete <br> in code. ( for wpautop:off ) */
        add_filter( 'wp_insert_post_data' , function( $data , $postarr ) {

            preg_match_all( '/<code(.*?)\/code>/s', $data[ 'post_content' ], $matches );

            foreach ( $matches[0] as $m ) {
                $re_m                   = preg_replace( '/<br[\s\/]*>/s', "\n", $m );
                $data[ 'post_content' ] = str_replace( $m, $re_m, $data[ 'post_content' ] );
            }
            return $data;

        } , 99, 2 );


        /* Set linenum */
        add_filter( 'the_content' , function( $content ) {

            $content = str_replace( 'prism on-numbers', 'prism line-numbers', $content );

            //個別設定が未定義のブロックはベース設定に依存
            if ( LOOS_HCB::$settings[ 'show_linenum' ] === 'on' ) {
                $content = str_replace( 'prism undefined-numbers', 'prism line-numbers', $content );
            }
            return $content;

        } , 99);


        /* Add code to Front Head. */
        add_action( 'wp_head', function() {

            /* HCB Style */
            $hcb_style = LOOS_HCB::get_file_contents( LOOS_HCB_PATH.'/assets/css/hcb_style.css' );
            $hcb_style .= LOOS_HCB::get_file_contents( self::$prism_css_path );
            $hcb_style = str_replace('@charset "UTF-8";', '', $hcb_style);
            $hcb_style = str_replace('../img', LOOS_HCB_URL.'/assets/img', $hcb_style);
            

            /* Font size */
            $hcb_style .= 
                '.hcb_wrap pre.prism{font-size: '. LOOS_HCB::$settings['fontsize_pc'] .'}'.
                '@media screen and (max-width: 599px){.hcb_wrap pre.prism{font-size: '. LOOS_HCB::$settings['fontsize_sp'] .'}}';

            /* Code margin */
            if ( LOOS_HCB::$settings[ 'show_lang' ] === 'off' ) {
                $hcb_style  .= '.hcb_wrap pre[class*="language-"] {padding-top: 1.2em;padding-bottom: 1.2em}'.
                    '.hcb_wrap pre::before{ content: none !important;}'.
                    '.hcb_wrap .line-highlight {margin-top: 1.2em}';
            }

            /* Font smoothing */
            if ( LOOS_HCB::$settings[ 'font_smoothing' ] === 'on' ) {
                $hcb_style  .= '.hcb_wrap pre{-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;}';
            }

            /* Font family */
            if ( LOOS_HCB::$settings[ 'font_family' ] ) {
                $hcb_style  .= '.hcb_wrap pre{font-family:'. LOOS_HCB::$settings[ 'font_family' ] .'}';
            }
            echo '<style id="hcb_style">'. $hcb_style .'</style>'."\n";
        },10);


        /* Add code to Admin Head. */
        add_action( 'admin_head', function() {

            /* style */
            $hcb_style  = '.wrap.hcb_setting th{min-width: 16em}';
            if ( LOOS_HCB::$settings[ 'block_width' ] ) {
                $hcb_style  .= '.wp-block{max-width: '. LOOS_HCB::$settings[ 'block_width' ] .'px !important}';
            }

            /* script */
            $hcb_script = "";
            $langs      = mb_convert_kana( LOOS_HCB::$settings[ 'support_langs' ], "as");
            $langs      = str_replace(["\r\n", "\r", "\n"], '', $langs);
            $hcb_script .= 'var hcbLangArray = {'. $langs .'};';
            //$hcb_script .= 'var hcbShowLinenum = "'. LOOS_HCB::$settings[ 'show_linenum' ].'";';
            echo '<style>'. $hcb_style .'</style>'."\n";
            echo '<script>'. $hcb_script .'</script>'."\n";

        } , 1 );


    }
    

}
