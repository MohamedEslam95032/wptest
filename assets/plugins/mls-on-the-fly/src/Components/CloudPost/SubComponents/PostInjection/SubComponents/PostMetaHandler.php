<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\PostInjection\SubComponents;
use Realtyna\MlsOnTheFly\Boot\Log;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;

class PostMetaHandler{

    /**
     * @var int|mixed
     */
    private int $cloudPostId;
    private IntegrationInterface $integration;

    public function __construct(IntegrationInterface $integration)
    {
        $this->integration = $integration;
        add_filter('update_post_metadata', [$this, 'filterUpdatedMetas'], 10, 5);
        add_filter('add_post_metadata', [$this, 'filterUpdatedMetas'], 10, 5);
    }


    /**
     * Filter to control whether updated post metadata should be saved.
     *
     * This method is used as a filter for 'update_post_metadata' in WordPress. It checks if the
     * metadata update is related to a listing with a specific ListingKey and a specific meta_key
     * pattern ('property_'). If the conditions are met, it prevents the metadata update from being
     * saved.
     *
     * @param bool|null $check The check for whether to save the updated metadata.
     * @param int $object_id The ID of the object (post) for which metadata is being updated.
     * @param string $meta_key The meta key being updated.
     * @param mixed $meta_value The new meta value.
     * @param mixed $prev_value The previous meta value.
     * @return bool|null The modified check value indicating whether to save the updated metadata.
     */
    public function filterUpdatedMetas(
        ?bool $check,
        int $object_id,
        string $meta_key,
        $meta_value,
        $prev_value
    ): ?bool {
        global $wpdb;

        // Table name with prefix
        $rfMappingTable = $wpdb->prefix . 'realtyna_rf_mappings';

        // Retrieve the ListingKey associated with the object_id (post ID)
        $listingKey = $wpdb->get_var(
            $wpdb->prepare("SELECT listing_key FROM $rfMappingTable WHERE post_id = %d", $object_id)
        );

        // Check if a ListingKey exists and the meta_key contains 'property_'
        if ($listingKey !== null) {
            // Prevent post metas from being updated from the front end to avoid cache deletion
            if (!is_admin()) {
                return false;
            }
            // Get Integration instance and check if the meta_key is in blacklist or whitelist
            return $this->integration->checkMetaToSave($check, $meta_key) || $meta_key == '_edit_lock' || $meta_key == '_edit_last';
        }

        return $check;
    }


}