<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$houzez_activation = get_option( 'houzez_activation' );
$purchase_code = get_option( 'houzez_purchase_code' );


// Handle notifications
$notification = '';
$notification_type = '';

if (isset($_GET['verification_success'])) {
    $notification = __('Purchase code verified successfully! All features are now unlocked.', 'houzez');
    $notification_type = 'success';
} elseif (isset($_GET['deactivation_success'])) {
    $notification = __('Purchase code deactivated successfully.', 'houzez');
    $notification_type = 'success';
} elseif (isset($_GET['verification_error'])) {
    $notification = __('Verification failed. Please check your purchase code and try again.', 'houzez');
    $notification_type = 'error';
}

$is_verified = ($houzez_activation == 'activated');
?>

<div class="wrap houzez-template-library">
    <div class="houzez-header">
        <div class="houzez-header-content">
            <div class="houzez-logo">
                <h1><?php esc_html_e('Purchase Verification', 'houzez'); ?></h1>
            </div>
            <div class="houzez-header-actions">
                <?php if ($is_verified): ?>
                    <button type="submit" form="admin-houzez-form" class="houzez-btn houzez-btn-secondary">
                        <i class="dashicons dashicons-dismiss"></i>
                        <?php esc_html_e('Deactivate', 'houzez'); ?>
                    </button>
                <?php else: ?>
                    <button type="submit" form="admin-houzez-form" class="houzez-btn houzez-btn-primary">
                        <i class="dashicons dashicons-yes-alt"></i>
                        <?php esc_html_e('Verify Purchase', 'houzez'); ?>
                    </button>
                <?php endif; ?>
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
                    <i class="dashicons dashicons-<?php echo ($houzez_activation == 'activated') ? 'yes-alt' : 'lock'; ?>"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php echo ($houzez_activation == 'activated') ? __('Verified', 'houzez') : __('Unverified', 'houzez'); ?></h3>
                    <p><?php esc_html_e('License Status', 'houzez'); ?></p>
                </div>
            </div>

            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-admin-plugins"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php echo ($houzez_activation == 'activated') ? __('Enabled', 'houzez') : __('Disabled', 'houzez'); ?></h3>
                    <p><?php esc_html_e('Plugin Installation', 'houzez'); ?></p>
                </div>
            </div>

            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-download"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php echo ($houzez_activation == 'activated') ? __('Available', 'houzez') : __('Restricted', 'houzez'); ?></h3>
                    <p><?php esc_html_e('Demo Import', 'houzez'); ?></p>
                </div>
            </div>
        </div>

        <!-- Main Verification Card -->
        <div class="houzez-main-card">
            <div class="houzez-card-header">
                <h2>
                    <i class="dashicons dashicons-<?php echo ($houzez_activation == 'activated') ? 'yes-alt' : 'lock'; ?>"></i>
                    <?php esc_html_e('Houzez Purchase Verification', 'houzez'); ?>
                </h2>
                <div class="houzez-status-badge <?php echo ($houzez_activation == 'activated') ? 'houzez-status-success' : 'houzez-status-warning'; ?>">
                    <?php echo ($houzez_activation == 'activated') ? __('Active', 'houzez') : __('Inactive', 'houzez'); ?>
                </div>
            </div>
            <div class="houzez-card-body">
                <p class="houzez-description">
                    <?php esc_html_e('Enter purchase code to verify your purchase. This will allow you to install plugins, import demo and unlock all features', 'houzez'); ?>
                </p>

						<form id="admin-houzez-form" class="admin-houzez-form">
							<?php echo wp_nonce_field( 'envato_api_nonce', 'envato_api_nonce_field' ,true, false ); ?>

							<div class="form-field">
								<?php if( $houzez_activation == 'activated' ) { ?>
									<label><?php esc_html_e('Purchase Code', 'houzez'); ?> *</label>
									<?php if( ! empty( $purchase_code ) ) { ?>
                                <div class="houzez-verified-field-wrapper">
                                    <input id="item_purchase_code" autocomplete="off" readonly class="regular-text houzez-verified-input" type="text" placeholder="Enter item purchase code." value="<?php echo esc_attr($purchase_code); ?>">
                                    <div class="verified-overlay">
                                        <i class="dashicons dashicons-yes-alt"></i>
                                        <span><?php esc_html_e('Verified', 'houzez'); ?></span>
                                    </div>
                                </div>
									<?php } ?>
									<input type="hidden" name="action" value="houzez_deactivate_purchase">
                        <?php } else { ?>
									<label><?php esc_html_e('Purchase Code', 'houzez'); ?> *</label>
									<input id="item_purchase_code" autocomplete="off" class="regular-text" type="text" placeholder="Enter item purchase code.">
									<input type="hidden" name="action" value="houzez_purchase_verify">
                        <?php } ?>
							</div>

							<div>
								<p>
				                    You can consult <a target="_blank" href="https://favethemes.zendesk.com/hc/en-us/articles/360038085112-Where-Is-My-Purchase-Code-"> this article</a> to learn how to get item purchase code or you can purchase <a href="https://themeforest.net/item/houzez-real-estate-wordpress-theme/15752549" target="_blank">new license</a> from themeforest which will include 6 months free support and lifetime updates.  
				                </p>
							</div>

							<div class="submit">
								<?php if( $houzez_activation == 'activated' ) { ?>
									<button id="houzez-deactivate-code" type="submit" class="button button-primary"><?php esc_html_e('Deactivate', 'houzez'); ?></button>
                        <?php } else { ?>
									<button id="houzez-purchase-code" type="submit" class="button button-primary"><?php esc_html_e('Verify Purchase', 'houzez'); ?></button>
                        <?php } ?>
							</div>

							<div class="form-field" id="form-messages"></div>
						</form>
            </div>
        </div>

        <!-- Features Information Card -->
        <div class="houzez-main-card">
            <div class="houzez-card-header">
                <h2>
                    <i class="dashicons dashicons-star-filled"></i>
                    <?php esc_html_e('Premium Features', 'houzez'); ?>
                </h2>
            </div>
            
            <div class="houzez-card-body">
                <div class="houzez-actions">
                    <div class="houzez-action">
                        <div class="houzez-action-icon <?php echo $is_verified ? '' : 'houzez-icon-danger'; ?>">
                            <i class="dashicons dashicons-admin-plugins"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('Plugin Installation', 'houzez'); ?></h4>
                            <p><?php esc_html_e('Install and manage premium Houzez plugins including CRM, Login/Register, and Theme Functionality.', 'houzez'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-action">
                        <div class="houzez-action-icon <?php echo $is_verified ? '' : 'houzez-icon-danger'; ?>">
                            <i class="dashicons dashicons-download"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('Demo Content Import', 'houzez'); ?></h4>
                            <p><?php esc_html_e('One-click import of demo content, including properties, pages, and theme settings to get started quickly.', 'houzez'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-action">
                        <div class="houzez-action-icon <?php echo $is_verified ? '' : 'houzez-icon-danger'; ?>">
                            <i class="dashicons dashicons-sos"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('Premium Support', 'houzez'); ?></h4>
                            <p><?php esc_html_e('Access to dedicated support team with 6 months of free support included with your purchase.', 'houzez'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>

<style>
/* Purchase Code Field Enhancements - Only UI improvements */
.houzez-verified-field-wrapper {
    position: relative;
    display: inline-block;
    width: 100%;
}

.houzez-verified-input {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%) !important;
    border: 2px solid #28a745 !important;
    color: #155724 !important;
    font-weight: 600 !important;
    font-family: 'Courier New', monospace !important;
    letter-spacing: 1px !important;
    cursor: not-allowed !important;
    padding-right: 100px !important;
}

.houzez-verified-input:focus {
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.2) !important;
    border-color: #28a745 !important;
}

.verified-overlay {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    gap: 6px;
    background: #28a745;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    pointer-events: none;
    box-shadow: 0 2px 6px rgba(40, 167, 69, 0.3);
}

.verified-overlay .dashicons {
    font-size: 14px;
    width: 14px;
    height: 14px;
    line-height: 14px;
}

/* Enhanced ps-verified styling */
.ps-verified {
    color: #28a745 !important;
    font-weight: 600 !important;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
    padding: 6px 12px;
    background: #d4edda;
    border-radius: 6px;
    border: 1px solid #c3e6cb;
}

.ps-verified:before {
    content: "\f147";
    font-family: "dashicons";
    font-size: 16px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .verified-overlay {
        position: static;
        transform: none;
        margin-top: 8px;
        align-self: flex-start;
    }
    
    .houzez-verified-field-wrapper {
        display: flex;
        flex-direction: column;
        align-items: stretch;
    }
    
    .houzez-verified-input {
        padding-right: 16px !important;
    }
}
</style>