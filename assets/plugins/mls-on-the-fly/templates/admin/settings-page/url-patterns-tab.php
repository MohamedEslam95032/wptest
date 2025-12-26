<?php


use Realtyna\Core\Utilities\SettingsField;

SettingsField::input(array(
    'parent_name' => 'mls-on-the-fly-settings',
    'child_name' => 'url_patterns',
    'id' => 'mls-on-the-fly-settings-url-patterns',
    'label' => __( 'URL Pattern', 'realtyna-mls-on-the-fly' ),
    'class' => 'bold',
    'type'  => 'text',
    'description'  => 'Listing Key is automatically included at the start and it cannot be removed.',
    'value' => $settings['url_patterns'] ?? '',
));