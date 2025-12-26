<?php
global $properties_ids, $houzez_local;

$agency_properties_ids = array();
$agents_properties_ids = array();

$agency_agents_ids = Houzez_Query::loop_agency_agents_ids(get_the_ID());

$agency_properties_ids = Houzez_Query::get_property_ids_by_agency(get_the_ID());

if (!empty($agency_agents_ids)) {
    $agents_properties_ids = Houzez_Query::get_property_ids_by_agents($agency_agents_ids);
}

$properties_ids = array_merge( $agency_properties_ids, $agents_properties_ids );
$properties_ids = array_unique( $properties_ids );
?>
<div class="agent-stats-wrap">
    <div class="row g-4">
        <div class="col-lg-4 col-md-12 col-sm-12">
            <?php get_template_part('template-parts/realtors/agency/stats-property-types'); ?> 
        </div>
        <div class="col-lg-4 col-md-12 col-sm-12">
            <?php get_template_part('template-parts/realtors/agency/stats-property-status'); ?> 
        </div>

        <?php if(taxonomy_exists('property_city')) { ?>
        <div class="col-lg-4 col-md-12 col-sm-12">
            <?php get_template_part('template-parts/realtors/agency/stats-property-cities'); ?> 
        </div>
        <?php } ?>
    </div>
</div>