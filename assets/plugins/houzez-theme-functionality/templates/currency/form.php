<?php
$instance = null;

if ( Houzez_Currencies::is_edit_field() ) {
    $page_title = esc_html__( 'Update Currency', 'houzez-theme-functionality' );
    $button_title = esc_html__( 'Update Currency', 'houzez-theme-functionality' );
    $instance = Houzez_Currencies::get_edit_field();
    $form_icon = 'dashicons-edit';
    $form_description = esc_html__( 'Update the currency settings below and save your changes.', 'houzez-theme-functionality' );
} else {
    $page_title = esc_html__( 'Create New Currency', 'houzez-theme-functionality' );
    $button_title = esc_html__( 'Create Currency', 'houzez-theme-functionality' );
    $form_icon = 'dashicons-plus-alt';
    $form_description = esc_html__( 'Fill in the details below to create a new currency for your properties.', 'houzez-theme-functionality' );
}

// Check for success/error messages
$show_success = false;
$show_error = false;
$message = '';

if (isset($_GET['currency_added']) && $_GET['currency_added'] == '1') {
    $show_success = true;
    $message = esc_html__('Currency has been added successfully!', 'houzez-theme-functionality');
} elseif (isset($_GET['currency_updated']) && $_GET['currency_updated'] == '1') {
    $show_success = true;
    $message = esc_html__('Currency has been updated successfully!', 'houzez-theme-functionality');
} elseif (isset($_GET['currency_error']) && $_GET['currency_error'] == '1') {
    $show_error = true;
    $message = esc_html__('There was an error processing your request. Please try again.', 'houzez-theme-functionality');
}
?>

<!-- Notifications -->
<div id="houzez-notifications" class="houzez-notifications">
    <?php if ($show_success): ?>
        <div class="houzez-notification success">
            <i class="dashicons dashicons-yes-alt"></i>
            <?php echo esc_html($message); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($show_error): ?>
        <div class="houzez-notification error">
            <i class="dashicons dashicons-dismiss"></i>
            <?php echo esc_html($message); ?>
        </div>
    <?php endif; ?>
</div>

<!-- Back to Currencies List -->
<div class="houzez-breadcrumb">
    <a href="<?php echo esc_url(admin_url('admin.php?page=houzez_currencies')); ?>" class="houzez-back-link">
        <i class="dashicons dashicons-arrow-left-alt2"></i>
        <?php esc_html_e('Back to Currencies List', 'houzez-theme-functionality'); ?>
    </a>
</div>

