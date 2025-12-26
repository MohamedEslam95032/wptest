<?php

use Elementor\Plugin as Elementor;

class Houzez_Library
{
	public function __construct()
	{
		$this->hooks();
		$this->register_templates_source();
	}

	public function hooks()
	{
		add_action('elementor/editor/after_enqueue_scripts', array($this, 'enqueue'));
		add_action('elementor/editor/footer', array($this, 'render'));
		add_action('elementor/frontend/before_enqueue_styles', array($this, 'inline_styles'));
		// Register proper AJAX actions for Houzez templates.
		add_action( 'elementor/ajax/register_actions', array( $this, 'register_ajax_actions' ), 20 );
		
		// Admin hooks for sync functionality
		add_action('wp_ajax_houzez_sync_templates', array($this, 'ajax_sync_templates'));
		add_action('wp_ajax_houzez_sync_chunked', array($this, 'ajax_sync_chunked'));
		add_action('wp_ajax_houzez_get_sync_progress', array($this, 'ajax_get_sync_progress'));
		add_action('wp_ajax_houzez_clear_templates', array($this, 'ajax_clear_templates'));
		add_action('wp_ajax_houzez_get_sync_status', array($this, 'ajax_get_sync_status'));
		add_action('wp_ajax_houzez_get_local_templates', array($this, 'ajax_get_local_templates'));
		
		// Progressive loading hooks for library popup
		add_action('wp_ajax_houzez_get_templates_batch', array($this, 'ajax_get_templates_batch'));
		add_action('wp_ajax_houzez_get_templates_progressive', array($this, 'ajax_get_templates_progressive'));
	}

