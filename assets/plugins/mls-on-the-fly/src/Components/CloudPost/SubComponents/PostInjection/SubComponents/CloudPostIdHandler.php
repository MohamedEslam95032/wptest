<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\PostInjection\SubComponents;
use Realtyna\MlsOnTheFly\Boot\Log;

class CloudPostIdHandler{

    /**
     * @var int|mixed
     */
    private int $cloudPostId;

    /**
     * Initializes the Cloud Post IDs used for synchronization between WordPress and RF.
     *
     * This method calculates and sets the initial Cloud Post ID based on the current state of the
     * WordPress and RF mappings. It ensures that the Cloud Post ID is correctly initialized to
     * maintain synchronization.
     *
     * @author Chandler.p chandler.p@realtyna.com
     */
    public function __construct()
    {
        // Get the last WordPress Post ID
        $wordPressLastPostID = $this->getWordPressLastPostID();
        Log::info('Getting last WordPress post ID: ' . $wordPressLastPostID);

        // Check if there is an RF mapping for the first and last RF Post IDs
        global $wpdb;
        $RFFirstPostID = $wpdb->get_row(
            "SELECT * FROM {$wpdb->prefix}realtyna_rf_mappings ORDER BY post_id ASC LIMIT 1"
        );
        $RFLastPostID = $wpdb->get_row(
            "SELECT * FROM {$wpdb->prefix}realtyna_rf_mappings ORDER BY post_id DESC LIMIT 1"
        );

        if (!$RFFirstPostID) {
            // If there is no RF post yet, set the first Cloud Post ID with a buffer of 1000
            // TODO: Consider making 1000 a dynamic value
            $cloudPostId = $wordPressLastPostID + 1000;
            Log::info('There is no RF post yet, setting first post ID to: ' . $cloudPostId);
        } else {
            // If there is at least one RF post, set the first Cloud Post ID to the next sequential ID
            $cloudPostId = $RFLastPostID->post_id + 1;
            Log::info('There is a RF post, setting first post ID to: ' . $cloudPostId);
        }

        // Check the distance between the first RF Post ID and the last WordPress Post ID
        if ($RFFirstPostID != null && $RFFirstPostID->post_id - $wordPressLastPostID < 200) {
            // If the distance is less than 200, log a warning
            $cloudPostId = $this->updateCloudPostIds();
            Log::warning('Distance between last WordPress post ID and first RF post ID is less than 100');
        }

        // Set the calculated Cloud Post ID for synchronization
        $this->cloudPostId = $cloudPostId;
    }

    /**
     * Get WordPress last post id
     *
     * @return mixed
     * @author Chandler.p chandler.p@realtyna.com
     */
    public function getWordPressLastPostID(): int
    {
        global $wpdb;
        $query = "SELECT ID FROM $wpdb->posts ORDER BY ID DESC LIMIT 0,1";
        $result = $wpdb->get_results($query);
        if (!empty($result)) {
            return $result[0]->ID;
        }

        return 0;
    }

    /**
     * Uploads Cloud Post IDs to ensure synchronization with RF mappings.
     *
     * This method takes the first RF Post ID as a reference and updates all RF mappings'
     * post IDs to ensure a consistent synchronization between WordPress and RF. It also
     * updates the corresponding postmeta records.
     *
     * @author Chandler.p chandler.p@realtyna.com
     *
     */
    public function updateCloudPostIds(): int
    {
        global $wpdb;

        // Table names with prefixes
        $rfMappingTable = $wpdb->prefix . 'realtyna_rf_mappings';
        $postmetaTable = $wpdb->prefix . 'postmeta';

        // Retrieve all RF mappings
        $rfMappings = $wpdb->get_results("SELECT * FROM $rfMappingTable");

        // Initialize a variable to store the last updated post ID
        $lastId = null;

        foreach ($rfMappings as $rfMapping) {
            // Update the RF mapping's post ID by adding a buffer of 1000
            $oldID = $rfMapping->post_id;
            $newID = $rfMapping->post_id + 1000;

            // Update the RF mapping's post ID in the database
            $wpdb->update(
                $rfMappingTable,
                ['post_id' => $newID],
                ['id' => $rfMapping->id],
                ['%d'],
                ['%d']
            );

            // Update the corresponding postmeta records with the new post ID
            $wpdb->update(
                $postmetaTable,
                ['post_id' => $newID],
                ['post_id' => $oldID],
                ['%d'],
                ['%d']
            );

            // Trigger action for each updated post ID
            do_action('realtyna_mls_on_the_fly_update_cloud_post_ids', $newID, $oldID);

            // Store the last updated post ID
            $lastId = $newID;
        }

        // Set the calculated last updated post ID to maintain synchronization
        return $lastId ? $lastId + 1 : 1;
    }

    public function getCloudPostId(): int
    {
        return $this->cloudPostId;
    }
}