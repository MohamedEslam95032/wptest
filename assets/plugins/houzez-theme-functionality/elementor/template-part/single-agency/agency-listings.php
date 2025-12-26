<?php 
global $settings, $paged, $total_listing_found, $hide_author, $hide_author_date, $hide_button, $hide_date;
if ( is_front_page()  ) {
    $paged = (get_query_var('page')) ? get_query_var('page') : 1;
}

$hide_button = isset($settings['hide_button']) && $settings['hide_button'] === 'none' ? false : true;
$hide_author_date = isset($settings['hide_author_date']) && $settings['hide_author_date'] === 'none' ? false : true;
$hide_author = isset($settings['hide_author']) && $settings['hide_author'] === 'none' ? false : true;
$hide_date = isset($settings['hide_date']) && $settings['hide_date'] === 'none' ? false : true;

$agency_id = get_the_ID();

$tax_query = array();
$meta_query = array();

if ( isset( $_GET['tab'] ) && !empty($_GET['tab']) && $_GET['tab'] != "reviews" && $_GET['tab'] != "all") {
    $tax_query[] = array(
        'taxonomy' => 'property_status',
        'field' => 'slug',
        'terms' => $_GET['tab']
    );
}

$posts_limit = $settings['posts_limit'] ?? 12;
$pagination_type = $settings['pagination_type'] ?? '_loadmore';

$args = array(
    'post_type' => 'property',
    'posts_per_page' => intval($posts_limit),
    'paged' => $paged,
    'post_status' => 'publish',
);

$args = apply_filters( 'houzez_sold_status_filter', $args );

$agents_array = array();
$agency_properties_ids = array();
$agents_properties_ids = array();

$agency_agents_ids = Houzez_Query::loop_agency_agents_ids(get_the_ID());

$agency_properties_ids = Houzez_Query::get_property_ids_by_agency(get_the_ID());

if (!empty($agency_agents_ids)) {
    $agents_properties_ids = Houzez_Query::get_property_ids_by_agents($agency_agents_ids);
}

$properties_ids = array_merge( $agency_properties_ids, $agents_properties_ids );
$properties_ids = array_unique( $properties_ids );


if (!empty($properties_ids)) {
    $args['post__in'] = $properties_ids;
} else {
    $args['post__in'] = array(-1); // To return no results if no properties are found.
}


$tax_count = count($tax_query);
if($tax_count > 0 ) {
    $args['tax_query'] = $tax_query;
}


$args = houzez_prop_sort($args);

$the_query = new WP_Query( $args );
$total_listing_found = $the_query->found_posts;

$layout_columns = $settings['module_type'];

// Extract numeric value from layout_columns (e.g., 'grid-view-3-cols' becomes 3)
if (is_string($layout_columns) && preg_match('/grid-view-(\d+)-cols/', $layout_columns, $matches)) {
    $layout_columns = intval($matches[1]);
}

// Set up the default view
$default_view = isset($settings['listings_layout']) ? $settings['listings_layout'] : 'list-view-v1';

// Default arguments for agency listings
$view_args = array(
    'default_view' => $default_view,
    'layout' => 'no-sidebar', // Agency listings in Elementor always full width
    'grid_columns' => $layout_columns,
    'show_switch' => true,
);

// Determine if we should show the view switcher based on version
if (in_array($default_view, array('grid-view-v3', 'grid-view-v4', 'grid-view-v5', 'grid-view-v6', 'list-view-v7'))) {
    $view_args['show_switch'] = false;
}

// Get view settings
$view_settings = houzez_get_listing_view_settings($view_args['default_view']);
$current_view = $view_settings['current_view'];
$current_item_template = $view_settings['current_item_template'];
$item_version = $view_settings['item_version'];

// Get listing view class
$listing_view_class = houzez_get_listing_view_class($current_view, $item_version, $view_args['layout'], $view_args['grid_columns']);

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