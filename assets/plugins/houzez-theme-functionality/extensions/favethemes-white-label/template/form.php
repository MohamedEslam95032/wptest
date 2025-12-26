<?php
$settings = Favethemes_White_Label::get_settings(); 


// Handle notifications
$notification = '';
$notification_type = '';

if (isset($_GET['settings-updated'])) {
    $notification = __('White label settings updated successfully!', 'houzez');
    $notification_type = 'success';
}

// Calculate stats
$configured_fields = 0;
$total_fields = 6; // branding, name, author, author_url, description, screenshot, branding-logo

if (!empty($settings['branding'])) $configured_fields++;
if (!empty($settings['name'])) $configured_fields++;
if (!empty($settings['author'])) $configured_fields++;
if (!empty($settings['author_url'])) $configured_fields++;
if (!empty($settings['description'])) $configured_fields++;
if (!empty($settings['screenshot'])) $configured_fields++;
if (!empty($settings['branding-logo'])) $configured_fields++;

$completion_percentage = ($configured_fields / $total_fields) * 100;
?>

<div class="wrap houzez-template-library">
    <div class="houzez-header">
        <div class="houzez-header-content">
            <div class="houzez-logo">
                <h1><?php esc_html_e('White Label Settings', 'houzez'); ?></h1>
            </div>
            <div class="houzez-header-actions">
                <button type="submit" form="white-label-form" class="houzez-btn houzez-btn-primary">
                    <i class="dashicons dashicons-yes-alt"></i>
                    <?php esc_html_e('Save Changes', 'houzez'); ?>
                </button>
                <button type="button" class="houzez-btn houzez-btn-secondary" onclick="document.getElementById('white-label-form').reset();">
                    <i class="dashicons dashicons-undo"></i>
                    <?php esc_html_e('Reset Form', 'houzez'); ?>
                </button>
            </div>
        </div>
    </div>

    <div class="houzez-dashboard">
        <!-- Notifications -->
        <?php if ($notification): ?>
        <div id="houzez-notification" class="houzez-notification <?php echo esc_attr($notification_type); ?>">
            <span class="dashicons dashicons-<?php echo $notification_type === 'success' ? 'yes-alt' : 'warning'; ?>"></span>
            <?php echo esc_html($notification); ?>
        </div>
        <?php endif; ?>

        <!-- Quick Stats -->
        <div class="houzez-stats-grid">
            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-admin-appearance"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php echo esc_html($configured_fields); ?>/<?php echo esc_html($total_fields); ?></h3>
                    <p><?php esc_html_e('Fields Configured', 'houzez'); ?></p>
                </div>
            </div>

            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-chart-bar"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php echo round($completion_percentage); ?>%</h3>
                    <p><?php esc_html_e('Completion Rate', 'houzez'); ?></p>
                </div>
            </div>

            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-<?php echo $settings['hide_themes_customizer'] ? 'hidden' : 'visibility'; ?>"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php echo $settings['hide_themes_customizer'] ? __('Hidden', 'houzez') : __('Visible', 'houzez'); ?></h3>
                    <p><?php esc_html_e('Customizer Themes', 'houzez'); ?></p>
                </div>
            </div>

            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-<?php echo !empty($settings['branding-logo']) ? 'format-image' : 'format-aside'; ?>"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php echo !empty($settings['branding-logo']) ? __('Custom', 'houzez') : __('Default', 'houzez'); ?></h3>
                    <p><?php esc_html_e('Branding Logo', 'houzez'); ?></p>
                </div>
            </div>
        </div>

        <!-- Main Form Card -->
        <div class="houzez-main-card">
            <div class="houzez-card-header">
                <h2>
                    <i class="dashicons dashicons-admin-appearance"></i>
                    <?php esc_html_e('Theme Branding Configuration', 'houzez'); ?>
                </h2>
                <div class="houzez-status-badge <?php echo $completion_percentage >= 80 ? 'houzez-status-success' : 'houzez-status-warning'; ?>">
                    <?php echo $completion_percentage >= 80 ? __('Well Configured', 'houzez') : __('Needs Setup', 'houzez'); ?>
                </div>
            </div>
            <div class="houzez-card-body">
                <p class="houzez-description">
                    <?php esc_html_e('Customize the theme branding to match your agency or company identity. These settings will replace the default Houzez branding throughout the admin area and theme appearance.', 'houzez'); ?>
                </p>
                
                <form id="white-label-form" class="houzez-fields-form" method="post" action="options.php">
							<?php settings_fields( 'favethemes_branding' ); ?>
							<?php wp_nonce_field( 'favethemes-white-label', 'favethemes-white-label-nonce' ); ?>

                    <div class="houzez-form-grid">
                        <!-- Theme Branding -->
                        <div class="houzez-form-group">
                            <label class="houzez-form-label" for="branding">
                                <i class="dashicons dashicons-admin-generic"></i>
                                <?php esc_html_e('Theme Branding', 'houzez'); ?>
                                <span class="required">*</span>
                            </label>
                            <input type="text" class="houzez-form-input" name="favethemes_branding[branding]" id="branding" value="<?php echo esc_attr( $settings['branding'] ); ?>" placeholder="<?php esc_attr_e('Enter your brand name', 'houzez'); ?>">
                            <p class="houzez-form-help"><?php esc_html_e('This replaces "Houzez" throughout the admin area', 'houzez'); ?></p>
							</div>
							
                        <!-- Theme Name -->
                        <div class="houzez-form-group">
                            <label class="houzez-form-label" for="theme-name">
                                <i class="dashicons dashicons-admin-appearance"></i>
                                <?php esc_html_e('Theme Name', 'houzez'); ?>
                            </label>
                            <input type="text" class="houzez-form-input" name="favethemes_branding[name]" id="theme-name" value="<?php echo esc_attr( $settings['name'] ); ?>" placeholder="<?php esc_attr_e('Custom theme name', 'houzez'); ?>">
                            <p class="houzez-form-help"><?php esc_html_e('Replaces the theme name in Appearance > Themes', 'houzez'); ?></p>
							</div>

                        <!-- Theme Author -->
                        <div class="houzez-form-group">
                            <label class="houzez-form-label" for="theme-author">
                                <i class="dashicons dashicons-admin-users"></i>
                                <?php esc_html_e('Theme Author', 'houzez'); ?>
                            </label>
                            <input type="text" class="houzez-form-input" name="favethemes_branding[author]" id="theme-author" value="<?php echo esc_attr( $settings['author'] ); ?>" placeholder="<?php esc_attr_e('Your company name', 'houzez'); ?>">
                            <p class="houzez-form-help"><?php esc_html_e('Replaces the theme author in Appearance > Themes', 'houzez'); ?></p>
							</div>

                        <!-- Author URL -->
                        <div class="houzez-form-group">
                            <label class="houzez-form-label" for="author_url">
                                <i class="dashicons dashicons-admin-links"></i>
                                <?php esc_html_e('Author URL', 'houzez'); ?>
                            </label>
                            <input type="url" class="houzez-form-input" name="favethemes_branding[author_url]" id="author_url" value="<?php echo esc_url( $settings['author_url'] ); ?>" placeholder="<?php esc_attr_e('https://yourwebsite.com', 'houzez'); ?>">
                            <p class="houzez-form-help"><?php esc_html_e('Your website URL that will be linked from the theme author', 'houzez'); ?></p>
							</div>

                        <!-- Theme Description -->
                        <div class="houzez-form-group houzez-form-group-full">
                            <label class="houzez-form-label" for="theme-description">
                                <i class="dashicons dashicons-text-page"></i>
                                <?php esc_html_e('Theme Description', 'houzez'); ?>
                            </label>
                            <textarea class="houzez-form-textarea" name="favethemes_branding[description]" id="theme-description" rows="3" placeholder="<?php esc_attr_e('Enter a custom description for your theme...', 'houzez'); ?>"><?php echo esc_attr( $settings['description'] ); ?></textarea>
                            <p class="houzez-form-help"><?php esc_html_e('Custom description that appears in Appearance > Themes', 'houzez'); ?></p>
                        </div>
							</div>

                    <!-- Media Upload Section -->
                    <div class="houzez-main-card" style="margin-top: 30px;">
                        <div class="houzez-card-header">
                            <h2>
                                <i class="dashicons dashicons-format-image"></i>
                                <?php esc_html_e('Visual Branding', 'houzez'); ?>
                            </h2>
                        </div>
                        <div class="houzez-card-body">
                            <div class="houzez-form-grid">
                                <!-- Theme Screenshot -->
                                <div class="houzez-form-group">
                                    <label class="houzez-form-label" for="theme-screenshot">
                                        <i class="dashicons dashicons-format-image"></i>
                                        <?php esc_html_e('Theme Screenshot', 'houzez'); ?>
                                    </label>
                                    <div class="houzez-media-upload-wrapper">
                                        <div class="favethemes-media-live-preview" <?php echo !empty($settings['screenshot']) ? '' : 'style="display:none;"'; ?>>
                                            <?php if (!empty($settings['screenshot'])): ?>
                                                <img src="<?php echo esc_url($settings['screenshot']); ?>" alt="<?php esc_attr_e('Theme Screenshot', 'houzez'); ?>" />
                                            <?php endif; ?>
								</div>
								<div class="favethemes-upload-field">
                                            <input class="favethemes-media-input houzez-form-input" type="text" name="favethemes_branding[screenshot]" value="<?php echo esc_url( $settings['screenshot'] ); ?>" placeholder="<?php esc_attr_e('Screenshot URL', 'houzez'); ?>">
                                            <div class="houzez-upload-buttons">
                                                <button type="button" class="favethemes-screenshot-upload-button houzez-btn houzez-btn-outline">
                                                    <i class="dashicons dashicons-upload"></i>
                                                    <?php esc_html_e('Upload', 'houzez'); ?>
                                                </button>
                                                <button type="button" class="favethemes-media-remove houzez-btn houzez-btn-danger" <?php echo empty($settings['screenshot']) ? 'style="display:none;"' : ''; ?>>
                                                    <i class="dashicons dashicons-trash"></i>
                                                    <?php esc_html_e('Remove', 'houzez'); ?>
                                                </button>
                                            </div>
								</div>
                                    </div>
                                    <p class="houzez-form-help"><?php esc_html_e('Custom screenshot for Appearance > Themes. Recommended size: 880x660px', 'houzez'); ?></p>
							</div>

                                <!-- Branding Logo -->
                                <div class="houzez-form-group">
                                    <label class="houzez-form-label" for="branding-logo">
                                        <i class="dashicons dashicons-admin-customizer"></i>
                                        <?php esc_html_e('Admin Logo', 'houzez'); ?>
                                    </label>
                                    <div class="houzez-media-upload-wrapper">
                                        <div class="favethemes-logo-live-preview" <?php echo !empty($settings['branding-logo']) ? '' : 'style="display:none;"'; ?>>
                                            <?php if (!empty($settings['branding-logo'])): ?>
                                                <img src="<?php echo esc_url($settings['branding-logo']); ?>" alt="<?php esc_attr_e('Branding Logo', 'houzez'); ?>" />
                                            <?php endif; ?>
								</div>
								<div class="favethemes-logo-upload-field">
                                            <input class="favethemes-logo-input houzez-form-input" type="text" name="favethemes_branding[branding-logo]" value="<?php echo esc_url( $settings['branding-logo'] ); ?>" placeholder="<?php esc_attr_e('Logo URL', 'houzez'); ?>">
                                            <div class="houzez-upload-buttons">
                                                <button type="button" class="favethemes-logo-upload-button houzez-btn houzez-btn-outline">
                                                    <i class="dashicons dashicons-upload"></i>
                                                    <?php esc_html_e('Upload', 'houzez'); ?>
                                                </button>
                                                <button type="button" class="favethemes-logo-remove houzez-btn houzez-btn-danger" <?php echo empty($settings['branding-logo']) ? 'style="display:none;"' : ''; ?>>
                                                    <i class="dashicons dashicons-trash"></i>
                                                    <?php esc_html_e('Remove', 'houzez'); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="houzez-form-help"><?php esc_html_e('Logo displayed in the admin panel header. Recommended size: 127x24px', 'houzez'); ?></p>
                                </div>
                            </div>
								</div>
							</div>

                    <!-- Settings Section -->
                    <div class="houzez-main-card" style="margin-top: 30px;">
                        <div class="houzez-card-header">
                            <h2>
                                <i class="dashicons dashicons-admin-settings"></i>
                                <?php esc_html_e('Advanced Settings', 'houzez'); ?>
                            </h2>
                        </div>
                        <div class="houzez-card-body">
                            <div class="houzez-form-group houzez-form-group-full">
                                <label class="houzez-checkbox-label">
									<input type="checkbox" id="themes-hide-customizer" name="favethemes_branding[hide_themes_customizer]" value="1" <?php checked( '1', $settings['hide_themes_customizer'] ); ?>>
                                    <span class="houzez-checkbox-text">
                                        <strong><?php esc_html_e('Hide Themes Section in Customizer', 'houzez'); ?></strong>
                                        <span class="houzez-checkbox-desc"><?php esc_html_e('Prevents users from switching themes through the WordPress Customizer', 'houzez'); ?></span>
                                    </span>
								</label>
                            </div>
                        </div>
							</div>

                    <div class="houzez-form-actions">
                        <div class="houzez-form-actions-left">
                            <button type="button" class="houzez-btn houzez-btn-outline" onclick="document.getElementById('white-label-form').reset();">
                                <i class="dashicons dashicons-undo"></i>
                                <?php esc_html_e('Reset Form', 'houzez'); ?>
                            </button>
                        </div>
                        <div class="houzez-form-actions-right">
                            <button type="submit" name="favethemes_branding_save" class="houzez-btn houzez-btn-primary">
                                <i class="dashicons dashicons-yes-alt"></i>
                                <?php esc_html_e('Save Changes', 'houzez'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
							</div>

        <!-- Information Card -->
        <div class="houzez-main-card">
            <div class="houzez-card-header">
                <h2>
                    <i class="dashicons dashicons-info"></i>
                    <?php esc_html_e('How White Labeling Works', 'houzez'); ?>
                </h2>
            </div>
            <div class="houzez-card-body">
                <div class="houzez-actions houzez-actions-three-column">
                    <div class="houzez-action">
                        <div class="houzez-action-icon">
                            <i class="dashicons dashicons-admin-appearance"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('Theme Appearance', 'houzez'); ?></h4>
                            <p><?php esc_html_e('Customize how your theme appears in the WordPress admin, including name, author, and description in the Themes section.', 'houzez'); ?></p>
                        </div>
							</div>

                    <div class="houzez-action">
                        <div class="houzez-action-icon">
                            <i class="dashicons dashicons-admin-generic"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('Admin Branding', 'houzez'); ?></h4>
                            <p><?php esc_html_e('Replace "Houzez" branding throughout the admin area with your own company or agency branding for a professional look.', 'houzez'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-action">
                        <div class="houzez-action-icon">
                            <i class="dashicons dashicons-shield"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('Client Protection', 'houzez'); ?></h4>
                            <p><?php esc_html_e('Hide theme switching options and present a seamless, branded experience to your clients without exposing the underlying theme.', 'houzez'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>

<!-- Notifications Auto-hide Script -->
<script>
jQuery(document).ready(function($) {
    // Auto-hide notifications after 5 seconds
    setTimeout(function() {
        $('#houzez-notification').fadeOut();
    }, 5000);
    
    // Animate stats on page load
    $('.houzez-stat-card').each(function(index) {
        $(this).css('opacity', '0').delay(index * 100).animate({
            opacity: 1
        }, 500);
    });
});
</script>