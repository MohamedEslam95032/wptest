<?php
namespace HouzezStudio\admin\metaboxes;

use HouzezStudio\admin\fieldsManager as FieldManager;

defined( 'ABSPATH' ) || exit;

class Houzez_Studio_Metaboxes {

	/**
	 * Version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';


	/**
	 * The single instance of the class.
	 *
	 * @var Houzez_Studio_Metaboxes
	 * @since 1.0
	 */
	private static $_instance;

	/**
	 * Main Houzez_Studio_Metaboxes Instance.
	 *
	 * Ensures only one instance of Houzez_Studio_Metaboxes is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return Houzez_Studio_Metaboxes - Main instance.
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
		add_action( 'add_meta_boxes', array( $this, 'metaboxes' ) );
		add_action( 'save_post', array( $this, 'save_metabox_data' ) );
		add_action( 'init', array( $this, 'maybe_clear_fts_cache' ) );
	}

	/**
	 * Clear FTS cache if requested via URL parameter.
	 * Add ?fts_clear_cache=1 to any page to clear all FTS caches.
	 *
	 * @since 1.0.0
	 */
	public function maybe_clear_fts_cache() {
		if (isset($_GET['fts_clear_cache']) && $_GET['fts_clear_cache'] == '1' && current_user_can('manage_options')) {
			$this->clear_fts_template_caches();

			// Show admin notice
			add_action('admin_notices', function() {
				echo '<div class="notice notice-success is-dismissible"><p>FTS Template caches have been cleared!</p></div>';
			});

			// Also show notice on frontend for testing
			if (!is_admin()) {
				add_action('wp_footer', function() {
					echo '<div style="position:fixed;bottom:20px;right:20px;background:#46b450;color:white;padding:15px;border-radius:5px;z-index:9999;">FTS Cache Cleared!</div>';
				});
			}
		}
	}


	/**
	 * Meta Box In btf_builder post type.
	 */
	public function metaboxes() {
		add_meta_box( 'fts_metaboxes_setting', 'Template Settings', array( $this, 'fts_metaboxes_output' ), 'fts_builder', 'normal', 'high' );
	}


