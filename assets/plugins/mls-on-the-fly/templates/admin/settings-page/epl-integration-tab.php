<?php

use Realtyna\Core\Utilities\SettingsField;

$users = [];
foreach (get_users() as $user) {
    $users[$user->ID] = $user->display_name;
}

$contacts = [];
SettingsField::select(array(
    'parent_name' => 'mls-on-the-fly-settings',
    'child_name' => 'mls_on_the_fly_epl_default_post_author',
    'label' => __( 'Default Post author', 'realtyna-mls-on-the-fly' ),
    'options' => $users,
    'value' => $settings['mls_on_the_fly_epl_default_post_author'] ?? '',
));

SettingsField::select(array(
    'parent_name' => 'mls-on-the-fly-settings',
    'child_name' => 'mls_on_the_fly_epl_default_property_contact_id',
    'label' => __( 'Default Contact ID', 'realtyna-mls-on-the-fly' ),
    'options' => $contacts,
    'value' => $settings['mls_on_the_fly_epl_default_property_contact_id'] ?? '',
));