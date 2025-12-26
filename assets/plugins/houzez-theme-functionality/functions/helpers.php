<?php
// Remove namespace declaration to make functions globally accessible
use Elementor\Core\Files\Assets\Svg\Svg_Handler;
use Elementor\Utils;


if (!function_exists('houzez_get_property_cards')) {
    /**
     * Core function to render property cards
     *
     * @param array $attributes Shortcode/widget attributes
     * @param string $module_type The module type (grid_3_cols, grid_4_cols, etc.)
     * @param string $card_version The card version (v1, v2, etc.)
     * @return string HTML output
     */
    function houzez_get_property_cards($attributes, $module_type = 'grid_3_cols', $card_version = 'v1') {
        // Start output buffering
        ob_start();
        
        // Set up global variables
        global $paged, $ele_thumbnail_size, $hide_button, $hide_author_date, $hide_author, $hide_date;
        $ele_thumbnail_size = isset($attributes['thumb_size']) ? $attributes['thumb_size'] : '';
        $hide_button = isset($attributes['hide_button']) ? $attributes['hide_button'] : '';
        $hide_author_date = isset($attributes['hide_author_date']) ? $attributes['hide_author_date'] : '';
        $hide_author = isset($attributes['hide_author']) ? $attributes['hide_author'] : '';
        $hide_date = isset($attributes['hide_date']) ? $attributes['hide_date'] : '';
        
        // Handle pagination for front page
        if (is_front_page()) {
            $paged = (get_query_var('page')) ? get_query_var('page') : 1;
        }

        // Process any tab parameters from URL
        $attributes = houzez_process_tab_parameters($attributes);
        
        // Get layout classes based on module type and card version
        list($cols_class, $item_view, $wrapper_class) = houzez_get_layout_classes($module_type, $card_version);

        // Get the query
        $the_query = Houzez_Data_Source::get_wp_query($attributes, $paged);
        
        // Render the property cards
        houzez_render_property_cards($the_query, $cols_class, $item_view, $attributes, $wrapper_class, $card_version);
        
        // Get the buffered content and return it
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }
}

/**
 * Render property cards and pagination
 *
 * @param WP_Query $the_query The WordPress query
 * @param string $cols_class CSS classes for columns
 * @param string $item_view The item view type
 * @param array $attributes Shortcode attributes
 * @param string $btn_class CSS class for buttons
 * @param string $wrapper_class CSS class for wrapper
 * @param string $card_version The card version (v1, v2, etc.)
 */
function houzez_render_property_cards($the_query, $cols_class, $item_view, $attributes, $wrapper_class, $card_version = 'v1') {
    ?>
    <div id="properties_module_section" class="property-cards-module property-cards-module-<?php echo esc_attr($card_version); ?> <?php echo esc_attr($wrapper_class); ?>">
        <div id="module_properties" class="<?php echo esc_attr($cols_class); ?>">
            <?php
            if ($the_query->have_posts()) :
                while ($the_query->have_posts()) : $the_query->the_post();
                    get_template_part('template-parts/listing/item', $item_view);
                endwhile;
            else:
                get_template_part('template-parts/listing/item', 'none');
            endif;
            wp_reset_postdata();
            ?>
        </div><!-- listing-view -->

        <?php houzez_render_pagination($the_query, $attributes, $item_view); ?>
    </div><!-- property-grid-module -->
    <?php
}

/**
 * Process tab parameters from URL
 *
 * @param array $attributes Shortcode attributes
 * @return array Modified attributes
 */
function houzez_process_tab_parameters($attributes) {
    $tabs_taxonomy = isset($_GET['tax']) ? $_GET['tax'] : '';
    $tab = isset($_GET['tab']) ? $_GET['tab'] : '';
    
    if ($tabs_taxonomy == 'property_status' && !empty($tab)) {
        $attributes['property_status'] = $tab;
    } elseif ($tabs_taxonomy == 'property_type' && !empty($tab)) {
        $attributes['property_type'] = $tab;
    } elseif ($tabs_taxonomy == 'property_city' && !empty($tab)) {
        $attributes['property_city'] = $tab;
    }
    
    return $attributes;
}

