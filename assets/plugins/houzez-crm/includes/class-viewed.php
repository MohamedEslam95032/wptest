<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if(!class_exists('Houzez_Viewed_Listings')) {

    class Houzez_Viewed_Listings {

        public function __construct() {
           
            // Count listing visits.
            add_action( 'template_redirect', array($this, 'add_views') );
            add_action( 'wp_ajax_houzez_delete_viewed_listings', array( $this, 'delete_viewed_listings') );
            

        }

        public function delete_viewed_listings() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'houzez_crm_viewed_listings';

            // Check if 'ids' is set in POST request
            if ( !isset( $_POST['ids'] ) ) {
                $ajax_response = array( 'success' => false , 'reason' => esc_html__( 'No listing selected', 'houzez-crm' ) );
                echo json_encode( $ajax_response );
                die();
            }

            // Sanitize and validate the IDs
            $ids = sanitize_text_field($_POST['ids']);
            $id_array = explode(',', $ids);
            $id_array = array_map('intval', $id_array); // Convert each ID to an integer
            $ids = implode(',', $id_array);

            // Prepare and execute the SQL query
            $query = "DELETE FROM {$table_name} WHERE id IN ($ids)";
            $wpdb->query($query);

            // Return success response
            $ajax_response = array( 'success' => true , 'reason' => '' );
            echo json_encode( $ajax_response );
            die();
        }


        //Add visits data
        public function add_views() { 
            global $wpdb, $post;

            $post_id = isset($post->ID) ? $post->ID : '';

            if(empty($post_id)) {
                return;
            }

            $user_id = get_current_user_id();
            $already_exist = $this->checklisting($post->ID, $user_id);


            if ( ! is_singular( 'property' ) || empty($user_id) || $already_exist ) {
                return;
            }

            $table_name        = $wpdb->prefix . 'houzez_crm_viewed_listings';

            $data = array(
                'user_id'        => $user_id,
                'listing_id'     => $post->ID,
                
            );

            $format = array(
                '%d',
                '%d'
            );

            $wpdb->insert($table_name, $data, $format);
        }

        public function checklisting($listing_id, $user_id) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'houzez_crm_viewed_listings';

            // Sanitize and validate the inputs
            $listing_id = intval($listing_id); // Ensure $listing_id is an integer
            $user_id = intval($user_id); // Ensure $user_id is an integer

            // Secure the SQL query using prepare()
            $sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE listing_id = %d AND user_id = %d", $listing_id, $user_id);
            $result = $wpdb->get_row($sql, OBJECT);

            if (is_object($result) && !empty($result)) {
                return true;
            }
            return false;
        }



    } // end class

    new Houzez_Viewed_Listings();
} // End !exist