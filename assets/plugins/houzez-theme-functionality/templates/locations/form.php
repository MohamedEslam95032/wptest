<?php
$settings = array(); 

// Handle notifications
$notification = '';
$notification_type = '';

if (isset($_GET['import_success'])) {
    $notification = __('Locations imported successfully!', 'houzez');
    $notification_type = 'success';
} elseif (isset($_GET['import_error'])) {
    $notification = __('Import failed. Please check your CSV file and try again.', 'houzez');
    $notification_type = 'error';
}

// Get location counts for stats
$countries_count = wp_count_terms(array('taxonomy' => 'property_country', 'hide_empty' => false));
$states_count = wp_count_terms(array('taxonomy' => 'property_state', 'hide_empty' => false));
$cities_count = wp_count_terms(array('taxonomy' => 'property_city', 'hide_empty' => false));
$areas_count = wp_count_terms(array('taxonomy' => 'property_area', 'hide_empty' => false));

// Check for errors and convert to integers
$countries_count = is_wp_error($countries_count) ? 0 : (int) $countries_count;
$states_count = is_wp_error($states_count) ? 0 : (int) $states_count;
$cities_count = is_wp_error($cities_count) ? 0 : (int) $cities_count;
$areas_count = is_wp_error($areas_count) ? 0 : (int) $areas_count;

$total_locations = $countries_count + $states_count + $cities_count + $areas_count;
?>

