<?php
/*
 * Widget Name: Login - Register
 * Version: 1.0
 * Author: Waqas Riaz
 * Author URI: http://favethemes.com/
 */

class houzez_login_widget extends WP_Widget {


    /**
     * Register widget
     **/
    public function __construct() {

        parent::__construct(
            'houzez_login_widget', // Base ID
            esc_html__( 'HOUZEZ: Login', 'houzez' ), // Name
            array( 'description' => esc_html__( 'houzez login widget', 'houzez' ), 'classname' => 'widget-login' ) // Args
        );

    }


    /**
     * Front-end display of widget
     **/
    public function widget( $args, $instance ) {

        extract( $args );

        $allowed_html_array = array(
            'div' => array(
                'id' => array(),
                'class' => array()
            ),
            'h3' => array(
                'class' => array()
            )
        );

        $title = apply_filters('widget_title', $instance['title'] );

        echo wp_kses( $before_widget, $allowed_html_array );

        global $current_user;
        wp_get_current_user();
        $userID  =  $current_user->ID;
        $user_custom_picture =  get_the_author_meta( 'fave_author_custom_picture' , $userID );
        if( empty( $user_custom_picture )) {
            $user_custom_picture = get_template_directory_uri().'/images/profile-avatar.png';
        }


        if ( $title ) echo wp_kses( $before_title, $allowed_html_array ) . $title . wp_kses( $after_title, $allowed_html_array );

        if( is_user_logged_in() ) { ?>

            <div class="widget-body" role="region">
                <div class="logged-in-wrap">
                    <div class="d-flex align-items-center">
                        <img class="me-3 rounded-circle" src="<?php echo esc_url( $user_custom_picture ); ?>" alt="<?php echo esc_attr( sprintf( __( 'Profile picture of %s', 'houzez' ), $current_user->display_name ) ); ?>">
                        <div>
                            <span role="heading" aria-level="4"><?php echo esc_attr( $current_user->display_name ); ?></span><br>
                            <a href="<?php echo wp_logout_url( home_url('/') ); ?>" role="button"><?php esc_html_e( 'Log out', 'houzez' ); ?></a>
                        </div>    
                    </div>
                </div>        
            </div>

        <?php } else { ?>

            <div class="widget-body" role="tablist">
                <div class="login-register-tabs pb-2">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#widget-login-form-tab" role="tab" aria-selected="true" aria-controls="widget-login-form-tab"><?php esc_html_e( 'Login', 'houzez' ); ?></a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#widget-register-form-tab" role="tab" aria-selected="false" aria-controls="widget-register-form-tab"><?php esc_html_e( 'Register', 'houzez' ); ?></a>
                        </li>
                    </ul>    
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="widget-login-form-tab" role="tabpanel">
                        <?php get_template_part('template-parts/login-register/login-form'); ?>
                    </div>
                    <div class="tab-pane fade" id="widget-register-form-tab" role="tabpanel">
                        <?php get_template_part('template-parts/login-register/register-form'); ?>
                    </div>
                </div>
            </div>

            <?php
        }
        echo wp_kses( $after_widget, $allowed_html_array );

    }


    /**
     * Sanitize widget form values as they are saved
     **/
    public function update( $new_instance, $old_instance ) {

        $instance = array();

        /* Strip tags to remove HTML. For text inputs and textarea. */
        $instance['title'] = strip_tags( $new_instance['title'] );

        return $instance;

    }


    /**
     * Back-end widget form
     **/
    public function form( $instance ) {

        /* Default widget settings. */
        $defaults = array(
            'title' => 'Login',
        );
        $instance = wp_parse_args( (array) $instance, $defaults );

        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:', 'houzez'); ?></label>
            <input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
        </p>

        <?php
    }

}

if ( ! function_exists( 'houzez_login_widget_loader' ) ) {
    function houzez_login_widget_loader (){
        register_widget( 'houzez_login_widget' );
    }
    add_action( 'widgets_init', 'houzez_login_widget_loader' );
}