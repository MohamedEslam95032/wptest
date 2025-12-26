<?php
namespace HouzezStudio;
use Elementor\Controls_Manager;
/*use Elementor\Core\DynamicTags\Base_Tag;
use Elementor\Core\DynamicTags\Dynamic_CSS;
use Elementor\Core\Files\CSS\Post;*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( ! class_exists( 'FTS_Elementor' ) ) {
    final class FTS_Elementor {

        const HOUZEZ_GROUP = 'houzez_studio';

        /**
         * Current theme template
         *
         * @var String
         */
        public $template;

        /**
         * Instance of Elemenntor Frontend class.
         *
         * @var \Elementor\Frontend()
         */
        private static $elementor_instance;

        /**
         * The single instance of the class.
         *
         * @var FTS_Elementor
         * @since 1.0
         */
        private static $_instance;

        /**
         * Main FTS_Elementor Instance.
         *
         * Ensures only one instance of FTS_Elementor is loaded or can be loaded.
         *
         * @since 1.0
         * @static
         * @return FTS_Elementor - Main instance.
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Constructor function.
         * @access  public
         * @since   1.0.0
         * @return  void
         */
        public function __construct() {
            if ( defined( 'ELEMENTOR_VERSION' ) && is_callable( 'Elementor\Plugin::instance' ) ) {
                self::$elementor_instance = \Elementor\Plugin::instance();
                // Scripts and styles.
                add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
                add_shortcode( 'fts_template', array( $this, 'render_shortcode' ) );

                //add_action( 'elementor/documents/register_controls', array( $this, 'add_preview_settings_section' ) );
            }
        }

        /**
         * Enqueue styles and scripts.
         */
        public function enqueue_scripts() {

            if (! class_exists( '\Elementor\Plugin' ) ) {
                return;
            }
            
            // Skip on admin pages to prevent timeouts
            if ( is_admin() ) {
                return;
            }
            
            // Skip on non-singular pages for performance
            if ( ! is_singular() && ! is_home() && ! is_front_page() && ! is_archive() ) {
                return;
            }

            // Enqueue Elementor and Elementor Pro styles
            $elementor = \Elementor\Plugin::instance();
            $elementor->frontend->enqueue_styles();

            if ( class_exists( '\ElementorPro\Plugin' ) ) {
                $elementor_pro = \ElementorPro\Plugin::instance();
                if ( method_exists( $elementor_pro, 'enqueue_styles' ) ) {
                    $elementor_pro->enqueue_styles();
                }
            }

            // Only load templates that are needed for the current page type
            $section_ids = [];
            
            // Always load header and footer on frontend
            if ( ! is_admin() ) {
                $header_id = fts_get_header_id();
                if ( $header_id ) {
                    $section_ids['header'] = $header_id;
                }
                
                $footer_id = fts_get_footer_id();
                if ( $footer_id ) {
                    $section_ids['footer'] = $footer_id;
                }
                
                // Only load before/after header/footer if they exist
                if ( $before_header = fts_get_before_header_id() ) {
                    $section_ids['before_header'] = $before_header;
                }
                if ( $after_header = fts_get_after_header_id() ) {
                    $section_ids['after_header'] = $after_header;
                }
                if ( $before_footer = fts_get_before_footer_id() ) {
                    $section_ids['before_footer'] = $before_footer;
                }
                if ( $after_footer = fts_get_after_footer_id() ) {
                    $section_ids['after_footer'] = $after_footer;
                }
            }
            
            // Only load single templates on relevant single pages
            if ( is_singular() ) {
                $post_type = get_post_type();
                
                if ( $post_type === 'property' && ( $single_id = fts_get_single_listing_id() ) ) {
                    $section_ids['single_listing'] = $single_id;
                } elseif ( $post_type === 'houzez_agent' && ( $single_id = fts_get_single_agent_id() ) ) {
                    $section_ids['single_agent'] = $single_id;
                } elseif ( $post_type === 'houzez_agency' && ( $single_id = fts_get_single_agency_id() ) ) {
                    $section_ids['single_agency'] = $single_id;
                } elseif ( $post_type === 'post' && ( $single_id = fts_get_single_post_id() ) ) {
                    $section_ids['single_post'] = $single_id;
                }
            }

            // Enqueue only the needed styles
            foreach ($section_ids as $id) {
                $this->enqueue_section_styles($id);
            }
        }

        /**
         * Enqueue styles for a specific section if the section ID is valid.
         *
         * @param int|false $section_id The ID of the section.
         */
        private function enqueue_section_styles($section_id) {
            if (!$section_id) {
                return;
            }
            
            // Check if the post exists and is published
            $post = get_post($section_id);
            if (!$post || $post->post_status !== 'publish') {
                return;
            }
            
            try {
                // Use transient to cache CSS file generation status
                $cache_key = 'fts_css_generated_' . $section_id;
                $css_generated = get_transient($cache_key);
                
                if ($css_generated === false) {
                    if (class_exists('\Elementor\Core\Files\CSS\Post')) {
                        $css_file = new \Elementor\Core\Files\CSS\Post($section_id);
                    } elseif (class_exists('\Elementor\Post_CSS_File')) {
                        $css_file = new \Elementor\Post_CSS_File($section_id);
                    } else {
                        return; // Elementor CSS classes not available
                    }
                    
                    // Check if CSS file exists before enqueueing
                    if (method_exists($css_file, 'is_css_file_exists') && !$css_file->is_css_file_exists()) {
                        // Try to generate the CSS file with a timeout
                        if (method_exists($css_file, 'update')) {
                            // Temporarily set a short timeout for CSS generation
                            add_filter('http_request_timeout', function() { return 2; }, 999);
                            $css_file->update();
                            remove_filter('http_request_timeout', function() { return 2; }, 999);
                        }
                    }
                    
                    $css_file->enqueue();
                    
                    // Cache that CSS was generated for 1 hour
                    set_transient($cache_key, true, HOUR_IN_SECONDS);
                } else {
                    // CSS already generated, just enqueue it
                    if (class_exists('\Elementor\Core\Files\CSS\Post')) {
                        $css_file = new \Elementor\Core\Files\CSS\Post($section_id);
                    } elseif (class_exists('\Elementor\Post_CSS_File')) {
                        $css_file = new \Elementor\Post_CSS_File($section_id);
                    } else {
                        return;
                    }
                    $css_file->enqueue();
                }
            } catch (Exception $e) {
                // Log error but don't break the page
                error_log('Houzez Studio: Failed to enqueue CSS for section ' . $section_id . ': ' . $e->getMessage());
            }
        }


        /**
         * Renders content for a shortcode.
         *
         * This method handles the shortcode rendering process by enqueuing necessary styles and
         * retrieving the content generated by Elementor based on the provided shortcode attributes.
         *
         * @param array $atts Attributes for the shortcode.
         * @return string The rendered content.
         */
        public function render_shortcode($atts) {
            // Parse and sanitize the shortcode attributes
            $atts = shortcode_atts(['id' => ''], $atts, 'fts_template');
            $id = !empty($atts['id']) ? intval(apply_filters('fts_render_template_id', $atts['id'])) : '';

            // Return early if the ID is empty
            if (empty($id)) {
                return '';
            }

            // Enqueue the CSS file for the Elementor content, if available
            $this->enqueue_elementor_css($id);

            // Return the content rendered by Elementor
            return self::$elementor_instance->frontend->get_builder_content_for_display($id);
        }

        /**
         * Enqueues Elementor CSS file for a given post ID.
         *
         * @param int $id The post ID.
         */
        private function enqueue_elementor_css($id) {
            if (class_exists('\Elementor\Core\Files\CSS\Post')) {
                $css_file = new \Elementor\Core\Files\CSS\Post($id);
            } elseif (class_exists('\Elementor\Post_CSS_File')) {
                $css_file = new \Elementor\Post_CSS_File($id);
            }

            if (isset($css_file)) {
                $css_file->enqueue();
            }
        }


        /**
         * Get Elemetor Content Template.
         *
         * @param boolean $with_css | with css.
         * @return Header Template.
         */
        public static function get_elementor_template( $id = null, $with_css = false ) {

            $id = !empty($id) ? intval(apply_filters('fts_render_template_id', $id)) : '';
            
            return self::$elementor_instance->frontend->get_builder_content_for_display( $id );
        }

        public function add_preview_settings_section(\Elementor\Controls_Stack $controls_stack) {
            if(houzez_tb_get_template_type(get_the_ID()) === 'single-listing' || houzez_tb_get_template_type(get_the_ID()) === 'loop-item') {
                $controls_stack->start_controls_section(
                    'houzez_preview_settings',
                    [
                        'label' => esc_html__( 'Preview Settings', 'houzez-studio' ),
                        'tab' => Controls_Manager::TAB_SETTINGS,
                    ]
                );

                $post_types = [
                    'post' => __('Post', 'houzez-studio'),
                    'page' => __('Page', 'houzez-studio'),
                ];
                $post_types_data = get_post_types(array(
                    'public' => true,
                    '_builtin' => false
                ), 'objects');

                foreach ($post_types_data as $post_type) {
                    if (!empty($post_type->name) && !in_array($post_type->name, ['elementor_library', 'fts_builder', 'e-landing-page', 'page'])) {
                        $post_types[$post_type->name] = $post_type->label;
                    }
                }

                $controls_stack->add_control(
                    'houzez_preview_type',
                    [
                        'label' => __('Post Type to Preview Dynamic Content', 'houzez-studio'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'label_block' => true,
                        'options' => $post_types,
                        'default' => 'post',
                        'save_always' => 'true',
                    ]
                );

                foreach ($post_types as $post_type_name => $post_type_label) {
                    $controls_stack->add_control(
                        'houzez_preview_post_' . $post_type_name,
                        [
                            'label' => __('Select Post', 'houzez-studio'),
                            'type' => 'houzez_autocomplete',
                            'make_search' => 'houzez_get_posts',
                            'render_result' => 'houzez_render_posts_title',
                            'post_type' => $post_type_name,
                            'label_block' => true,
                            'multiple' => false,
                            'condition' => [
                                'houzez_preview_type' => $post_type_name,
                            ],
                        ]
                    );
                
                }

                $controls_stack->add_control(
                    'houzez_apply_preview',
                    [
                        'type' => Controls_Manager::BUTTON,
                        'label' => esc_html__( 'Apply & Preview', 'houzez-studio' ),
                        'label_block' => true,
                        'show_label' => false,
                        'text' => esc_html__( 'Apply & Preview', 'houzez-studio' ),
                        'separator' => 'none',
                        'event' => 'elementorThemeBuilder:ApplyPreview',
                    ]
                );

                /*if'houzez_get_template_type(get_the_ID()) === 'loop-item') {
                    $controls_stack->add_responsive_control(
                        'houzez_preview_width',
                        [
                            'label' => esc_html__( 'Width', 'houzez-studio' ),
                            'type' => Controls_Manager::SLIDER,
                            'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                            'range' => [
                                'px' => [
                                    'min' => 200,
                                    'max' => 1140,
                                ],
                            ],
                            'selectors' => [
                                '{{WRAPPER}} #main-content .houzez-template-wrapper > .elementor' => 'width: {{SIZE}}{{UNIT}};',
                            ],
                        ]
                    );
                }*/

                $controls_stack->end_controls_section();
            }

        }


    }
}
FTS_Elementor::instance();