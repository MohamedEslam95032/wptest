<?php

namespace Realtyna\MlsOnTheFly\Components\CustomPostType\Models;
class Price
{
    /**
     * Return given amount in a configured format.
     * 
     * @author Melvin <melvin.g@realtyna.com>
     * 
     * @param string $amount
     * 
     * @return string
     */
    public static function formatAmount($amount)
    {
        // Return if amount is empty or not a number.
        if (empty($amount) || is_nan($amount)) {
            return '';
        }

        $currencySign       = self::getCurrencySign();
        $decimals           = intval(carbon_get_theme_option('x_wpl_decimals') ? carbon_get_theme_option('x_wpl_decimals') : 0);
        $decimalPoint       = carbon_get_theme_option('x_wpl_dec_point') ? carbon_get_theme_option('x_wpl_dec_point') : '.';
        $thousandsSeparator = carbon_get_theme_option('theme_thousands_sep', ',');
        $currencyPosition   = carbon_get_theme_option('x_wpl_thousands_sep') ? carbon_get_theme_option('x_wpl_thousands_sep') : 'before';
        $formattedPrice     = number_format($amount, $decimals, $decimalPoint, $thousandsSeparator);
        $formattedPrice     = apply_filters('x_wpl_property_price', $formattedPrice, $amount);

        return ('after' == $currencyPosition) ? $formattedPrice . $currencySign : $currencySign . $formattedPrice;
    }

    /**
     * Get Currency
     * 
     * @author Melvin <melvin.g@realtyna.com>
     * 
     * @return string
     */
    public static function getCurrencySign()
    {
        return apply_filters('x_wpl_currency_sign', carbon_get_theme_option('x_wpl_currency_sign') ? carbon_get_theme_option('x_wpl_currency_sign') : esc_html__('$', REALTYNA_X_WPL_SLUG));
    }

    /**
     * Returns text to display in case of empty price
     * 
     * @author Melvin <melvin.g@realtyna.com>
     * 
     * @return string
     */
    public static function noPriceText()
    {
        return apply_filters('x_wpl_no_price_text', carbon_get_theme_option('x_wpl_no_price_text') ? carbon_get_theme_option('x_wpl_no_price_text') : '');
    }

    /**
     * Returns property current and old price if not empty otherwise current price.
     * 
     * @author Melvin <melvin.g@realtyna.com>
     * 
     * @param string $currentPrice
     * 
     * @param string $oldPrice Old
     * 
     * @return string
     */
    public static function propertySalePrice($currentPrice, $oldPrice)
    {
        if (!empty($currentPrice) && !empty($oldPrice)) {
            return sprintf('<span class="x-wpl-property-price-wrapper"><ins class="x-wpl-property-current-price">%s</ins> <del class="x-wpl-property-old-price">%s</del></span>', $currentPrice, $oldPrice);
        }

        return $currentPrice;
    }

    /**
     * Returns property old price in configured format.
     * 
     * @author Melvin <melvin.g@realtyna.com>
     * 
     * @param int $propertyId
     * 
     * @return string
     */
    public static function getPropertyOldPrice($propertyId = 0)
    {
        // Set property ID if it's not given.
        if (empty($propertyId)) {
            $propertyId = get_the_ID();
        }

        // Get property old price.
        $amount = floatval(carbon_get_post_meta($propertyId, 'x_wpl_property_old_price'));

        return self::formatAmount($amount);
    }

    /**
     * Returns property price in configured format.
     * 
     * @author Melvin <melvin.g@realtyna.com>
     * 
     * @param int $propertyId Property ID to get price for.
     * 
     * @param bool $showOldPrice
     * 
     * @param bool $markup Property price with html
     * 
     * @return string
     */
    public static function getPropertyPrice($propertyId = 0, $showOldPrice = false, $markup = false)
    {
        // Set property ID if it's not given.
        if (empty($propertyId)) {
            $propertyId = get_the_ID();
        }

        // Get property price.
        $amount = floatval(carbon_get_post_meta($propertyId, 'x_wpl_property_price'));

        // Return no price text if price is empty.
        if (empty($amount) || is_nan($amount)) {
            return self::noPriceText();
        }

        $price = self::formatAmount($amount);

        /**
         * Filter condition to show property current and old price for all or specific properties.
         *
         * @param bool $showOldPrice Current boolean. False by default.
         * 
         * @param int $propertyId Current property id.
         */
        if (true === apply_filters('x_wpl_show_properties_old_price', $showOldPrice, $propertyId)) {
            $price = self::propertySalePrice($price, self::getPropertyOldPrice());
        }

        // Get price prefix & postfix.
        $pricePrefix  = carbon_get_post_meta($propertyId, 'x_wpl_property_price_prefix', true);
        $pricePostfix = carbon_get_post_meta($propertyId, 'x_wpl_property_price_postfix', true);

        if (true == $markup) {
            $priceMarkup = '';

            if (!empty($pricePrefix)) {
                $priceMarkup .= '<span class="x-wpl-price-prefix">' . $pricePrefix . '</span> ';
            }

            $priceMarkup .= '<span class="x-wpl-price-display">' . $price . '</span>';

            if (!empty($pricePostfix)) {
                $priceMarkup .= ' <span class="x-wpl-price-slash">/</span> <span class="x-wpl-price-postfix">' . $pricePostfix . '</span>';
            }

            return $priceMarkup;
        }


        return $pricePrefix . ' ' . $price . ' ' . $pricePostfix;
    }

    /**
     * Output property price.
     * 
     * @author Melvin <melvin.g@realtyna.com>
     * 
     * @param int $propertyId
     * 
     * @param bool $showOldPrice
     * 
     * @param bool markup
     * 
     * @return void
     */
    public static function propertyPrice($propertyId = 0, $showOldPrice = false, $markup = false)
    {
        echo self::getPropertyPrice($propertyId, $showOldPrice, $markup);
    }
}