	public function inline_styles()
	{
	?>
		<style>
		.houzez-library-modal-btn {margin-left: 5px;background: #35AAE1;vertical-align: top;font-size: 0 !important;}
		.houzez-library-modal-btn:before {content: '';width: 16px;height: 16px;background-image: url('<?php echo get_template_directory_uri() . '/img/studio-icon.png';?>');background-position: center;background-size: contain;background-repeat: no-repeat;}
		#houzez-library-modal .houzez-elementor-template-library-template-name {text-align: right;flex: 1 0 0%;}
		.houzez-notice {padding: 10px 15px;margin: 10px 0;border-radius: 4px;font-size: 14px;}
		.houzez-notice.houzez-success {background: #d4edda;color: #155724;border: 1px solid #c3e6cb;}
		.houzez-notice.houzez-error {background: #f8d7da;color: #721c24;border: 1px solid #f5c6cb;}
		.houzez-notice.houzez-info {background: #d1ecf1;color: #0c5460;border: 1px solid #bee5eb;}
		</style>
	<?php
	}

	public function register_templates_source()
	{
		Elementor::instance()->templates_manager->register_source('Houzez_Library_Source');
	}

	public function enqueue()
	{
		wp_enqueue_script('houzez-blocks', get_template_directory_uri() . '/inc/blocks/assets/js/blocks-templates.js', array('jquery'), '1.0.1', true);

		// Get JSON directory URL (requires favethemes-api plugin)
		$json_base_url = '';
		$use_json_files = false;
		$json_version = '';

		if (function_exists('favethemes_get_json_directory_url')) {
			$json_base_url = favethemes_get_json_directory_url();

			// Check if all-templates.json exists
			if (function_exists('favethemes_get_json_stats')) {
				$json_stats = favethemes_get_json_stats();
				$use_json_files = $json_stats['all_templates_exists'];

				// Use last generation timestamp for cache busting
				if (isset($json_stats['last_generated']) && $json_stats['last_generated']) {
					$json_version = $json_stats['last_generated'];
				} else {
					// Fallback to current time if no generation timestamp
					$json_version = time();
				}
			}
		}

		// Add localization for AJAX
		wp_localize_script('houzez-blocks', 'houzez_library_ajax', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('houzez_library_nonce'),
			'sync_text' => __('Sync Templates', 'houzez'),
			'syncing_text' => __('Syncing...', 'houzez'),
			'sync_success' => __('Templates synced successfully!', 'houzez'),
			'sync_error' => __('Sync failed. Please try again.', 'houzez'),
			'progressive_loading' => array(
				'loading_more' => __('Loading more templates...', 'houzez'),
				'loading_batch' => __('Loading batch', 'houzez'),
				'templates_loaded' => __('templates loaded', 'houzez'),
				'complete' => __('All templates loaded!', 'houzez'),
				'error_batch' => __('Failed to load some templates', 'houzez'),
			),
			'json_files' => array(
				'enabled' => $use_json_files,
				'base_url' => $json_base_url,
				'all_templates_url' => $use_json_files ? $json_base_url . 'all-templates.json' : '',
				'version' => $json_version, // Cache busting parameter
			),
		));
	}

	/**
	 * Admin page for template management
	 */
	public static function admin_page() {
		$last_sync = Houzez_Library_Source::get_last_sync_time();
		$templates = get_option('houzez_local_templates', []);
		$template_count = isset($templates['elements']) ? count($templates['elements']) : 0;
		?>
		<div class="wrap houzez-template-library">
			<div class="houzez-header">
				<div class="houzez-header-content">
					<div class="houzez-logo">
						<!-- <img src="<?php echo get_template_directory_uri() . '/img/logo.png'; ?>" alt="Houzez" style="height: 40px;"> -->
						<h1><?php _e('Template Library Management', 'houzez'); ?></h1>
					</div>
					<div class="houzez-header-actions">
						<button type="button" id="sync-templates-btn" class="houzez-btn houzez-btn-primary">
							<i class="dashicons dashicons-update"></i>
							<?php _e('Sync Templates', 'houzez'); ?>
						</button>
						<button type="button" id="clear-templates-btn" class="houzez-btn houzez-btn-secondary">
							<i class="dashicons dashicons-trash"></i>
							<?php _e('Clear Cache', 'houzez'); ?>
						</button>
					</div>
				</div>
			</div>

			<div class="houzez-dashboard">
				<!-- Quick Stats -->
				<div class="houzez-stats-grid">
					<div class="houzez-stat-card">
						<div class="houzez-stat-icon">
							<i class="dashicons dashicons-admin-page"></i>
						</div>
						<div class="houzez-stat-content">
							<h3><?php echo $template_count; ?></h3>
							<p><?php _e('Templates Available', 'houzez'); ?></p>
						</div>
					</div>

					<div class="houzez-stat-card">
						<div class="houzez-stat-icon">
							<i class="dashicons dashicons-clock"></i>
						</div>
						<div class="houzez-stat-content">
							<h3>
								<?php if ($last_sync): ?>
									<?php echo human_time_diff($last_sync, current_time('timestamp')); ?> ago
								<?php else: ?>
									<?php _e('Never', 'houzez'); ?>
								<?php endif; ?>
							</h3>
							<p><?php _e('Last Sync', 'houzez'); ?></p>
						</div>
					</div>

					<div class="houzez-stat-card">
						<div class="houzez-stat-icon">
							<i class="dashicons dashicons-performance"></i>
						</div>
						<div class="houzez-stat-content">
							<h3><?php echo $template_count > 0 ? 'Local' : 'Remote'; ?></h3>
							<p><?php _e('Storage Mode', 'houzez'); ?></p>
						</div>
					</div>
				</div>

				<!-- Main Actions -->
				<div class="houzez-main-card">
					<div class="houzez-card-header">
						<h2>
							<i class="dashicons dashicons-admin-tools"></i>
							<?php _e('Template Management', 'houzez'); ?>
						</h2>
						<div class="houzez-status-badge <?php echo $template_count > 0 ? 'houzez-status-success' : 'houzez-status-warning'; ?>">
							<?php echo $template_count > 0 ? __('Active', 'houzez') : __('Inactive', 'houzez'); ?>
						</div>
					</div>
					<div class="houzez-card-body">
						<p class="houzez-description">
							<?php _e('Templates are stored locally for instant access in Elementor. Sync manually when needed.', 'houzez'); ?>
						</p>
						
						<div class="houzez-actions">
							<div class="houzez-action">
								<div class="houzez-action-icon">
									<i class="dashicons dashicons-download"></i>
								</div>
								<div class="houzez-action-content">
									<h4><?php _e('Sync Templates', 'houzez'); ?></h4>
									<p><?php _e('Download latest templates from studio.houzez.co', 'houzez'); ?></p>
									<button type="button" id="sync-action-btn" class="houzez-btn houzez-btn-outline">
										<?php _e('Start Sync', 'houzez'); ?>
									</button>
								</div>
							</div>

							<div class="houzez-action">
								<div class="houzez-action-icon houzez-icon-danger">
									<i class="dashicons dashicons-database-remove"></i>
								</div>
								<div class="houzez-action-content">
									<h4><?php _e('Clear Templates', 'houzez'); ?></h4>
									<p><?php _e('Remove all locally stored templates', 'houzez'); ?></p>
									<button type="button" id="clear-action-btn" class="houzez-btn houzez-btn-outline houzez-btn-danger">
										<?php _e('Clear All', 'houzez'); ?>
									</button>
								</div>
							</div>
						</div>

						<!-- Performance Info -->
						<div class="houzez-performance-info">
							<div class="houzez-perf-item">
								<strong><?php _e('Performance:', 'houzez'); ?></strong>
								<?php if ($template_count > 0): ?>
									<span class="houzez-success"><?php _e('Optimized - Instant loading', 'houzez'); ?></span>
								<?php else: ?>
									<span class="houzez-warning"><?php _e('Not optimized - Remote loading', 'houzez'); ?></span>
								<?php endif; ?>
							</div>
							<div class="houzez-perf-item">
								<strong><?php _e('API Calls:', 'houzez'); ?></strong>
								<span><?php echo $template_count > 0 ? '0 per request' : '315+ per request'; ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Progress Modal -->
			<div id="houzez-progress-modal" class="houzez-modal" style="display: none;">
				<div class="houzez-modal-content">
					<div class="houzez-modal-header">
						<h3 id="houzez-modal-title"><?php _e('Processing...', 'houzez'); ?></h3>
					</div>
					<div class="houzez-modal-body">
						<div class="houzez-progress-bar small">
							<div class="houzez-progress-fill default animated" style="width: 0%;"></div>
						</div>
						<div class="houzez-progress-text">
							<span id="houzez-progress-message"><?php _e('Initializing...', 'houzez'); ?></span>
							<span id="houzez-progress-percentage">0%</span>
						</div>
						<div id="houzez-progress-details" class="houzez-progress-details"></div>
					</div>
				</div>
			</div>

			<!-- Notifications -->
			<div id="houzez-notifications" class="houzez-notifications"></div>
		</div>

		<script>
		jQuery(document).ready(function($) {
			// Notification system
			function showNotification(message, type = 'info') {
				const notification = $(`
					<div class="houzez-notification ${type}">
						${message}
					</div>
				`);
				
				$('#houzez-notifications').append(notification);
				
				setTimeout(() => {
					notification.fadeOut(() => notification.remove());
				}, 5000);
			}

			// Progress modal functions
			function showProgressModal(title = 'Processing...') {
				$('#houzez-modal-title').text(title);
				$('#houzez-progress-modal').fadeIn();
				updateProgress(0, 'Initializing...');
			}

			function hideProgressModal() {
				$('#houzez-progress-modal').fadeOut();
			}

			function updateProgress(percentage, message, details = '') {
				$('.houzez-progress-fill').css('width', percentage + '%');
				$('#houzez-progress-percentage').text(Math.round(percentage) + '%');
				$('#houzez-progress-message').text(message);
				$('#houzez-progress-details').html(details);
			}

			// Sync templates functionality
			function handleSync() {
				showProgressModal('Syncing Templates');
				
				// Check template count to decide sync method
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'houzez_get_sync_status',
						nonce: '<?php echo wp_create_nonce('houzez_library_nonce'); ?>'
					},
					success: function(response) {
						if (response.success && response.data.template_count > 100) {
							startChunkedSync();
						} else {
							startRegularSync();
						}
					},
					error: function() {
						startRegularSync();
					}
				});
			}

			function startRegularSync() {
				updateProgress(10, 'Connecting to studio.houzez.co...');
				
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'houzez_sync_templates',
						nonce: '<?php echo wp_create_nonce('houzez_library_nonce'); ?>'
					},
					success: function(response) {
						if (response.success) {
							let details = '';
							
							if (response.data.hybrid) {
								// Hybrid sync results
								details = `<strong>Hybrid Sync Results:</strong><br>`;
								details += `Bulk Download: ${response.data.downloaded_bulk || 0} templates<br>`;
								details += `Individual Download: ${response.data.downloaded_individual || 0} templates<br>`;
								details += `Total Downloaded: ${response.data.downloaded || 0} templates<br>`;
								details += `Failed: ${response.data.failed || 0}<br>`;
								details += `API calls: ${response.data.api_calls || 0}`;
								
								if (response.data.cached) {
									details += `<br><span style="color: #059669; font-weight: bold;">✓ Bulk used server cache</span>`;
								}
								
								updateProgress(100, 'Hybrid sync completed!', details);
							} else {
								// Regular sync results
								details = `Downloaded: ${response.data.downloaded || 0} templates`;
								if (response.data.api_calls) {
									details += `<br>API calls: ${response.data.api_calls}`;
								}
								if (response.data.cached) {
									details += `<br><span style="color: #059669; font-weight: bold;">✓ Using server cache (lightning fast!)</span>`;
								}
								if (response.data.api_calls === 2) {
									details += `<br><span style="color: #059669;">✓ Bulk sync successful (2 calls vs 333!)</span>`;
								}
								
								updateProgress(100, 'Sync completed!', details);
							}
							
							setTimeout(() => {
								hideProgressModal();
								let message = 'Templates synced successfully!';
								if (response.data.hybrid) {
									message += ` (${response.data.downloaded} total templates)`;
								} else if (response.data.cached) {
									message += ' (Used server cache)';
								}
								showNotification(message, 'success');
								setTimeout(() => location.reload(), 1500);
							}, 2000);
						} else {
							if (response.data && response.data.use_chunked) {
								updateProgress(20, 'Large template set detected. Switching to chunked sync...');
								setTimeout(startChunkedSync, 1000);
							} else {
								hideProgressModal();
								showNotification(response.data.message || 'Sync failed. Please try again.', 'error');
							}
						}
					},
					error: function() {
						hideProgressModal();
						showNotification('Sync failed. Please check your connection.', 'error');
					}
				});
			}

