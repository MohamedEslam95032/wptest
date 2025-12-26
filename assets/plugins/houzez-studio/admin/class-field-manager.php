<?php
namespace HouzezStudio\admin\fieldsManager;

defined( 'ABSPATH' ) || exit;

class Favethemes_Field_Manager {

	/**
	 * Version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';


	/**
	 * The single instance of the class.
	 *
	 * @var Favethemes_Field_Manager
	 * @since 1.0
	 */
	private static $_instance;

	/**
	 * Meta Option
	 *
	 * @since  1.0.0
	 *
	 * @var $meta_option
	 */
	private static $meta_option;

	/**
	 * Current page type
	 *
	 * @since  1.0.0
	 *
	 * @var $current_page_type
	 */
	private static $current_page_type = null;

	/**
	 * CUrrent page data
	 *
	 * @since  1.0.0
	 *
	 * @var $current_page_data
	 */
	private static $current_page_data = array();

	/**
	 * Location Selection Option
	 *
	 * @since  1.0.0
	 *
	 * @var $selection_options
	 */
	private static $selection_options;

	/**
	 * Main Favethemes_Field_Manager Instance.
	 *
	 * Ensures only one instance of Favethemes_Field_Manager is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return Favethemes_Field_Manager - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}



	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'houzez-studio' ), '1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'houzez-studio' ), '1.0' );
	}


	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_action_edit', array( $this, 'init_options' ) );
		add_action( 'wp_ajax_fts_retrieve_posts_based_on_query', array( $this, 'retrieve_posts_based_on_query' ) );
	}

	
	/**
	 * Init variables.
	 *
	 * @return void
	 */
	public function init_options() {
		self::$selection_options = self::selection_options();
	}

	/**
	 * Ajax handler to return posts based on the search query, searching only in titles.
	 *
	 * @since 1.0.0
	 */
	public function retrieve_posts_based_on_query() {
	    check_ajax_referer('fts_nonce', 'nonce');

	    $search_string = isset($_POST['q']) ? sanitize_text_field($_POST['q']) : '';
	    $result = array();

	    // Consolidated post types retrieval.
	    $post_types = $this->get_post_types();

	    foreach ($post_types as $key => $post_type) {
	        $data = $this->get_posts_data($post_type, $search_string);

	        if (!empty($data)) {
	            $result[] = array(
	                'text' => $key,
	                'children' => $data,
	            );
	        }
	    }

	    // Handle taxonomies.
	    $taxonomies_result = $this->get_taxonomies_data($search_string);
	    $result = array_merge($result, $taxonomies_result);

	    wp_send_json($result);
	}

	/**
	 * Retrieves a list of custom post types, including 'post' and 'page'.
	 */
	private function get_post_types() {
	    $args = array(
	        'public' => true,
	        '_builtin' => false,
	    );

	    $post_types = get_post_types($args, 'names', 'and');
	    unset($post_types['fts_builder']);
	    unset($post_types['elementor_library']);

	    $post_types['Pages'] = 'page';
	    $post_types['Posts'] = 'post';

	    return $post_types;
	}

	/**
	 * Retrieves posts data for a given post type.
	 */
	private function get_posts_data($post_type, $search_string) {
	    add_filter('posts_search', array($this, 'filter_search_by_post_titles'), 10, 2);

	    $query = new \WP_Query(array(
	        's' => $search_string,
	        'post_type' => $post_type,
	        'posts_per_page' => -1,
	    ));

	    $data = array();
	    if ($query->have_posts()) {
	        while ($query->have_posts()) {
	            $query->the_post();
	            $title = get_the_title();
	            $title .= (get_post_parent()) ? ' (' . get_the_title(get_post_parent()) . ')' : '';
	            $id = get_the_ID();

	            $data[] = array(
	                'id' => 'post-' . $id,
	                'text' => $title,
	            );
	        }
	    }

	    wp_reset_postdata();
	    return $data;
	}

	/**
	 * Retrieves and formats taxonomy data.
	 */
	private function get_taxonomies_data($search_string) {
	    $args = array(
	        'public' => true,
	    );

	    $taxonomies = get_taxonomies($args, 'objects', 'and');
	    $result = array();

	    foreach ($taxonomies as $taxonomy) {
	        $data = $this->get_taxonomy_terms($taxonomy, $search_string);

	        if (!empty($data)) {
	            $result[] = array(
	                'text' => ucwords($taxonomy->label),
	                'children' => $data,
	            );
	        }
	    }

	    return $result;
	}

	/**
	 * Retrieves terms for a given taxonomy.
	 */
	private function get_taxonomy_terms($taxonomy, $search_string) {
	    $terms = get_terms($taxonomy->name, array(
	        'orderby' => 'count',
	        'hide_empty' => false,
	        'name__like' => $search_string,
	    ));

	    $data = array();
	    foreach ($terms as $term) {
	        $data[] = array(
	            'id' => 'tax-' . $term->term_id,
	            'text' => $term->name . ' archive page',
	        );

	        $data[] = array(
	            'id' => 'tax-' . $term->term_id . '-single-' . $taxonomy->name,
	            'text' => 'All singulars from ' . $term->name,
	        );
	    }

	    return $data;
	}

	/**
	 * Filter the search query to only include results based on post titles.
	 * This function is hooked to the 'posts_search' filter in WordPress.
	 *
	 * @param string   $search   Existing search SQL for WHERE clause.
	 * @param WP_Query $wp_query The current WP_Query object.
	 *
	 * @return string Modified search SQL to include only post titles.
	 */
	function filter_search_by_post_titles($search, $wp_query) {
	    global $wpdb;

	    // Check if search terms are available.
	    if (!empty($search) && !empty($wp_query->query_vars['search_terms'])) {
	        $like_escape_char = !empty($wp_query->query_vars['exact']) ? '' : '%';

	        // Constructing the new search query.
	        $new_search = array();
	        foreach ((array) $wp_query->query_vars['search_terms'] as $term) {
	            $term_like = $wpdb->esc_like($term);
	            $new_search[] = $wpdb->prepare("{$wpdb->posts}.post_title LIKE %s", $like_escape_char . $term_like . $like_escape_char);
	        }

	        // If the user is not logged in, exclude password-protected posts.
	        if (!is_user_logged_in()) {
	            $new_search[] = "{$wpdb->posts}.post_password = ''";
	        }

	        // Combine the search query parts.
	        $search = ' AND ' . implode(' AND ', $new_search);
	    }

	    return $search;
	}



