<?php 
global $settings;
$agents_query = Houzez_Query::loop_agency_agents(get_the_ID());
?>

<div id='tab-agents' class="agents-list-view">
    <?php
    if ( $agents_query->have_posts() ) :
        while ( $agents_query->have_posts() ) : $agents_query->the_post(); 

            get_template_part('template-parts/realtors/agent/list');

        endwhile;
        wp_reset_postdata();
    else:
        get_template_part('template-parts/realtors/agent/none');
    endif;
    ?> 
</div><!-- listing-view -->