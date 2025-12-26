<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets;

use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;

/**
 * Integration class for PropertyDrive.
 *
 * This class provides integration between the MLS On The Fly<sup>&reg;</sup> and PropertyDrive, a WordPress theme
 * that allows users to create and manage property listings.
 *
 * The integration features include:
 *
 * - Setting the default post author for newly created posts.
 * - Setting the default contact ID for newly created property listings.
 * - Uploading the Cloud Post IDs to ensure synchronization with PropertyDrive.
 *
 * @autor Chandler.p
 *         chandler.p@realtyna.com
 */
class PropertyDriveIntegration implements IntegrationInterface
{

    /**
     * An array of meta keys that are allowed to be saved.
     *
     * @var array
     */
    public array $metaWhiteList = array();
    public string $name = 'propertydrive';
    public array $customPostTypes = array(
        'property',
    );
    public array $customTaxonomies = array(
        'property_type',
        'property_county',
        'property_area',
    );

    /**
     * Constructor.
     *
     * This method initializes the integration class by setting the allowed meta keys.
     *
     * @throws \ReflectionException
     */
    public function __construct()
    {
        add_action('realtyna_mls_on_the_fly_upload_cloud_post_ids', array($this, 'uploadCloudPostID'), 10, 2);
        add_filter('realtyna_mls_on_the_fly_current_post_id_to_inject', array($this, 'currentPostIDToInject'), 10, 2);

        // Get images
        add_filter(
            'realtyna_mls_on_the_fly_cloud_posts',
            function ($posts, $listings, $RFQuery) {
                foreach ($posts as $post) {
                    $images = array();

                    if ($post instanceof \WP_Post && is_array($post->medias) && count($post->medias) > 0) {
                        foreach ($post->medias as $media) {
                            $images[] = $media['MediaURL'];
                        }
                    }
                    if(isset($post->meta_data)){
                        $post->meta_data['wppd_pics'] = maybe_serialize($images);
                    }
                }
                return $posts;
            },
            10,
            3
        );

        return self::class;
    }

    public function currentPostIDToInject($postID, $data)
    {
        if (isset($data['listing_id'])) {
            return $data['listing_id'];
        }
        return $postID;
    }

    /**
     * Uploads Cloud Post IDs to ensure synchronization with EPL.
     *
     * This method is called when the Cloud Post IDs need to be uploaded to ensure synchronization
     * with EPL. It takes the new ID as a reference and updates all EPL mappings' post IDs to
     * ensure a consistent synchronization between WordPress and EPL.
     *
     * @param int $newID The new Cloud Post ID.
     * @param int $oldID The old Cloud Post ID.
     */
    public function uploadCloudPostID(int $newID, int $oldID): void
    {
    }


    /**
     * Checks if a meta key is allowed to be saved.
     *
     * This method checks if a meta key is allowed to be saved based on the white list of allowed meta keys. If the meta key is in the white list, it is allowed to be saved. If the meta key is not in the white list and it contains the string 'property_', it is not allowed to be saved.
     *
     * @param bool|null $check Whether to allow the meta key to be saved or not.
     * @param string $metaKey The meta key to be checked.
     *
     * @return bool|null Whether to allow the meta key to be saved or not.
     */
    public function checkMetaToSave($check, $metaKey): bool|null
    {
        if (in_array($metaKey, $this->metaWhiteList)) {
            return true;
        }

        if (str_contains($metaKey, 'propertydrive_')) {
            return false;
        }

        return $check;
    }

    public function getMappingDir(): string
    {
        return '';
    }
}
