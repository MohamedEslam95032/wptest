<?php
global $settings;
$houzez_local = houzez_get_localization();

$agency_name = isset ( $_GET['agency_name'] ) ? sanitize_text_field($_GET['agency_name']) : '';

$purl = get_post_type_archive_link("houzez_agency");

?>
<div class="agent-ele-search-wrap">

    <?php if($settings['agency_search_title']) { ?>
    <h3 class="widget-title"><?php echo esc_html($settings['agency_search_title']); ?></h3>
    <?php } ?>

    <form method="get" action="<?php echo esc_url($purl); ?>">
        <div class="form-group">
            <div class="search-icon">
                <input type="text" class="form-control" value="<?php echo esc_attr($agency_name); ?>" name="agency_name" placeholder="<?php echo esc_attr($houzez_local['search_agency_name']); ?>">
            </div><!-- search-icon -->
        </div>
        <button type="submit" class="btn btn-search btn-secondary btn-full-width"><?php echo $houzez_local['search_agency_btn']; ?></button>
    </form>
</div>