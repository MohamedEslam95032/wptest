<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets;

use Realtyna\MlsOnTheFly\Boot\App;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\Mapping;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\RFQuery;
use Realtyna\MlsOnTheFly\Settings\Settings;

/**
 * Integration class for Easy Property Listings (EPL).
 *
 * This class provides integration between the MLS On The Fly<sup>&reg;</sup> and Easy Property Listings
 * (EPL), a WordPress plugin that allows users to create and manage property listings.
 *
 * The integration features include:
 *
 * - Setting the default post author for newly created posts.
 * - Setting the default contact ID for newly created property listings.
 * - Uploading the Cloud Post IDs to ensure synchronization with EPL.
 *
 * @author Chandler.p
 *         chandler.p@realtyna.com
 */
class EPLIntegration implements IntegrationInterface
{
    /**
     * An array of meta keys that are allowed to be saved.
     *
     * @var array
     */
    public array $metaWhiteList = [
        'property_owner'
    ];
    public string $name = 'epl';
    public array $customPostTypes = [
        'property',
        'land',
        'rental',
        'rural',
        'commercial',
        'commercial_land',
        'business',
    ];
    public array $customTaxonomies = [
        'location',
        'tax_feature',
        'property-feature',
        'property-type',
        'property-city',
        'property-status'
    ];

    /**
     * Constructor.
     *
     * This method initializes the integration class by setting the allowed meta keys.
     */
    public function __construct()
    {
        //Load this integration custom taxonomies array
        // TODO I had to use init hook to manage when does mapping files will be read
        add_action('init', function (){
            /** @var Mapping $mapping */
            $mapping = App::get(Mapping::class);
            $mappingFile = $mapping->mappingConfig->getQueryMapping();
            $this->customTaxonomies = array_keys($mappingFile['taxonomies']);
        }, 99999);
        $this->metaWhiteList = [
            'property_owner'
        ];

        add_filter( 'realtyna/mls-on-the-fly/admin/settings/tabs', array( __CLASS__, 'filter_settings_tabs' ) );
//        add_filter('realtyna_mls_on_the_fly_cloud_posts', [$this, 'setPostAuthor'], 10, 3);
//        add_filter('realtyna_mls_on_the_fly_cloud_posts', [$this, 'setContactID'], 10, 3);
        add_action('realtyna_mls_on_the_fly_upload_cloud_post_ids', [$this, 'uploadCloudPostID'], 10, 2);
    }

    /**
     * Add custom tab to settings page
     *
     * @param array $tabs
     *
     * @author Cyrus <cyrus.a@realtyna.com>
     *
     * @return array
     */
    public static function filter_settings_tabs( $tabs ) {

        $tabs['epl_integration'] = array(
            'title'  => __( 'EPL', 'realtyna-mls-on-the-fly' ),
            'priority' => 40,
            'filename' => REALTYNA_MLS_ON_THE_FLY_TEMPLATES_PATH . "admin/settings-page/epl-integration-tab.php"
        );

        return $tabs;
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
     * Sets the default contact ID for newly created property listings.
     *
     * This method filters the list of property listings retrieved from Realty Feed's API and sets
     * the default contact ID for each listing based on the value stored in the WordPress options
     * table. The contact ID is added as a meta data to the listing.
     *
     * @param array $posts The list of property listings retrieved from Realty Feed's API.
     * @param array $listings The list of property listings retrieved from WordPress.
     * @param RFQuery $RFQuery The Realty Feed API query object.
     *
     * @return array The list of property listings with the default contact ID set.
     */
    public function setContactID(array $posts, array $listings, RFQuery $RFQuery): array
    {
        foreach ($posts as $post) {
            // TODO this should be dynamic
            $post->meta_data['property_owner'] = 1011;
        }
        return $posts;
    }

    /**
     * Sets the default post author for newly created posts.
     *
     * This method filters the list of posts retrieved from Realtyna's API and sets the default post author for each post based on the value stored in the WordPress options table. The post author is set as the post's author.
     *
     * @param array $posts The list of posts retrieved from Realtyna's API.
     * @param array $listings The list of posts retrieved from WordPress.
     * @param RFQuery $RFQuery The Realtyna API query object.
     *
     * @return array The list of posts with the default post author set.
     */
    public function setPostAuthor(array $posts, array $listings, RFQuery $RFQuery): array
    {
        $userId = Settings::get_setting('epl_default_post_author', 1);
        foreach ($posts as $post) {
            $post->post_author = $userId;
        }
        return $posts;
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
        if (str_contains($metaKey, 'property_')) {
            return false;
        }
        return $check;
    }
    public function getMappingDir(): string
    {
        return '';
    }
}