	/**
	 * Get location selection options with improved structure and naming.
	 *
	 * @return array
	 */
	public static function selection_options() {
	    $post_types = self::fts_GetPostTypes(['public' => true, '_builtin' => true], 'objects');
	    unset($post_types['attachment']);

	    $custom_post_types = self::fts_GetPostTypes(['public' => true, '_builtin' => false], 'objects');
	    unset($custom_post_types['fts_builder'], $custom_post_types['favethemes-blocks'], $custom_post_types['elementor_library'], $custom_post_types['e-landing-page']);

	    $post_types = array_merge($post_types, $custom_post_types);

	    $special_pages = self::Unique_Pages();

	    $selection_options = array(
	        'standard' => array(
	            'label' => __('Standard', 'houzez-studio'),
	            'value' => array(
	                'standard-global'    => __('Entire Website', 'houzez-studio'),
	                'standard-singulars' => __('All Singulars', 'houzez-studio'),
	                'standard-single-listing' => __('All Single Listings', 'houzez-studio'),
	                'standard-single-agent' => __('All Single Agents', 'houzez-studio'),
	                'standard-single-agency' => __('All Single Agencies', 'houzez-studio'),
	                'standard-single-post' => __('All Single Posts', 'houzez-studio'),
	                'standard-archives'  => __('All Archives', 'houzez-studio'),
	            ),
	        ),
	        'unique-pages' => array(
	            'label' => __('Special Pages', 'houzez-studio'),
	            'value' => $special_pages,
	        ),
	        'specific-selection' => array(
	            'label' => __('Specific Selection', 'houzez-studio'),
	            'value' => array('specifics' => __('Specific Pages / Listings / Posts / Taxonomies, etc.', 'houzez-studio')),
	        ),
	    );

	    $taxonomies = get_taxonomies(['public' => true], 'objects');
	    self::fts_AddTaxonomyOptions($taxonomies, $post_types, $selection_options);

	    return apply_filters('fts_display_on_list', $selection_options);
	}

	private static function fts_GetPostTypes($args, $output) {
	    return get_post_types($args, $output);
	}

	private static function Unique_Pages() {
	    $special_pages = array(
	        'unique-all-listings'    => __('All Listings Pages', 'houzez-studio'),
	        'unique-listings-search'    => __('Listings Search Page', 'houzez-studio'),
	        'unique-all-agents'    => __('All Agents Page', 'houzez-studio'),
	        'unique-all-agencies'    => __('All Agencies Page', 'houzez-studio'),
	        'unique-404'    => __('404 Page', 'houzez-studio'),
	        'unique-search' => __('Blog / Search Page', 'houzez-studio'),
	        'unique-blog'   => __('Blog / Posts Page', 'houzez-studio'),
	        'unique-front'  => __('Front Page', 'houzez-studio'),
	        'unique-date'   => __('Date Archive', 'houzez-studio'),
	        'unique-author' => __('Author Archive', 'houzez-studio'),
	    );

	    if (class_exists('WooCommerce')) {
	        $special_pages['unique-woo-shop'] = __('WooCommerce Shop Page', 'houzez-studio');
	    }

	    return $special_pages;
	}

	private static function fts_AddTaxonomyOptions($taxonomies, $post_types, &$selection_options) {
	    if (!empty($taxonomies)) {
	        foreach ($taxonomies as $taxonomy) {
	            if ($taxonomy->name == 'post_format') continue;

	            foreach ($post_types as $post_type) {
	                $post_opt = self::fts_PostRuleOptions($post_type, $taxonomy);
	                if (!isset($selection_options[$post_opt['post_key']])) {
	                    $selection_options[$post_opt['post_key']] = array(
	                        'label' => $post_opt['label'],
	                        'value' => $post_opt['value'],
	                    );
	                } else {
	                    $selection_options[$post_opt['post_key']]['value'] += $post_opt['value'];
	                }
	            }
	        }
	    }
	}

	/**
	 * Generate target rule options for a given post type and taxonomy.
	 *
	 * @since  1.0.0
	 *
	 * @param object $post_type Post type object.
	 * @param object $taxonomy Taxonomy object for creating target rule options.
	 * @return array
	 */
	public static function fts_PostRuleOptions($post_type, $taxonomy) {
	    $post_key = strtolower(str_replace(' ', '-', $post_type->label));
	    $post_label = ucwords($post_type->label);
	    $post_name = $post_type->name;

	    $post_options = array();

	    // For all posts of this type.
	    $all_posts_key = "{$post_name}|all";
	    $post_options[$all_posts_key] = sprintf(__('All %s', 'houzez-studio'), $post_label);

	    if ('pages' !== $post_key) {
	        // For the archive of this post type.
	        $all_archive_key = "{$post_name}|all|archive";
	        $post_options[$all_archive_key] = sprintf(__('All %s Archive', 'houzez-studio'), $post_label);
	    }

	    if (in_array($post_type->name, $taxonomy->object_type)) {
	        $tax_label = ucwords($taxonomy->label);
	        $tax_archive_key = "{$post_name}|all|taxarchive|{$taxonomy->name}";
	        $post_options[$tax_archive_key] = sprintf(__('All %s Archive', 'houzez-studio'), $tax_label);
	    }

	    return array(
	        'post_key' => $post_key,
	        'label'    => $post_label,
	        'value'    => $post_options,
	    );
	}

	/**
	 * Generate markup for rendering the location selection.
	 *
	 * @since  1.0.0
	 * @param  string $type               Rule type: display or exclude.
	 * @param  array  $selection_options  Available selection fields.
	 * @param  string $input_name         Input name for the settings.
	 * @param  array  $saved_values       Array of saved values.
	 * @param  string $button_label     Label for the Add rule button.
	 * @return string HTML Markup for location settings.
	 */
	public static function fts_SelectorGenerator($type, $selection_options, $input_name, $saved_values, $button_label) {
	    $output = '<div class="fts-fields_builder_wrap">';

	    $saved_values = self::fts_InitializeSavedValues($saved_values);

	    foreach ($saved_values['rule'] as $index => $data) {
	        $output .= self::fts_RenderRuleCondition($index, $data, $selection_options, $input_name);
	        $output .= self::fts_RenderTargetedPageSelection($index, $data, $saved_values, $input_name);
	    }

	    $output .= '</div>'; // Closing fts-fields_builder_wrap

	    $output .= '<div class="houzez-custom-wpadmin-form-row">';
	    $output .= self::fts_RenderAddNewRuleButton($index, $type, $button_label);

	    if ('display' === $type) {
	        $output .= self::fts_RenderAddExclusionRuleButton();
	    }
	    $output .= '</div>';

	    return $output;
	}