/**
 * Normalize card version format
 * 
 * @param string $version Card version in any format (v1, v_1, etc.)
 * @return string Normalized version (v1, v2, etc.)
 */
function houzez_normalize_card_version($version) {
    // Convert v_1, v_2, etc. to v1, v2, etc.
    if (strpos($version, 'v_') === 0) {
        return 'v' . substr($version, 2);
    }
    return $version;
}

/**
 * Get layout classes based on module type
 *
 * @param string $module_type The module type
 * @param string $card_version The card version (v1, v2, v_1, v_2, etc.)
 * @return array Array containing column class, item view, and wrapper class
 */
function houzez_get_layout_classes($module_type, $card_version = 'v1') {
    // Default values
    $cols_class = '';
    $wrapper_class = '';
    
    // Normalize card version format (convert v_1 to v1, etc.)
    $card_version = houzez_normalize_card_version($card_version);
    
    // Set the item view to the normalized card version
    $item_view = $card_version;
    
    // Normalize module type (convert simplified formats to standard formats)
    if ($module_type == '3cols') {
        $module_type = 'grid_3_cols';
    } elseif ($module_type == '2cols') {
        $module_type = 'grid_2_cols';
    } elseif ($module_type == '4cols') {
        $module_type = 'grid_4_cols';
    }
    
    // Configuration for grid layouts
    $grid_layouts = [
        'grid_3_cols' => [
            'class' => 'listing-view grid-view row row-cols-1 row-cols-md-2 row-cols-lg-3 gy-4 gx-4',
            'wrapper' => 'property-cards-module-col-3'
        ],
        'grid_4_cols' => [
            'class' => 'listing-view grid-view row row-cols-1 row-cols-md-2 row-cols-lg-4 gy-4 gx-4',
            'wrapper' => 'property-cards-module-col-4'
        ],
        'grid_2_cols' => [
            'class' => 'listing-view grid-view row row-cols-1 row-cols-md-2 row-cols-lg-2 gy-4 gx-4',
            'wrapper' => 'property-cards-module-col-2'
        ],
        'grid_1_col' => [
            'class' => 'listing-view grid-view',
            'wrapper' => 'property-cards-module-col-1'
        ]
    ];
    
    // Configuration for list layouts
    $list_layouts = [
        'v1' => [
            'class' => 'listing-view list-view d-flex flex-column gap-3',
            'wrapper' => 'property-cards-module-list',
            'view' => 'list-v1'
        ],
        'v2' => [
            'class' => 'listing-view list-view d-flex flex-column gap-3',
            'wrapper' => 'property-cards-module-list',
            'view' => 'list-v2'
        ],
        'v7' => [
            'class' => 'listing-view list-view d-flex flex-column gap-3',
            'wrapper' => 'property-cards-module-list',
            'view' => 'list-v7'
        ],
        'v8' => [
            'class' => 'listing-view list-view d-flex flex-column gap-3',
            'wrapper' => '',
            'view' => 'list-v4'
        ]
    ];
    
    // Special case for v8 which is always list view
    if ($card_version == 'v8') {
        return [$list_layouts['v8']['class'], $list_layouts['v8']['view'], $list_layouts['v8']['wrapper']];
    }
    
    // Handle list view for all versions
    if ($module_type == 'list' && isset($list_layouts[$card_version])) {
        return [
            $list_layouts[$card_version]['class'],
            $list_layouts[$card_version]['view'],
            $list_layouts[$card_version]['wrapper']
        ];
    }
    
    // Handle grid views
    if (isset($grid_layouts[$module_type])) {
        $cols_class = $grid_layouts[$module_type]['class'];
        $wrapper_class = $grid_layouts[$module_type]['wrapper'];
    } else {
        // Default to 3 columns grid if module type is not recognized
        $cols_class = $grid_layouts['grid_3_cols']['class'];
        $wrapper_class = $grid_layouts['grid_3_cols']['wrapper'];
    }
    
    return [$cols_class, $item_view, $wrapper_class];
}

