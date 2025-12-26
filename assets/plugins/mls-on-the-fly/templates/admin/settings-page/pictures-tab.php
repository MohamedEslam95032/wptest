<?php

use Realtyna\Core\Utilities\SettingsField;

if (!defined('ABSPATH')) {
    exit;
}

SettingsField::select(array(
    'parent_name' => 'mls-on-the-fly-settings',
    'child_name' => 'pictures_size',
    'id' => 'mls-on-the-fly-settings-default-integration',
    'label' => __('Default Integration', 'realtyna-mls-on-the-fly'),
    'options' => [
        'full-size' => __('Higher quality', 'realtyna-mls-on-the-fly'),
        'thumbnail' => __('Thumbnail', 'realtyna-mls-on-the-fly'),
    ],
    'description' => 'Higher quality - Heavy Load , Thumbnail - Fast Load',
    'value' => $settings['pictures_size'] ?? 'thumbnail',
));

do_action('mls_on_the_fly_settings_pictures_tab_after');
