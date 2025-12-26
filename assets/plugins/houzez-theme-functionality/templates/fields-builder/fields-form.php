<?php
$instance = null;

if ( Houzez_Fields_Builder::is_edit_field() ) {
    $page_title = esc_html__( 'Update Field', 'houzez-theme-functionality' );
    $button_title = esc_html__( 'Update Field', 'houzez-theme-functionality' );
    $instance = Houzez_Fields_Builder::get_edit_field();
    $form_icon = 'dashicons-edit';
    $form_description = esc_html__( 'Update the field settings below and save your changes.', 'houzez-theme-functionality' );
} else {
    $page_title = esc_html__( 'Create New Field', 'houzez-theme-functionality' );
    $button_title = esc_html__( 'Create Field', 'houzez-theme-functionality' );
    $form_icon = 'dashicons-plus-alt';
    $form_description = esc_html__( 'Fill in the details below to create a new custom field for your properties.', 'houzez-theme-functionality' );
}

// Check for success/error messages
$show_success = false;
$show_error = false;
$message = '';

if (isset($_GET['field_added']) && $_GET['field_added'] == '1') {
    $show_success = true;
    $message = esc_html__('Field has been added successfully!', 'houzez-theme-functionality');
} elseif (isset($_GET['field_updated']) && $_GET['field_updated'] == '1') {
    $show_success = true;
    $message = esc_html__('Field has been updated successfully!', 'houzez-theme-functionality');
} elseif (isset($_GET['field_error']) && $_GET['field_error'] == '1') {
    $show_error = true;
    $message = esc_html__('There was an error processing your request. Please try again.', 'houzez-theme-functionality');
} elseif (isset($_GET['field_exists']) && $_GET['field_exists'] == '1') {
    $show_error = true;
    $message = esc_html__('A field with this name already exists. Please choose a different name.', 'houzez-theme-functionality');
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

<!-- Back to Fields List -->
<div class="houzez-breadcrumb">
    <a href="<?php echo esc_url(admin_url('admin.php?page=houzez_fbuilder')); ?>" class="houzez-back-link">
        <i class="dashicons dashicons-arrow-left-alt2"></i>
        <?php esc_html_e('Back to Fields List', 'houzez-theme-functionality'); ?>
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
        
        <form action="" method="POST" class="houzez-fields-form" id="houzez-field-form">
            <div class="houzez-form-grid">
                <!-- Field Name -->
                <div class="houzez-form-group houzez-form-group-full">
                    <label for="field-name" class="houzez-form-label">
                        <i class="dashicons dashicons-tag"></i>
                        <?php esc_html_e('Field Name', 'houzez-theme-functionality'); ?>
                        <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="field-name"
                           name="hz_fbuilder[label]" 
                           class="houzez-form-input hz-fbuilder-name-js" 
                           placeholder="<?php esc_attr_e('Enter field name', 'houzez-theme-functionality'); ?>"
                           value="<?php echo esc_attr(Houzez_Fields_Builder::get_field_value( $instance, 'label' )); ?>"
                           required>
                    <small class="houzez-form-help">
                        <?php esc_html_e('This will be the display name for your custom field.', 'houzez-theme-functionality'); ?>
                    </small>
                </div>

                <!-- Placeholder -->
                <div class="houzez-form-group houzez-form-group-full">
                    <label for="field-placeholder" class="houzez-form-label">
                        <i class="dashicons dashicons-text"></i>
                        <?php esc_html_e('Placeholder Text', 'houzez-theme-functionality'); ?>
                    </label>
                    <input type="text" 
                           id="field-placeholder"
                           name="hz_fbuilder[placeholder]" 
                           class="houzez-form-input" 
                           placeholder="<?php esc_attr_e('Enter field placeholder', 'houzez-theme-functionality'); ?>"
                           value="<?php echo esc_attr(Houzez_Fields_Builder::get_field_value( $instance, 'placeholder' )); ?>">
                    <small class="houzez-form-help">
                        <?php esc_html_e('Optional placeholder text that appears inside the field.', 'houzez-theme-functionality'); ?>
                    </small>
                </div>

                <!-- Field Type -->
                <div class="houzez-form-group">
                    <label for="field-type" class="houzez-form-label">
                        <i class="dashicons dashicons-admin-generic"></i>
                        <?php esc_html_e('Field Type', 'houzez-theme-functionality'); ?>
                        <span class="required">*</span>
                    </label>
                    <select id="field-type" 
                            name="hz_fbuilder[type]" 
                            class="houzez-form-select houzez-fbuilder-js-on-change" 
                            required>
                        <option value=""><?php esc_html_e('-- Choose field type --', 'houzez-theme-functionality'); ?></option>
                        <?php foreach(Houzez_Fields_Builder::get_field_types() as $key => $value): ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected(Houzez_Fields_Builder::get_field_value( $instance, 'type' ), $key); ?>>
                                <?php echo esc_html($value); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="houzez-form-help">
                        <?php esc_html_e('Select the type of input field you want to create.', 'houzez-theme-functionality'); ?>
                    </small>
                </div>

                <!-- Search Availability -->
                <div class="houzez-form-group">
                    <label for="field-search" class="houzez-form-label">
                        <i class="dashicons dashicons-search"></i>
                        <?php esc_html_e('Available for Search', 'houzez-theme-functionality'); ?>
                        <span class="required">*</span>
                    </label>
                    <select id="field-search" 
                            name="hz_fbuilder[is_search]" 
                            class="houzez-form-select" 
                            required>
                        <option value="no" <?php selected(Houzez_Fields_Builder::get_field_value( $instance, 'is_search' ), 'no'); ?>>
                            <?php esc_html_e('No', 'houzez-theme-functionality'); ?>
                        </option>
                        <option value="yes" <?php selected(Houzez_Fields_Builder::get_field_value( $instance, 'is_search' ), 'yes'); ?>>
                            <?php esc_html_e('Yes', 'houzez-theme-functionality'); ?>
                        </option>
                    </select>
                    <small class="houzez-form-help">
                        <?php esc_html_e('Whether this field should be available in property search forms.', 'houzez-theme-functionality'); ?>
                    </small>
                </div>

                <!-- Options for Select/Multi-select/Radio/Checkbox -->
                <div class="houzez-form-group houzez-form-group-full houzez_multi_line_js" style="display:none;">
                    <label for="field-options" class="houzez-form-label">
                        <i class="dashicons dashicons-list-view"></i>
                        <?php esc_html_e('Field Options', 'houzez-theme-functionality'); ?>
                    </label>
                    <textarea id="field-options"
                              name="hz_fbuilder[options]" 
                              class="houzez-form-textarea" 
                              rows="4"
                              placeholder="<?php esc_attr_e('Please add comma separated options. Example: One, Two, Three', 'houzez-theme-functionality'); ?>"><?php 
                              $field_options = Houzez_Fields_Builder::get_field_option($instance);
                              echo esc_textarea($field_options ? $field_options : ''); 
                              ?></textarea>
                    <small class="houzez-form-help">
                        <?php esc_html_e('Enter comma-separated options for select, radio, or checkbox fields.', 'houzez-theme-functionality'); ?>
                    </small>
                </div>

                <!-- Dynamic Options Container -->
                <div class="houzez-form-group houzez-form-group-full houzez_select_options_loader_js">
                    <?php
                        if( isset($instance['type']) &&  ( $instance['type'] == 'select' || $instance['type'] == 'multiselect') ) {
                            include HOUZEZ_TEMPLATES . '/fields-builder/multiple.php';
                        }
                    ?>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="houzez-form-actions">
                <div class="houzez-form-actions-left">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=houzez_fbuilder')); ?>" class="houzez-btn houzez-btn-outline">
                        <i class="dashicons dashicons-arrow-left-alt2"></i>
                        <?php esc_html_e('Cancel', 'houzez-theme-functionality'); ?>
                    </a>
                        </div>
                <div class="houzez-form-actions-right">
                    <button type="submit" class="houzez-btn houzez-btn-primary" id="submit-field-btn">
                        <i class="dashicons <?php echo $instance ? 'dashicons-update' : 'dashicons-plus-alt'; ?>"></i>
                        <?php echo esc_html($button_title); ?>
                    </button>
                    </div>
            </div>

            <!-- Hidden Fields -->
            <?php if ( ! empty( $instance['id'] ) ) : ?>
                <input type="hidden" name="hz_fbuilder[id]" value="<?php echo esc_attr($instance['id']); ?>"/>
            <?php endif; ?>
            <?php wp_nonce_field( 'houzez_fbuilder_save_field', 'houzez_fbuilder_save_field' ); ?>
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
    $('#houzez-field-form').on('submit', function() {
        var $submitBtn = $('#submit-field-btn');
        var originalText = $submitBtn.html();
        
        // Add loading state
        $submitBtn.prop('disabled', true);
        $submitBtn.html('<i class="dashicons dashicons-update"></i> ' + '<?php esc_html_e('Processing...', 'houzez-theme-functionality'); ?>');
        
        // Add loading class to form
        $(this).addClass('loading');
        
        // Show processing notification
        showNotification('<?php esc_html_e('Processing your request...', 'houzez-theme-functionality'); ?>', 'info');
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