	/**
	 * Helper methods (can be added as private static functions in the same class)
	 */

	// Initialize saved values array.
	private static function fts_InitializeSavedValues($saved_values) {
	    if (!is_array($saved_values) || empty($saved_values)) {
	        return ['rule' => [''], 'specifics' => ['']];
	    }

	    return $saved_values;
	}

	// Render rule condition.
	private static function fts_RenderRuleCondition($index, $data, $selection_options, $input_name) {
	    $output = '<div class="fts-rule_condition_block fts-rule-' . $index . '" data-rule="' . $index . '">';
	    $output .= '<div class="fts-selection_field">';
	    $output .= '<select name="' . esc_attr($input_name) . '[rule][' . $index . ']" class="fts-selection_dropdown form-control">';
	    $output .= '<option value="">' . __('Select', 'houzez-studio') . '</option>';

	    foreach ($selection_options as $group => $group_data) {
	        $output .= '<optgroup label="' . $group_data['label'] . '">';
	        foreach ($group_data['value'] as $opt_key => $opt_value) {
	            $selected = $data === $opt_key ? 'selected="selected"' : '';
	            $output .= '<option value="' . $opt_key . '" ' . $selected . '>' . $opt_value . '</option>';
	        }
	        $output .= '</optgroup>';
	    }

	    $output .= '</select></div>';
	    $output .= '<span class="fts-delete_rule_icon dashicons dashicons-dismiss"></span>';
	    $output .= '</div>';
	    return $output;
	}

	// Render specific page selection.
	private static function fts_RenderTargetedPageSelection($index, $data, $saved_values, $input_name) {
	    $output = '<div class="fts-targeted-page-wrap" style="display:none">';
	    $output .= '<select name="' . esc_attr($input_name) . '[specific][]" class="fts-targeted-select2 fts-targeted-page form-control" multiple="multiple">';

	    if ('specifics' === $data && isset($saved_values['specific']) && is_array($saved_values['specific'])) {
	        foreach ($saved_values['specific'] as $sel_value) {
	            $output .= self::fts_RenderTargetedOption($sel_value);
	        }
	    }

	    $output .= '</select></div>';
	    return $output;
	}

	// Helper method to render each specific option.
	private static function fts_RenderTargetedOption($sel_value) {
	    if (strpos($sel_value, 'post-') !== false) {
	        return self::fts_RenderPostOption($sel_value);
	    }

	    if (strpos($sel_value, 'tax-') !== false) {
	        return self::fts_RenderTaxonomyOption($sel_value);
	    }

	    return ''; // Default return for non-matching cases.
	}

	// Render post option for specific selection.
	private static function fts_RenderPostOption($sel_value) {
	    $post_id = (int) str_replace('post-', '', $sel_value);
	    $post_title = get_the_title($post_id);
	    return '<option value="post-' . $post_id . '" selected="selected">' . $post_title . '</option>';
	}

	// Render taxonomy option for specific selection.
	private static function fts_RenderTaxonomyOption($sel_value) {
	    $tax_data = explode('-', $sel_value);
	    $tax_id = (int) str_replace('tax-', '', $sel_value);
	    $term = get_term($tax_id);
	    $term_name = '';

	    if (!is_wp_error($term)) {
	        $term_taxonomy = ucfirst(str_replace('_', ' ', $term->taxonomy));

	        if (isset($tax_data[2]) && 'single' === $tax_data[2]) {
	            $term_name = 'All singulars from ' . $term->name;
	        } else {
	            $term_name = $term->name . ' - ' . $term_taxonomy;
	        }
	    }

	    return '<option value="' . $sel_value . '" selected="selected">' . $term_name . '</option>';
	}


	// Render 'Add New Rule' button.
	private static function fts_RenderAddNewRuleButton($index, $type, $button_label) {
	    return '<div class="fts-create_new_rule"><a href="#" class="button button-secondary" data-rule-id="' . absint($index) . '" data-rule-type="' . $type . '">' . $button_label . '</a></div>';
	}

	// Render 'Add Exclusion Rule' button.
	private static function fts_RenderAddExclusionRuleButton() {
	    return '<div class="fts-create_exclusion_rule"><a href="#" class="button button-secondary">' . __('Define Exclusion Rule', 'houzez-studio') . '</a></div>';
	}


	/**
	 * Function to handle new input type for target rule settings.
	 *
	 * @param string $name Name attribute for the input.
	 * @param array  $settings Configuration settings for the input.
	 * @param string $value Current value of the input.
	 */
	public static function fts_FieldSettings($name, $settings, $value) {
	    $input_name = $name;
	    $rule_type = $settings['rule_type'] ?? '';
	    $button_label = $settings['button_label'] ?? __('Add Rule', 'houzez-studio');
	    $saved_values = $value;

	    if (!isset(self::$selection_options) || empty(self::$selection_options)) {
	        self::$selection_options = self::selection_options();
	    }

	    $output = '<script type="text/html" id="tmpl-fts-' . esc_attr($rule_type) . '-condition">';
	    $output .= '<div class="fts-rule_condition_block fts-rule-{{data.id}}" data-rule="{{data.id}}">';
	    $output .= '<div class="fts-selection_field">';
	    $output .= '<select name="' . esc_attr($input_name) . '[rule][{{data.id}}]" class="fts-selection_dropdown form-control">';
	    $output .= '<option value="">' . __('Select', 'houzez-studio') . '</option>';

	    foreach (self::$selection_options as $group => $group_data) {
	        $output .= '<optgroup label="' . esc_html($group_data['label']) . '">';
	        foreach ($group_data['value'] as $opt_key => $opt_value) {
	            $output .= '<option value="' . esc_attr($opt_key) . '">' . esc_html($opt_value) . '</option>';
	        }
	        $output .= '</optgroup>';
	    }

	    $output .= '</select></div>';
	    $output .= '<span class="fts-delete_rule_icon dashicons dashicons-dismiss"></span>';
	    $output .= '</div>'; // Closing condition wrap and condition div

	    $output .= '<div class="fts-targeted-page-wrap" style="display:none">';
	    $output .= '<select name="' . esc_attr($input_name) . '[specific][]" class="fts-targeted-select2 fts-targeted-page form-control" multiple="multiple"></select>';
	    $output .= '</div></script>'; // Closing targeted page wrap and script tag

	    $output .= '<div class="fts-fields_container fts-' . esc_attr($rule_type) . '-on-wrap" data-type="' . esc_attr($rule_type) . '">';
	    $output .= '<div class="fts-selector_container fts-' . esc_attr($rule_type) . '-on">';
	    $output .= self::fts_SelectorGenerator($rule_type, self::$selection_options, $input_name, $saved_values, $button_label);
	    $output .= '</div></div>'; // Closing fts-fields_container & fts-selector_container

	    echo $output;
	}


