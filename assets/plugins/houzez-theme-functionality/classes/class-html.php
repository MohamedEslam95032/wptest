<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Houzez_HTML {
    
    private static $_instance = null;

    public function __construct() {

        add_action( 'init', array( $this, 'setup' ) );
        
    }


    public static function instance() {
        if ( is_null( self::$_instance ) )
            self::$_instance = new self();
        return self::$_instance;
    }


    public function setup() {

        add_action( 'admin_enqueue_scripts', array( __CLASS__ , 'enqueue_scripts' ) );
    }


    public static function enqueue_scripts( $hook ) {
        $js_path = 'assets/admin/js/';
        $css_path = 'assets/admin/css/';

        /*wp_enqueue_style('houzez-newhtml-style', HOUZEZ_PLUGIN_URL . $css_path . 'style.css', array(), '1.0.0', 'all');

        wp_register_script( 'houzez-newhtml-custom', HOUZEZ_PLUGIN_URL . $js_path . 'custom.js', array('jquery') );
        wp_enqueue_script( 'houzez-newhtml-custom' );*/
    }

    /**
     * Render dashboard
     * @return void
     */
    public static function render()
    {
        $template = apply_filters( 'houzez_locations_template_path', HOUZEZ_TEMPLATES . 'newhtml.php' );

        if ( file_exists( $template ) ) {
            include_once( $template );
        }
    }
        
}