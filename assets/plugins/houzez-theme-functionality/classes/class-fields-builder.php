<?php
/**
 * Class Houzez_Post_Type_Agency
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 28/09/16
 * Time: 10:16 PM
 */
class Houzez_Fields_Builder {


    /**
     * Sets up init
     *
     */
    public static function init() {
        add_action( 'admin_enqueue_scripts', array( __CLASS__ , 'enqueue_scripts' ) );
        add_action( 'init', array( __CLASS__ , 'save_fields' ) );
        add_action( 'init', array( __CLASS__ , 'delete_field' ) );
        add_action( 'wp_ajax_houzez_load_select_options', array( __CLASS__ , 'load_select_options' ) );
    }


    public static function render() {
       ?>
        <div class="wrap houzez-template-library">
            <div class="houzez-header">
                <div class="houzez-header-content">
                    <div class="houzez-logo">
                        <h1><?php esc_html_e('Fields Builder', 'houzez-theme-functionality'); ?></h1>
                    </div>
                    <div class="houzez-header-actions">
                        <a href="<?php echo esc_url(self::field_add_link()); ?>" class="houzez-btn houzez-btn-primary">
                            <i class="dashicons dashicons-plus"></i>
                            <?php esc_html_e('Add New Field', 'houzez-theme-functionality'); ?>
                        </a>
                    </div>
                </div>
            </div>

            <div class="houzez-dashboard">
            <?php
            $template = apply_filters( 'houzez_fbuilder_index_template_path', HOUZEZ_TEMPLATES . '/fields-builder/page.php' );

            if ( file_exists( $template ) ) {
                load_template( $template );
            } ?>
            </div>
        </div>
    <?php        
    }

    public static function enqueue_scripts()
    {
        $path = 'assets/admin/js/';

        wp_register_script( 'houzez-jquery-cloneya', HOUZEZ_PLUGIN_URL . $path . 'jquery-cloneya.min.js', array('jquery') );
        wp_enqueue_script( 'houzez-jquery-cloneya' );
    }

    public static function get_form_fields() {
        global $wpdb;

        // Prepare and execute the SQL query
        $query = "SELECT * FROM " . $wpdb->prefix . "houzez_fields_builder ORDER BY id ASC";
        $result = $wpdb->get_results($query);

        if (!empty($result)) {
            return $result;
        }
        return false;
    }

    public static function get_search_fields() {
        global $wpdb;

        // Prepare and execute the SQL query
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "houzez_fields_builder WHERE is_search = %s ORDER BY id ASC", 'yes');
        $result = $wpdb->get_results($query);

