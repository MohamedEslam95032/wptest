<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Houzez_Post_Type_Reviews {
    /**
     * Initialize custom post type
     *
     * @access public
     * @return void
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'definition' ) );
        add_action( 'wp_after_insert_post', array( __CLASS__, 'save_reviews_meta_after_insert' ), 10, 3 );
        add_filter( 'manage_edit-houzez_reviews_columns', array( __CLASS__, 'custom_columns' ) );
        add_action( 'manage_houzez_reviews_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );
        add_action( 'admin_action_houzez_review_accept', array( __CLASS__, 'review_accept' ) );
        add_action( 'admin_action_houzez_review_reject', array( __CLASS__, 'review_reject' ) );
    }

    /**
     * Custom post type definition
     *
     * @access public
     * @return void
     */
    public static function definition() {
        $labels = array(
            'name' => __( 'Reviews','houzez-theme-functionality'),
            'singular_name' => __( 'Review','houzez-theme-functionality' ),
            'add_new' => __('Add New','houzez-theme-functionality'),
            'add_new_item' => __('Add New Review','houzez-theme-functionality'),
            'edit_item' => __('Edit Review','houzez-theme-functionality'),
            'new_item' => __('New Review','houzez-theme-functionality'),
            'view_item' => __('View Review','houzez-theme-functionality'),
            'search_items' => __('Search Review','houzez-theme-functionality'),
            'not_found' =>  __('No Review found','houzez-theme-functionality'),
            'not_found_in_trash' => __('No Review found in Trash','houzez-theme-functionality'),
            'parent_item_colon' => ''
        );

        $labels = apply_filters( 'houzez_post_type_review_labels', $labels );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'show_in_menu'        => false,
            'show_in_admin_bar'   => true,
            'publicly_queryable' => false,
            'show_ui' => true,
            'query_var' => false,
            'has_archive' => false,
            'capability_type' => 'post',
            'exclude_from_search' => true,
            'hierarchical' => true,
            'can_export' => true,
            'menu_position' => 15,
            'supports' => array('title','editor','revisions', 'author'),
            'show_in_rest'       => true,
            'rest_base'          => 'houzez_reviews',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'rewrite' => array( 'slug' => 'reviews' )
        );

        $args = apply_filters( 'houzez_post_type_review_args', $args );

        register_post_type('houzez_reviews',$args);
    }

    public static function save_reviews_meta_after_insert($post, $update, $post_before) {
        $post = get_post( $post );

        if ( ! $post ) {
            return;
        }

        $review_id = $post->ID;

        // Proceed only if the post type is 'houzez_reviews'.
        if ( 'houzez_reviews' !== get_post_type( $review_id ) ) {
            return;
        }

        $review_post_type = get_post_meta($review_id, 'review_post_type', true);
        $review_stars = get_post_meta($review_id, 'review_stars', true);

        //Call your custom function to update the review meta.
        houzez_admin_review_meta_on_save( $review_id );
    }


    /**
     * Custom admin columns for post type
     *
     * @access public
     * @return array
     */
    public static function custom_columns() {
        $fields = array(
            'cb'                => '<input type="checkbox" />',
            'title'             => esc_html__( 'Title', 'houzez-theme-functionality' ),
            'ratings'       => esc_html__( 'Stars', 'houzez-theme-functionality' ),
            'post_title'          => esc_html__( 'Review On', 'houzez-theme-functionality' ),
            'review_actions' => __( 'Actions','houzez-theme-functionality' ),
            'date' => esc_html__('Date', 'houzez-theme-functionality')
        );

        return $fields;
    }

    /**
     * Custom admin columns implementation
     *
     * @access public
     * @param string $column
     * @return array
     */
    public static function custom_columns_manage( $column ) {
        global $post;
        switch ( $column ) {
            case 'ratings':
                echo get_post_meta($post->ID, 'review_stars', true);
                break;
            case 'post_title':
                $review_id = $post->ID;
                $review_post_type = get_post_meta($review_id, 'review_post_type', true);
                if($review_post_type == 'property') {
                    $listing_id = get_post_meta($review_id, 'review_property_id', true);
                    $meta_key = 'review_property_id';

                } else if($review_post_type == 'houzez_agent') {
                    $listing_id = get_post_meta($review_id, 'review_agent_id', true);
                    $meta_key = 'review_agent_id';

                } else if($review_post_type == 'houzez_agency') {
                    $listing_id = get_post_meta($review_id, 'review_agency_id', true);
                    $meta_key = 'review_agency_id';

                } else if($review_post_type == 'houzez_author') {
                    $listing_id = get_post_meta($review_id, 'review_author_id', true);
                    $meta_key = 'review_author_id';
                } else {
                    $listing_id = $review_id;
                }

                echo '<a target="_blank" href="'.get_permalink( $listing_id ).'">';
                echo get_the_title($listing_id);
                echo '</a>';
                break;
            case 'review_actions':
                
                echo '<div class="actions">';

                $admin_actions = apply_filters( 'post_row_actions', array(), $post );


                $user = wp_get_current_user();

                if ( in_array( $post->post_status, array( 'pending', 'review_rejected' ) ) && (in_array( 'administrator', (array) $user->roles ) || in_array( 'editor', (array) $user->roles ) || in_array( 'houzez_manager', (array) $user->roles )) ) {
                    $admin_actions['review_accept']   = array(
                        'class'  => 'accept',
                        'name'    => __( 'Approve', 'houzez-theme-functionality' ),
                        'icon'    => 'dashicons dashicons-yes',
                        'url' => add_query_arg( array(
                            'action' => 'houzez_review_accept',
                            'review_id' => $post->ID,
                        ), 'admin.php' )
                    );
                }
                
                if ( in_array( $post->post_status, array( 'pending', 'publish' ) ) && (in_array( 'administrator', (array) $user->roles ) || in_array( 'editor', (array) $user->roles ) || in_array( 'houzez_manager', (array) $user->roles )) ) {
                    $admin_actions['review_reject']   = array(
                        'class'  => 'reject',
                        'name'    => __( 'Unapprove', 'houzez-theme-functionality' ),
                        'icon'    => 'dashicons dashicons-no-alt',
                        'url' => add_query_arg( array(
                            'action' => 'houzez_review_reject',
                            'review_id' => $post->ID,
                        ), 'admin.php' )
                    );
                }

                $admin_actions = apply_filters( 'review_admin_actions', $admin_actions, $post );

                foreach ( $admin_actions as $action ) {
                    if ( is_array( $action ) ) {
                        printf( '<a class="button houzez-button-icon tips icon-%1$s" href="%2$s" data-tip="%3$s"><span class="%4$s"></span></a>', $action['class'], esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_html( $action['icon'] ) );
                    } else {
                        
                    }
                }


                echo '</div>';

                break;

        }
    }

    public static function review_accept() {

        if (! ( isset( $_GET['review_id']) || isset( $_POST['review_id'])  || ( isset($_REQUEST['action']) && 'houzez_review_accept' == $_REQUEST['action'] ) ) ) {
            wp_die('No review exist');
        }
     
        /*
         * get the original listing id
         */
        $review_id = (isset($_GET['review_id']) ? $_GET['review_id'] : $_POST['review_id']);

        $post_id = absint($review_id);
        $agrs = array(
            'ID' => $post_id,
            'post_status' => 'publish'
        );
        wp_update_post($agrs);

        if(!empty($post_id)) {
            houzez_admin_review_meta_on_save($post_id);
        }

        wp_redirect( admin_url( 'edit.php?post_type=houzez_reviews') );
        exit;
    }

    public static function review_reject() {

        if (! ( isset( $_GET['review_id']) || isset( $_POST['review_id'])  || ( isset($_REQUEST['action']) && 'houzez_review_accept' == $_REQUEST['action'] ) ) ) {
            wp_die('No review exist');
        }
     
        /*
         * get the original listing id
         */
        $review_id = (isset($_GET['review_id']) ? $_GET['review_id'] : $_POST['review_id']);

        $post_id = absint($review_id);
        $agrs = array(
            'ID' => $post_id,
            'post_status' => 'review_rejected'
        );
        wp_update_post($agrs);

        if(!empty($post_id)) {
            houzez_admin_review_meta_on_save($post_id);
        }

        wp_redirect( admin_url( 'edit.php?post_type=houzez_reviews') );
        exit;
    }

}
?>