<?php 
global $settings, $paged, $hide_author, $hide_author_date, $hide_button, $hide_date;
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

$active_reviews_content = $active_reviews_tab = '';
$active_listings_tab = 'active';
$active_listings_content = 'show active';

if(isset($_GET['tab']) || $paged > 0) {

    if(isset($_GET['tab']) && $_GET['tab'] == 'reviews') {
        $active_reviews_tab = 'active';
        $active_reviews_content = 'show active';
        $active_listings_tab = '';
        $active_listings_content = '';
    }
    ?>
    <script>
        jQuery(document).ready(function ($) {
            $('html, body').animate({
                scrollTop: $(".agent-nav-wrap").offset().top
            }, 'slow');
        });
    </script>
    <?php
}
?>
<div id="review-scroll" class="agent-nav-wrap">
    <ul class="nav nav-pills nav-justified gap-2" role="tablist">
        
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo esc_attr($active_listings_tab); ?> py-3" href="#tab-properties" data-bs-toggle="pill" role="tab">
                <?php esc_html_e('Listings', 'houzez'); ?> (<?php echo esc_attr($total_listing_found); ?>)
            </a>
        </li>
        
        <li class="nav-item" role="presentation">
            <a class="nav-link hz-review-tab <?php echo esc_attr($active_reviews_tab); ?> py-3" href="#tab-reviews" data-bs-toggle="pill" role="tab">
                <?php esc_html_e('Reviews', 'houzez'); ?> (<?php echo houzez_reviews_count('review_agent_id'); ?>)
            </a>
        </li>
    </ul>
</div><!-- agent-nav-wrap -->

<div class="tab-content" id="tab-content">
    
    <div class="tab-pane fade <?php echo esc_attr($active_listings_content); ?>" id="tab-properties" role="tabpanel">
        <div class="listing-tools-wrap">
            <div class="d-flex align-items-center">
                <div id="houzez-listings-tabs-wrap" class="listing-tabs flex-grow-1">
                    <?php 
                    if( $settings['listing_tabs'] == 'yes' ) {
                        htf_get_template_part('elementor/template-part/listing-tabs'); 
                    }?> 
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
    </div><!-- tab-pane -->

    <div class="tab-pane fade <?php echo esc_attr($active_reviews_content); ?>" id="tab-reviews" role="tabpanel">
        <?php htf_get_template_part('elementor/template-part/single-agent/agent-reviews'); ?> 
    </div><!-- tab-pane -->
</div><!-- tab-content -->