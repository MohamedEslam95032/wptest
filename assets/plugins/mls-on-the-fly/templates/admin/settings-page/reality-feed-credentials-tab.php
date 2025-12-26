<?php

use Realtyna\Core\Utilities\SettingsField;

if ( !defined('ABSPATH') ) exit;

SettingsField::input(array(
    'parent_name' => 'mls-on-the-fly-settings',
    'id' => 'mls-on-the-fly-settings-api-key',
    'child_name' => 'api_key',
    'label' => __( 'Realty Feed API Key', 'realtyna-mls-on-the-fly' ),
    'type'  => 'password',
    'value' => $settings['api_key'] ?? '',
));

SettingsField::input(array(
    'parent_name' => 'mls-on-the-fly-settings',
    'child_name' => 'client_id',
    'id' => 'mls-on-the-fly-settings-client-id',
    'label' => __( 'Realty Feed Client ID', 'realtyna-mls-on-the-fly' ),
    'type'  => 'text',
    'value' => $settings['client_id'] ?? '',
));

SettingsField::input(array(
    'parent_name' => 'mls-on-the-fly-settings',
    'child_name' => 'client_secret',
    'id' => 'mls-on-the-fly-settings-client-secret',
    'label' => __( 'Realty Feed Client Secret', 'realtyna-mls-on-the-fly' ),
    'type'  => 'password',
    'value' => $settings['client_secret'] ?? '',
));

SettingsField::input(array(
    'parent_name' => 'mls-on-the-fly-settings',
    'child_name' => 'rf_origin',
    'id' => 'mls-on-the-fly-settings-rf_origin',
    'label' => __( 'Realty Feed Origin', 'realtyna-mls-on-the-fly' ),
    'type'  => 'text',
    'value' => $settings['rf_origin'] ?? '',
));

SettingsField::input(array(
    'parent_name' => 'mls-on-the-fly-settings',
    'child_name' => 'rf_referer',
    'id' => 'mls-on-the-fly-settings-rf_referer',
    'label' => __( 'Realty Feed Referer', 'realtyna-mls-on-the-fly' ),
    'type'  => 'text',
    'value' => $settings['rf_referer'] ?? '',
));
