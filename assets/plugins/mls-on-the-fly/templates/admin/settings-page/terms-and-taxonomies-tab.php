<?php

use Realtyna\Core\Utilities\SettingsField;

if ( !defined('ABSPATH') ) exit;

$terms_mode = $settings['terms_mode'] ?? '';

SettingsField::select(array(
    'label' => __( 'Import Mode', 'realtyna-mls-on-the-fly' ),
    'options' => array(
        'first-200' => __( 'Only import first 200 sorted by count', 'realtyna-mls-on-the-fly' ),
        'all' => __( 'Import all', 'realtyna-mls-on-the-fly' ),
    ),
    'value' => $terms_mode,
    'parent_name' => 'mls-on-the-fly-settings',
    'child_name' => 'terms_mode',
    'id' => 'mls-on-the-fly-settings-termsmode',
));