	/**
	 * Retrieve the label associated with a given location key.
	 *
	 * @param string $key The key for which to find the corresponding location label.
	 * @return string The label of the location or the key itself if no label is found.
	 */
	public static function fetch_location_label($key) {
	    self::ensure_selection_options_loaded();
	    $label = self::find_label_in_selection_options($key);
	    if ($label !== null) {
	        return $label;
	    }

	    return self::derive_label_from_key($key);
	}

	/**
	 * Ensure that the location selection array is loaded.
	 */
	private static function ensure_selection_options_loaded() {
	    if (!self::$selection_options) {
	        self::$selection_options = self::get_selection_options();
	    }
	}

	/**
	 * Find a label in the location selection using the provided key.
	 *
	 * @param string $key The key to search for.
	 * @return string|null The found label or null if not found.
	 */
	private static function find_label_in_selection_options($key) {
	    foreach (self::$selection_options as $group) {
	        if (isset($group['value'][$key])) {
	            return $group['value'][$key];
	        }
	    }
	    return null;
	}

	/**
	 * Derive a label from a special format key.
	 *
	 * @param string $key The key to parse.
	 * @return string The derived label or the key itself if no specific format is matched.
	 */
	public static function derive_label_from_key($key) {
	    if (strpos($key, 'post-') === 0) {
	        $post_id = (int) str_replace('post-', '', $key);
	        return get_the_title($post_id);
	    }

	    if (strpos($key, 'tax-') === 0) {
	        $tax_id = (int) str_replace('tax-', '', $key);
	        $term = get_term($tax_id);
	        if (!is_wp_error($term)) {
	            return $term->name . ' - ' . ucfirst(str_replace('_', ' ', $term->taxonomy));
	        }
	    }

	    $textMappings = array(
            'standard-global'    => 'Entire Site',
            'standard-singulars' => 'All Singulars',
            'standard-single-listing' => 'All Single Listings',
            'standard-single-agent' => 'All Single Agents',
            'standard-single-agency' => 'All Single Agencies',
            'standard-single-post' => 'All Single Posts',
            'standard-archives'  => 'All Archives',
            'unique-404'      	 => '404 Page',
            'unique-search'   	 => 'Blog Search Page',
            'unique-front'    	 => 'Front Page',
            'unique-date'     	 => 'Date Archive',
            'unique-author'   	 => 'Author Archive',
            'unique-all-listings'=> 'All Listings Pages',
            'unique-listings-search'=> 'Listings Search Page',
            'unique-all-agents'	 => 'All Agents Page',
            'unique-all-agencies' => 'All Agencies Page',
            'unique-woo-shop' 	 => 'WooCommerce Shop Page',
            'page|all'  		 => 'All Pages',
            'post|all'  		 => 'All Posts',
            'post|all|archive'   => 'All Posts Archive',
            'post|all|taxarchive|category' => 'All Categories Archive',
            'post|all|taxarchive|post_tag' => 'All Tags Archive',
            'property|all'  		 					=> 'All Properties',
            'property|all|archive'  		 			=> 'All Properties Archive',
            'property|all|taxarchive|property_type'  	=> 'All Type Archive',
            'property|all|taxarchive|property_status'  	=> 'All Status Archive',
            'property|all|taxarchive|property_feature'  => 'All Feature Archive',
            'property|all|taxarchive|property_label'  	=> 'All Label Archive',
            'property|all|taxarchive|property_country'  => 'All Country Archive',
            'property|all|taxarchive|property_state'  	=> 'All State Archive',
            'property|all|taxarchive|property_city'  	=> 'All City Archive',
            'property|all|taxarchive|property_area'  	=> 'All Area Archive',
            'houzez_agency|all'  		 				=> 'All Agencies',
            'houzez_agency|all|archive'  		 		=> 'All Agencies Archive',
            'houzez_agent|all'  		 				=> 'All Agents',
            'houzez_agent|all|archive'  		 		=> 'All Agents Archive',
            'houzez_agent|all|taxarchive|agent_category'=> 'All Agents Categories Archive',
            'houzez_agent|all|taxarchive|agent_city'  	=> 'All Agents Cities Archive',
        );
	    
	    // Check if the text has a mapped value and update it accordingly
        if (array_key_exists($key, $textMappings)) {
            $key = $textMappings[$key];
        }

	    return $key;
	}

	/**
	 * Determine the current page type.
	 *
	 * @return string The type of the current page.
	 */
	public function determine_current_page_type() {
	    if (self::$current_page_type === null) {
	        self::$current_page_type = $this->identify_page_type();
	        self::$current_page_data['ID'] = get_the_id();
	    }

	    return self::$current_page_type;
	}

	/**
	 * Identify the type of the current WordPress page.
	 *
	 * @return string The identified page type.
	 */
	private function identify_page_type() {
	    if (is_404()) {
	        return 'is_404';
	    }
	    if (is_search()) {
	        return 'is_search';
	    }
	    if (is_archive()) {
	        return $this->classify_archive_page();
	    }
	    if (is_home()) {
	        return 'is_home';
	    }
	    if (is_front_page()) {
	        return 'is_front_page';
	    }
	    if ( houzez_is_listings_template() ) {
	        return 'is_listings_template';
	    }
	    if ( houzez_is_search_result() ) {
	        return 'is_listings_search';
	    }
	    if ( houzez_is_agents_template() ) {
	        return 'is_agents_template';
	    }
	    if ( houzez_is_agencies_template() ) {
	        return 'is_agencies_template';
	    }
	    if (is_singular('property')) {
	        return 'is_single_listing';
	    }
	    if (is_singular('houzez_agent')) {
	        return 'is_single_agent';
	    }
	    if (is_singular('houzez_agency')) {
	        return 'is_single_agency';
	    }
	    if (is_singular('post')) {
	        return 'is_single_post';
	    }
	    if (is_singular()) {
	        return 'is_singular';
	    }

	    return '';
	}