// Functions that should be globally accessible
if (!function_exists('houzez_render_pagination')) {
    /**
     * Render pagination
     *
     * @param WP_Query $the_query The WordPress query
     * @param array $attributes Shortcode attributes
     * @param string $btn_class CSS class for buttons
     * @param string $item_view The item view type (e.g., 'v1', 'v2', etc.)
     */
    function houzez_render_pagination($the_query, $attributes, $item_view = 'v1') {
        $pagination_type = $attributes['pagination_type'] ?? 'number';
        $posts_found = $the_query->found_posts;
        
        if ($pagination_type == 'number') { 
            houzez_pagination($the_query->max_num_pages, $range = 2);
        } elseif ($pagination_type == 'loadmore' || $pagination_type == 'infinite_scroll') {
            houzez_render_load_more_button($attributes, $posts_found, $item_view);
        }
    }
}

if (!function_exists('houzez_render_load_more_button')) {
    /**
     * Render load more button for property listings
     *
     * @param array $attributes Shortcode attributes
     * @param string $item_view The item view type (e.g., 'v1', 'v2', etc.)
     * @return void
     */
    function houzez_render_load_more_button($attributes, $posts_found,$item_view = 'v1') {
        if (!isset($attributes['pagination_type'])) {
            return;
        }

        if($posts_found <= $attributes['posts_limit']) {
            return;
        }

        $pagination_type = $attributes['pagination_type'];
        $btn_class = ($pagination_type == 'loadmore') ? 'btn-primary-outlined' : '';
        
        // Merge with default attributes
        $attributes = wp_parse_args($attributes, houzez_get_property_card_default_attributes());

        // Define attributes that should be excluded from data attributes
        $excluded_attributes = array(
            'slide_dots',
            'navigation',
            'auto_speed',
            'slide_auto',
            'slide_infinite',
            'slides_to_scroll',
            'slides_to_show',
            'all_url',
            'all_btn',
            'hide_author',
            'hide_author_date',
            'hide_button',
            'module_type',
            'thumb_size'
        );
        
        // Build data attributes HTML
        $data_attributes = '';
        foreach ($attributes as $key => $value) {
            // Skip pagination_type and posts_limit as they're handled separately
            // Also skip excluded attributes
            if ($key != 'posts_limit' && $key != 'pagination_type' && !in_array($key, $excluded_attributes)) {
                $data_attributes .= 'data-' . esc_attr($key) . '="' . esc_attr($value) . '" ';
            }
        }
        ?>
        <div id="fave-pagination-loadmore" class="load-more-wrap mb-4 fave-load-more">
            <a class="btn btn-load-more <?php echo esc_attr($btn_class); ?>"
               data-pagination_type="<?php echo esc_attr($pagination_type); ?>"
               data-paged="2" 
               data-posts_limit="<?php echo esc_attr($attributes['posts_limit']); ?>" 
               data-card="<?php echo esc_attr($item_view); ?>" 
               <?php echo $data_attributes; ?>
            >
            <?php 
                if ($pagination_type == 'infinite_scroll') {
                    get_template_part('template-parts/loader-dots');
                } else {
                    get_template_part('template-parts/loader');
                    esc_html_e('Load More', 'houzez'); 
                }
            ?> 
            </a>
        </div>
        <?php
    }
}

