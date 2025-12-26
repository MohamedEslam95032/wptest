<?php

if ( !defined('ABSPATH') ) exit;

$global_filters = get_option('realtyna_mls_on_the_fly_global_filters', array());
// Check if the form is submitted
if (isset($_POST['save_filter'])) {
    // Process and save the filter to the database
    $current_filter = str_replace('\\', '', $_POST['current_filter']);
    $current_filter = sanitize_text_field($current_filter);
    $global_filters = array($current_filter); // Replace the old filters with the new one

    // Save the updated filters to the database
    update_option('realtyna_mls_on_the_fly_global_filters', $global_filters);

    // Display a success message or perform other actions
    echo '<div class="updated"><p>Filter saved successfully!</p></div>';
}

?>
<h3>Rendered Filters</h3>
<div class="textarea-container">
    <form method="post">
        <textarea id="rendered-filters" name="current_filter" class="form-control" rows="5"><?php echo esc_textarea(implode(' and ', $global_filters)); ?></textarea>
        <input type="submit" name="save_filter" value="Save Filter" class="btn btn-success mt-2">
    </form>
</div>