	/**
	 * Classify the type of archive page.
	 *
	 * @return string The archive page type.
	 */
	private function classify_archive_page() {
	    if (is_category() || is_tag() || is_tax()) {
	        return 'is_tax';
	    }
	    if (is_date()) {
	        return 'is_date';
	    }
	    if (is_author()) {
	        return 'is_author';
	    }
	    if (function_exists('is_shop') && is_shop()) {
	        return 'is_woo_shop_page';
	    }

	    return 'is_archive';
	}

	/**
	 * Retrieves meta option data for a given post type.
	 *
	 * @param string $postType The post type.
	 * @param array  $option   Meta option information.
	 *
	 * @return mixed Returns an array of meta data or false if no data is found.
	 */
	public static function fetch_meta_option_data($postType, $option) {
	    $pageMeta = $option['page_meta'] ?? false;

	    if (!$pageMeta) {
	        return false;
	    }

	    $currentPostId = $option['current_post_id'] ?? false;
	    $metaId = get_post_meta($currentPostId, $pageMeta, true);

	    if ($metaId) {
	        self::$current_page_data[$postType][$metaId] = [
	            'id' => $metaId,
	            'location' => ''
	        ];

	        return self::$current_page_data[$postType];
	    }

	    return false;
	}


	/**
	 * Formats rule metadata for storage.
	 *
	 * @param array  $saveData Data to be saved.
	 * @param string $key      Variable key.
	 *
	 * @return array Formatted rule data.
	 */
	public static function format_rule_metadata($saveData, $key) {
	    if (empty($saveData[$key]['rule'])) {
	        return [];
	    }

	    $rules = array_unique($saveData[$key]['rule']);
	    $specifics = isset($saveData[$key]['specific']) ? array_unique($saveData[$key]['specific']) : [];

	    $rules = array_diff($rules, [''], ['specifics']);
	    if (!empty($specifics)) {
	        $rules[] = 'specifics';
	    }

	    $formattedMeta = self::sanitize_rule_metadata($rules, $specifics);

	    if (empty($formattedMeta['rule'])) {
	        return [];
	    }

	    return $formattedMeta;
	}

	/**
	 * Sanitize and organize rule metadata.
	 *
	 * @param array $rules     Array of rules.
	 * @param array $specifics Array of specific rules.
	 *
	 * @return array Sanitized and organized metadata.
	 */
	private static function sanitize_rule_metadata($rules, $specifics) {
	    $metaValue = ['rule' => array_map('esc_attr', $rules)];

	    if (in_array('specifics', $rules, true)) {
	        $metaValue['specific'] = array_map('esc_attr', $specifics);
	    } else {
	        $metaValue['specific'] = [];
	    }

	    return $metaValue;
	}

	/**
	 * Get posts by conditions
	 *
	 * @since 1.0.0
	 * @param string $post_type Post Type.
	 * @param array $option Meta option name.
	 *
	 * @return object Posts.
	 */
	public function fetch_posts_by_criteria($post_type, $option) {
	    global $wpdb, $post;

	    $post_type = $post_type ?: $post->post_type;

	    // Skip caching in admin, Elementor editor, or AJAX requests
	    $skip_cache = is_admin() ||
	                  (isset($_GET['elementor-preview']) || isset($_GET['action']) && $_GET['action'] === 'elementor') ||
	                  (defined('DOING_AJAX') && DOING_AJAX) ||
	                  (defined('WP_DEBUG') && WP_DEBUG);

	    if (!$skip_cache) {
	        // Create a more specific cache key that includes detailed page context
	        $current_page_type = $this->determine_current_page_type();
	        $cache_context = $this->get_detailed_cache_context();
	        $cache_key = 'fts_posts_criteria_' . md5($post_type . '_' . $current_page_type . '_' . $cache_context . '_' . serialize($option));

	        // Try to get cached data
	        $cached_data = get_transient($cache_key);
	        if ($cached_data !== false) {
	            self::$current_page_data[$post_type] = $cached_data;
	            return apply_filters('fts_fetch_posts_by_criteria', $cached_data, $post_type);
	        }
	    }

	    // Reset the current page data
	    self::$current_page_data[$post_type] = [];

	    // Get current page type for processing
	    $current_page_type = $this->determine_current_page_type();

	    $option['current_post_id'] = self::$current_page_data['ID'];

	    $meta_header = self::fetch_meta_option_data($post_type, $option);
	    if ($meta_header === false) {
	        $this->process_meta_option($post_type, $option, $current_page_type);
	    }

	    $result = self::$current_page_data[$post_type];

	    // Only cache if not skipping cache
	    if (!$skip_cache && isset($cache_key)) {
	        // Cache the result for 30 minutes
	        set_transient($cache_key, $result, 30 * MINUTE_IN_SECONDS);
	    }

	    return apply_filters('fts_fetch_posts_by_criteria', $result, $post_type);
	}
	
	/**
	 * Get detailed cache context for more accurate caching
	 */
	private function get_detailed_cache_context() {
	    $context = '';
	    
	    // Get current post/page ID
	    if (is_singular()) {
	        $context .= 'id_' . get_the_ID() . '_';
	    }
	    
	    // Get current URL for unique identification
	    $context .= 'url_' . md5($_SERVER['REQUEST_URI']) . '_';
	    
	    // Add query vars that might affect template selection
	    $important_vars = ['post_type', 'taxonomy', 'term', 'paged', 's'];
	    foreach ($important_vars as $var) {
	        $value = get_query_var($var);
	        if ($value) {
	            $context .= $var . '_' . $value . '_';
	        }
	    }
	    
	    // Add page template
	    if (is_page_template()) {
	        $context .= 'tpl_' . get_page_template_slug() . '_';
	    }
	    
	    // Add user role (templates might have user-based conditions)
	    if (is_user_logged_in()) {
	        $user = wp_get_current_user();
	        $context .= 'role_' . implode('_', $user->roles) . '_';
	    }
	    
	    return $context;
	}


