<?php


use Realtyna\Core\Utilities\SettingsField;

if ( !defined('ABSPATH') ) exit;

$checked = isset($settings['show_raw_data']) && $settings['show_raw_data'];
SettingsField::checkbox(array(
    'parent_name' => 'mls-on-the-fly-settings',
    'child_name' => 'show_raw_data',
    'id' => 'mls-on-the-fly-settings-show-raw-data',
    'label' => __( 'Show Raw Data', 'realtyna-mls-on-the-fly' ),
    'value' => 'yes',
    'checked' => $checked,
));