	/**
	 * Render Meta field.
	 *
	 * @param  POST $post Currennt post object which is being displayed.
	 */
	function fts_metaboxes_output( $post ) {
		$values            = get_post_custom( $post->ID );
		$template_type     = isset( $values['fts_template_type'] ) ? esc_attr( $values['fts_template_type'][0] ) : '';
		$block_hook     = isset( $values['fts_block_hook'] ) ? esc_attr( $values['fts_block_hook'][0] ) : '';
		
		// We'll use this nonce field later on when saving.
		wp_nonce_field( 'fts_meta_nounce', 'fts_meta_nounce' );
		?>
		<table class="houzez-custom-wpadmin-table houzez-fts-options-table fts-options-none">
			<tbody>
				<tr>
					<th scope="row">
						<label for="select_id"><?php _e( 'Template Type', 'houzez-studio' ); ?></label>
					</th>
					<td>
						<div class="houzez-custom-wpadmin-form-row">
							<select name="fts_template_type" id="fts_template_type">
								<option value="" <?php selected( $template_type, '' ); ?>><?php _e( 'Select Option', 'houzez-studio' ); ?></option>
								
								<option value="tmp_header" <?php selected( $template_type, 'tmp_header' ); ?>><?php _e( 'Header', 'houzez-studio' ); ?></option>

								<option value="tmp_footer" <?php selected( $template_type, 'tmp_footer' ); ?>><?php _e( 'Footer', 'houzez-studio' ); ?></option>

								<option value="single-listing" <?php selected( $template_type, 'single-listing' ); ?>><?php _e( 'Single Listing', 'houzez-studio' ); ?></option>

								<option value="single-agent" <?php selected( $template_type, 'single-agent' ); ?>><?php _e( 'Single Agent', 'houzez-studio' ); ?></option>

								<option value="single-agency" <?php selected( $template_type, 'single-agency' ); ?>><?php _e( 'Single Agency', 'houzez-studio' ); ?></option>

								<option value="single-post" <?php selected( $template_type, 'single-post' ); ?>><?php _e( 'Single Post', 'houzez-studio' ); ?></option>

								<!-- <option value="listing-archive" <?php selected( $template_type, 'listing-archive' ); ?>><?php _e( 'Listing Archive', 'houzez-studio' ); ?></option> -->

								<option value="tmp_megamenu" <?php selected( $template_type, 'tmp_megamenu' ); ?>><?php _e( 'Mega Menu', 'houzez-studio' ); ?></option>

								<option value="tmp_custom_block" <?php selected( $template_type, 'tmp_custom_block' ); ?>><?php _e( 'Block', 'houzez-studio' ); ?></option>
							</select>
						</div><!-- houzez-custom-wpadmin-form-row -->
					</td>
				</tr>

				<tr class="fts-row fts-block-hook">
					<th scope="row">
						<label for="fts_block_hooks">
							<?php esc_html_e( 'Block Hook', 'houzez-studio' ); ?>
						</label>
					</th>
					<td>
						<select name="fts_block_hooks" id="fts_block_hooks" class="form-control">
							<option value=""><?php esc_html_e( 'Select a Hook', 'houzez-studio' );?></option>
							<option <?php selected( $block_hook, 'shortcode' ); ?> value="shortcode"><?php esc_html_e( 'Custom (Shortcode)', 'houzez-studio' );?></option>
							<option <?php selected( $block_hook, 'before_header' ); ?> value="before_header"><?php esc_html_e( 'Before Header', 'houzez-studio' );?></option>
							<option <?php selected( $block_hook, 'after_header' ); ?> value="after_header"><?php esc_html_e( 'After Header', 'houzez-studio' );?></option>
							<option <?php selected( $block_hook, 'before_footer' ); ?> value="before_footer"><?php esc_html_e( 'Before Footer', 'houzez-studio' );?></option>
							<option <?php selected( $block_hook, 'after_footer' ); ?> value="after_footer"><?php esc_html_e( 'After Footer', 'houzez-studio' );?></option>
						</select>
					</td>
				</tr>
				
				<?php $this->display_rules_tab(); ?>

				<tr class="fts-row fts-shortcode-row">
					<th scope="row">
						<label for="fts_shortcode">
							<?php esc_html_e( 'Shortcode', 'houzez-studio' ); ?>
						</label>
					</th>
					<td>
						<span class="fts-shortcode-wrap">
							<input type="text" onfocus="this.select();" readonly="readonly" value="[fts_template id='<?php echo esc_attr( $post->ID ); ?>']" class="code">
						</span>
					</td>
				</tr>

			</tbody>
		</table>
		<?php
	}