<!-- Main Form Card -->
<div class="houzez-main-card">
    <div class="houzez-card-header">
        <h2>
            <i class="dashicons <?php echo esc_attr($form_icon); ?>"></i>
            <?php echo esc_html($page_title); ?>
        </h2>
        <div class="houzez-status-badge houzez-status-success">
            <?php esc_html_e('Form', 'houzez-theme-functionality'); ?>
        </div>
    </div>
    
    <div class="houzez-card-body">
        <p class="houzez-description">
            <?php echo esc_html($form_description); ?>
        </p>
        
        <form action="" method="POST" class="houzez-fields-form" id="houzez-currency-form">
            <div class="houzez-form-grid">
                <!-- Currency Name -->
                <div class="houzez-form-group">
                    <label for="currency-name" class="houzez-form-label">
                        <i class="dashicons dashicons-tag"></i>
                        <?php esc_html_e('Currency Name', 'houzez-theme-functionality'); ?>
                        <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="currency-name"
                           name="hz_currency[name]" 
                           class="houzez-form-input" 
                           placeholder="<?php esc_attr_e('Enter currency name (e.g., United States Dollar)', 'houzez-theme-functionality'); ?>"
                           value="<?php echo esc_attr(Houzez_Currencies::get_field_value( $instance, 'currency_name' )); ?>"
                           required>
                    <small class="houzez-form-help">
                        <?php esc_html_e('The full name of the currency.', 'houzez-theme-functionality'); ?>
                    </small>
                </div>

                <!-- Currency Code -->
                <div class="houzez-form-group">
                    <label for="currency-code" class="houzez-form-label">
                        <i class="dashicons dashicons-admin-network"></i>
                        <?php esc_html_e('Currency Code', 'houzez-theme-functionality'); ?>
                        <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="currency-code"
                           name="hz_currency[code]" 
                           class="houzez-form-input" 
                           placeholder="<?php esc_attr_e('Enter currency code (e.g., USD)', 'houzez-theme-functionality'); ?>"
                           value="<?php echo esc_attr(Houzez_Currencies::get_field_value( $instance, 'currency_code' )); ?>"
                           maxlength="3"
                           style="text-transform: uppercase;"
                           required>
                    <small class="houzez-form-help">
                        <?php esc_html_e('3-letter ISO currency code (e.g., USD, EUR, GBP).', 'houzez-theme-functionality'); ?>
                    </small>
                </div>

                <!-- Currency Symbol -->
                <div class="houzez-form-group">
                    <label for="currency-symbol" class="houzez-form-label">
                        <i class="dashicons dashicons-money-alt"></i>
                        <?php esc_html_e('Currency Symbol', 'houzez-theme-functionality'); ?>
                        <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="currency-symbol"
                           name="hz_currency[symbol]" 
                           class="houzez-form-input" 
                           placeholder="<?php esc_attr_e('Enter currency symbol (e.g., $)', 'houzez-theme-functionality'); ?>"
                           value="<?php echo esc_attr(Houzez_Currencies::get_field_value( $instance, 'currency_symbol' )); ?>"
                           required>
                    <small class="houzez-form-help">
                        <?php esc_html_e('The symbol used to represent this currency.', 'houzez-theme-functionality'); ?>
                    </small>
                </div>

                <!-- Currency Position -->
                <div class="houzez-form-group">
                    <label for="currency-position" class="houzez-form-label">
                        <i class="dashicons dashicons-admin-generic"></i>
                        <?php esc_html_e('Symbol Position', 'houzez-theme-functionality'); ?>
                        <span class="required">*</span>
                    </label>
                    <select id="currency-position" 
                            name="hz_currency[position]" 
                            class="houzez-form-select" 
                            required>
                        <option value=""><?php esc_html_e('-- Choose position --', 'houzez-theme-functionality'); ?></option>
                        <option value="before" <?php selected(Houzez_Currencies::get_field_value( $instance, 'currency_position' ), 'before'); ?>>
                            <?php esc_html_e('Before ($100)', 'houzez-theme-functionality'); ?>
                        </option>
                        <option value="after" <?php selected(Houzez_Currencies::get_field_value( $instance, 'currency_position' ), 'after'); ?>>
                            <?php esc_html_e('After (100$)', 'houzez-theme-functionality'); ?>
                        </option>
                    </select>
                    <small class="houzez-form-help">
                        <?php esc_html_e('Where to display the currency symbol relative to the amount.', 'houzez-theme-functionality'); ?>
                    </small>
                </div>

                <!-- Number of Decimals -->
                <div class="houzez-form-group">
                    <label for="currency-decimals" class="houzez-form-label">
                        <i class="dashicons dashicons-editor-ol"></i>
                        <?php esc_html_e('Decimal Places', 'houzez-theme-functionality'); ?>
                        <span class="required">*</span>
                    </label>
                    <select id="currency-decimals" 
                            name="hz_currency[decimals]" 
                            class="houzez-form-select" 
                            required>
                        <?php for($i = 0; $i <= 10; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php selected(Houzez_Currencies::get_field_value( $instance, 'currency_decimal' ), $i); ?>>
                                <?php echo $i; ?> <?php echo $i == 1 ? esc_html__('decimal place', 'houzez-theme-functionality') : esc_html__('decimal places', 'houzez-theme-functionality'); ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <small class="houzez-form-help">
                        <?php esc_html_e('Number of digits after the decimal point.', 'houzez-theme-functionality'); ?>
                    </small>
                </div>

                <!-- Decimal Separator -->
                <div class="houzez-form-group">
                    <label for="currency-decimal-separator" class="houzez-form-label">
                        <i class="dashicons dashicons-admin-tools"></i>
                        <?php esc_html_e('Decimal Separator', 'houzez-theme-functionality'); ?>
                        <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="currency-decimal-separator"
                           name="hz_currency[decimal_separator]" 
                           class="houzez-form-input" 
                           placeholder="<?php esc_attr_e('Enter decimal separator (e.g., .)', 'houzez-theme-functionality'); ?>"
                           value="<?php echo esc_attr(Houzez_Currencies::get_field_value( $instance, 'currency_decimal_separator' )); ?>"
                           maxlength="1"
                           required>
                    <small class="houzez-form-help">
                        <?php esc_html_e('Character used to separate decimal places (e.g., . or ,).', 'houzez-theme-functionality'); ?>
                    </small>
                        </div>

                <!-- Thousands Separator -->
                <div class="houzez-form-group">
                    <label for="currency-thousands-separator" class="houzez-form-label">
                        <i class="dashicons dashicons-admin-tools"></i>
                        <?php esc_html_e('Thousands Separator', 'houzez-theme-functionality'); ?>
                        <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="currency-thousands-separator"
                           name="hz_currency[thousand_separator]" 
                           class="houzez-form-input" 
                           placeholder="<?php esc_attr_e('Enter thousands separator (e.g., ,)', 'houzez-theme-functionality'); ?>"
                           value="<?php echo esc_attr(Houzez_Currencies::get_field_value( $instance, 'currency_thousand_separator' )); ?>"
                           maxlength="1"
                           required>
                    <small class="houzez-form-help">
                        <?php esc_html_e('Character used to separate thousands (e.g., , or space).', 'houzez-theme-functionality'); ?>
                    </small>
                    </div>
            </div>

            <!-- Form Actions -->
            <div class="houzez-form-actions">
                <div class="houzez-form-actions-left">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=houzez_currencies')); ?>" class="houzez-btn houzez-btn-outline">
                        <i class="dashicons dashicons-arrow-left-alt2"></i>
                        <?php esc_html_e('Cancel', 'houzez-theme-functionality'); ?>
                    </a>
                </div>
                <div class="houzez-form-actions-right">
                    <button type="submit" class="houzez-btn houzez-btn-primary" id="submit-currency-btn">
                        <i class="dashicons <?php echo $instance ? 'dashicons-update' : 'dashicons-plus-alt'; ?>"></i>
                        <?php echo esc_html($button_title); ?>
                    </button>
            </div>
        </div>

            <!-- Hidden Fields -->
            <?php if ( ! empty( $instance['id'] ) ) : ?>
                <input type="hidden" name="hz_currency[id]" value="<?php echo esc_attr($instance['id']); ?>"/>
            <?php endif; ?>
            <?php wp_nonce_field( 'houzez_currency_save_field', 'houzez_currency_save_field' ); ?>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Auto-hide notifications after 5 seconds
    setTimeout(function() {
        $('.houzez-notification').fadeOut(500);
    }, 5000);
    
    // Form submission with loading state
    $('#houzez-currency-form').on('submit', function() {
        var $submitBtn = $('#submit-currency-btn');
        var originalText = $submitBtn.html();
        
        // Add loading state
        $submitBtn.prop('disabled', true);
        $submitBtn.html('<i class="dashicons dashicons-update"></i> ' + '<?php esc_html_e('Processing...', 'houzez-theme-functionality'); ?>');
        
        // Add loading class to form
        $(this).addClass('loading');
        
        // Show processing notification
        showNotification('<?php esc_html_e('Processing your request...', 'houzez-theme-functionality'); ?>', 'info');
    });
    
    // Auto-uppercase currency code
    $('#currency-code').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
    
    // Notification function
    function showNotification(message, type) {
        var notification = $('<div class="houzez-notification ' + type + '">' + message + '</div>');
        $('#houzez-notifications').append(notification);
        
        setTimeout(function() {
            notification.fadeOut(500, function() {
                $(this).remove();
            });
        }, 3000);
    }
});
</script>
    