if (!function_exists('houzez_render_load_more_button_old')) {
    /**
     * Render load more button
     *
     * @param array $attributes Shortcode attributes
     * @param string $item_view The item view type (e.g., 'v1', 'v2', etc.)
     */
    function houzez_render_load_more_button_old($attributes, $item_view = 'v1') {
        if (!isset($attributes['pagination_type'])) {
            return;
        }
        
        $pagination_type = $attributes['pagination_type'];
        $btn_class = ($pagination_type == 'loadmore') ? 'btn-primary-outlined' : '';
        
        // Merge with default attributes
        $attributes = wp_parse_args($attributes, houzez_get_property_card_default_attributes());
        
        ?>
        <div id="fave-pagination-loadmore" class="load-more-wrap mb-4 fave-load-more">
            <a class="btn btn-load-more <?php echo esc_attr($btn_class); ?>"
               data-pagination-type="<?php echo esc_attr($pagination_type); ?>"
               data-paged="2" 
               data-prop-limit="<?php echo esc_attr($attributes['posts_limit']); ?>" 
               data-card="item-<?php echo esc_attr($item_view); ?>" 
               data-type="<?php echo esc_attr($attributes['property_type']); ?>" 
               data-status="<?php echo esc_attr($attributes['property_status']); ?>" 
               data-state="<?php echo esc_attr($attributes['property_state']); ?>" 
               data-city="<?php echo esc_attr($attributes['property_city']); ?>" 
               data-country="<?php echo esc_attr($attributes['property_country']); ?>" 
               data-area="<?php echo esc_attr($attributes['property_area']); ?>" 
               data-label="<?php echo esc_attr($attributes['property_label']); ?>" 
               data-user-role="<?php echo esc_attr($attributes['houzez_user_role']); ?>" 
               data-featured-prop="<?php echo esc_attr($attributes['featured_prop']); ?>" 
               data-offset="<?php echo esc_attr($attributes['offset']); ?>"
               data-sortby="<?php echo esc_attr($attributes['sort_by']); ?>"
               data-property_ids="<?php echo esc_attr($attributes['property_ids']); ?>"
               data-min_price="<?php echo esc_attr($attributes['min_price']); ?>"
               data-max_price="<?php echo esc_attr($attributes['max_price']); ?>"
               data-min_beds="<?php echo esc_attr($attributes['min_beds']); ?>"
               data-max_beds="<?php echo esc_attr($attributes['max_beds']); ?>"
               data-min_baths="<?php echo esc_attr($attributes['min_baths']); ?>"
               data-max_baths="<?php echo esc_attr($attributes['max_baths']); ?>"
               data-agents="<?php echo esc_attr($attributes['properties_by_agents']); ?>"
               data-agencies="<?php echo esc_attr($attributes['properties_by_agencies']); ?>"
               data-post_status="<?php echo esc_attr($attributes['post_status']); ?>"
               href="#">
                <?php 
                if ($pagination_type == 'infinite_scroll') {
                    get_template_part('template-parts/loader-dots');
                } else {
                    get_template_part('template-parts/loader');
                    esc_html_e('Load More', 'houzez'); 
                }
                ?>        
            </a>               
        </div><!-- load-more-wrap -->
        <?php
    }
}

/**
 * Get default property card attributes
 *
 * @return array Default attributes for property cards
 */
if( ! function_exists('houzez_get_property_card_default_attributes') ) {
    function houzez_get_property_card_default_attributes() {
        return array(
            'module_type' => '',
            'property_type' => '',
            'property_status' => '',
            'property_country' => '',
            'property_state' => '',
            'property_city' => '',
            'property_area' => '',
            'property_label' => '',
            'property_ids' => '',
            'property_id' => '',
            'hide_button' => '',
            'hide_author_date' => '',
            'houzez_user_role' => '',
            'featured_prop' => '',
            'posts_limit' => '',
            'sort_by' => '',
            'offset' => '',
            'pagination_type' => '',
            'post_status' => '',
            'thumb_size' => '',
            'min_price' => '',
            'max_price' => '',
            'min_beds' => '',
            'max_beds' => '',
            'min_baths' => '',
            'max_baths' => '',
            'properties_by_agents' => '',
            'properties_by_agencies' => ''
        );
    }
}

