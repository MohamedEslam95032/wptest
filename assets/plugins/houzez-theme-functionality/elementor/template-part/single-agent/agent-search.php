<?php
global $settings;
$houzez_local = houzez_get_localization();

$city = $category = $agent_name = '';

$default_category = array();

$category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : $default_category;
$agent_name = isset ( $_GET['agent_name'] ) ? sanitize_text_field($_GET['agent_name']) : '';

$default_city = array();
$city = isset($_GET['city']) ? sanitize_text_field($_GET['city']) : $default_city;

$purl = houzez_get_template_link('template/template-agents.php');

?>
<div class="agent-ele-search-wrap">

    <?php if($settings['agent_search_title']) { ?>
    <h3 class="widget-title mb-4"><?php echo esc_html($settings['agent_search_title']); ?></h3>
    <?php } ?>

    <form method="get" action="<?php echo esc_url($purl); ?>">
        <input type="hidden" name="agent-search" value="yes">
        <div class="form-group mb-2">
            <div class="search-icon">
                <input type="text" class="form-control" value="<?php echo esc_attr($agent_name); ?>" name="agent_name" placeholder="<?php echo esc_attr($houzez_local['search_agent_name']); ?>">
            </div><!-- search-icon -->
        </div>
        
        <div class="form-group mb-2">
            <select name="category[]" class="selectpicker form-control bs-select-hidden" title="<?php echo $houzez_local['all_agent_cats']; ?>" data-live-search="true" data-selected-text-format="count" multiple data-actions-box="true" data-select-all-text="<?php echo houzez_option('cl_select_all', 'Select All'); ?>" data-deselect-all-text="<?php echo houzez_option('cl_deselect_all', 'Deselect All'); ?>" data-none-results-text="<?php echo houzez_option('cl_no_results_matched', 'No results matched');?> {0}">
                        <?php houzez_get_search_taxonomies('agent_category', $category ); ?>
                    </select><!-- selectpicker -->
        </div><!-- form-group -->

        <div class="form-group mb-2">
            <select name="city[]" class="selectpicker form-control bs-select-hidden" title="<?php echo $houzez_local['all_agent_cities']; ?>" data-live-search="true" data-selected-text-format="count" multiple data-actions-box="true" data-select-all-text="<?php echo houzez_option('cl_select_all', 'Select All'); ?>" data-deselect-all-text="<?php echo houzez_option('cl_deselect_all', 'Deselect All'); ?>" data-none-results-text="<?php echo houzez_option('cl_no_results_matched', 'No results matched');?> {0}">
                        <?php houzez_get_search_taxonomies('agent_city', $city ); ?>
                 
                    </select><!-- selectpicker -->
        </div><!-- form-group -->

        <button type="submit" class="btn btn-search btn-secondary w-100"><?php echo $houzez_local['search_agent_btn']; ?></button>
    </form>
</div>