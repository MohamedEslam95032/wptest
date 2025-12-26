<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();


// Handle notifications
$notification = '';
$notification_type = '';

if (isset($_GET['feedback_sent'])) {
    $notification = __('Feedback sent successfully! Thank you for your input.', 'houzez');
    $notification_type = 'success';
}
?>

<div class="wrap houzez-template-library">
    <div class="houzez-header">
        <div class="houzez-header-content">
            <div class="houzez-logo">
                <h1><?php esc_html_e('Send Feedback', 'houzez'); ?></h1>
            </div>
            <div class="houzez-header-actions">
                <button type="submit" form="feedback-form" class="houzez-btn houzez-btn-primary">
                    <i class="dashicons dashicons-email-alt"></i>
                    <?php esc_html_e('Send Feedback', 'houzez'); ?>
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
                    <i class="dashicons dashicons-megaphone"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php esc_html_e('Your Voice', 'houzez'); ?></h3>
                    <p><?php esc_html_e('Matters to Us', 'houzez'); ?></p>
                </div>
            </div>

            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-lightbulb"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php esc_html_e('Feature', 'houzez'); ?></h3>
                    <p><?php esc_html_e('Requests Welcome', 'houzez'); ?></p>
                </div>
            </div>

            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-businessman"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php esc_html_e('Business', 'houzez'); ?></h3>
                    <p><?php esc_html_e('Inquiries Supported', 'houzez'); ?></p>
                </div>
            </div>

            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-email-alt"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php esc_html_e('Direct', 'houzez'); ?></h3>
                    <p><?php esc_html_e('Communication', 'houzez'); ?></p>
                </div>
            </div>
        </div>

        <!-- Main Feedback Card -->
        <div class="houzez-main-card">
            <div class="houzez-card-header">
                <h2>
                    <i class="dashicons dashicons-megaphone"></i>
                    <?php esc_html_e('Share Your Thoughts', 'houzez'); ?>
                </h2>
                <div class="houzez-status-badge houzez-status-success">
                    <?php esc_html_e('Active', 'houzez'); ?>
                </div>
            </div>
            <div class="houzez-card-body">
                <p class="houzez-description">
                    <?php esc_html_e('Your feedback and comments have played a major role in determining the features incorporated into Houzez. We\'d like to hear your thoughts and ideas on what we should be considering for the next or future updates.', 'houzez'); ?>
                </p>
                
                <form id="feedback-form" class="houzez-feedback-form">
                    <input type="hidden" name="action" value="houzez_feedback">
                    <?php wp_nonce_field( 'houzez_feedback_security', 'houzez_feedback_nonce' ); ?>

                    <div class="houzez-feedback-grid">
                        <div class="houzez-feedback-group">
                            <label for="houzez_feedback_email" class="houzez-feedback-label">
                                <i class="dashicons dashicons-email"></i>
                                <?php esc_html_e('Email Address', 'houzez'); ?>
                                <span class="required">*</span>
                            </label>
                            <div class="houzez-input-wrapper">
                                <input type="email" id="houzez_feedback_email" name="feedback_email" value="<?php echo esc_attr( $current_user->user_email ); ?>" class="houzez-feedback-input" placeholder="<?php esc_attr_e('Enter your email address', 'houzez'); ?>" required>
                                
                            </div>
                            <div class="houzez-feedback-help">
                                <?php esc_html_e('We\'ll use this to respond to your feedback', 'houzez'); ?>
                            </div>
                        </div>

                        <div class="houzez-feedback-group">
                            <label for="houzez_feedback_subject" class="houzez-feedback-label">
                                <i class="dashicons dashicons-category"></i>
                                <?php esc_html_e('Subject Category', 'houzez'); ?>
                                <span class="required">*</span>
                            </label>
                            <div class="houzez-select-wrapper">
                                <select id="houzez_feedback_subject" name="feedback_subject" class="houzez-feedback-select" required>
                                    <option value=""><?php esc_html_e('Select a category', 'houzez'); ?></option>
                                    <option value="Feature Request"><?php esc_html_e('💡 Feature Request', 'houzez'); ?></option>
                                    <option value="Demo Request"><?php esc_html_e('🎯 Demo Request', 'houzez'); ?></option>
                                    <option value="Business Inquiry"><?php esc_html_e('💼 Business Inquiry', 'houzez'); ?></option>
                                    <option value="Suggestions"><?php esc_html_e('💭 Suggestions', 'houzez'); ?></option>
                                    <option value="Bug Report"><?php esc_html_e('🐛 Bug Report', 'houzez'); ?></option>
                                    <option value="Other"><?php esc_html_e('📝 Other', 'houzez'); ?></option>
                                </select>
                            </div>
                            <div class="houzez-feedback-help">
                                <?php esc_html_e('Help us categorize your feedback for better response', 'houzez'); ?>
                            </div>
                        </div>

                        <div class="houzez-feedback-group houzez-feedback-group-full">
                            <label for="houzez_feedback_message" class="houzez-feedback-label">
                                <i class="dashicons dashicons-edit"></i>
                                <?php esc_html_e('Your Message', 'houzez'); ?>
                                <span class="required">*</span>
                            </label>
                            <div class="houzez-textarea-wrapper">
                                <textarea id="houzez_feedback_message" name="feedback_message" rows="6" class="houzez-feedback-textarea" placeholder="<?php esc_attr_e('Share your thoughts, ideas, or questions with us...', 'houzez'); ?>" required></textarea>
                                <div class="textarea-counter">
                                    <span id="char-count">0</span> / 1000 <?php esc_html_e('characters', 'houzez'); ?>
                                </div>
                            </div>
                            <div class="houzez-feedback-help">
                                <?php esc_html_e('Please provide as much detail as possible to help us understand your feedback', 'houzez'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="houzez-feedback-actions">
                        <div class="houzez-feedback-info">
                            <div class="info-card">
                                <i class="dashicons dashicons-info"></i>
                                <div class="info-content">
                                    <strong><?php esc_html_e('Privacy Notice', 'houzez'); ?></strong>
                                    <p><?php esc_html_e('Any data submitted through this form is not stored on our servers but just sent to our support email address, so that we are able to process your request.', 'houzez'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="houzez-feedback-submit">
                            <button type="submit" class="houzez-feedback-btn">
                                <span class="btn-icon">
                                    <i class="dashicons dashicons-email-alt"></i>
                                </span>
                                <span class="btn-text"><?php esc_html_e('Send Feedback', 'houzez'); ?></span>
                                <span class="btn-loading" style="display: none;">
                                    <i class="dashicons dashicons-update"></i>
                                </span>
                            </button>
                        </div>
                    </div>

                    <div id="form-messages" class="houzez-form-messages"></div>
                </form>
            </div>
        </div>

        <!-- Information Cards -->
        <div class="houzez-main-card">
            <div class="houzez-card-header">
                <h2>
                    <i class="dashicons dashicons-info"></i>
                    <?php esc_html_e('How We Use Your Feedback', 'houzez'); ?>
                </h2>
            </div>
            
            <div class="houzez-card-body">
                <div class="houzez-actions">
                    <div class="houzez-action">
                        <div class="houzez-action-icon">
                            <i class="dashicons dashicons-lightbulb"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('Feature Development', 'houzez'); ?></h4>
                            <p><?php esc_html_e('Your feature requests directly influence our development roadmap and help us prioritize new functionality.', 'houzez'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-action">
                        <div class="houzez-action-icon">
                            <i class="dashicons dashicons-admin-tools"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('Product Improvement', 'houzez'); ?></h4>
                            <p><?php esc_html_e('Bug reports and suggestions help us improve existing features and enhance user experience.', 'houzez'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-action">
                        <div class="houzez-action-icon">
                            <i class="dashicons dashicons-groups"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('Community Building', 'houzez'); ?></h4>
                            <p><?php esc_html_e('Your feedback helps us build a stronger community and create solutions that work for everyone.', 'houzez'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-action">
                        <div class="houzez-action-icon">
                            <i class="dashicons dashicons-businessman"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('Business Solutions', 'houzez'); ?></h4>
                            <p><?php esc_html_e('Business inquiries help us understand enterprise needs and develop professional solutions.', 'houzez'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Auto-hide notification after 5 seconds
    setTimeout(function() {
        $('#houzez-notification').fadeOut();
    }, 5000);

    // Character counter for textarea
    $('#houzez_feedback_message').on('input', function() {
        var length = $(this).val().length;
        var maxLength = 1000;
        $('#char-count').text(length);
        
        if (length > maxLength * 0.9) {
            $('#char-count').css('color', '#dc3545');
        } else if (length > maxLength * 0.7) {
            $('#char-count').css('color', '#ffc107');
        } else {
            $('#char-count').css('color', '#28a745');
        }
    });

    // Enhanced form validation
    function validateForm() {
        var isValid = true;
        var email = $('#houzez_feedback_email').val();
        var subject = $('#houzez_feedback_subject').val();
        var message = $('#houzez_feedback_message').val();
        
        // Reset previous validation states
        $('.houzez-feedback-group').removeClass('has-error');
        
        if (!email || !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            $('#houzez_feedback_email').closest('.houzez-feedback-group').addClass('has-error');
            isValid = false;
        }
        
        if (!subject) {
            $('#houzez_feedback_subject').closest('.houzez-feedback-group').addClass('has-error');
            isValid = false;
        }
        
        if (!message || message.length < 10) {
            $('#houzez_feedback_message').closest('.houzez-feedback-group').addClass('has-error');
            isValid = false;
        }
        
        return isValid;
    }

    // Form submission with enhanced loading state
    $('#feedback-form').on('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            $('#form-messages').html('<div class="message-error"><i class="dashicons dashicons-warning"></i> <?php echo esc_js(__('Please fill in all required fields correctly.', 'houzez')); ?></div>');
            return;
        }
        
        var $form = $(this);
        var $submitBtn = $('.houzez-feedback-btn');
        
        // Show loading state
        $submitBtn.addClass('loading').prop('disabled', true);
        $('.btn-icon, .btn-text').hide();
        $('.btn-loading').show();
        
        // Clear previous messages
        $('#form-messages').empty();
        
        // Simulate form submission (replace with actual AJAX call)
        setTimeout(function() {
            $('#form-messages').html('<div class="message-success"><i class="dashicons dashicons-yes-alt"></i> <?php echo esc_js(__('Thank you! Your feedback has been sent successfully.', 'houzez')); ?></div>');
            $form[0].reset();
            $('#char-count').text('0').css('color', '#28a745');
            
            // Reset button state
            $submitBtn.removeClass('loading').prop('disabled', false);
            $('.btn-loading').hide();
            $('.btn-icon, .btn-text').show();
            
            // Scroll to success message
            $('html, body').animate({
                scrollTop: $('#form-messages').offset().top - 100
            }, 500);
        }, 2000);
    });

    // Real-time validation feedback
    $('#houzez_feedback_email').on('blur', function() {
        var email = $(this).val();
        var $group = $(this).closest('.houzez-feedback-group');
        
        if (email && email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            $group.removeClass('has-error').addClass('has-success');
        } else if (email) {
            $group.removeClass('has-success').addClass('has-error');
        } else {
            $group.removeClass('has-error has-success');
        }
    });

    $('#houzez_feedback_subject').on('change', function() {
        var $group = $(this).closest('.houzez-feedback-group');
        if ($(this).val()) {
            $group.removeClass('has-error').addClass('has-success');
        } else {
            $group.removeClass('has-success');
        }
    });

    $('#houzez_feedback_message').on('blur', function() {
        var message = $(this).val();
        var $group = $(this).closest('.houzez-feedback-group');
        
        if (message && message.length >= 10) {
            $group.removeClass('has-error').addClass('has-success');
        } else if (message) {
            $group.removeClass('has-success').addClass('has-error');
        } else {
            $group.removeClass('has-error has-success');
        }
    });
});
</script>

<style>
/* Enhanced Feedback Form Styles */
.houzez-feedback-form {
    max-width: none;
}

.houzez-feedback-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-bottom: 30px;
}

.houzez-feedback-group {
    display: flex;
    flex-direction: column;
    position: relative;
}

.houzez-feedback-group-full {
    grid-column: 1 / -1;
}

.houzez-feedback-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #1d2327;
    margin-bottom: 8px;
}

