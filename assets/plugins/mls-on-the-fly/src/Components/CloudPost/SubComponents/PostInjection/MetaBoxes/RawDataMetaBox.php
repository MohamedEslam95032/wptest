<?php
namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\PostInjection\MetaBoxes;


use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;
use Realtyna\MlsOnTheFly\Settings\Settings;

class RawDataMetaBox {

    private IntegrationInterface $integration;

    public function __construct(IntegrationInterface $integration)
    {
        $this->integration = $integration;
        if (Settings::get_setting('show_raw_data', false) == 'yes') {
            add_action('add_meta_boxes', [$this, 'showRawData']);
        }
    }

    public function showRawData(): void
    {
        foreach ($this->integration->customPostTypes as $type) {
            add_meta_box(
                'mls-on-the-fly-raw-data',       // $id
                'MLS On The FLY Raw Data',     // $title
                [$this, 'showRawDataMataBox'],    // $callback
                $type,                              // $page
                'normal',                   // $context
                'high'                      // $priority
            );
        }
    }

    public function showRawDataMataBox(): void
    {
        global $post;
        d(get_post_meta($post->ID, 'realty_feed_raw_data'));
    }

}