if (!function_exists('houzez_get_property_carousel')) {
    /**
     * Core function to render property carousels
     *
     * @param array $attributes Shortcode/widget attributes
     * @param string $carousel_version The carousel version (v1, v2, etc.)
     * @return string HTML output
     */
    function houzez_get_property_carousel($attributes, $carousel_version = 'v1') {
        // Start output buffering
        ob_start();
        
        // Set up global variables
        global $post, $ele_thumbnail_size, $hide_button, $hide_author_date, $hide_date;
        $ele_thumbnail_size = isset($attributes['thumb_size']) ? $attributes['thumb_size'] : '';
        
        // Set default values
        $token = wp_generate_password(5, false, false);
        $hide_button = isset($attributes['hide_button']) ? $attributes['hide_button'] : '';
        $hide_author_date = isset($attributes['hide_author_date']) ? $attributes['hide_author_date'] : '';
        $hide_date = isset($attributes['hide_date']) ? $attributes['hide_date'] : '';
        $slides_to_show = isset($attributes['slides_to_show']) ? $attributes['slides_to_show'] : 3;
        $slides_to_scroll = isset($attributes['slides_to_scroll']) ? $attributes['slides_to_scroll'] : 1;
        $slide_auto = isset($attributes['slide_auto']) ? $attributes['slide_auto'] : 'false';
        $slide_infinite = isset($attributes['slide_infinite']) ? $attributes['slide_infinite'] : 'false';
        $auto_speed = isset($attributes['auto_speed']) ? $attributes['auto_speed'] : 3000;
        $navigation = isset($attributes['navigation']) ? $attributes['navigation'] : 'true';
        $nav_position = isset($attributes['nav_position']) ? $attributes['nav_position'] : '';
        $slide_dots = isset($attributes['slide_dots']) ? $attributes['slide_dots'] : 'false';
        $all_url = isset($attributes['all_url']) ? $attributes['all_url'] : '';
        $all_btn = isset($attributes['all_btn']) ? $attributes['all_btn'] : esc_html__('View All', 'houzez');
        
        // Generate CSS class for columns
        $columns_class = 'property-carousel-module-'.$carousel_version.'-'.$slides_to_show.'cols';
        
        // Normalize carousel version format
        $carousel_version = houzez_normalize_card_version($carousel_version);
        
        // Load JS for carousel functionality
        $minify_js = houzez_option('minify_js');
        $js_minify_prefix = '';
        if ($minify_js != 0) {
            $js_minify_prefix = '.min';
        }
        
        wp_register_script('houzez_prop_carousel', get_theme_file_uri('/js/property-carousels'.$js_minify_prefix.'.js'), array('jquery'), HOUZEZ_THEME_VERSION, true);
        $local_args = array(
            'slide_auto' => $slide_auto,
            'auto_speed' => $auto_speed,
            'navigation' => $navigation,
            'slide_dots' => $slide_dots,
            'slide_infinite' => $slide_infinite,
            'slides_to_show' => $slides_to_show,
            'slides_to_scroll' => $slides_to_scroll,
        );
        wp_localize_script('houzez_prop_carousel', 'houzez_prop_carousel_' . $token, $local_args);
        wp_enqueue_script('houzez_prop_carousel');
        
        // Get the query
        $the_query = Houzez_Data_Source::get_wp_query($attributes);
        
        // Render the carousel
        ?>
        <div class="property-carousel-module property-carousel-module-<?php echo esc_attr($carousel_version); ?> <?php echo esc_attr($columns_class); ?> houzez-carousel-arrows-<?php echo esc_attr($token); ?>">
            <div class="property-carousel-buttons-wrap <?php echo esc_attr($nav_position); ?>">
                <?php if($navigation != "false") { ?>
                <button type="button" class="slick-prev-js-<?php echo esc_attr($token); ?> slick-prev btn-primary-outlined"><?php esc_html_e('Prev', 'houzez'); ?></button>
                <button type="button" class="slick-next-js-<?php echo esc_attr($token); ?> slick-next btn-primary-outlined"><?php esc_html_e('Next', 'houzez'); ?></button>
                <?php } ?>
                <?php if($all_url != '') { ?>
                <a href="<?php echo esc_url($all_url); ?>" class="btn btn-primary-outlined btn-view-all"><?php echo esc_attr($all_btn); ?></a>
                <?php } ?>
            </div><!-- property-carousel-buttons-wrap -->

            <div class="listing-view grid-view">
                <div id="houzez-properties-carousel-<?php echo esc_attr($token); ?>" data-token="<?php echo esc_attr($token); ?>" class="houzez-properties-carousel-js houzez-all-slider-wrap property-carousel-wrap-<?php echo esc_attr($carousel_version); ?>-<?php echo esc_attr($slides_to_show); ?>cols">
                    <?php
                    if ($the_query->have_posts()) : 
                        while ($the_query->have_posts()) : $the_query->the_post();
                            get_template_part('template-parts/listing/item', $carousel_version);
                        endwhile; 
                    endif;
                    wp_reset_postdata();
                    ?>
                </div><!-- carousel-wrap -->
            </div><!-- listing-view grid-view -->
            
            <?php if (isset($attributes['add_style']) && $attributes['add_style'] == 'yes') : ?>
            <style>
                .slick-slide {
                    padding-left: 10px; /* space between slides */
                    padding-right: 10px; /* must be equal left and right */
                }
            </style>
            <?php endif; ?>
        </div><!-- property-carousel-module -->
        <?php
        
        // Get the buffered content and return it
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }
}