.houzez-feedback-label .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
    color: #0088cc;
}

.houzez-feedback-label .required {
    color: #dc3545;
    font-weight: 700;
}

/* Enhanced Input Wrapper */
.houzez-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.houzez-feedback-input {
    width: 100%;
    padding: 12px 16px 12px 45px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 14px;
    line-height: 1.5;
    background: #fff;
    transition: all 0.3s ease;
    box-sizing: border-box;
    font-family: inherit;
}

.houzez-feedback-input:focus {
    outline: none;
    border-color: #0088cc;
    box-shadow: 0 0 0 3px rgba(0, 136, 204, 0.1);
    transform: translateY(-1px);
}

.houzez-feedback-input::placeholder {
    color: #999;
    font-style: normal;
}

.input-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    font-size: 16px;
    pointer-events: none;
    transition: color 0.3s ease;
    z-index: 2;
}

.houzez-feedback-input:focus + .input-icon {
    color: #0088cc;
}

/* Enhanced Select Wrapper */
.houzez-select-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.houzez-feedback-select {
    width: 100%;
    padding: 12px 45px 12px 16px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 14px;
    line-height: 1.5;
    background: #fff;
    transition: all 0.3s ease;
    box-sizing: border-box;
    font-family: inherit;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
}

.houzez-feedback-select:focus {
    outline: none;
    border-color: #0088cc;
    box-shadow: 0 0 0 3px rgba(0, 136, 204, 0.1);
    transform: translateY(-1px);
}