	/**
	 * Markup for Display Rules Tabs.
	 *
	 * @since  1.0.0
	 */
	public function display_rules_tab() {
	
		$included_settings = get_post_meta( get_the_id(), 'fts_included_options', true );
		$excluded_settings = get_post_meta( get_the_id(), 'fts_excluded_options', true );
		?>
		<tr class="fts-row fts-row-rules">
			<th scope="row">
				<label for="fts_included_display_rules">
					<?php esc_html_e( 'Display Location', 'houzez-studio' ); ?>
				</label>
			</th>
			<td>
				<?php
				FieldManager\Favethemes_Field_Manager::fts_FieldSettings(
					'fts_included_display_rules',
					[
						'rule_type'      => 'display',
						'button_label' => __( 'Create Display Rule', 'houzez-studio' ),
					],
					$included_settings
				);
				?>
			</td>
		</tr>

		<tr class="fts-row fts-row-rules-exclude hidden">
			<th scope="row">
				<label for="fts_included_display_rules">
					<?php esc_html_e( 'Exlcude Location', 'houzez-studio' ); ?>
				</label>
			</th>
			<td>
				<?php
				FieldManager\Favethemes_Field_Manager::fts_FieldSettings(
					'fts_excluded_display_rules',
					[
						'rule_type'      => 'exclude',
						'button_label' => __( 'Create Exlcude Rule', 'houzez-studio' ),
					],
					$excluded_settings
				);
				?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save metabox data for the current post.
	 *
	 * @param int $postId Current post ID.
	 */
	public function save_metabox_data($postId) {
	    if ($this->should_not_save_metabox($postId)) {
	        return;
	    }

	    $this->update_template_rules_metadata($postId, $_POST);
	    $this->update_template_type_metadata($postId, $_POST);
	    $this->update_block_hook_metadata($postId, $_POST);

	    // Clear all FTS template caches when template settings are saved
	    $this->clear_fts_template_caches();
	}

	/**
	 * Determine if metabox data should not be saved.
	 *
	 * @param int $postId Current post ID.
	 * @return bool True if data should not be saved, false otherwise.
	 */
	private function should_not_save_metabox($postId) {
	    return defined('DOING_AUTOSAVE') && DOING_AUTOSAVE
	        || !isset($_POST['fts_meta_nounce'])
	        || !wp_verify_nonce(sanitize_text_field($_POST['fts_meta_nounce']), 'fts_meta_nounce')
	        || !current_user_can('edit_post', $postId);
	}

	/**
	 * Update target location metadata.
	 *
	 * @param int   $postId Current post ID.
	 * @param array $postData POST data.
	 */
	private function update_template_rules_metadata($postId, $postData) {
	    $targetLocations = FieldManager\Favethemes_Field_Manager::format_rule_metadata($postData, 'fts_included_display_rules');
	    $targetExclusion = FieldManager\Favethemes_Field_Manager::format_rule_metadata($postData, 'fts_excluded_display_rules');
	    
	    update_post_meta($postId, 'fts_included_options', $targetLocations);
	    update_post_meta($postId, 'fts_excluded_options', $targetExclusion);
	}

	/**
	 * Update template type metadata.
	 *
	 * @param int   $postId Current post ID.
	 * @param array $postData POST data.
	 */
	private function update_template_type_metadata($postId, $postData) {
	    if (isset($postData['fts_template_type'])) {
	    	$fts_template_type = sanitize_text_field($postData['fts_template_type']);
	        update_post_meta($postId, 'fts_template_type', $fts_template_type);
	        $taxonomy_term = $this->get_single_tax_type($fts_template_type);
	        wp_set_object_terms( $postId, $taxonomy_term, 'fts_types' );
	    }
	}

	private function get_single_tax_type($fts_template_type) {
	    
	    if( $fts_template_type == 'single-listing' ||
	    	$fts_template_type == 'single-agent' ||
	    	$fts_template_type == 'single-agency' ||
	    	$fts_template_type == 'single-post'
	    ) {
	    	return 'tmp_single';
	    }

	    // Return the original template type for all other types
	    return $fts_template_type;
	}

	/**
	 * Update block hook metadata.
	 *
	 * @param int   $postId Current post ID.
	 * @param array $postData POST data.
	 */
	private function update_block_hook_metadata($postId, $postData) {
	    if (isset($postData['fts_block_hooks'])) {
	    	$fts_block_hooks = sanitize_text_field($postData['fts_block_hooks']);
	        update_post_meta($postId, 'fts_block_hook', $fts_block_hooks);
	    }
	}

	/**
	 * Clear all FTS template transient caches.
	 * This ensures templates display correctly after settings are changed.
	 *
	 * @since 1.0.0
	 */
	private function clear_fts_template_caches() {
	    global $wpdb;

	    // Delete all transients that start with 'fts_template_' or 'fts_posts_criteria_'
	    $wpdb->query(
	        "DELETE FROM {$wpdb->options}
	        WHERE option_name LIKE '_transient_fts_template_%'
	        OR option_name LIKE '_transient_timeout_fts_template_%'
	        OR option_name LIKE '_transient_fts_posts_criteria_%'
	        OR option_name LIKE '_transient_timeout_fts_posts_criteria_%'"
	    );

	    // Also clear object cache if available
	    if (function_exists('wp_cache_flush')) {
	        wp_cache_flush();
	    }
	}

}
Houzez_Studio_Metaboxes::instance();