        if (!empty($result)) {
            return $result;
        }
        return false;
    }



    public static function delete_field() {
        $nonce = 'houzez-delete-field';

        if ( ! empty( $_GET['nonce'] ) && wp_verify_nonce( $_GET['nonce'], $nonce ) && ! empty( $_GET['id'] ) ) {
            global $wpdb;

            // Sanitize the ID
            $id = intval($_GET['id']);

            // Use wpdb->delete with sanitized data
            $wpdb->delete($wpdb->prefix . 'houzez_fields_builder', array('id' => $id), array('%d'));

            // Redirect after deletion
            wp_redirect('admin.php?page=houzez_fbuilder');
            exit;
        }
    }


    public static function load_select_options() {
        // Initialize $instance as an empty array for new fields
        $instance = array();
        
        // If we're editing an existing field, get the field data
        if (!empty($_POST['field_id'])) {
            $field_id = intval($_POST['field_id']);
            $instance = self::get_field($field_id);
        }
        
        // Ensure $instance is always an array
        if (!$instance) {
            $instance = array();
        }

        $file = HOUZEZ_TEMPLATES . '/fields-builder/multiple.php';
        if ( file_exists( $file ) ) {
            include $file;
        }
        die;
    }

    public static function get_field_types() {

        return apply_filters( 'houzez_fbuilder_field_types', array(
            'text' => esc_html__( 'Text', 'houzez-theme-functionality' ),
            'number' => esc_html__( 'Number', 'houzez-theme-functionality' ),
            'url' => esc_html__( 'URL/Link', 'houzez-theme-functionality' ),
            'textarea' => esc_html__( 'Text area', 'houzez-theme-functionality' ),
            'select' => esc_html__( 'Select', 'houzez-theme-functionality' ),
            'multiselect' => esc_html__( 'Multi Select', 'houzez-theme-functionality' ),
            'checkbox_list' => esc_html__( 'Checkbox List', 'houzez-theme-functionality' ),
            'radio' => esc_html__( 'Radio', 'houzez-theme-functionality' ),
        ) );
    }

    public static function get_field_option($instance) {
        if( isset($instance['type']) && ( $instance['type'] == 'checkbox_list' || $instance['type'] == 'radio' ) ) {
            return isset($instance['fvalues']) && ! empty( $instance['fvalues'] ) ? $instance['fvalues'] : '';
        }
        
        // Always return an empty string if no conditions are met
        return '';
    }

    public static function field_edit_link( $id ) {
        return add_query_arg( array(
            'action' => 'houzez-edit-field',
            'id' => $id,
        ) );
    }
    public static function field_add_link() {
        $url = site_url( 'wp-admin/admin.php?page=houzez_fbuilder' );
        return add_query_arg( 'action', 'add-new', $url);
    }

    public static function field_delete_link( $id ) {
        
        return add_query_arg( array(
            'action' => 'houzez-delete-field',
            'id' => $id,
            'nonce' => wp_create_nonce( 'houzez-delete-field' )
        ) );
    }

    public static function get_field_value( $instance, $key, $default = null )
    {   
        if( isset($instance[ $key ]) ) {
            $instance = stripslashes($instance[ $key ]);
            return apply_filters( 'houzez_fbuilder_get_field_value', ! empty($instance) ? $instance : $default, $key, $instance );
        }
    }


    public static function is_edit_field() {
        return self::get_edit_field() ? true : false;
    }

    
    public static function get_edit_field()
    {
        if ( ! empty( $_GET['id'] ) && ! empty( $_GET['action'] ) ) {
            $field =  self::get_field( $_GET['id'] );

            return $field;
        }

        return null;
    }

    public static function get_field($id) {
        global $wpdb;

        // Sanitize the ID
        $id = intval($id);

        // Secure the SQL query using prepare()
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "houzez_fields_builder WHERE id = %d", $id);
        $instance = $wpdb->get_row($query, ARRAY_A);

        if ($instance) {
            $instance['fvalues'] = !empty($instance['fvalues']) ? unserialize($instance['fvalues']) : array();
        }

        return $instance;
    }

    public static function get_field_by_slug($slug) {
        global $wpdb;

        // Sanitize the slug
        $slug = sanitize_text_field($slug);

        // Secure the SQL query using prepare()
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "houzez_fields_builder WHERE field_id = %s", $slug);
        $instance = $wpdb->get_row($query, ARRAY_A);

        return $instance;
    }

    public static function get_field_title_type_by_slug($slug) {
        global $wpdb;

        // Sanitize the slug
        $slug = sanitize_text_field($slug);

        // Secure the SQL query using prepare()
        $query = $wpdb->prepare("SELECT label, type FROM " . $wpdb->prefix . "houzez_fields_builder WHERE field_id = %s", $slug);
        $result = $wpdb->get_row($query, ARRAY_A);

        return $result;
    }



    public static function add_field_notice() { ?>
        <div class="updated notice notice-success is-dismissible">
            <p><?php esc_html_e( 'The field has been added, excellent!', 'houzez-theme-functionality' ); ?></p>
        </div>
    <?php    
    }

    public static function update_field_notice() { ?>
        <div class="updated notice notice-success is-dismissible">
            <p><?php esc_html_e( 'The field has been updated, excellent!', 'houzez-theme-functionality' ); ?></p>
        </div>
    <?php    
    }

    public static function error_field_notice() { ?>
        <div class="error notice notice-error is-dismissible">
            <p><?php esc_html_e( 'There has been an error. Bummer!', 'houzez-theme-functionality' ); ?></p>
        </div>
    <?php    
    }

    public static function stripslashes_deep($value)
    {
        $value = is_array($value) ?
                    array_map('stripslashes_deep', $value) :
                    stripslashes($value);

        return $value;
    }


    public static function save_fields_old() {
        global $wpdb;

        $nonce = 'houzez_fbuilder_save_field';

        if ( ! empty( $_REQUEST[ $nonce ] ) && wp_verify_nonce( $_REQUEST[ $nonce ], $nonce ) ) {
            $data = $_POST['hz_fbuilder'];

            $fvalues = ! empty( $data['fvalues'] ) ? ( $data['fvalues'] ) : null;

            if(!empty($fvalues)) {
                $fvalues = stripslashes_deep($fvalues);
            }

            if(!empty($fvalues)) {
                foreach ($fvalues as $option) {
                    do_action( 'wpml_register_single_string', 'houzez_cfield', $option, $option );
                }
            }

            $field_type = self::get_field_value( $data, 'type' );
            $field_label = self::get_field_value( $data, 'label' );
            $placeholder = self::get_field_value( $data, 'placeholder' );
            $field_is_search = self::get_field_value( $data, 'is_search' );

            if($field_type == 'select' || $field_type == 'multiselect') {
                $fvalues = $fvalues ? serialize( array_combine( $fvalues, $fvalues  ) ) : null;
            } else {
                $fvalues = $data['options'] ? serialize( $data['options'] ) : null;
            }

            $instance = apply_filters( 'houzez_fields_builder_before_fields_save', array(
                'label' => $field_label,
                'type' => $field_type,
                'fvalues' => $fvalues,
                'is_search' => $field_is_search,
                'placeholder' => $placeholder,
                'options' => ''//$field_icon,
                
                
            ) );
            do_action( 'wpml_register_single_string', 'houzez_cfield', $field_label, $field_label );
            do_action( 'wpml_register_single_string', 'houzez_cfield', $placeholder, $placeholder );

            if ( ! empty( $data['id'] ) ) {
                $updated = $wpdb->update( $wpdb->prefix . 'houzez_fields_builder', $instance, array( 'id' => $data['id'] ) );
                
                add_action( 'admin_notices', array( __CLASS__ , 'update_field_notice' ) );
                
            } else {
                $field_id = self::houzez_slugify( self::get_field_value( $data, 'label' ) );

                if(is_rtl()) {
                    $field_id = self::houzez_slugify( uniqid( 'f' ) );
                } else {
                    $field_id = self::houzez_slugify( self::get_field_value( $data, 'label' ) );
                }

                if( in_array($field_id, self::builtInFields()) ) {
                    $field_id = $field_id.'-1';
                }

                $inserted = $wpdb->insert( $wpdb->prefix . 'houzez_fields_builder', array_merge( array(
                    'field_id' => $field_id ), $instance ));
                if($inserted) {
                    add_action( 'admin_notices', array( __CLASS__ , 'add_field_notice' ) );
                } else {
                    add_action( 'admin_notices', array( __CLASS__ , 'error_field_notice' ) );
                }

            }

        } //$nonce

    }

    public static function save_fields() {
        global $wpdb;

        $nonce = 'houzez_fbuilder_save_field';

        if (!empty($_REQUEST[$nonce]) && wp_verify_nonce($_REQUEST[$nonce], $nonce)) {
            $data = $_POST['hz_fbuilder'];

            $fvalues = !empty($data['fvalues']) ? stripslashes_deep($data['fvalues']) : null;

            if (!empty($fvalues)) {
                self::register_option_strings($fvalues);
            }

            $instance = self::prepare_instance_data($data, $fvalues);
            self::register_label_and_placeholder_strings($instance);

            if (!empty($data['id'])) {
                self::update_field($wpdb, $data['id'], $instance);
            } else {
                self::insert_field($wpdb, $instance);
            }
        }
    }

    private static function register_option_strings($fvalues) {
        foreach ($fvalues as $option) {
            do_action('wpml_register_single_string', 'houzez_cfield', $option, $option);
        }
    }

    private static function prepare_instance_data($data, $fvalues) {
        $field_type = self::get_field_value($data, 'type');
        $field_label = self::get_field_value($data, 'label');
        $placeholder = self::get_field_value($data, 'placeholder');
        $field_is_search = self::get_field_value($data, 'is_search');

        if ($field_type == 'select' || $field_type == 'multiselect') {
            $fvalues = $fvalues ? serialize(array_combine($fvalues, $fvalues)) : null;
        } else {
            $fvalues = $data['options'] ? serialize($data['options']) : null;
        }

        return apply_filters('houzez_fields_builder_before_fields_save', [
            'label' => $field_label,
            'type' => $field_type,
            'fvalues' => $fvalues,
            'is_search' => $field_is_search,
            'placeholder' => $placeholder,
            'options' => '',
        ]);
    }

    private static function register_label_and_placeholder_strings($instance) {
        do_action('wpml_register_single_string', 'houzez_cfield', $instance['label'], $instance['label']);
        do_action('wpml_register_single_string', 'houzez_cfield', $instance['placeholder'], $instance['placeholder']);
    }

    private static function update_field($wpdb, $id, $instance) {
        $updated = $wpdb->update(
            $wpdb->prefix . 'houzez_fields_builder',
            $instance,
            ['id' => $id]
        );

        // Check if update was successful OR if no rows were affected (no changes made)
        // $wpdb->update() returns the number of rows updated, or false on error
        // 0 means no rows were updated (no changes), which is not an error
        if ($updated !== false) {
            wp_redirect(add_query_arg(['action' => 'houzez-edit-field', 'id' => $id, 'field_updated' => '1'], admin_url('admin.php?page=houzez_fbuilder')));
            exit;
        } else {
            wp_redirect(add_query_arg(['action' => 'houzez-edit-field', 'id' => $id, 'field_error' => '1'], admin_url('admin.php?page=houzez_fbuilder')));
            exit;
        }
    }

    private static function insert_field($wpdb, $instance) {
        $field_id = self::generate_field_id($instance['label']);
        
        if (self::field_id_exists($wpdb, $field_id) || in_array($field_id, self::builtInFields())) {
            wp_redirect(add_query_arg(['action' => 'add-new', 'field_exists' => '1'], admin_url('admin.php?page=houzez_fbuilder')));
            exit;
        }

        $inserted = $wpdb->insert(
            $wpdb->prefix . 'houzez_fields_builder',
            array_merge(['field_id' => $field_id], $instance)
        );

        if ($inserted) {
            wp_redirect(add_query_arg(['action' => 'add-new', 'field_added' => '1'], admin_url('admin.php?page=houzez_fbuilder')));
            exit;
        } else {
            wp_redirect(add_query_arg(['action' => 'add-new', 'field_error' => '1'], admin_url('admin.php?page=houzez_fbuilder')));
            exit;
        }
    }

    private static function generate_field_id($label) {
        if (is_rtl()) {
            return self::houzez_slugify(uniqid('f'));
        } else {
            return self::houzez_slugify($label);
        }
    }

    private static function field_id_exists($wpdb, $field_id) {
        $table_name = $wpdb->prefix . 'houzez_fields_builder';
        $query = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE field_id = %s", $field_id);
        $count = $wpdb->get_var($query);

        return $count > 0;
    }

    public static function field_exists_notice() {
        $class = 'notice notice-error';
        $message = __('A field with the name provided already exists', 'houzez-theme-functionality');
        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }


    public static function builtInFields() {
        $array = array(
            'bed',
            'room',
            'bath',
            'garage',
            'area-size',
            'land-area',
            'year-built',
            'property-id',
            'property_price',
            'property_price_prefix',
            'location',
            'type',
            'status',
            'country',
            'states',
            'areas',
            'bathrooms',
            'bedrooms',
            'currency',
            'use_radius',
            'feature',
            'search_location',
            'keyword',
            'label',
            'max-area',
            'max-land-area',
            'max-price',
            'min-area',
            'min-land-area',
            'min-price',
            'property_id',
            'year-built',
        );
        return $array;
    }

    public static function houzez_slugify($text) {
      // replace non letter or digits by -
      $text = preg_replace('~[^\pL\d]+~u', '-', $text);

      // transliterate
      if (seems_utf8($text)) {
            if (function_exists('mb_strtolower')) {
                $text = mb_strtolower($text, 'UTF-8');
            }
            $text = utf8_uri_encode($text, 200);
        }


      // remove unwanted characters
      $text = preg_replace('~[^-\w]+~', '', $text);

      // trim
      $text = trim($text, '-');

      // remove duplicate -
      $text = preg_replace('~-+~', '-', $text);

      // lowercase
      $text = strtolower($text);

      if (empty($text)) {
        return 'n-a';
      }

      return $text;
    }

    
}