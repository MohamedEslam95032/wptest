<?php
/**
 * Verification Requests Admin Page
 *
 * @package Houzez
 * @since Houzez 1.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if user has permission
if (!current_user_can('manage_options')) {
    wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'houzez'));
}


// Get verification requests
$status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$verification_requests = $GLOBALS['houzez_user_verification']->get_verification_requests($status_filter);

// Sort verification requests by submission date (newest first)
usort($verification_requests, function($a, $b) {
    $a_date = isset($a['verification_data']['submitted_on']) ? strtotime($a['verification_data']['submitted_on']) : 0;
    $b_date = isset($b['verification_data']['submitted_on']) ? strtotime($b['verification_data']['submitted_on']) : 0;
    
    // If additional info was submitted, use that date instead if it's more recent
    if (isset($a['verification_data']['additional_info_submitted_on'])) {
        $a_additional_date = strtotime($a['verification_data']['additional_info_submitted_on']);
        if ($a_additional_date > $a_date) {
            $a_date = $a_additional_date;
        }
    }
    
    if (isset($b['verification_data']['additional_info_submitted_on'])) {
        $b_additional_date = strtotime($b['verification_data']['additional_info_submitted_on']);
        if ($b_additional_date > $b_date) {
            $b_date = $b_additional_date;
        }
    }
    
    return $b_date - $a_date; // Descending order
});

// Count requests with additional information
$requests_with_additional_info = 0;
$pending_count = 0;
$approved_count = 0;
$rejected_count = 0; 
$info_required_count = 0;

foreach ($verification_requests as $request) {
    $verification_data = $request['verification_data'];
    $status = isset($verification_data['status']) ? $verification_data['status'] : '';
    
    // Count by status
    if ($status === 'pending') {
        $pending_count++;
        if (isset($verification_data['additional_document_url']) && !empty($verification_data['additional_document_url'])) {
            $requests_with_additional_info++;
        }
    } elseif ($status === 'approved') {
        $approved_count++;
    } elseif ($status === 'rejected') {
        $rejected_count++;
    } elseif ($status === 'additional_info_required') {
        $info_required_count++;
    }
}

// Total count
$total_requests = count($verification_requests);

// Handle notifications
$notification = '';
$notification_type = '';

if (isset($_GET['verification_processed'])) {
    $notification = __('Verification request processed successfully!', 'houzez');
    $notification_type = 'success';
} elseif (isset($_GET['verification_error'])) {
    $notification = __('Error processing verification request. Please try again.', 'houzez');
    $notification_type = 'error';
}
?>

<div class="wrap houzez-template-library">
    <div class="houzez-header">
        <div class="houzez-header-content">
            <div class="houzez-logo">
                <h1><?php esc_html_e('User Verification Requests', 'houzez'); ?></h1>
            </div>
            <div class="houzez-header-actions">
                <a href="<?php echo esc_url(admin_url('users.php?page=houzez-verification-requests&refresh=1')); ?>" class="houzez-btn houzez-btn-secondary">
                    <i class="dashicons dashicons-update"></i>
                    <?php esc_html_e('Refresh', 'houzez'); ?>
                </a>
            </div>
        </div>
    </div>

    <div class="houzez-dashboard">
        <!-- Notification -->
        <?php if (!empty($notification)) : ?>
            <div class="houzez-notification <?php echo esc_attr($notification_type); ?>" style="margin-bottom: 20px;">
                <span class="dashicons dashicons-<?php echo $notification_type === 'success' ? 'yes-alt' : 'warning'; ?>"></span>
                <?php echo esc_html($notification); ?>
            </div>
        <?php endif; ?>

        <!-- Quick Stats -->
        <div class="houzez-stats-grid">
            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-groups"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php echo esc_html($total_requests); ?></h3>
                    <p><?php esc_html_e('Total Requests', 'houzez'); ?></p>
                </div>
            </div>

            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-clock"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php echo esc_html($pending_count); ?></h3>
                    <p><?php esc_html_e('Pending Review', 'houzez'); ?></p>
                </div>
            </div>

            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-yes-alt"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php echo esc_html($approved_count); ?></h3>
                    <p><?php esc_html_e('Approved', 'houzez'); ?></p>
                </div>
            </div>

            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-info"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php echo esc_html($info_required_count); ?></h3>
                    <p><?php esc_html_e('Info Required', 'houzez'); ?></p>
                </div>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="houzez-main-card">
            <div class="houzez-card-header">
                <h2>
                    <i class="dashicons dashicons-admin-users"></i>
                    <?php esc_html_e('Verification Management', 'houzez'); ?>
                </h2>
                <div class="houzez-status-badge <?php echo $pending_count > 0 ? 'houzez-status-warning' : 'houzez-status-success'; ?>">
                    <?php echo $pending_count > 0 ? sprintf(__('%d Pending', 'houzez'), $pending_count) : __('All Reviewed', 'houzez'); ?>
                </div>
            </div>
            <div class="houzez-card-body">
                <!-- Filter Section -->
                <div class="tablenav top">
                    <div class="alignleft actions">
                        <form method="get" class="filter-form">
                            <input type="hidden" name="page" value="houzez-verification-requests">
                            <div class="hz-filter-group">
                                
                                <select name="status" class="filter-select">
                                    <option value="" <?php selected($status_filter, ''); ?>><?php esc_html_e('All Requests', 'houzez'); ?></option>
                                    <option value="pending" <?php selected($status_filter, 'pending'); ?>><?php esc_html_e('Pending', 'houzez'); ?></option>
                                    <option value="approved" <?php selected($status_filter, 'approved'); ?>><?php esc_html_e('Approved', 'houzez'); ?></option>
                                    <option value="rejected" <?php selected($status_filter, 'rejected'); ?>><?php esc_html_e('Rejected', 'houzez'); ?></option>
                                    <option value="additional_info_required" <?php selected($status_filter, 'additional_info_required'); ?>><?php esc_html_e('Awaiting Additional Info', 'houzez'); ?></option>
                                    <option value="with_additional_info" <?php selected($status_filter, 'with_additional_info'); ?>><?php esc_html_e('With Additional Info', 'houzez'); ?></option>
                                </select>
                                <input type="submit" class="button action" value="<?php esc_attr_e('Apply', 'houzez'); ?>">
                            </div>
                        </form>
                    </div>
                    <div class="tablenav-pages one-page">
                        <span class="displaying-num">
                            <?php echo sprintf(_n('%s item', '%s items', $total_requests, 'houzez'), number_format_i18n($total_requests)); ?>
                        </span>
                    </div>
                    <br class="clear">
                </div>
                
                <!-- Verification Table -->
                <table class="wp-list-table widefat fixed striped verification-table">
                    <thead>
                        <tr>
                            <th scope="col" class="manage-column column-user"><?php esc_html_e('User', 'houzez'); ?></th>
                            <th scope="col" class="manage-column column-name"><?php esc_html_e('Full Name', 'houzez'); ?></th>
                            <th scope="col" class="manage-column column-document"><?php esc_html_e('Document Type', 'houzez'); ?></th>
                            <th scope="col" class="manage-column column-status"><?php esc_html_e('Status', 'houzez'); ?></th>
                            <th scope="col" class="manage-column column-date"><?php esc_html_e('Submission Date', 'houzez'); ?></th>
                            <th scope="col" class="manage-column column-actions"><?php esc_html_e('Actions', 'houzez'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($verification_requests)) : ?>
                        <tr>
                            <td colspan="6" class="no-items">
                                <div class="no-items-message">
                                    <span class="dashicons dashicons-info"></span>
                                    <p><?php esc_html_e('No verification requests found.', 'houzez'); ?></p>
                                </div>
                            </td>
                        </tr>
                        <?php else : ?>
                            <?php foreach ($verification_requests as $request) : 
                                $user = $request['user_data'];
                                $verification_data = $request['verification_data'];
                                $document_url = isset($verification_data['document_url']) ? $verification_data['document_url'] : '';
                                $status = isset($verification_data['status']) ? $verification_data['status'] : '';
                                $status_class = '';
                                $status_text = '';
                                
                                // Check if this request has additional information
                                $has_additional_info = isset($verification_data['additional_document_url']) && !empty($verification_data['additional_document_url']);
                                $row_class = $has_additional_info && $status === 'pending' ? 'has-additional-info' : '';
                                
                                // For highlighting newest entries
                                $is_recent = false;
                                if (isset($verification_data['submitted_on'])) {
                                    $submission_time = strtotime($verification_data['submitted_on']);
                                    $is_recent = (time() - $submission_time) < 86400; // Less than 24 hours
                                }
                                
                                if ($is_recent) {
                                    $row_class .= ' recent-submission';
                                }
                                
                                switch ($status) {
                                    case 'pending':
                                        $status_class = 'status-pending';
                                        $status_text = esc_html__('Pending', 'houzez');
                                        break;
                                    case 'approved':
                                        $status_class = 'status-approved';
                                        $status_text = esc_html__('Approved', 'houzez');
                                        break;
                                    case 'rejected':
                                        $status_class = 'status-rejected';
                                        $status_text = esc_html__('Rejected', 'houzez');
                                        break;
                                    case 'additional_info_required':
                                        $status_class = 'status-additional-info';
                                        $status_text = esc_html__('Additional Info Required', 'houzez');
                                        break;
                                    default:
                                        $status_class = 'status-none';
                                        $status_text = esc_html__('Unknown', 'houzez');
                                }
                            ?>
                            <tr class="<?php echo esc_attr($row_class); ?>">
                                <td class="column-user">
                                    <div class="user-info">
                                        <?php echo get_avatar( $user->ID, 40 ); ?>
                                        <div class="user-details">
                                            <a href="<?php echo esc_url(admin_url('user-edit.php?user_id=' . $user->ID)); ?>" target="_blank" class="user-name">
                                                <?php echo esc_html($user->display_name); ?>
                                            </a>
                                            <span class="user-email"><?php echo esc_html($user->user_email); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="column-name"><?php echo esc_html($verification_data['full_name']); ?></td>
                                <td class="column-document">
                                    <?php 
                                    // Get document types from main.php
                                    $document_types = array(
                                        'id_card' => esc_html__('ID Card', 'houzez'),
                                        'passport' => esc_html__('Passport', 'houzez'),
                                        'drivers_license' => esc_html__('Driver\'s License', 'houzez'),
                                        'business_license' => esc_html__('Business License', 'houzez'),
                                        'other' => esc_html__('Other Document', 'houzez')
                                    );
                                    
                                    $doc_type = $verification_data['document_type'];
                                    ?>
                                    <div class="document-info">
                                        <span class="document-type">
                                            <?php echo isset($document_types[$doc_type]) ? esc_html($document_types[$doc_type]) : esc_html($doc_type); ?>
                                        </span>
                                        
                                        <?php if ($document_url) : ?>
                                            <div class="document-links">
                                                <a href="<?php echo esc_url($document_url); ?>" target="_blank" class="document-link">
                                                    <span class="dashicons dashicons-visibility"></span>
                                                    <?php esc_html_e('Front', 'houzez'); ?>
                                                </a>
                                            
                                            <?php 
                                            // Show back side document if it exists
                                            if (isset($verification_data['document_back_url']) && !empty($verification_data['document_back_url'])) :
                                                $document_back_url = $verification_data['document_back_url'];
                                            ?>
                                                <a href="<?php echo esc_url($document_back_url); ?>" target="_blank" class="document-link">
                                                    <span class="dashicons dashicons-visibility"></span>
                                                    <?php esc_html_e('Back', 'houzez'); ?>
                                                </a>
                                            <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php 
                                    // Show additional document if it exists
                                    if (isset($verification_data['additional_document_url']) && !empty($verification_data['additional_document_url'])) :
                                        $additional_doc_url = $verification_data['additional_document_url'];
                                        if ($additional_doc_url) :
                                            $additional_doc_type = $verification_data['additional_document_type'];
                                    ?>
                                        <div class="additional-document">
                                            <div class="additional-header">
                                                <span class="dashicons dashicons-plus-alt"></span>
                                                <span class="document-type">
                                                    <?php echo isset($document_types[$additional_doc_type]) ? esc_html($document_types[$additional_doc_type]) : esc_html($additional_doc_type); ?>
                                                </span>
                                            </div>
                                            
                                            <div class="document-links">
                                                <a href="<?php echo esc_url($additional_doc_url); ?>" target="_blank" class="document-link">
                                                    <span class="dashicons dashicons-visibility"></span>
                                                    <?php esc_html_e('Front', 'houzez'); ?>
                                                </a>
                                                
                                                <?php 
                                                // Show back side of additional document if it exists
                                                if (isset($verification_data['additional_document_back_url']) && !empty($verification_data['additional_document_back_url'])) : 
                                                    $additional_back_url = $verification_data['additional_document_back_url'];
                                                ?>
                                                    <a href="<?php echo esc_url($additional_back_url); ?>" target="_blank" class="document-link">
                                                        <span class="dashicons dashicons-visibility"></span>
                                                        <?php esc_html_e('Back', 'houzez'); ?>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <?php if (isset($verification_data['additional_notes']) && !empty($verification_data['additional_notes'])) : ?>
                                                <div class="additional-notes">
                                                    <strong><?php esc_html_e('Notes:', 'houzez'); ?></strong>
                                                    <p><?php echo esc_html($verification_data['additional_notes']); ?></p>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($verification_data['additional_info_submitted_on']) && !empty($verification_data['additional_info_submitted_on'])) : ?>
                                                <div class="additional-info-date">
                                                    <span class="dashicons dashicons-calendar-alt"></span>
                                                    <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($verification_data['additional_info_submitted_on']))); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php 
                                        endif;
                                    endif; 
                                    ?>
                                </td>
                                <td class="column-status">
                                    <span class="status-badge <?php echo esc_attr($status_class); ?>"><?php echo $status_text; ?></span>
                                    <?php if ($status === 'pending' && $has_additional_info) : ?>
                                        <span class="additional-info-icon dashicons dashicons-info" title="<?php esc_attr_e('Additional information provided', 'houzez'); ?>"></span>
                                    <?php endif; ?>
                                    <?php if ($status === 'rejected' && !empty($verification_data['rejection_reason'])) : ?>
                                        <div class="rejection-reason">
                                            <strong><?php esc_html_e('Reason:', 'houzez'); ?></strong> 
                                            <?php echo esc_html($verification_data['rejection_reason']); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($status === 'additional_info_required' && !empty($verification_data['additional_info_request'])) : ?>
                                        <div class="additional-info-request">
                                            <strong><?php esc_html_e('Requested:', 'houzez'); ?></strong> 
                                            <?php echo esc_html($verification_data['additional_info_request']); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="column-date">
                                    <?php 
                                    $original_date = isset($verification_data['submitted_on']) 
                                        ? strtotime($verification_data['submitted_on']) 
                                        : 0;
                                    
                                    $has_additional = isset($verification_data['additional_info_submitted_on']) 
                                        && !empty($verification_data['additional_info_submitted_on']);
                                    
                                    $additional_date = $has_additional 
                                        ? strtotime($verification_data['additional_info_submitted_on']) 
                                        : 0;
                                    
                                    if ($has_additional && $additional_date > $original_date) {
                                        // Show additional info submission date with indicator
                                        echo '<div class="date-wrapper hz-updated">';
                                        echo '<span class="dashicons dashicons-backup"></span>';
                                        echo '<div class="date-info">';
                                        echo '<span class="date-label">' . esc_html__('Updated:', 'houzez') . '</span>';
                                        echo '<span class="date-value">' . esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $additional_date)) . '</span>';
                                        echo '</div></div>';
                                        
                                        // Show original submission date in smaller text
                                        echo '<div class="date-wrapper original">';
                                        echo '<span class="dashicons dashicons-calendar-alt"></span>';
                                        echo '<div class="date-info">';
                                        echo '<span class="date-label">' . esc_html__('Original:', 'houzez') . '</span>';
                                        echo '<span class="date-value">' . esc_html(date_i18n(get_option('date_format'), $original_date)) . '</span>';
                                        echo '</div></div>';
                                    } else {
                                        // Show original submission date
                                        echo '<div class="date-wrapper">';
                                        echo '<span class="dashicons dashicons-calendar-alt"></span>';
                                        echo '<div class="date-info">';
                                        echo '<span class="date-label">' . esc_html__('Submitted:', 'houzez') . '</span>';
                                        echo '<span class="date-value">' . esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $original_date)) . '</span>';
                                        echo '</div></div>';
                                    }
                                    ?>
                                </td>
                                <td class="column-actions">
                                    <?php if ($status === 'pending') : ?>
                                        <div class="request-actions">
                                            <button type="button" class="button button-primary approve-request" data-user-id="<?php echo esc_attr($user->ID); ?>">
                                                <span class="dashicons dashicons-yes"></span>
                                                <?php esc_html_e('Approve', 'houzez'); ?>
                                            </button>
                                            <button type="button" class="button button-reject reject-request" data-user-id="<?php echo esc_attr($user->ID); ?>">
                                                <span class="dashicons dashicons-no-alt"></span>
                                                <?php esc_html_e('Reject', 'houzez'); ?>
                                            </button>
                                            <button type="button" class="button button-info request-info-btn" data-user-id="<?php echo esc_attr($user->ID); ?>">
                                                <span class="dashicons dashicons-info"></span>
                                                <?php esc_html_e('Request Info', 'houzez'); ?>
                                            </button>
                                        </div>
                                    <?php elseif ($status === 'approved') : ?>
                                        <button type="button" class="button button-secondary revoke-approval" data-user-id="<?php echo esc_attr($user->ID); ?>">
                                            <span class="dashicons dashicons-undo"></span>
                                            <?php esc_html_e('Revoke Approval', 'houzez'); ?>
                                        </button>

                                    <?php elseif ($status === 'rejected') : ?>
                                        <button type="button" class="button button-primary approve-request" data-user-id="<?php echo esc_attr($user->ID); ?>">
                                            <span class="dashicons dashicons-yes"></span>
                                            <?php esc_html_e('Approve', 'houzez'); ?>
                                        </button>
                                    
                                    <?php elseif ($status === 'additional_info_required') : ?>
                                        <div class="request-actions">
                                            <button type="button" class="button button-primary approve-request" data-user-id="<?php echo esc_attr($user->ID); ?>">
                                                <span class="dashicons dashicons-yes"></span>
                                                <?php esc_html_e('Approve', 'houzez'); ?>
                                            </button>
                                            <button type="button" class="button button-reject reject-request" data-user-id="<?php echo esc_attr($user->ID); ?>">
                                                <span class="dashicons dashicons-no-alt"></span>
                                                <?php esc_html_e('Reject', 'houzez'); ?>
                                            </button>
                                            <button type="button" class="button button-info request-info-btn" data-user-id="<?php echo esc_attr($user->ID); ?>">
                                                <span class="dashicons dashicons-edit"></span>
                                                <?php esc_html_e('Update Info Request', 'houzez'); ?>
                                            </button>
                                        </div>
                                    
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejection-modal" class="houzez-modal" style="display: none;">
    <div class="houzez-modal-content">
        <div class="houzez-modal-header">
            <h3><?php esc_html_e('Reject Verification Request', 'houzez'); ?></h3>
            <span class="close">&times;</span>
        </div>
        <div class="houzez-modal-body">
            <form id="rejection-form">
                <input type="hidden" id="rejection-user-id" name="user_id" value="">
                <input type="hidden" name="action_type" value="reject">
                <input type="hidden" name="action" value="houzez_process_verification">
                <input type="hidden" name="security" value="<?php echo esc_attr(wp_create_nonce('houzez_admin_verification_nonce')); ?>">
                
                <div class="form-group">
                    <label for="rejection-reason"><?php esc_html_e('Rejection Reason:', 'houzez'); ?></label>
                    <textarea id="rejection-reason" name="rejection_reason" rows="4" class="widefat" placeholder="<?php esc_attr_e('Please provide a reason for rejecting this verification request...', 'houzez'); ?>"></textarea>
                    <p class="description"><?php esc_html_e('This reason will be included in the rejection email sent to the user.', 'houzez'); ?></p>
                </div>
            </form>
        </div>
        <div class="houzez-modal-footer">
            <button type="button" class="button button-secondary cancel-rejection"><?php esc_html_e('Cancel', 'houzez'); ?></button>
            <button type="submit" form="rejection-form" class="button button-primary"><?php esc_html_e('Confirm Rejection', 'houzez'); ?></button>
        </div>
    </div>
</div>

<!-- Additional Info Modal -->
<div id="additional-info-modal" class="houzez-modal" style="display: none;">
    <div class="houzez-modal-content">
        <div class="houzez-modal-header">
            <h3><?php esc_html_e('Request Additional Information', 'houzez'); ?></h3>
            <span class="close">&times;</span>
        </div>
        <div class="houzez-modal-body">
            <form id="additional-info-form">
                <input type="hidden" id="additional-info-user-id" name="user_id" value="">
                <input type="hidden" name="action_type" value="request_info">
                <input type="hidden" name="action" value="houzez_process_verification">
                <input type="hidden" name="security" value="<?php echo esc_attr(wp_create_nonce('houzez_admin_verification_nonce')); ?>">
                
                <div class="form-group">
                    <label for="additional-info"><?php esc_html_e('Specify Additional Information Needed:', 'houzez'); ?></label>
                    <textarea id="additional-info" name="additional_info" rows="4" class="widefat" required placeholder="<?php esc_attr_e('Please specify what additional information is needed from the user...', 'houzez'); ?>"></textarea>
                    <p class="description"><?php esc_html_e('Be clear about what additional documentation or information is needed from the user.', 'houzez'); ?></p>
                </div>
            </form>
        </div>
        <div class="houzez-modal-footer">
            <button type="button" class="button button-secondary cancel-additional-info"><?php esc_html_e('Cancel', 'houzez'); ?></button>
            <button type="submit" form="additional-info-form" class="button button-primary"><?php esc_html_e('Send Request', 'houzez'); ?></button>
        </div>
    </div>
</div>