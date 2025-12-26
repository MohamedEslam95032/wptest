<?php
global $post, $settings, $hide_author_date;
$layout_columns = $settings['layout_columns'];

$hide_author_date = isset($settings['hide_author_date']) && $settings['hide_author_date'] === 'none' ? false : true;

// Extract numeric value from layout_columns (e.g., 'grid-view-3-cols' becomes 3)
if (is_string($layout_columns) && preg_match('/grid-view-(\d+)-cols/', $layout_columns, $matches)) {
    $layout_columns = intval($matches[1]);
}

$similer_criteria = $settings['listing_from'];
$similer_count = $settings['no_of_posts'];
$sort_by = $settings['orderby'];

// Set up the default view
$default_view_option = $settings['listings_layout'];

// Determine if we should show the view switcher based on version
$show_switch = false; // Generally not needed for similar properties

// Default arguments
$args = array(
    'default_view' => $default_view_option,
    'layout' => 'no-sidebar', // Similar properties are always full width
    'grid_columns' => $layout_columns, // Default to 3 columns for grid
);

// Get view settings
$view_settings = houzez_get_listing_view_settings($args['default_view']);
$current_view = $view_settings['current_view'];
$current_item_template = $view_settings['current_item_template'];
$item_version = $view_settings['item_version'];

// Get listing view class
$listing_view_class = houzez_get_listing_view_class($current_view, $item_version, $args['layout'], $args['grid_columns']);

// Get similar properties
$similar_query = houzez_get_similar_properties(null, $similer_criteria, $similer_count, $sort_by);

if ($similar_query->have_posts()) : ?>
    <div id="similar-listings-wrap" class="similar-property-wrap property-section-wrap listing-<?php echo esc_attr($item_version); ?>">
        <?php if($settings['section_header']) { ?>
        <div class="block-title-wrap">
            <h2><?php echo houzez_option('sps_similar_listings', 'Similar Listings'); ?></h2>
        </div><!-- block-title-wrap -->
        <?php } ?>
        
        <div class="<?php echo esc_attr($listing_view_class); ?>" role="list" data-view="<?php echo esc_attr($current_view); ?>">
            <?php
            while ($similar_query->have_posts()) : $similar_query->the_post();
                get_template_part('template-parts/listing/item', $current_item_template);
            endwhile;
            wp_reset_postdata();
            ?> 
        </div><!-- listing-view -->
    </div><!-- similar-property-wrap -->
<?php
endif;
?>