			function startChunkedSync() {
				let chunkIndex = 0;
				let totalChunks = 0;
				let totalApiCalls = 0;
				
				function processChunk() {
					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: {
							action: 'houzez_sync_chunked',
							nonce: '<?php echo wp_create_nonce('houzez_library_nonce'); ?>',
							chunk_size: 10,
							chunk_index: chunkIndex
						},
						success: function(response) {
							if (response.success) {
								const data = response.data;
								totalChunks = data.total_chunks;
								
								if (data.api_calls) {
									totalApiCalls += data.api_calls;
								}
								
								const progress = Math.round((chunkIndex + 1) / totalChunks * 100);
								const message = `Processing chunk ${chunkIndex + 1} of ${totalChunks}`;
								let details = `Progress: ${progress}%`;
								if (totalApiCalls > 0) {
									details += `<br>API calls: ${totalApiCalls}`;
								}
								
								updateProgress(progress, message, details);
								
								if (data.completed) {
									updateProgress(100, 'Chunked sync completed!', 
										`Total API calls: ${totalApiCalls}<br>All templates downloaded successfully`);
									
									setTimeout(() => {
										hideProgressModal();
										showNotification('Templates synced successfully!', 'success');
										setTimeout(() => location.reload(), 1500);
									}, 2000);
								} else {
									chunkIndex++;
									setTimeout(processChunk, 2000);
								}
							} else {
								hideProgressModal();
								showNotification('Chunk sync failed: ' + response.data.message, 'error');
							}
						},
						error: function() {
							hideProgressModal();
							showNotification('Chunk sync failed. Please try again.', 'error');
						}
					});
				}
				
				processChunk();
			}

			// Clear templates functionality
			function handleClear() {
				if (!confirm('Are you sure you want to clear all local templates? This will impact performance until templates are synced again.')) {
					return;
				}
				
				showProgressModal('Clearing Templates');
				updateProgress(50, 'Removing local templates...');
				
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'houzez_clear_templates',
						nonce: '<?php echo wp_create_nonce('houzez_library_nonce'); ?>'
					},
					success: function(response) {
						if (response.success) {
							updateProgress(100, 'Templates cleared successfully!');
							setTimeout(() => {
								hideProgressModal();
								showNotification('Local templates cleared successfully.', 'success');
								setTimeout(() => location.reload(), 1500);
							}, 1000);
						} else {
							hideProgressModal();
							showNotification(response.data.message || 'Failed to clear templates.', 'error');
						}
					},
					error: function() {
						hideProgressModal();
						showNotification('Clear operation failed. Please try again.', 'error');
					}
				});
			}

			// Event handlers
			$('#sync-templates-btn, #sync-action-btn').on('click', handleSync);
			$('#clear-templates-btn, #clear-action-btn').on('click', handleClear);

			// Close modal on outside click
			$('#houzez-progress-modal').on('click', function(e) {
				if (e.target === this) {
					hideProgressModal();
				}
			});

			// Add loading states to buttons
			$(document).on('ajaxStart', function() {
				$('.houzez-btn').prop('disabled', true);
			}).on('ajaxStop', function() {
				$('.houzez-btn').prop('disabled', false);
			});

			// Animate stats on page load
			$('.houzez-stat-card').each(function(index) {
				$(this).css('opacity', '0').delay(index * 100).animate({
					opacity: 1
				}, 500);
			});

			// Animate cards on page load
			$('.houzez-card').each(function(index) {
				$(this).css('opacity', '0').delay((index + 4) * 100).animate({
					opacity: 1
				}, 500);
			});
		});
		</script>
		<?php
	}

	/**
	 * AJAX handler for syncing templates
	 */
	public function ajax_sync_templates() {
		check_ajax_referer('houzez_library_nonce', 'nonce');
		
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}

		$result = Houzez_Library_Source::sync_templates_from_remote();
		
		if ($result['success']) {
			wp_send_json_success($result);
		} else {
			wp_send_json_error($result);
		}
	}

	/**
	 * AJAX handler for syncing chunked templates
	 */
	public function ajax_sync_chunked() {
		check_ajax_referer('houzez_library_nonce', 'nonce');
		
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}

		$chunk_size = isset($_POST['chunk_size']) ? intval($_POST['chunk_size']) : 20;
		$chunk_index = isset($_POST['chunk_index']) ? intval($_POST['chunk_index']) : 0;
		
		$result = Houzez_Library_Source::sync_templates_chunked($chunk_size, $chunk_index);
		
		if ($result['success']) {
			wp_send_json_success($result);
		} else {
			wp_send_json_error($result);
		}
	}

	/**
	 * AJAX handler for getting sync progress
	 */
	public function ajax_get_sync_progress() {
		check_ajax_referer('houzez_library_nonce', 'nonce');
		
		$progress = Houzez_Library_Source::get_sync_progress();
		
		wp_send_json_success($progress);
	}

	/**
	 * AJAX handler for clearing templates
	 */
	public function ajax_clear_templates() {
		check_ajax_referer('houzez_library_nonce', 'nonce');
		
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}

		$result = Houzez_Library_Source::clear_local_templates();
		
		if ($result) {
			wp_send_json_success(array('message' => __('Local templates cleared successfully.', 'houzez')));
		} else {
			wp_send_json_error(array('message' => __('Failed to clear local templates.', 'houzez')));
		}
	}

	/**
	 * AJAX handler for getting sync status
	 */
	public function ajax_get_sync_status() {
		check_ajax_referer('houzez_library_nonce', 'nonce');
		
		$templates = get_option('houzez_local_templates', []);
		$template_count = isset($templates['elements']) ? count($templates['elements']) : 0;
		$last_sync = Houzez_Library_Source::get_last_sync_time();
		
		wp_send_json_success(array(
			'template_count' => $template_count,
			'last_sync' => $last_sync,
			'has_templates' => $template_count > 0
		));
	}

	/**
	 * AJAX handler for getting local templates
	 */
	public function ajax_get_local_templates() {
		check_ajax_referer('houzez_library_nonce', 'nonce');
		
		$templates = get_option('houzez_local_templates', []);
		
		if (!empty($templates) && isset($templates['elements'])) {
			wp_send_json_success($templates);
		} else {
			wp_send_json_error(array('message' => __('No local templates found.', 'houzez')));
		}
	}

	/**
	 * Background sync chunk handler
	 */
	public function background_sync_chunk($chunk_size = 20, $chunk_index = 0) {
		$result = Houzez_Library_Source::sync_templates_chunked($chunk_size, $chunk_index);
		
		if ($result['success'] && isset($result['completed']) && $result['completed']) {
			// All chunks completed
			$progress = Houzez_Library_Source::get_sync_progress();
			$final_message = sprintf('Chunked sync completed. Total downloaded: %d, Failed: %d', 
				$progress['downloaded'] ?? 0, $progress['failed'] ?? 0);
		}
	}

	/**
	 * AJAX handler for getting templates in batches (progressive loading)
	 */
	public function ajax_get_templates_batch() {
		check_ajax_referer('houzez_library_nonce', 'nonce');
		
		$batch_index = isset($_POST['batch_index']) ? intval($_POST['batch_index']) : 0;
		$batch_size = isset($_POST['batch_size']) ? min(100, max(10, intval($_POST['batch_size']))) : 50;
		
		$source = new Houzez_Library_Source();
		$result = $source->get_templates_batch($batch_index, $batch_size);
		
		if ($result && isset($result['elements'])) {
			wp_send_json_success($result);
		} else {
			wp_send_json_error(array('message' => __('Failed to load template batch.', 'houzez')));
		}
	}

	/**
	 * AJAX handler for getting templates with progressive loading info
	 */
	public function ajax_get_templates_progressive() {
		check_ajax_referer('houzez_library_nonce', 'nonce');
		
		$source = new Houzez_Library_Source();
		$result = $source->get_templates_progressive();
		
		if ($result && isset($result['elements'])) {
			wp_send_json_success($result);
		} else {
			wp_send_json_error(array('message' => __('Failed to load templates.', 'houzez')));
		}
	}

	/**
	 * Clean up auto-sync related options (for migration from auto-sync to manual-only)
	 */
	public static function cleanup_auto_sync_options() {
		$auto_sync_options = [
			'houzez_auto_sync_on_update',
			'houzez_auto_sync_logging',
			'houzez_theme_version',
			'houzez_last_auto_sync_result',
			'houzez_auto_sync_log',
			'houzez_auto_sync_on_update_set',
			'houzez_last_sync_trigger'
		];
		
		foreach ($auto_sync_options as $option) {
			delete_option($option);
		}
		
		// Clear any scheduled auto-sync events
		wp_clear_scheduled_hook('houzez_auto_sync_templates');
		wp_clear_scheduled_hook('houzez_sync_chunk');
	}

	/**
	 * Override registered Elementor native actions.
	 *
	 * @since 1.0.0
	 *
	 * @param object $ajax AJAX manager.
	 */
	public function register_ajax_actions( $ajax ) {
		// phpcs:disable
		if ( ! isset( $_REQUEST['actions'] ) ) {
			return;
		}

		$actions = json_decode( stripslashes( $_REQUEST['actions'] ), true );

		$data = false;

		foreach ( $actions as $action_data ) {
			if ( ! isset( $action_data['get_template_data'] ) ) {
				$data = $action_data;
			}
		}

		if ( ! $data ) {
			return;
		}

		if ( ! isset( $data['data'] ) ) {
			return;
		}

		$data = $data['data'];

		if ( empty( $data['template_id'] ) ) {
			return;
		}

		if ( false === strpos( $data['template_id'], 'houzez_' ) ) {
			return;
		}

		// Once found out that current request is for Houzez then replace the native action.
		$ajax->register_ajax_action( 'get_template_data', array( $this, 'get_template_data' ) );
		// phpcs:enable
	}

	/**
	 * Get template data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Request arguments.
	 *
	 * @return array Template data.
	 */
	public static function get_template_data( $args ) {
		$source = Elementor::instance()->templates_manager->get_source( 'houzez' );

		$args['template_id'] = intval( str_replace( 'houzez_', '', $args['template_id'] ) );
		
		$data = $source->get_data( $args );

		// Check if data is a WP_Error before proceeding
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		// Log cache information to browser console if available
		if (isset($data['houzez_cache_info'])) {
			$cache_info = $data['houzez_cache_info'];
			$log_message = sprintf(
				'Houzez Template Import | ID: %d | Source: %s | API Cached: %s',
				$cache_info['template_id'],
				$cache_info['cache_source'],
				isset($cache_info['api_cached']) ? ($cache_info['api_cached'] ? 'Yes' : 'No') : 'Unknown'
			);
			
			// Add JavaScript to log to console
			add_action('wp_footer', function() use ($log_message, $cache_info) {
				echo '<script>console.log("' . esc_js($log_message) . '", ' . wp_json_encode($cache_info) . ');</script>';
			});
			
			add_action('admin_footer', function() use ($log_message, $cache_info) {
				echo '<script>console.log("' . esc_js($log_message) . '", ' . wp_json_encode($cache_info) . ');</script>';
			});
		}

		return $data;
	}

	public function render()
	{
	?>
		<script type="text/html" id="tmpl-elementor-houzez-library-modal-header">
			<div class="elementor-templates-modal__header">
				<div class="elementor-templates-modal__header__logo-area">
					<div class="elementor-templates-modal__header__logo">
						<span class="elementor-templates-modal__header__logo__title">
							Houzez Library
						</span>
					</div>
				</div>

				<div class="elementor-templates-modal__header__menu-area">
					<div id="elementor-houzez-library-header-menu">
						<div id="houzez-tab-block" class="elementor-component-tab elementor-template-library-menu-item elementor-active" data-tab="block">Blocks</div>
						<div id="houzez-tab-template" class="elementor-component-tab elementor-template-library-menu-item" data-tab="template">Pages</div>
					</div>
				</div>

				<div class="elementor-templates-modal__header__items-area">
					<div class="elementor-templates-modal__header__close elementor-templates-modal__header__close--normal elementor-templates-modal__header__item">
						<i class="eicon-close" aria-hidden="true" title="<?php echo esc_html__('Close', 'houzez'); ?>"></i>

						<span class="elementor-screen-only">
							<?php echo esc_html__('Close', 'houzez'); ?>
						</span>
					</div>
				</div>
			</div>
		</script>

		<script type="text/html" id="tmpl-elementor-houzez-library-modal-order">
			<div id="elementor-template-library-filter">
				<select id="elementor-template-library-filter-subtype" class="elementor-template-library-filter-select" data-elementor-filter="subtype">
					<option value="all"><?php echo esc_html__('All', 'houzez'); ?></option>
					<# data.tags.forEach(function(item, i) { #>
						<option value="{{{item.slug}}}">{{{item.title}}}</option>
						<# }); #>
				</select>
			</div>
		</script>

		<script type="text/template" id="tmpl-elementor-houzez-library-header-menu">
			<# jQuery.each( tabs, ( tab, args ) => { #>	
				<div class="elementor-component-tab elementor-template-library-menu-item" data-tab="{{{ tab }}}">{{{ args.title }}}</div>
			<# } ); #>
		</script>

		<script type="text/html" id="tmpl-elementor-houzez-library-modal">
			<div id="elementor-template-library-templates" data-template-source="remote">
				<div id="elementor-template-library-toolbar">
					<div id="elementor-template-library-filter-toolbar-remote" class="elementor-template-library-filter-toolbar"></div>

					<div id="elementor-template-library-filter-text-wrapper">
						<label for="elementor-template-library-filter-text" class="elementor-screen-only"><?php echo esc_html__('Search Templates:', 'houzez'); ?></label>
						<input id="elementor-template-library-filter-text" placeholder="<?php echo esc_attr__('Search', 'houzez'); ?>">
						<i class="eicon-search"></i>
					</div>
				</div>

				<div id="elementor-template-library-templates-container"></div>

				<div id="elementor-template-library-footer-banner">
					<img class="elementor-nerd-box-icon" src="<?php echo get_bloginfo('url'); ?>/wp-content/plugins/elementor/assets/images/information.svg">
					<div class="elementor-excerpt">Templates loaded from local storage. Use sync button to update.</div>
				</div>
			</div>

			<div class="elementor-loader-wrapper" style="display: none">
				<div class="elementor-loader">
					<div class="elementor-loader-boxes">
						<div class="elementor-loader-box"></div>
						<div class="elementor-loader-box"></div>
						<div class="elementor-loader-box"></div>
						<div class="elementor-loader-box"></div>
					</div>
				</div>
				<div class="elementor-loading-title"><?php echo esc_html__('Loading', 'houzez'); ?></div>
			</div>
		</script>

		<script type="text/html" id="tmpl-elementor-houzez-library-modal-item">
			<# data.elements.forEach(function(item, i) { #>
				
				<div class="elementor-template-library-template elementor-template-library-template-remote elementor-template-library-template-{{{item.type === 'template' ? 'page' : 'block'}}}" data-slug="{{{item.slug}}}" data-tag="{{{item.category}}}" data-type="{{{item.type}}}" data-name="{{{item.title}}}">
						
					<div class="elementor-template-library-template-body">
						<# if (item.type === 'block') { #>
							<img src="{{{item.image}}}">
						<# } else { #>
						<div class="elementor-template-library-template-screenshot" style="background-image: url({{{item.image}}})"></div>
						<# } #>

						<a class="elementor-template-library-template-preview" href="{{{item.link}}}" target="_blank">
							<i class="eicon-zoom-in-bold" aria-hidden="true"></i>
						</a>
					</div>

					<div class="elementor-template-library-template-footer">
						<a class="elementor-template-library-template-action elementor-template-library-template-insert elementor-button" data-id="{{{item.id}}}">
							<i class="eicon-file-download" aria-hidden="true"></i>
							<span class="elementor-button-title">Insert</span>
						</a>
						<div class="houzez-elementor-template-library-template-name">{{{item.title}}}</div>
					</div>
				</div>
				<# }); #>
		</script>
<?php
	}
}