/**
 * Get default property carousel attributes
 *
 * @return array Default attributes for property carousels
 */
if (!function_exists('houzez_get_property_carousel_default_attributes')) {
    function houzez_get_property_carousel_default_attributes() {
        return array_merge(houzez_get_property_card_default_attributes(), array(
            'slides_to_show' => '3',
            'slides_to_scroll' => '1',
            'slide_auto' => 'false',
            'slide_infinite' => 'false',
            'auto_speed' => '3000',
            'navigation' => 'true',
            'slide_dots' => 'false',
            'all_btn' => esc_html__('View All', 'houzez'),
            'all_url' => '',
            'add_style' => 'no'
        ));
    }
}

/**
 * Factory function to create shortcodes
 *
 * @param string $function_name The function name to register
 * @param string $shortcode_name The shortcode name to register
 * @param string $card_version Optional card version for property cards or carousel version (v1, v2, etc.)
 * @return void
 */
if( ! function_exists('houzez_create_shortcode') ) {
    function houzez_create_shortcode($function_name, $shortcode_name, $card_version = '') {
        // Check if the function already exists
        if (function_exists($function_name)) {
            return;
        }
        
        // For property card shortcodes
        if (strpos($function_name, 'houzez_property_card_') === 0) {
            // Extract version if not provided but included in function name
            if (empty($card_version) && strpos($function_name, 'houzez_property_card_') === 0) {
                $card_version = str_replace('houzez_property_card_', '', $function_name);
            }
            
            $function = function($atts, $content = null) use ($card_version) {
                // Parse shortcode attributes with defaults
                $attributes = shortcode_atts(houzez_get_property_card_default_attributes(), $atts);
                
                // Use the core function to render property cards
                return houzez_get_property_cards($attributes, $attributes['module_type'], $card_version);
            };
            
            // Register the function in the global namespace
            $GLOBALS[$function_name] = $function;
            
            // Add the shortcode
            add_shortcode($shortcode_name, $function);
        }
        // For property carousel shortcodes
        else if (strpos($function_name, 'houzez_property_carousel_') === 0) {
            // Extract version if not provided but included in function name
            if (empty($card_version) && strpos($function_name, 'houzez_property_carousel_') === 0) {
                $card_version = str_replace('houzez_property_carousel_', '', $function_name);
            }
            
            $function = function($atts, $content = null) use ($card_version) {
                // Parse shortcode attributes with defaults
                $attributes = shortcode_atts(houzez_get_property_carousel_default_attributes(), $atts);
                
                // Use the core function to render property carousels
                return houzez_get_property_carousel($attributes, $card_version);
            };
            
            // Register the function in the global namespace
            $GLOBALS[$function_name] = $function;
            
            // Add the shortcode
            add_shortcode($shortcode_name, $function);
        }
        else {
            // For shortcodes that rely on their own implementation files
            $shortcode_file = HOUZEZ_PLUGIN_DIR . 'shortcodes/' . str_replace('houzez_', '', $function_name) . '.php';
            
            // If the file exists, just require it - it will register itself
            if (file_exists($shortcode_file)) {
                require_once $shortcode_file;
            }
            // Otherwise, create an empty handler
            else {
                $function = function($atts, $content = null) {
                    return ''; // Return empty for now
                };
                
                // Register the function in the global namespace
                $GLOBALS[$function_name] = $function;
                
                // Add the shortcode
                add_shortcode($shortcode_name, $function);
            }
        }
    }
}

/**
 * Register all shortcodes
 * 
 * @return void
 */
