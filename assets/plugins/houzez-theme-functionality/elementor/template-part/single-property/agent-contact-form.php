<?php 
global $settings, $ele_settings; 

if( $settings['form_tabs_enabled'] == 'yes' ) {

	$default_tab = $settings['default_tab'];

	$tab01 = $default_tab == 'request_info' ? '' : 'active';
	$content01 = $default_tab == 'request_info' ? '' : 'show active';
	$tab02 = $default_tab == 'request_info' ? 'active' : '';
	$content02 = $default_tab == 'request_info' ? 'show active' : '';

?>
<div class="property-form-tabs-wrap mb-4">
	<div class="property-form-tabs">
        <ul class="nav nav-tabs">
            <li class="nav-item flex-fill">
                <a class="nav-link text-center py-3 <?php echo esc_attr($tab01);?>" data-bs-toggle="tab" href="#tab_tour" role="tab"><span class="tab-title"><?php echo esc_attr($settings['schedule_tour_title']); ?></span></a>
            </li>
            <li class="nav-item flex-fill">
                <a class="nav-link text-center py-3 <?php echo esc_attr($tab02);?>" data-bs-toggle="tab" href="#tab_agent_form" role="tab"><span class="tab-title"><?php echo esc_attr($settings['request_info_title']); ?></span></a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="property-form-tabs-tab-pane tab-pane fade <?php echo esc_attr($content01);?>" id="tab_tour" role="tabpanel">
            	<?php get_template_part('property-details/partials/schedule-tour-sidebar-form'); ?>
            </div>
            <div class="property-tabs-module-tab-pane tab-pane fade <?php echo esc_attr($content02);?>" id="tab_agent_form" role="tabpanel">
                <div class="widget widget-wrap p-4 mb-4 widget-property-form">
                    <?php get_template_part('property-details/agent-form'); ?>
                </div>
            </div>
        </div>
    </div><!-- property-form-tabs -->
</div><!-- property-form-tabs-wrap -->
<?php } else { 
	get_template_part('property-details/agent-form'); } 
?>