	/**
	 * Processes meta options based on the given post type, option, and current page type.
	 * This function constructs a SQL query to fetch posts that match certain conditions
	 * and updates the current page data accordingly.
	 *
	 * @param string $post_type The post type being processed.
	 * @param array  $option The options containing conditions for post selection.
	 * @param string $current_page_type The type of the current page being viewed.
	 */
	private function process_meta_option($post_type, $option, $current_page_type) {
	    global $wpdb;

	    $current_post_type = esc_sql( get_post_type() );
	    $current_post_id = false;
	    $q_obj = get_queried_object();
	    $location = isset($option['included']) ? esc_sql($option['included']) : '';

	    // Prepare the base query
	    $query = $wpdb->prepare(
	        "SELECT p.ID, pm.meta_value FROM {$wpdb->postmeta} AS pm
	         INNER JOIN {$wpdb->posts} AS p ON pm.post_id = p.ID
	         WHERE pm.meta_key = %s AND p.post_type = %s AND p.post_status = 'publish'",
	        $location, $post_type
	    );

	    // Dynamic query parts based on current page type
	    $meta_args = $this->get_meta_args_based_on_page_type($current_page_type, $current_post_type, $q_obj);

	    // Complete SQL query
	    $orderby = ' ORDER BY p.post_date DESC';
	    $final_query = $query . ' AND (' . $meta_args . ')' . $orderby;

	    // Fetch results
	    $posts = $wpdb->get_results($final_query);

	    foreach ($posts as $local_post) {
	        self::$current_page_data[$post_type][$local_post->ID] = [
	            'id' => $local_post->ID,
	            'location' => unserialize($local_post->meta_value),
	        ];
	    }

	    if( isset(self::$current_page_data['current_post_id']) ) {
	    	$current_post_id = self::$current_page_data['current_post_id'];
	    }

	    $option['current_post_id'] = $current_post_id;

	    $this->filter_excluded_posts($post_type, $option);
	}


	/**
	 * Constructs meta arguments for the SQL query based on the current page type.
	 * This function dynamically builds a part of the SQL query to fetch posts
	 * that match specific display conditions based on the page type.
	 *
	 * @param string $current_page_type The type of the current page.
	 * @param string $current_post_type The current post type.
	 * @param object $q_obj The queried object.
	 * @return string A string of SQL conditions for post selection.
	 */
	private function get_meta_args_based_on_page_type($current_page_type, $current_post_type, $q_obj) {
	    $meta_args = "pm.meta_value LIKE '%\"standard-global\"%'";

	    switch ($current_page_type) {
	        case 'is_404':
	            $meta_args .= " OR pm.meta_value LIKE '%\"unique-404\"%'";
	            break;
	        case 'is_listings_template':
	        	$current_id = get_the_ID();
	            self::$current_page_data['current_post_id'] = $current_id;
	            $meta_args .= " OR pm.meta_value LIKE '%\"unique-all-listings\"%'";
	            $meta_args .= " OR pm.meta_value LIKE '%\"post-{$current_id}\"%'";
	            break;
	        case 'is_agents_template':
	        	$current_id = get_the_ID();
	            self::$current_page_data['current_post_id'] = $current_id;
	            $meta_args .= " OR pm.meta_value LIKE '%\"unique-all-agents\"%'";
	            $meta_args .= " OR pm.meta_value LIKE '%\"post-{$current_id}\"%'";
	            break;
	        case 'is_agencies_template':
	        	$current_id = get_the_ID();
	            self::$current_page_data['current_post_id'] = $current_id;
	            $meta_args .= " OR pm.meta_value LIKE '%\"unique-all-agencies\"%'";
	            $meta_args .= " OR pm.meta_value LIKE '%\"post-{$current_id}\"%'";
	            break;
	       case 'is_listings_search':
	       		$current_id = get_the_ID();
	            self::$current_page_data['current_post_id'] = $current_id;
	            $meta_args .= " OR pm.meta_value LIKE '%\"unique-listings-search\"%'";
	            break;
	        case 'is_search':
	            $meta_args .= " OR pm.meta_value LIKE '%\"unique-search\"%'";
	            break;
	        case 'is_archive':
	        case 'is_tax':
	        case 'is_date':
	        case 'is_author':
	            $meta_args .= $this->handle_archive_page_type($current_post_type, $q_obj, $current_page_type);
	            break;
	        case 'is_home':
	            $meta_args .= " OR pm.meta_value LIKE '%\"unique-blog\"%'";
	            break;
	        case 'is_front_page':
	            $current_id = get_the_ID();
	            self::$current_page_data['current_post_id'] = $current_id;
	            $meta_args .= $this->handle_front_page_type($current_post_type, $current_id);
	            break;
	        case 'is_single_listing':
	            $current_id = get_the_ID();
	            self::$current_page_data['current_post_id'] = $current_id;
	            $meta_args .= $this->handle_single_listing_type($q_obj, $current_post_type, $current_id);
	            break;
	        case 'is_single_agent':
	            $current_id = get_the_ID();
	            self::$current_page_data['current_post_id'] = $current_id;
	            $meta_args .= $this->handle_single_agent_type($q_obj, $current_post_type, $current_id);
	            break;
	        case 'is_single_agency':
	            $current_id = get_the_ID();
	            self::$current_page_data['current_post_id'] = $current_id;
	            $meta_args .= $this->handle_single_agency_type($q_obj, $current_post_type, $current_id);
	            break;
	        case 'is_single_post':
	            $current_id = get_the_ID();
	            self::$current_page_data['current_post_id'] = $current_id;
	            $meta_args .= $this->handle_single_post_type($q_obj, $current_post_type, $current_id);
	            break;
	        case 'is_singular':
	            $current_id = get_the_ID();
	            self::$current_page_data['current_post_id'] = $current_id;
	            $meta_args .= $this->handle_singular_page_type($q_obj, $current_post_type, $current_id);
	            break;
	        case 'is_woo_shop_page':
	            $meta_args .= " OR pm.meta_value LIKE '%\"unique-woo-shop\"%'";
	            break;
	        case '':
	            $current_post_id = get_the_id();
	            self::$current_page_data['current_post_id'] = $current_post_id;
	            break;
	    }

	    return $meta_args;
	}


	/**
	 * Builds meta arguments for the SQL query specific to archive page types.
	 * This helper function generates a string of conditions for archive-related pages.
	 *
	 * @param string $current_post_type The current post type.
	 * @param object $q_obj The queried object.
	 * @param string $current_page_type The type of the current page.
	 * @return string A string of SQL conditions for archive page types.
	 */
	private function handle_archive_page_type($current_post_type, $q_obj, $current_page_type) {
	    $meta_args = " OR pm.meta_value LIKE '%\"standard-archives\"%'";
	    $meta_args .= " OR pm.meta_value LIKE '%\"{$current_post_type}|all|archive\"%'";

	    if ('is_tax' == $current_page_type && (is_category() || is_tag() || is_tax())) {
	        if (is_object($q_obj)) {
	            $meta_args .= " OR pm.meta_value LIKE '%\"{$current_post_type}|all|taxarchive|{$q_obj->taxonomy}\"%'";
	            $meta_args .= " OR pm.meta_value LIKE '%\"tax-{$q_obj->term_id}\"%'";
	        }
	    } elseif ('is_date' == $current_page_type) {
	        $meta_args .= " OR pm.meta_value LIKE '%\"unique-date\"%'";
	    } elseif ('is_author' == $current_page_type) {
	        $meta_args .= " OR pm.meta_value LIKE '%\"unique-author\"%'";
	    }
	    
	    return $meta_args;
	}