if( ! function_exists('houzez_register_all_shortcodes') ) {
    function houzez_register_all_shortcodes() {
        // Register property card shortcodes
        houzez_create_shortcode('houzez_property_card_v1', 'houzez_property_card_v1', 'v1');
        houzez_create_shortcode('houzez_property_card_v2', 'houzez_property_card_v2', 'v2');
        houzez_create_shortcode('houzez_property_card_v3', 'houzez_property_card_v3', 'v3');
        houzez_create_shortcode('houzez_property_card_v4', 'houzez_property_card_v4', 'v4');
        houzez_create_shortcode('houzez_property_card_v5', 'houzez_property_card_v5', 'v5');
        houzez_create_shortcode('houzez_property_card_v6', 'houzez_property_card_v6', 'v6');
        houzez_create_shortcode('houzez_property_card_v7', 'houzez_property_card_v7', 'v7');
        houzez_create_shortcode('houzez_property_card_v8', 'houzez_property_card_v8', 'v8');
        
        // Register property carousel shortcodes
        houzez_create_shortcode('houzez_property_carousel_v1', 'houzez-prop-carousel-v1', 'v1');
        houzez_create_shortcode('houzez_property_carousel_v2', 'houzez-prop-carousel-v2', 'v2');
        houzez_create_shortcode('houzez_property_carousel_v3', 'houzez-prop-carousel-v3', 'v3');
        houzez_create_shortcode('houzez_property_carousel_v5', 'houzez-prop-carousel-v5', 'v5');
        houzez_create_shortcode('houzez_property_carousel_v6', 'houzez-prop-carousel-v6', 'v6');
        houzez_create_shortcode('houzez_property_carousel_v7', 'houzez-prop-carousel-v7', 'v7');

        // Legacy shortcodes for WP Bakery Page Builder
        houzez_create_shortcode('houzez_property_carousel_v1', 'houzez-prop-carousel-v2', 'v1');
        houzez_create_shortcode('houzez_property_carousel_v2', 'houzez-prop-carousel-v2n', 'v2');
        houzez_create_shortcode('houzez_property_carousel_v3', 'houzez-prop-carousel', 'v3');
        
        
        // houzez_create_shortcode('houzez_property_carousel_v2', 'houzez-prop-carousel-v2', 'v2');
        // houzez_create_shortcode('houzez_property_carousel_v3', 'houzez-prop-carousel-v3', 'v3');
        // houzez_create_shortcode('houzez_property_carousel_v5', 'houzez-prop-carousel-v5', 'v5');
        // houzez_create_shortcode('houzez_property_carousel_v6', 'houzez-prop-carousel-v6', 'v6');
        // houzez_create_shortcode('houzez_property_carousel_v7', 'houzez-prop-carousel-v7', 'v7');
        
        // Register other common shortcodes
        houzez_create_shortcode('houzez_property_by_id', 'houzez-property-by-id');
        houzez_create_shortcode('houzez_property_by_ids', 'houzez-property-by-ids');
        houzez_create_shortcode('houzez_recent_viewed_properties', 'houzez-recent-viewed-properties');
        houzez_create_shortcode('houzez_grids', 'hz-grids');
        houzez_create_shortcode('houzez_partners', 'houzez-partners');
        houzez_create_shortcode('houzez_agents', 'houzez-agents');
        houzez_create_shortcode('houzez_agents_grid', 'houzez-agents-grid');
        houzez_create_shortcode('houzez_team_member', 'houzez-team-member');
        houzez_create_shortcode('houzez_properties', 'houzez-properties');
        houzez_create_shortcode('houzez_properties_grids', 'houzez-properties-grids');
        
        // Taxonomy displays
        houzez_create_shortcode('houzez_taxonomies_cards', 'houzez-taxonomies-cards');
        houzez_create_shortcode('houzez_taxonomies_cards_carousel', 'houzez-taxonomies-cards-carousel');
        houzez_create_shortcode('houzez_taxonomies_grids', 'houzez-taxonomies-grids');
        houzez_create_shortcode('houzez_taxonomies_grids_carousel', 'houzez-taxonomies-grids-carousel');
        houzez_create_shortcode('houzez_taxonomies_list', 'houzez-taxonomies-list');
        
        // Testimonials
        houzez_create_shortcode('houzez_testimonials', 'houzez-testimonials');
        houzez_create_shortcode('houzez_testimonials_v2', 'houzez-testimonials-v2');
        houzez_create_shortcode('houzez_testimonials_v3', 'houzez-testimonials-v3');
        
        // Blog
        houzez_create_shortcode('houzez_blog_posts', 'houzez-blog-posts');
        houzez_create_shortcode('houzez_blog_posts_carousel', 'houzez-blog-posts-carousel');
        
        // Other
        houzez_create_shortcode('houzez_advance_search', 'houzez-advance-search');
        houzez_create_shortcode('houzez_search', 'houzez-search');
        houzez_create_shortcode('houzez_price_table', 'houzez-price-table');
        houzez_create_shortcode('houzez_section_title', 'houzez-section-title');
        houzez_create_shortcode('houzez_space', 'houzez-space');
    }
}

