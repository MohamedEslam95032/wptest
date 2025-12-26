<?php

use Realtyna\Core\Utilities\SettingsField;


SettingsField::select(array(
    'parent_name' => 'mls-on-the-fly-settings',
    'child_name' => 'featured_key',
    'id' => 'mls-on-the-fly-settings-featured-key',
    'label' => __('Featured Field', 'realtyna-mls-on-the-fly'),
    'options' => [
        'none' => __('None', 'realtyna-mls-on-the-fly'),
        'ListingId' => __('ListingId', 'realtyna-mls-on-the-fly'),
        'ListingKey' => __('ListingKey', 'realtyna-mls-on-the-fly'),
        'ListAgentMlsId' => __('ListAgentMlsId', 'realtyna-mls-on-the-fly'),
        'ListOfficeMlsId' => __('ListOfficeMlsId', 'realtyna-mls-on-the-fly'),
        'ListAgentKey' => __('ListAgentKey', 'realtyna-mls-on-the-fly'),
        'ListOfficeKey' => __('ListOfficeKey', 'realtyna-mls-on-the-fly'),
        'PostalCode' => __('PostalCode', 'realtyna-mls-on-the-fly'),
        'City' => __('City', 'realtyna-mls-on-the-fly'),
        'CountyOrParish' => __('CountyOrParish', 'realtyna-mls-on-the-fly'),
        'StandardStatus' => __('StandardStatus', 'realtyna-mls-on-the-fly'),
    ],
    'description' => 'Select the field you want to use to set featured properties',
    'value' => $settings['featured_key'] ?? 'none',
));




SettingsField::repeater_input([
    'parent_name' => 'mls-on-the-fly-settings',
    'child_name' => 'featured_values',
    'id' => 'realtyna_custom_fields',
    'label' => 'Featured Values',
    'description' => 'You can add multiple values for featured properties',
    'values' => $settings['featured_values'] ?? []
]);
