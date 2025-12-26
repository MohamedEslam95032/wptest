<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets;

use Realtyna\MlsOnTheFly\Boot\App;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\Mapping;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\RFQuery;


class WPLIntegration implements IntegrationInterface
{
    /**
     * An array of meta keys that are allowed to be saved.
     *
     * @var array
     */
    public array $metaWhiteList = [];
    public string $name = 'wpl';
    public array $customPostTypes = [
        'wpl_property',
    ];
    public array $customTaxonomies = [
        'wpl_property_location2_name',
        'wpl_property_location3_name',
        'wpl_property_location4_name',
        'wpl_property_location5_name',
        'wpl_property_mls_id',
        'wpl_property_field_42',
        'wpl_property_zip_name',
        'wpl_property_location_text',
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

        add_filter('realtyna_mls_on_the_fly_rf_skip', function ($RFSkip, $paged, $postsPerPage) {
            if ($paged >= 1){
                $paged++;
            }
            return ($paged - 1) * $postsPerPage;
        }, 10, 3);


        // Register the custom taxonomies
        add_action('init', [$this, 'registerCustomTaxonomies']);

        return self::class;
    }

    /**
     * Registers custom taxonomies.
     */
    public function registerCustomTaxonomies()
    {
        foreach ($this->customTaxonomies as $taxonomy) {
            register_taxonomy(
                $taxonomy,
                'wpl_property', // Change this to your custom post type if needed
                [
                    'label' => __( ucfirst(str_replace('_', ' ', $taxonomy)), 'textdomain' ),
                    'rewrite' => [ 'slug' => $taxonomy ],
                    'hierarchical' => true,
                    'show_ui' => false,
                    'show_admin_column' => false,
                    'query_var' => true,
                    'show_in_rest' => false,
                    'public' => false, // Set to false to make it non-public
                    'publicly_queryable' => false, // Prevents queries from being publicly accessible
                ]
            );
        }
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
        return $check;
    }

    public function getMappingDir(): string
    {
        return '';
    }
}
?>
