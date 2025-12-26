<?php
global $agent_listing_ids;
$agent_listing_ids = Houzez_Query::get_agent_properties_ids_by_agent_id(get_the_ID());
?>
<div class="agent-stats-wrap">
    <div class="row g-4">
        <div class="col-lg-4 col-md-12 col-sm-12">
            <?php get_template_part('template-parts/realtors/agent/stats-property-types'); ?> 
        </div>
        <div class="col-lg-4 col-md-12 col-sm-12">
            <?php get_template_part('template-parts/realtors/agent/stats-property-status'); ?> 
        </div>

        <?php if(taxonomy_exists('property_city')) { ?>
        <div class="col-lg-4 col-md-12 col-sm-12">
            <?php get_template_part('template-parts/realtors/agent/stats-property-cities'); ?> 
        </div>
        <?php } ?>
    </div>
</div>