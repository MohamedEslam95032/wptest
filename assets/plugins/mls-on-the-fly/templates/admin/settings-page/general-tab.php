<?php


use Realtyna\Core\Utilities\SettingsField;
use Realtyna\MlsOnTheFly\Boot\App;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets\CrocoBlockIntegration;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets\EPLIntegration;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets\HouzezIntegration;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets\InspiryThemesIntegration;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets\PropertyDriveIntegration;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets\ToolsetIntegration;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets\WPLIntegration;

if (!defined('ABSPATH')) {
    exit;
}

SettingsField::input(array(
    'parent_name' => 'mls-on-the-fly-settings',
    'child_name' => 'cache_time',
    'id' => 'mls-on-the-fly-settings-cache-time',
    'label' => __('Cache Timeout', 'realtyna-mls-on-the-fly'),
    'type' => 'number',
    'value' => $settings['cache_time'] ?? '',
    'min' => 60,
));


$checked = isset($settings['self_custom_post_type']) && $settings['self_custom_post_type'];
SettingsField::checkbox(array(
    'parent_name' => 'mls-on-the-fly-settings',
    'child_name' => 'self_custom_post_type',
    'id' => 'mls-on-the-fly-settings-self-custom-post-type',
    'label' => __( 'Activate Property Custom Post Type', 'realtyna-mls-on-the-fly' ),
    'value' => 'yes',
    'checked' => $checked,
    'description' => ''
));

if (App::has(IntegrationInterface::class)) {
    $activeIntegration = App::get(IntegrationInterface::class);

    // Display a message about the active integration
    echo '<p><b>';
    echo __('The active integration detected is: ', 'realtyna-mls-on-the-fly') . $activeIntegration->name;
    echo '</b></p>';

    // Display a message to inform the user that they can override this option
    echo '<p>';
    echo __('If you want to override this option, you can use the select box below.', 'realtyna-mls-on-the-fly');
    echo '</p>';
}

$integrations = [
    'auto' => 'Auto Detect',
    WPLIntegration::class => 'WPL',
    EPLIntegration::class => 'EPL',
    HouzezIntegration::class => 'Houzez',
    ToolsetIntegration::class => 'Toolset',
    CrocoBlockIntegration::class => 'CrocoBlock',
    PropertyDriveIntegration::class => 'PropertyDrive',
    InspiryThemesIntegration::class => 'InspiryThemes',
];
if (isset($settings['default_integration'])) {
    $settings['default_integration'] = str_replace('\\\\', '\\', $settings['default_integration']);
}

SettingsField::select(array(
    'parent_name' => 'mls-on-the-fly-settings',
    'child_name' => 'default_integration',
    'id' => 'mls-on-the-fly-settings-default-integration',
    'label' => __('Default Integration', 'realtyna-mls-on-the-fly'),
    'options' => $integrations,
    'value' => $settings['default_integration'] ?? '',
));

