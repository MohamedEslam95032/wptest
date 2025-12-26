<?php
/**
 * Name         : Elementor Addons For Houzez
 * Description  : Provides additional Elementor Elements for the Houzez theme
 * Author : Waqas Riaz
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( ! class_exists( 'Houzez_Elementor_Extensions' ) ) {
    final class Houzez_Elementor_Extensions {

        const HOUZEZ_GROUP = 'houzez';

        /**
         * Houzez_Extensions The single instance of Houzez_Extensions.
         * @var     object
         * @access  private
         * @since   1.5.6
         */
        private static $_instance = null;

        /**
         * Constructor function.
         * @access  public
         * @since   1.5.6
         * @return  void
         */
        public function __construct() {

            $this->houzez_register_traits();

            add_action( 'elementor/elements/categories_registered', array( $this, 'add_widget_categories' ) );
            add_action( 'elementor/widgets/register', array( $this, 'elementor_widgets' ) );
            add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'houzez_enqueue_scripts' ) );
            add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'enqueue_frontend_styles' ) );
            add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_editor_styles' ), 0 );
            add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ), 0 );

            add_action( 'elementor/dynamic_tags/register', array( $this, 'register_tags' ) );
            add_action( 'elementor/controls/register', array( $this, 'register_controls' ) );


            //$this->houzez_wpml_elementor_translation();
        }

        /**
         * Houzez_Elementor_Extensions Instance
         *
         * Ensures only one instance of Houzez_Elementor_Extensions is loaded or can be loaded.
         *
         * @since 1.5.6
         * @static
         * @return Houzez_Elementor_Extensions instance
         */
        public static function instance () {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }


        /**
         * Widget Category Register
         *
         * @since  1.5.6
         * @access public
         */
        public function add_widget_categories( $elements_manager ) {
            $elements_manager->add_category(
                'houzez-elements',
                [
                    'title' => esc_html__( 'Houzez Elements', 'houzez-theme-functionality' ),
                    'icon' => 'fa fa-plug',
                ]
            );

            $elements_manager->add_category(
                'houzez-header-footer',
                [
                    'title' => esc_html__( 'Houzez Header & Footer', 'houzez-theme-functionality' ),
                    'icon' => 'fa fa-plug',
                ]
            );

            $elements_manager->add_category(
                'houzez-header-footer-builder',
                [
                    'title' => esc_html__( 'Houzez Header & Footer', 'houzez-theme-functionality' ),
                    'icon' => 'fa fa-plug',
                ]
            );
            
            $elements_manager->add_category(
                'houzez-single-property',
                [
                    'title' => esc_html__( 'Houzez Single Property', 'houzez-theme-functionality' ),
                    'icon' => 'fa fa-plug',
                ]
            );

            $elements_manager->add_category(
                'houzez-single-property-builder',
                [
                    'title' => esc_html__( 'Houzez Single Property', 'houzez-theme-functionality' ),
                    'icon' => 'fa fa-plug',
                ]
            );

            $elements_manager->add_category(
                'houzez-single-agent',
                [
                    'title' => esc_html__( 'Houzez Single Agent', 'houzez-theme-functionality' ),
                    'icon' => 'fa fa-plug',
                ]
            );

            $elements_manager->add_category(
                'houzez-single-agent-builder',
                [
                    'title' => esc_html__( 'Houzez Single Agent', 'houzez-theme-functionality' ),
                    'icon' => 'fa fa-plug',
                ]
            );

            $elements_manager->add_category(
                'houzez-single-agency',
                [
                    'title' => esc_html__( 'Houzez Single Agency', 'houzez-theme-functionality' ),
                    'icon' => 'fa fa-plug',
                ]
            );

            $elements_manager->add_category(
                'houzez-single-agency-builder',
                [
                    'title' => esc_html__( 'Houzez Single Agency', 'houzez-theme-functionality' ),
                    'icon' => 'fa fa-plug',
                ]
            );

            $elements_manager->add_category(
                'houzez-loop-builder',
                [
                    'title' => esc_html__( 'Houzez Loop Builder', 'houzez-theme-functionality' ),
                    'icon' => 'fa fa-plug',
                ]
            );

            $elements_manager->add_category(
                'houzez-single-post-builder',
                [
                    'title' => esc_html__( 'Houzez Single Post', 'houzez-theme-functionality' ),
                    'icon' => 'fa fa-plug',
                ]
            );

            $elements_manager->add_category(
                'houzez-single-post',
                [
                    'title' => esc_html__( 'Houzez Single Post', 'houzez-theme-functionality' ),
                    'icon' => 'fa fa-plug',
                ]
            );

            $elements_manager->add_category(
                'houzez-sidebar-widgets',
                [
                    'title' => esc_html__( 'Houzez Sidebar/Footer Widgets ', 'houzez-theme-functionality' ),
                    'icon' => 'fa fa-plug',
                ]
            );
            
        }

        /**
         * Widgets
         *
         * @since  1.0.0
         * @access public
         */
        public function elementor_widgets( $widgets_manager ) {

            if( class_exists('Houzez_Data_Source') ) {
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/section-title.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/space.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/search-builder.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/advanced-search.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/page-title.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/sort-by.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/listings-tabs.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/properties-ajax-tabs.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/property-cards-v1.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/property-cards-v2.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/property-cards-v3.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/property-cards-v4.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/property-cards-v5.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/property-cards-v6.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/property-cards-v7.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/property-cards-v8.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/properties.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/property-carousel-v1.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/property-carousel-v2.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/property-carousel-v3.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/property-carousel-v5.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/property-carousel-v6.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/property-carousel-v7.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/property-by-id.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/property-by-ids.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/properties-recent-viewed.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/grids.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/grid-builder.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/taxonomies-cards.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/taxonomies-cards-carousel.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/taxonomies-grids.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/taxonomies-grids-carousel.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/properties-grids.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/taxonomies-list.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/properties-slider.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/banner-image.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/custom-carousel.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/mapbox.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/google-map.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/open-street-map.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/agents.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/agents-grid.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/contact-form.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/inquiry-form.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/testimonials.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/testimonials-v2.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/testimonials-v3.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/team-member.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/partners.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/icon-box.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/blog-posts.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/blog-posts-v2.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/blog-posts-carousel.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/price-table.php';

                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/header-footer/site-logo.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/header-footer/menu.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/header-footer/login-modal.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/header-footer/create-listing-btn.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/header-footer/currency.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/header-footer/area-switcher.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/header-footer/lang.php';

                // Single Property widgets
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/breadcrumb.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/property-title.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/property-title-area.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/property-price.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/property-address.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/property-meta-data.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/agent-card.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/item-tools.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/status-label.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/item-label.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/featured-label.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/property-content.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/property-excerpt.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/featured-image.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-toparea-v1.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-toparea-v2.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-toparea-v3.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-toparea-v5.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-toparea-v6.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-toparea-v7.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/images-gallery-v1.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/images-gallery-v2.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/images-gallery-v3.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/images-gallery-v4.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/images-gallery-v5.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-block-gallery.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-overview.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-overview-v2.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-description.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-details.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-address.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-google-map.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-map.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-open-street-map.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-features.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-attachments.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-video.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-floorplan.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-floorplan-v2.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-360-virtual.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-energy.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-calculator.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-nearby.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-walkscore.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-contact-bottom.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-contact-2.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-schedule-tour.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-schedule-tour-v2.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-similar.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-sublistings.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-review.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-property/section-calendar.php';

                //Single Agent Widgets
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-name.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-image.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-content.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-excerpt.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-rating.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-position.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-meta.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-contact-btn.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-contact-form.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-whatsapp-btn.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-call-btn.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-telegram-btn.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-line-btn.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-listings.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-review.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-map.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-profile-v1.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-profile-v2.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-stats.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-single-stats.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-about.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-listings-review.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-contact.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agent/agent-search.php';

                //Single Agency Widgets
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-profile-v1.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-profile-v2.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-stats.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-single-stats.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-about.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-listings-review.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-listings.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-agents.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-review.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-contact.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-search.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-meta.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-name.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-image.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-content.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-excerpt.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-rating.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-address.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-contact-form.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-contact-btn.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-call-btn.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-whatsapp-btn.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-telegram-btn.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-line-btn.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-agency/agency-map.php';

                //Single Post Widgets
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-post/post-title.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-post/post-info.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-post/post-content.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-post/post-excerpt.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-post/post-image.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-post/author-box.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-post/post-navigation.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/single-post/post-comments.php';

                //Sidebar widgets
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/sidebar/code-banner.php';

            }
        }

        public function houzez_register_traits() {
            require_once HOUZEZ_PLUGIN_PATH . '/elementor/traits/Houzez_Button_Traits.php';
            require_once HOUZEZ_PLUGIN_PATH . '/elementor/traits/Houzez_Form_Traits.php';
            require_once HOUZEZ_PLUGIN_PATH . '/elementor/traits/Houzez_Preview_Query.php';
            require_once HOUZEZ_PLUGIN_PATH . '/elementor/traits/Houzez_Filters_Traits.php';
            require_once HOUZEZ_PLUGIN_PATH . '/elementor/traits/Houzez_Style_Traits.php';
            require_once HOUZEZ_PLUGIN_PATH . '/elementor/traits/Houzez_Property_Cards_Traits.php';
            require_once HOUZEZ_PLUGIN_PATH . '/elementor/traits/Houzez_Testimonials_Traits.php';
        }

        public function houzez_wpml_elementor_translation() {

            if ( class_exists( 'SitePress' ) ) {

                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/wpml/section-title-wpml.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/wpml/advanced-search-wpml.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/wpml/sort-by-wpml.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/wpml/property-carousel-v1-wpml.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/wpml/property-carousel-v2-wpml.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/wpml/property-carousel-v3-wpml.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/wpml/property-carousel-v5-wpml.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/wpml/property-carousel-v6-wpml.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/wpml/grid-builder-wpml.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/wpml/contact-form-wpml.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/wpml/inquiry-form-wpml.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/wpml/search-builder-wpml.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/wpml/team-member-wpml.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/wpml/icon-box-wpml.php';
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/widgets/wpml/price-table-wpml.php';

            }

        }

        public function register_tags( $dynamic_tags ) {

        
            // In our Dynamic Tag we use a group named request-variables so we need 
            // To register that group as well before the tag
            \Elementor\Plugin::$instance->dynamic_tags->register_group( self::HOUZEZ_GROUP, [
                'title' => 'Houzez' 
            ] );

            $files = [
                'property-title',
                'property-excerpt',
                'property-content',
            ];

            foreach ( $files as $file ) {
                require_once HOUZEZ_PLUGIN_PATH . '/elementor/tags/'.$file.'.php';
            }


            $tags = [
                'Property_Title_Tag',
                'Property_Excerpt_Tag',
                'Property_Content_Tag',
            ];

            foreach ( $tags as $tag ) {
                $dynamic_tags->register( new $tag );
            }

        }

        public function register_controls( $controls_manager ) {

            require_once( HOUZEZ_PLUGIN_DIR . 'elementor/controls/warning.php' );
            require_once( HOUZEZ_PLUGIN_DIR . 'elementor/controls/info.php' );
            require_once( HOUZEZ_PLUGIN_DIR . 'elementor/controls/address-control.php' );
            require_once( HOUZEZ_PLUGIN_DIR . 'elementor/controls/details-control.php' );
            require_once( HOUZEZ_PLUGIN_DIR . 'elementor/controls/autocomplete.php' );

            $controls_manager->register( new Houzez_Warning_note() );
            $controls_manager->register( new Houzez_Info_note() );
            $controls_manager->register( new Houzez_Address_Control() );
            $controls_manager->register( new Houzez_Property_Details_Control() );
            $controls_manager->register( new Houzez_Autocomplete() );

        }


        /**
         * Enqueue scripts
         *
         * @since  1.0.0
         * @access public
         */
        public function houzez_enqueue_scripts() {
            $js_path = 'assets/frontend/js/';
            wp_register_script( 'houzez-validate-js', HOUZEZ_PLUGIN_URL . $js_path . 'houzez-validate.js', array( 'jquery' ), '1.0.0' );
        }

        public function enqueue_editor_scripts() {
            $js_path = 'assets/frontend/js/';

            // Check if we're in Elementor edit mode
            $is_edit_mode = false;

            // Method 1: Standard check
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                $is_edit_mode = true;
            }
            
            // Method 2: Through preview or request
            if (!$is_edit_mode) {
                if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
                    $is_edit_mode = true;
                } elseif (isset($_GET['elementor-preview'])) {
                    $is_edit_mode = true;
                }
            }

            // Enqueue script if in Elementor edit mode
            if ($is_edit_mode) {
                wp_enqueue_script('houzez-widgets-hooks', HOUZEZ_PLUGIN_URL.$js_path . 'widgets-hooks.js', [], '1.0.0', true);
            }
        }

        /**
         * Enqueue scripts
         *
         * @since  1.0.0
         * @access public
         */
        public function enqueue_editor_styles() {
            $js_path = 'assets/frontend/css/';
            wp_enqueue_style( 'houzez-ele-editor', HOUZEZ_PLUGIN_URL.$js_path . 'ele-editor.css', [], '1.0.0' );
        }

        /**
         * Enqueue Styles
         *
         * @since  2.2.0
         * @access public
         */
        public function enqueue_frontend_styles() { 
            
        }

    }
}

if ( did_action( 'elementor/loaded' ) ) {
    // Finally initialize code
    Houzez_Elementor_Extensions::instance();
}