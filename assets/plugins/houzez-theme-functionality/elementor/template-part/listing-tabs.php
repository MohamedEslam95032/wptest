<?php
global $post, $settings, $total_listing_found;
$tabs_field =  $settings['tabs_field'];
$show_all =  $settings['show_all'];

if( $tabs_field == 'property_type') {
    $taxonomies = $settings['type_data'];

} else if( $tabs_field == 'property_status') {
    $taxonomies = $settings['status_data'];

} else if( $tabs_field == 'property_city') {
    $taxonomies = $settings['city_data'];
}

$current_tab = $all_tab = '';
if(isset($_GET['tab']) && $_GET['tab'] != 'all') {
    $current_tab = $_GET['tab'];
} else {
    $all_tab = 'active';
}

$listing_page_link = get_permalink( $post->ID );

$all_tab_link = add_query_arg( 
    array(
        'tab' => 'all',
    ), $listing_page_link 
);
$property_label = houzez_option('cl_property', 'Property');
if( $total_listing_found > 1 ) {
   $property_label = houzez_option('cl_properties', 'Properties'); 
}

if( $settings['listing_tabs'] == 'yes' ) { ?>
<ul class="nav nav-tabs">
    <?php if($show_all == 'yes') { ?>
    <li class="nav-item">
        <a class="nav-link <?php echo esc_attr($all_tab); ?>" href="<?php echo esc_url($all_tab_link); ?>">
            <?php echo esc_html__('All', 'houzez'); ?>
        </a>
    </li>
    <?php } 
    if (!empty($taxonomies)) {
 
        foreach ($taxonomies as $slug) { 

            $tab_link = add_query_arg( 
                array(
                    'tab' => $slug, 
                    'tax' => $tabs_field, 
                ), $listing_page_link 
            );

            $active_tab = '';
            if( $current_tab == $slug ) {
                $active_tab = 'active';
            }

            $tabname = houzez_get_term_by( 'slug', $slug, $tabs_field );
            echo '<li class="nav-item">
                    <a class="nav-link '.$active_tab.'" href="'.esc_url($tab_link).'">
                        '.esc_attr($tabname->name).'
                    </a>
                </li>';
        }
    }
    ?>
</ul><!-- nav-tabs --> 
<?php } else {
    echo esc_attr($total_listing_found).' '; echo $property_label;
}?> 