<div class="wrap houzez-template-library">
    <div class="houzez-header">
        <div class="houzez-header-content">
            <div class="houzez-logo">
                <h1><?php esc_html_e('Import Locations', 'houzez'); ?></h1>
            </div>
            <div class="houzez-header-actions">
                <button type="button" id="fetch-locations-csv" class="houzez-btn houzez-btn-primary" disabled>
                    <i class="dashicons dashicons-database-import"></i>
                    <?php esc_html_e('Fetch CSV Headers', 'houzez'); ?>
                </button>
                <button type="button" class="houzez-btn houzez-btn-secondary" onclick="location.reload();">
                    <i class="dashicons dashicons-update"></i>
                    <?php esc_html_e('Refresh Page', 'houzez'); ?>
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
                    <i class="dashicons dashicons-admin-site-alt3"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php echo esc_html($countries_count); ?></h3>
                    <p><?php esc_html_e('Countries', 'houzez'); ?></p>
                </div>
            </div>

            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-admin-multisite"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php echo esc_html($states_count); ?></h3>
                    <p><?php esc_html_e('States/Counties', 'houzez'); ?></p>
                </div>
            </div>

            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-building"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php echo esc_html($cities_count); ?></h3>
                    <p><?php esc_html_e('Cities', 'houzez'); ?></p>
                </div>
            </div>

            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-location"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php echo esc_html($areas_count); ?></h3>
                    <p><?php esc_html_e('Areas', 'houzez'); ?></p>
                </div>
            </div>
        </div>

        <!-- CSV Upload Section -->
        <div class="houzez-main-card">
            <div class="houzez-card-header">
                <h2>
                    <i class="dashicons dashicons-upload"></i>
                    <?php esc_html_e('CSV File Upload', 'houzez'); ?>
                </h2>
                <div class="houzez-status-badge houzez-status-warning" id="csv-status-badge">
                    <?php esc_html_e('No File Selected', 'houzez'); ?>
                </div>
            </div>
            <div class="houzez-card-body">
                <p class="houzez-description">
                    <?php esc_html_e('Upload a CSV file containing location data to import countries, states, cities, and areas into your Houzez website. The system will automatically create hierarchical relationships between locations.', 'houzez'); ?>
                </p>
                
                <form class="houzez-fields-form" method="post" action="">
							<?php wp_nonce_field( 'favethemes-import-locations', 'favethemes-import-locations-nonce' ); ?>

                    <div class="houzez-form-grid">
                        <div class="houzez-form-group houzez-form-group-full">
                            <label class="houzez-form-label" for="locations-csv-file">
                                <i class="dashicons dashicons-media-document"></i>
                                <?php esc_html_e('CSV File', 'houzez'); ?>
                                <span class="required">*</span>
                            </label>
                            <div class="houzez-csv-upload-wrapper">
                                <input id="locations-csv-file" class="houzez-form-input" type="text" name="locations-csv-file" placeholder="<?php esc_attr_e('Select a CSV file...', 'houzez'); ?>" readonly>
                                <button id="upload-locations-csv" class="houzez-btn houzez-btn-outline" type="button">
                                    <i class="dashicons dashicons-upload"></i>
                                    <?php esc_html_e('Choose File', 'houzez'); ?>
                                </button>
                            </div>
                            <p class="houzez-form-help"><?php esc_html_e('Select a CSV file containing location data. The file should include columns for country, state, city, and area information.', 'houzez'); ?></p>
                        </div>
								</div>

                    <div class="houzez-form-actions">
                        <div class="houzez-form-actions-left">
                            <button type="button" class="houzez-btn houzez-btn-outline" onclick="document.getElementById('locations-csv-file').value = ''; updateCSVStatus();">
                                <i class="dashicons dashicons-dismiss"></i>
                                <?php esc_html_e('Clear Selection', 'houzez'); ?>
                            </button>
                        </div>
                        <div class="houzez-form-actions-right">
                            <button type="button" id="fetch-locations-csv-action" class="houzez-btn houzez-btn-primary" disabled>
                                <i class="dashicons dashicons-database-import"></i>
                                <?php esc_html_e('Fetch CSV Headers', 'houzez'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
							</div>

        <!-- Field Mapping Section (Initially Hidden) -->
        <div id="locations-mapping-section" class="houzez-main-card" style="display: none;">
            <div class="houzez-card-header">
                <h2>
                    <i class="dashicons dashicons-admin-tools"></i>
                    <?php esc_html_e('Field Mapping', 'houzez'); ?>
                </h2>
                <div class="houzez-status-badge houzez-status-warning">
                    <?php esc_html_e('Configure Mapping', 'houzez'); ?>
							</div>
							</div>
            <div class="houzez-card-body">
                <p class="houzez-description">
                    <?php esc_html_e('Map the columns from your CSV file to the corresponding location fields. At least one field mapping is required to proceed with the import.', 'houzez'); ?>
                </p>

						<div id="locations-mapping-container"></div>
            </div>
        </div>

        <!-- Import Results Section -->
        <div id="locations-results-section" class="houzez-main-card" style="display: none;">
            <div class="houzez-card-header">
                <h2>
                    <i class="dashicons dashicons-yes-alt"></i>
                    <?php esc_html_e('Import Results', 'houzez'); ?>
                </h2>
            </div>
            <div class="houzez-card-body">
                <div id="locations-locations-success" class="houzez-success-message"></div>
                <div id="locations-locations-error" class="houzez-error-message"></div>
            </div>
        </div>

        <!-- Information Card -->
        <div class="houzez-main-card">
            <div class="houzez-card-header">
                <h2>
                    <i class="dashicons dashicons-info"></i>
                    <?php esc_html_e('CSV Format Requirements', 'houzez'); ?>
                </h2>
            </div>
            <div class="houzez-card-body">
                <div class="houzez-actions houzez-actions-three-column">
                    <div class="houzez-action">
                        <div class="houzez-action-icon">
                            <i class="dashicons dashicons-media-spreadsheet"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('CSV Structure', 'houzez'); ?></h4>
                            <p><?php esc_html_e('Your CSV file should contain columns for location data. Common column names include Country, State, City, and Area. The first row should contain column headers.', 'houzez'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-action">
                        <div class="houzez-action-icon">
                            <i class="dashicons dashicons-networking"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('Hierarchical Import', 'houzez'); ?></h4>
                            <p><?php esc_html_e('The system automatically creates hierarchical relationships: Areas belong to Cities, Cities belong to States, and States belong to Countries.', 'houzez'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-action">
                        <div class="houzez-action-icon">
                            <i class="dashicons dashicons-database-add"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('Smart Import', 'houzez'); ?></h4>
                            <p><?php esc_html_e('Existing locations are automatically detected and updated. New locations are created as needed. Duplicate entries are handled intelligently.', 'houzez'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Sample CSV Format -->
                <div class="houzez-sample-csv">
                    <h4><?php esc_html_e('Sample CSV Format:', 'houzez'); ?></h4>
                    <div class="houzez-csv-preview">
                        <table class="houzez-csv-table">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th>State</th>
                                    <th>City</th>
                                    <th>Area</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>United States</td>
                                    <td>California</td>
                                    <td>Los Angeles</td>
                                    <td>Beverly Hills</td>
                                </tr>
                                <tr>
                                    <td>United States</td>
                                    <td>California</td>
                                    <td>San Francisco</td>
                                    <td>Mission District</td>
                                </tr>
                                <tr>
                                    <td>United Kingdom</td>
                                    <td>England</td>
                                    <td>London</td>
                                    <td>Westminster</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>

<!-- Basic JavaScript for UI only -->
<script>
// Global function for updating CSV status
function updateCSVStatus() {
    var csvFile = jQuery('#locations-csv-file').val();
    var statusBadge = jQuery('#csv-status-badge');
    var fetchBtn = jQuery('#fetch-locations-csv');
    var fetchActionBtn = jQuery('#fetch-locations-csv-action');
    
    if (csvFile && csvFile.trim() !== '') {
        statusBadge.removeClass('houzez-status-warning').addClass('houzez-status-success').text('<?php esc_html_e('File Selected', 'houzez'); ?>');
        fetchBtn.prop('disabled', false);
        fetchActionBtn.prop('disabled', false);
    } else {
        statusBadge.removeClass('houzez-status-success').addClass('houzez-status-warning').text('<?php esc_html_e('No File Selected', 'houzez'); ?>');
        fetchBtn.prop('disabled', true);
        fetchActionBtn.prop('disabled', true);
        jQuery('#locations-mapping-section').hide();
        jQuery('#locations-results-section').hide();
    }
}

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

    // Update status when file is selected or changed
    $('#locations-csv-file').on('input change keyup paste', function() {
        updateCSVStatus();
    });

    // Sync the action button with the main fetch button
    $('#fetch-locations-csv-action').on('click', function() {
        $('#fetch-locations-csv').click();
    });

    // Initial status check on page load
    updateCSVStatus();
});
</script>