.select-icon {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    font-size: 16px;
    pointer-events: none;
    transition: all 0.3s ease;
    z-index: 2;
}

.houzez-feedback-select:focus + .select-icon {
    color: #0088cc;
    transform: translateY(-50%) rotate(180deg);
}

/* Enhanced Textarea Wrapper */
.houzez-textarea-wrapper {
    position: relative;
}

.houzez-feedback-textarea {
    width: 100%;
    padding: 16px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 14px;
    line-height: 1.6;
    background: #fff;
    transition: all 0.3s ease;
    box-sizing: border-box;
    font-family: inherit;
    resize: vertical;
    min-height: 120px;
}

.houzez-feedback-textarea:focus {
    outline: none;
    border-color: #0088cc;
    box-shadow: 0 0 0 3px rgba(0, 136, 204, 0.1);
    transform: translateY(-1px);
}

.textarea-counter {
    position: absolute;
    bottom: 8px;
    right: 12px;
    font-size: 11px;
    color: #28a745;
    font-weight: 500;
    background: rgba(255, 255, 255, 0.9);
    padding: 2px 6px;
    border-radius: 4px;
}

.houzez-feedback-help {
    margin-top: 6px;
    font-size: 12px;
    color: #666;
    line-height: 1.4;
    font-style: italic;
}