	/**
	 * Builds meta arguments for the SQL query specific to front page type.
	 * This function generates conditions specific to front page display settings in the SQL query.
	 * @param string $current_post_type The current post type.
	 * @param int $current_id The ID of the current page or post.
	 * @return string A string of SQL conditions for front page type.
	*/
	private function handle_front_page_type($current_post_type, $current_id) {
	    $meta_args = " OR pm.meta_value LIKE '%\"unique-front\"%'";
	    $meta_args .= " OR pm.meta_value LIKE '%\"{$current_post_type}|all\"%'";
	    $meta_args .= " OR pm.meta_value LIKE '%\"post-{$current_id}\"%'";

	    return $meta_args;
	}


	/**
	* Builds meta arguments for the SQL query specific to singular page types.
	* This function handles conditions related to singular pages, including taxonomy and term checks.
	* @param object $q_obj The queried object, typically a post.
	* @param string $current_post_type The current post type.
	* @param int $current_id The ID of the current post.
	* @return string A string of SQL conditions for singular page types.
	*/
	private function handle_singular_page_type($q_obj, $current_post_type, $current_id) {
	    $meta_args = " OR pm.meta_value LIKE '%\"standard-singulars\"%'";
	    $meta_args .= " OR pm.meta_value LIKE '%\"{$current_post_type}|all\"%'";
	    $meta_args .= " OR pm.meta_value LIKE '%\"post-{$current_id}\"%'";

	    $taxonomies = get_object_taxonomies($q_obj->post_type);
	    $terms = wp_get_post_terms($q_obj->ID, $taxonomies);

	    foreach ($terms as $term) {
	        $meta_args .= " OR pm.meta_value LIKE '%\"tax-{$term->term_id}-single-{$term->taxonomy}\"%'";
	    }

	    return $meta_args;
	}


	/**
	* Builds meta arguments for the SQL query specific to singular page types.
	* This function handles conditions related to singular pages, including taxonomy and term checks.
	* @param object $q_obj The queried object, typically a post.
	* @param string $current_post_type The current post type.
	* @param int $current_id The ID of the current post.
	* @return string A string of SQL conditions for singular page types.
	*/
	private function handle_single_listing_type($q_obj, $current_post_type, $current_id) {
	    $meta_args = " OR pm.meta_value LIKE '%\"standard-single-listing\"%'";
	    $meta_args .= " OR pm.meta_value LIKE '%\"{$current_post_type}|all\"%'";
	    $meta_args .= " OR pm.meta_value LIKE '%\"post-{$current_id}\"%'";

	    $taxonomies = get_object_taxonomies($q_obj->post_type);
	    $terms = wp_get_post_terms($q_obj->ID, $taxonomies);

	    foreach ($terms as $term) {
	        $meta_args .= " OR pm.meta_value LIKE '%\"tax-{$term->term_id}-single-{$term->taxonomy}\"%'";
	    }

	    return $meta_args;
	}

	private function handle_single_agent_type($q_obj, $current_post_type, $current_id) {
	    $meta_args = " OR pm.meta_value LIKE '%\"standard-single-agent\"%'";
	    $meta_args .= " OR pm.meta_value LIKE '%\"{$current_post_type}|all\"%'";
	    $meta_args .= " OR pm.meta_value LIKE '%\"post-{$current_id}\"%'";

	    $taxonomies = get_object_taxonomies($q_obj->post_type);
	    $terms = wp_get_post_terms($q_obj->ID, $taxonomies);

	    foreach ($terms as $term) {
	        $meta_args .= " OR pm.meta_value LIKE '%\"tax-{$term->term_id}-single-{$term->taxonomy}\"%'";
	    }

	    return $meta_args;
	}

	private function handle_single_agency_type($q_obj, $current_post_type, $current_id) {
	    $meta_args = " OR pm.meta_value LIKE '%\"standard-single-agency\"%'";
	    $meta_args .= " OR pm.meta_value LIKE '%\"{$current_post_type}|all\"%'";
	    $meta_args .= " OR pm.meta_value LIKE '%\"post-{$current_id}\"%'";

	    $taxonomies = get_object_taxonomies($q_obj->post_type);
	    $terms = wp_get_post_terms($q_obj->ID, $taxonomies);

	    foreach ($terms as $term) {
	        $meta_args .= " OR pm.meta_value LIKE '%\"tax-{$term->term_id}-single-{$term->taxonomy}\"%'";
	    }

	    return $meta_args;
	}

	private function handle_single_post_type($q_obj, $current_post_type, $current_id) {
	    $meta_args = " OR pm.meta_value LIKE '%\"standard-single-post\"%'";
	    $meta_args .= " OR pm.meta_value LIKE '%\"{$current_post_type}|all\"%'";
	    $meta_args .= " OR pm.meta_value LIKE '%\"post-{$current_id}\"%'";

	    $taxonomies = get_object_taxonomies($q_obj->post_type);
	    $terms = wp_get_post_terms($q_obj->ID, $taxonomies);

	    foreach ($terms as $term) {
	        $meta_args .= " OR pm.meta_value LIKE '%\"tax-{$term->term_id}-single-{$term->taxonomy}\"%'";
	    }

	    return $meta_args;
	}


	/**
	 * Remove exclusion rule posts.
	 *
	 * Iterates over the current page data and removes posts that match the specified exclusion rules.
	 *
	 * @since  1.0.0
	 * @param  string $post_type Post Type.
	 * @param  array  $option Meta option name.
	 */
	public function filter_excluded_posts($post_type, $option) {
	    $exclusion = $option['exclusion'] ?? '';
	    $current_post_id = $option['current_post_id'] ?? false;

	    //echo '*** '.$current_post_id.' ***';

	    if (!$exclusion) {
	        return;
	    }

	    foreach (self::$current_page_data[$post_type] as $c_post_id => $c_data) {
	        $exclusion_rules = get_post_meta($c_post_id, $exclusion, true);
	        if ($this->is_excluded($current_post_id, $exclusion_rules)) {
	            unset(self::$current_page_data[$post_type][$c_post_id]);
	        }
	    }
	}

