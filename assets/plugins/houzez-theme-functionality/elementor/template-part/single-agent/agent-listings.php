<?php 
global $settings, $paged, $total_listing_found, $hide_author, $hide_author_date, $hide_button, $hide_date;
$the_query = Houzez_Query::loop_agent_properties();
$total_listing_found = $the_query->found_posts;

$hide_button = isset($settings['hide_button']) && $settings['hide_button'] === 'none' ? false : true;
$hide_author_date = isset($settings['hide_author_date']) && $settings['hide_author_date'] === 'none' ? false : true;
$hide_author = isset($settings['hide_author']) && $settings['hide_author'] === 'none' ? false : true;
$hide_date = isset($settings['hide_date']) && $settings['hide_date'] === 'none' ? false : true;

$posts_limit = $settings['posts_limit'] ?? 12;
$pagination_type = $settings['pagination_type'] ?? '_loadmore';

$layout_columns = $settings['module_type'];

// Extract numeric value from layout_columns (e.g., 'grid-view-3-cols' becomes 3)
if (is_string($layout_columns) && preg_match('/grid-view-(\d+)-cols/', $layout_columns, $matches)) {
    $layout_columns = intval($matches[1]);
}

// Set up the default view
$default_view = isset($settings['listings_layout']) ? $settings['listings_layout'] : 'list-view-v1';

// Default arguments for agent listings
$args = array(
    'default_view' => $default_view,
    'layout' => 'no-sidebar', // Agent listings in Elementor always full width
    'grid_columns' => $layout_columns,
    'show_switch' => true,
);

// Determine if we should show the view switcher based on version
if (in_array($default_view, array('grid-view-v3', 'grid-view-v4', 'grid-view-v5', 'grid-view-v6', 'list-view-v7'))) {
    $args['show_switch'] = false;
}

// Get view settings
$view_settings = houzez_get_listing_view_settings($args['default_view']);
$current_view = $view_settings['current_view'];
$current_item_template = $view_settings['current_item_template'];
$item_version = $view_settings['item_version'];

// Get listing view class
$listing_view_class = houzez_get_listing_view_class($current_view, $item_version, $args['layout'], $args['grid_columns']);

$active_listings_tab = 'active';
$active_listings_content = 'show active';

if(isset($_GET['tab']) || $paged > 0) {
    ?>
    <script>
        jQuery(document).ready(function ($) {
            $('html, body').animate({
                scrollTop: $(".hzele-agent-listings-wrap").offset().top - 30
            }, 'slow');
        });
    </script>
    <?php
};
?>

<div class="hzele-agent-listings-wrap">
    
    <div class="listing-tools-wrap">
        <div class="d-flex align-items-center">
            <div id="houzez-listings-tabs-wrap" class="listing-tabs flex-grow-1">
                <?php htf_get_template_part('elementor/template-part/listing-tabs'); ?> 
            </div>
            <?php get_template_part('template-parts/listing/listing-sort-by'); ?>  
        </div><!-- d-flex -->
    </div><!-- listing-tools-wrap -->

    <div class="<?php echo esc_attr($listing_view_class).' '.esc_attr($settings['module_type']); ?>" role="list" data-view="<?php echo esc_attr($current_view); ?>">
        <?php
        if ( $the_query->have_posts() ) :
            while ( $the_query->have_posts() ) : $the_query->the_post();

                $agent_listing_ids[] = get_the_ID(); 
                get_template_part('template-parts/listing/item', $current_item_template);

            endwhile;
            wp_reset_postdata();
        else:
            get_template_part('template-parts/listing/item', 'none');
        endif;
        ?> 
    </div><!-- listing-view -->

    <?php houzez_pagination( $the_query->max_num_pages, $total_listing_found, $posts_limit, $pagination_type ); ?>
    
</div><!-- hzele-agent-listings-wrap -->