// Register this function to run during init to ensure all shortcodes are available
add_action('init', 'houzez_register_all_shortcodes', 10);

if(!function_exists('houzez_realtor_social')) {
    function houzez_realtor_social() {
        $array = array(
            'fave_agent_whatsapp',
            'fave_agent_line_id',
            'fave_agent_telegram',
            'fave_agent_skype',
            'fave_agent_zillow',
            'fave_agent_realtor_com',
            'fave_agent_facebook',
            'fave_agent_twitter',
            'fave_agent_linkedin',
            'fave_agent_googleplus',
            'fave_agent_tiktok',
            'fave_agent_instagram',
            'fave_agent_pinterest',
            'fave_agent_youtube',
            'fave_agent_vimeo',
            'fave_agency_whatsapp',
            'fave_agency_line_id',
            'fave_agency_telegram',
            'fave_agency_skype',
            'fave_agency_zillow',
            'fave_agency_realtor_com',
            'fave_agency_facebook',
            'fave_agency_twitter',
            'fave_agency_linkedin',
            'fave_agency_googleplus',
            'fave_agency_tiktok',
            'fave_agency_instagram',
            'fave_agency_pinterest',
            'fave_agency_youtube',
            'fave_agency_vimeo',
        );
        return $array;
    }
}

if( ! function_exists('houzez_render_icon') ) {
    function houzez_render_icon( $icon, $attributes = [], $tag = 'i' ) {

        if ( empty( $icon['library'] ) ) {
            return false;
        }

        $output = '';
        // handler SVG Icon
        if ( 'svg' === $icon['library'] ) {
            $output = houzez_render_svg_icon( $icon['value'] );
        } else {
            $output = houzez_render_icon_html( $icon, $attributes, $tag );
        }

        return $output;
    }
}

if( ! function_exists('houzez_render_icon_html') ) {
    function houzez_render_icon_html( $icon, $attributes = [], $tag = 'i' ) {
        $icon_types = \Elementor\Icons_Manager::get_icon_manager_tabs();
        if ( isset( $icon_types[ $icon['library'] ]['render_callback'] ) && is_callable( $icon_types[ $icon['library'] ]['render_callback'] ) ) {
            return call_user_func_array( $icon_types[ $icon['library'] ]['render_callback'], [ $icon, $attributes, $tag ] );
        }

        if ( empty( $attributes['class'] ) ) {
            $attributes['class'] = $icon['value'];
        } else {
            if ( is_array( $attributes['class'] ) ) {
                $attributes['class'][] = $icon['value'];
            } else {
                $attributes['class'] .= ' ' . $icon['value'];
            }
        }
        return '<' . $tag . ' ' . Utils::render_html_attributes( $attributes ) . '></' . $tag . '>';
    }
}

if( ! function_exists('houzez_render_svg_icon') ) {
    function houzez_render_svg_icon( $value ) {
        if ( ! isset( $value['id'] ) ) {
            return '';
        }

        return Svg_Handler::get_inline_svg( $value['id'] );
    }
}

if( ! function_exists('htb_get_template_type') ) {
    function htb_get_template_type($post_id = '') {
        $post = get_post($post_id);
        if($post && get_post_type($post) === 'fts_builder') {
            $meta = get_post_meta( $post_id, 'fts_template_type', true );
            if( ! empty( $meta ) ) {
                return $meta;
            } else{
                return 'content';
            }
        }
        return false;
    }
}