/* Validation States */
.houzez-feedback-group.has-error .houzez-feedback-input,
.houzez-feedback-group.has-error .houzez-feedback-select,
.houzez-feedback-group.has-error .houzez-feedback-textarea {
    border-color: #dc3545;
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
}

.houzez-feedback-group.has-success .houzez-feedback-input,
.houzez-feedback-group.has-success .houzez-feedback-select,
.houzez-feedback-group.has-success .houzez-feedback-textarea {
    border-color: #28a745;
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
}

.houzez-feedback-group.has-error .input-icon,
.houzez-feedback-group.has-error .select-icon {
    color: #dc3545 !important;
}

.houzez-feedback-group.has-success .input-icon,
.houzez-feedback-group.has-success .select-icon {
    color: #28a745 !important;
}

/* Override focus states when validation states are active */
.houzez-feedback-group.has-error .houzez-feedback-input:focus,
.houzez-feedback-group.has-error .houzez-feedback-select:focus,
.houzez-feedback-group.has-error .houzez-feedback-textarea:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.2);
}

.houzez-feedback-group.has-success .houzez-feedback-input:focus,
.houzez-feedback-group.has-success .houzez-feedback-select:focus,
.houzez-feedback-group.has-success .houzez-feedback-textarea:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.2);
}

/* Enhanced Actions Section */
.houzez-feedback-actions {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 30px;
    padding-top: 25px;
    border-top: 1px solid #e1e5e9;
    margin-top: 30px;
}

.houzez-feedback-info {
    flex: 1;
}

.info-card {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 8px;
    border-left: 4px solid #0088cc;
}

.info-card .dashicons {
    color: #0088cc;
    font-size: 18px;
    margin-top: 2px;
    flex-shrink: 0;
}

.info-content strong {
    display: block;
    color: #1d2327;
    font-weight: 600;
    margin-bottom: 4px;
    font-size: 13px;
}

.info-content p {
    margin: 0;
    color: #666;
    font-size: 12px;
    line-height: 1.4;
}

.houzez-feedback-submit {
    flex-shrink: 0;
}

/* Enhanced Submit Button */
.houzez-feedback-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    background: linear-gradient(135deg, #0088cc 0%, #006ca8 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    line-height: 1.4;
    vertical-align: middle;
    box-sizing: border-box;
    box-shadow: 0 2px 8px rgba(0, 136, 204, 0.3);
    position: relative;
    overflow: hidden;
}

.houzez-feedback-btn:hover {
    background: linear-gradient(135deg, #006ca8 0%, #004274 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0, 136, 204, 0.4);
}

.houzez-feedback-btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(0, 136, 204, 0.2);
}

.houzez-feedback-btn.loading {
    pointer-events: none;
    opacity: 0.8;
}

.houzez-feedback-btn .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
    line-height: 16px;
}

.btn-loading .dashicons {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Enhanced Messages */
.houzez-form-messages {
    margin-top: 20px;
}

.message-success,
.message-error {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    animation: slideInUp 0.3s ease;
}

.message-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    border-left: 4px solid #28a745;
}

.message-error {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.message-success .dashicons,
.message-error .dashicons {
    font-size: 18px;
    flex-shrink: 0;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .houzez-feedback-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .houzez-feedback-actions {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }

    .houzez-feedback-submit {
        width: 100%;
    }

    .houzez-feedback-btn {
        width: 100%;
        justify-content: center;
    }

    .info-card {
        text-align: left;
    }
}
</style>