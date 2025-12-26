<?php
/**
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 04/09/18
 * Time: 11:39 PM
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Houzez_Currencies {
    
    /**
     * Initialize custom post type
     *
     * @access public
     * @return void
     */
    public static function init() {
        add_action( 'init', array( __CLASS__ , 'submit_currencies_form' ) );
        add_action( 'init', array( __CLASS__ , 'delete_currency' ) );
    }


    /**
     * Render currency main page
     * @return void
     */
    public static function render() {
        ?>
        <div class="wrap houzez-template-library">
            <div class="houzez-header">
                <div class="houzez-header-content">
                    <div class="houzez-logo">
                        <h1><?php esc_html_e('Currencies Management', 'houzez-theme-functionality'); ?></h1>
                    </div>
                    <div class="houzez-header-actions">
                        <a href="<?php echo esc_url(self::currency_add_link()); ?>" class="houzez-btn houzez-btn-primary">
                            <i class="dashicons dashicons-plus"></i>
                            <?php esc_html_e('Add New Currency', 'houzez-theme-functionality'); ?>
                        </a>
                    </div>
                </div>
            </div>

            <div class="houzez-dashboard">
                <?php
                if(isset($_GET['action']) && ( $_GET['action'] == 'add-new' || $_GET['action'] == 'edit-currency')) {
                    load_template( HOUZEZ_TEMPLATES . '/currency/form.php' );
                } else {
                    load_template( HOUZEZ_TEMPLATES . '/currency/currency-list.php' );
                } ?>
            </div>
        </div>
        <?php
    }

    public static function delete_currency() {

        $nonce = 'delete-currency';

        if ( ! empty( $_GET[ 'nonce' ] ) && wp_verify_nonce( $_GET[ 'nonce' ], $nonce ) && ! empty( $_GET['id'] ) ) {

            global $wpdb;

            $wpdb->delete( $wpdb->prefix . 'houzez_currencies', array( 'id' => $_GET['id'] ) );
            wp_redirect( 'admin.php?page=houzez_currencies' ); die;

        }

    }

    public static function currency_add_link() {
        $url = site_url( 'wp-admin/admin.php?page=houzez_currencies' );
        return add_query_arg( 'action', 'add-new', $url);
    }

    public static function get_property_currency($property_id) {
        global $wpdb;

        if(empty($property_id)) {
            $property_id = get_the_ID();
        }

        // Sanitize the property ID and get the currency code
        $property_id = intval($property_id);
        $currency_code = get_post_meta( $property_id, 'fave_currency', true);

        // Secure the SQL query using prepare()
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "houzez_currencies WHERE currency_code = %s", $currency_code);
        $result = $wpdb->get_results($query);

        if (!empty($result)) {
            return $result;
        }
        return false;
    }


    public static function get_property_currency_by_id($property_id) {
        global $wpdb;

        // Sanitize the property ID and get the currency code
        $property_id = intval($property_id);
        $currency_code = get_post_meta($property_id, 'fave_currency', true);

        // Secure the SQL query using prepare()
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "houzez_currencies WHERE currency_code = %s", $currency_code);
        $result = $wpdb->get_results($query);

        if (!empty($result)) {
            return $result;
        }
        return false;
    }


    public static function get_property_currency_2($property_id, $currency_code) {
        global $wpdb;

        // Sanitize the property ID and currency code
        $property_id = intval($property_id);
        $currency_code = sanitize_text_field($currency_code);

        // Secure the SQL query using prepare()
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "houzez_currencies WHERE currency_code = %s", $currency_code);
        $result = $wpdb->get_row($query, ARRAY_A);

        if (!empty($result)) {
            return $result;
        }
        return false;
    }

    public static function get_currency_by_code($currency_code) {
        global $wpdb;

        // Sanitize the currency code
        $currency_code = sanitize_text_field($currency_code);

        // Secure the SQL query using prepare()
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "houzez_currencies WHERE currency_code = %s", $currency_code);
        $result = $wpdb->get_row($query, ARRAY_A);

        if (!empty($result)) {
            return $result;
        }
        return false;
    }

    public static function get_form_fields() {
        global $wpdb;

        $result = $wpdb->get_results(" SELECT * FROM " . $wpdb->prefix . "houzez_currencies");

        if(!empty($result)) {
            return $result;
        }
        return false;
    }

    public static function get_currency_codes() {
        global $wpdb;

        $result = $wpdb->get_results(" SELECT currency_code FROM " . $wpdb->prefix . "houzez_currencies");

        if(!empty($result)) {
            return $result;
        }
        return false;
    }

    public static function get_currencies_data() {
        global $wpdb;

        $result = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "houzez_currencies");

        if(!empty($result)) {
            return $result;
        }
        return false;
    }

    public static function submit_currencies_form() {
        global $wpdb;
        $nonce = 'houzez_currency_save_field';

        if ( ! empty( $_REQUEST[ $nonce ] ) && wp_verify_nonce( $_REQUEST[ $nonce ], $nonce ) ) {

            $data = $_POST['hz_currency'];

            $currency_name = $data['name'];
            $currency_code = $data['code'];
            $currency_symbol = $data['symbol'];
            $currency_position = $data['position'];
            $currency_decimal = self::get_field_value( $data, 'decimals' );
            $currency_decimal_separator = self::get_field_value( $data, 'decimal_separator' );
            $currency_thousand_separator = self::get_field_value( $data, 'thousand_separator' );

            $instance = apply_filters( 'houzez_currencies_before_save', array(
                'currency_name' => $currency_name,
                'currency_code' => $currency_code,
                'currency_symbol' => $currency_symbol,
                'currency_position' => $currency_position,
                'currency_decimal' => !empty($currency_decimal) ? $currency_decimal : '0',
                'currency_decimal_separator' => $currency_decimal_separator,
                'currency_thousand_separator' => $currency_thousand_separator,
            ) );

            if ( ! empty( $data['id'] ) ) {
                $updated = $wpdb->update( $wpdb->prefix . 'houzez_currencies', $instance, array( 'id' => $data['id'] ) );
                
                if ($updated !== false) {
                    wp_redirect(add_query_arg(['action' => 'edit-currency', 'id' => $data['id'], 'currency_updated' => '1'], admin_url('admin.php?page=houzez_currencies')));
                    exit;
                } else {
                    wp_redirect(add_query_arg(['action' => 'edit-currency', 'id' => $data['id'], 'currency_error' => '1'], admin_url('admin.php?page=houzez_currencies')));
                    exit;
                }
            } else {
                $inserted = $wpdb->insert( $wpdb->prefix . 'houzez_currencies', $instance);
                if($inserted) {
                    $new_id = $wpdb->insert_id;
                    wp_redirect(add_query_arg(['action' => 'edit-currency', 'id' => $new_id, 'currency_added' => '1'], admin_url('admin.php?page=houzez_currencies')));
                    exit;
                } else {
                    wp_redirect(add_query_arg(['action' => 'add-new', 'currency_error' => '1'], admin_url('admin.php?page=houzez_currencies')));
                    exit;
                }
            }

            self::houzez_update_currency_data($data);
        }
    }

    public static function houzez_update_currency_data($data) {

        global $wpdb;
        $table = $wpdb->prefix . 'favethemes_currency_converter';

        $currency_decimal = self::get_field_value( $data, 'decimals' );
        $currency_decimal_separator = self::get_field_value( $data, 'decimal_separator' );
        $currency_thousand_separator = self::get_field_value( $data, 'thousand_separator' );

        $currency_data = array(
            'name'          => $data['name'],
            'symbol'        => $data['symbol'],
            'position'      => $data['position'],
            'decimals'      => !empty($currency_decimal) ? $currency_decimal : '0',
            'thousands_sep' => $currency_thousand_separator,
            'decimals_sep'  => $currency_decimal_separator,
        );

        $currency_data = json_encode( (array) $currency_data );

        $wpdb->update(
            $table, array(
            'currency_data' => $currency_data,
        ), array( 'currency_code' => $data['code'] ) );
    }


    public static function add_currency_notice() { ?>
        <div class="updated notice notice-success is-dismissible">
            <p><?php esc_html_e( 'The currency has been added, excellent!', 'houzez-theme-functionality' ); ?></p>
        </div>
    <?php    
    }

    public static function update_currency_notice() { ?>
        <div class="updated notice notice-success is-dismissible">
            <p><?php esc_html_e( 'The currency has been updated, excellent!', 'houzez-theme-functionality' ); ?></p>
        </div>
    <?php    
    }

    public static function error_currency_notice() { ?>
        <div class="error notice notice-error is-dismissible">
            <p><?php esc_html_e( 'There has been an error. Bummer!', 'houzez-theme-functionality' ); ?></p>
        </div>
    <?php    
    }

    public static function currency_edit_link( $id ) {
        return add_query_arg( array(
            'action' => 'edit-currency',
            'id' => $id,
        ) );
    }

    public static function currency_delete_link( $id ) {
        
        return add_query_arg( array(
            'action' => 'delete-currency',
            'id' => $id,
            'nonce' => wp_create_nonce( 'delete-currency' )
        ) );
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
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "houzez_currencies WHERE id = %d", $id);
        $instance = $wpdb->get_row($query, ARRAY_A);

        return $instance;
    }


    public static function get_field_value( $instance, $key, $default = null ) {
        return apply_filters( 'houzez_currencies_get_field_value', ! empty( $instance[ $key ] ) ? $instance[ $key ] : $default, $key, $instance );
    }

    public static function is_edit_field() {
        return self::get_edit_field() ? true : false;
    }

        
}
?>