	/**
	 * Determines if the current post is excluded based on the provided rules.
	 *
	 * @param int   $post_id The ID of the current post.
	 * @param array $rules The exclusion rules.
	 *
	 * @return boolean Returns true if the post is excluded, false otherwise.
	 */
	private function is_excluded($post_id, $rules) {
	    if (!empty($rules)) {
	        return $this->evaluate_display_condition($post_id, $rules);
	    }
	    return false;
	}

	/**
	 * Evaluates whether the current page meets specified display conditions.
	 *
	 * This method assesses a set of rules against the current page context. It checks if
	 * the conditions defined in the rules align with the characteristics of the current page,
	 * determining whether a specific layout should be displayed or not.
	 *
	 * @param int   $post_id The ID of the current post or page.
	 * @param array $rules   An array of conditions specifying when to display or exclude content.
	 *
	 * @return boolean True if the current page satisfies the conditions, false otherwise.
	 */

	public function evaluate_display_condition($post_id, $rules) {
	    if (empty($rules['rule']) || !is_array($rules['rule'])) {
	        return false;
	    }

	    foreach ($rules['rule'] as $rule) {
	        if ($this->evaluate_rule($rule, $post_id, $rules)) {
	            return true;
	        }
	    }

	    return false;
	}


	/**
	 * Evaluates a given rule against the current page context and post ID.
	 *
	 * @param string $rule    The rule to be evaluated.
	 * @param int    $post_id The current post ID.
	 * @param array  $rules   Array of all rules.
	 * @return boolean True if the rule is satisfied, false otherwise.
	 */
	private function evaluate_rule($rule, $post_id, $rules) {
		$rule_key = $this->get_rule_key($rule);
		switch ($rule_key) {
		    case 'standard-global':
		    case 'unique-all-listings':
		    case 'unique-listings-search':
		    case 'unique-all-agents':
		    case 'unique-all-agencies':
		    case 'unique-404':
		    case 'unique-search':
		    case 'unique-blog':
		    case 'unique-front':
		    case 'unique-date':
		    case 'unique-author':
		    case 'unique-woo-shop':
		        return $this->check_standard_rules($rule_key);

		    case 'all':
		        return $this->check_all_rules($rule, $post_id);

		    case 'specifics':
		        return $this->check_specific_rules($rules['specific'], $post_id);

		    default:
		        return false;
		}
	}


	/**
	 * Determines the key part of a rule.
	 *
	 * @param string $rule The rule from which the key part is to be extracted.
	 * @return string The key part of the rule.
	 */
	private function get_rule_key($rule) {
		return strrpos($rule, 'all') !== false ? 'all' : $rule;
	}


	/**
	 * Checks standard WordPress conditional rules.
	 *
	 * @param string $rule_key The key identifying the standard rule.
	 * @return boolean True if the standard rule is satisfied, false otherwise.
	 */
	private function check_standard_rules($rule_key) {
		switch ($rule_key) {
			case 'standard-global':
				return true;
			case 'unique-404':
				return is_404();
			case 'unique-search':
				return is_search();
			case 'unique-blog':
				return is_home();
			case 'unique-front':
				return is_front_page();
			case 'unique-date':
				return is_date();
			case 'unique-author':
				return is_author();
			case 'unique-all-listings':
				return houzez_is_listings_template();
			case 'unique-listings-search':
				return houzez_is_search_result();
			case 'unique-all-agents':
				return houzez_is_agents_template();
			case 'unique-all-agencies':
				return houzez_is_agencies_template();
			case 'unique-woo-shop':
			return

			function_exists('is_shop') && is_shop();
			default:
			return false;
		}
	}


	/**
	 * Evaluates 'all' rules against the current post ID.
	 *
	 * @param string $rule    The 'all' rule string.
	 * @param int    $post_id The current post ID.
	 * @return boolean True if the 'all' rule is satisfied, false otherwise.
	 */
	private function check_all_rules($rule, $post_id) {
		$rule_data = explode('|', $rule);
		$post_type = $rule_data[0] ?? false;
		$archive_type = $rule_data[2] ?? false;
		$taxonomy = $rule_data[3] ?? false;

		if (!$archive_type) {
		    return get_post_type($post_id) === $post_type;
		}

		if (is_archive()) {
		    $current_post_type = get_post_type();
		    if ($current_post_type === $post_type) {
		        if ($archive_type === 'archive') {
		            return true;
		        } elseif ($archive_type === 'taxarchive') {
		            $obj = get_queried_object();
		            return $obj->taxonomy === $taxonomy;
		        }
		    }
		}

		return false;
	}


	/**
	 * Evaluates specific rules for a given post ID.
	 * @param array $specific_rules Array of specific rules to be evaluated.
	 * @param int $post_id The current post ID.
	 * @return boolean True if any of the specific rules apply, false otherwise.
	*/
	private function check_specific_rules($specific_rules, $post_id) {
		if (!is_array($specific_rules)) {
			return false;
		}

		foreach ($specific_rules as $specific_rule) {
			$specific_data = explode('-', $specific_rule);
			$specific_post_type = $specific_data[0] ?? false;
			$specific_post_id = $specific_data[1] ?? false;

			if ($specific_post_type === 'post' && $specific_post_id == $post_id) {
		        return true;
		    } elseif ($specific_post_type === 'tax') {
		        if ($this->check_tax_rule($specific_data, $post_id)) {
		            return true;
		        }
		    }
		}
		return false;
	}


	/**
	 * Checks if a taxonomy-related rule applies to the current post.
	 * @param array $specific_data Array containing specific rule data for taxonomy checking.
	 * @param int $post_id The current post ID.
	 * @return boolean True if the taxonomy rule applies, false otherwise.
	*/
	private function check_tax_rule($specific_data, $post_id) {
		$specific_post_id = $specific_data[1] ?? false;
		$context = $specific_data[2] ?? '';

		if ($context === 'single' && is_singular()) {
		    $term_details = get_term($specific_post_id);
		    return isset($term_details->taxonomy) && has_term($specific_post_id, $term_details->taxonomy, $post_id);
		}

		if ($context === '' && $specific_post_id == get_queried_object_id()) {
		    return true;
		}

		return false;
	}

}
Favethemes_Field_Manager::instance();