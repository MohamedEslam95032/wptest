<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF;

use Realtyna\MlsOnTheFly\Boot\Log;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\Exceptions\RequestFailedException;
use Realtyna\MlsOnTheFly\Components\CloudPost\Settings\Settings;

/**
 * Class RFLookup
 *
 * @package Realtyna\MlsOnTheFly\SDK\RF
 */
class RFLookup {

    public static $RF = null;

    /**
     * Return RF
     *
     * @return null|RF
     * @throws \Exception
     * @author Cyrus <cyrus.a@realtyna.com>
     *
     */
    public static function RF(): null|RF {
        if ( ! is_null( self::$RF ) ) {
            return self::$RF;
        }

        // Retrieve RF credentials from WordPress options
        $RFClientId = Settings::get_setting('client_id', false);
        $RFClientSecret = Settings::get_setting('client_secret', false);
        $RFApiKey = Settings::get_setting('api_key', false);

        if ($RFApiKey && $RFClientId && $RFClientSecret) {
            try {
                self::$RF = new RF($RFClientId, $RFClientSecret, $RFApiKey);
                Log::info('RF client initialized');
                return self::$RF;
            } catch (RequestFailedException $e) {
                Log::error($e->getMessage());
            }
        }

        return null;
    }

    /**
     * Return Lookup items
     *
     * @param array $lookup_names
     *
     * @author Cyrus <cyrus.a@realtyna.com>
     *
     * @return array
     */
    public static function getLookupItems( array $lookup_names ) {

        $RFQuery = new RFQuery();
        $RFQuery->set_entity('v1/Lookup');
        $RFQuery->set_top(200);
        $RFQuery->set_select(['LookupValue']);
        $RFQuery->add_filter(
            'Where',
            'LookupName',
            'in',
            implode( ",", $lookup_names )
        );
        $RFResponse = self::RF()->get( $RFQuery );
        $items = array();
        foreach( $RFResponse->items as $item ) {
            $value = $item->LookupValue;
            $items[ $value ] = $value;
        }

        return $items;
    }
}
