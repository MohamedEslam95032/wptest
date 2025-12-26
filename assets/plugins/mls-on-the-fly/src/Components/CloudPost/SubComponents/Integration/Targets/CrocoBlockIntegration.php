<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets;

use Realtyna\MlsOnTheFly\Boot\App;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\Mapping;

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
 * @author Cyrus.a
 *         cyrus.a@realtyna.com
 */
class CrocoBlockIntegration implements IntegrationInterface
{
    /**
     * An array of meta keys that are allowed to be saved.
     *
     * @var array
     */
    public array $metaWhiteList = [];
    public string $name = 'crocoblock';
    public array $customPostTypes = [
        'properties'
    ];
    public array $customTaxonomies = [
        'property-type',
        'property-city',
        'property-state',
        'property-country',
        'property-label',
        'property-status',
        'property-area',
        'property-feature'
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
            $mappingFile = $mapping->mappingConfig->getMappings('query');
            $this->customTaxonomies = array_keys($mappingFile['taxonomies']);
        }, 99999);

        return self::class;
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

        return $check;
    }

    public function getMappingDir(): string
    {
        return '';
    }

}
