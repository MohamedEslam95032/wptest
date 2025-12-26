<?php


use Realtyna\MLSOnTheFly\Components\CloudPost\Settings\SettingsField;

if ( !defined('ABSPATH') ) exit;

//TODO: remove next version
$old_filters = get_option('realtyna_mls_on_the_fly_global_filters_old', 'empty');

wp_enqueue_script( 'realtyna-mls-on-the-fly-global-filters-old-js' );
include 'global-filters-old.php';

return;
//
//
//$default_global_filters = array(
//
//);
//$global_filters = get_option('realtyna_mls_on_the_fly_global_filters', $default_global_filters);
//$global_filters_json = get_option('realtyna_mls_on_the_fly_global_filters_json', json_encode( $default_global_filters ) );
//
//$fields = array(
//    //properties
//    'ListingKey',
//    'ListingId',
//    'PropertyType',
//    'PropertySubType',
//    'StandardStatus',
//    'OriginatingSystemName',
//    'City',
//    'StateOrProvince',
//    'PostalCode',
//    'UnparsedAddress',
//    'YearBuilt',
//    'ListAgentFullName',
//    'ListOfficeName',
//    'ListAgentMlSID'
//);
//$fields = array_combine( $fields, $fields );
//
//$OData_conditions = array(
//    'eq' => __( 'Equal To', 'realtyna-mls-on-the-fly' ),
//    'ne' => __( 'Not Equal To', 'realtyna-mls-on-the-fly' ),
//    'gt' => __( 'Greater Than', 'realtyna-mls-on-the-fly' ),
//    'ge' => __( 'Greater Than or Equal To', 'realtyna-mls-on-the-fly' ),
//    'lt' => __( 'Less Than', 'realtyna-mls-on-the-fly' ),
//    'le' => __( 'Less Than or Equal To', 'realtyna-mls-on-the-fly' ),
//    'and' => __( 'Both conditions are true', 'realtyna-mls-on-the-fly' ),
//    'or' => __( 'Either condition is true', 'realtyna-mls-on-the-fly' ),
//    'in' => __( 'Value is in List', 'realtyna-mls-on-the-fly' ),
//);
//
//?>
<!--<div id="mls-on-the-fly-rendered-filters">-->
<!--    <h3>--><?php //esc_html_e( 'Rendered Filters', 'realtyna-mls-on-the-fly' ); ?><!--</h3>-->
<!--    <form action="--><?php //echo admin_url('admin-post.php'); ?><!--" method="post">-->
<!--        --><?php //wp_nonce_field( 'realtyna-mls-on-the-fly-nonce-global-filters-settings', 'mls_on_the_fly_global_filters_settings_nonce' ); ?>
<!--        <input type="hidden" name="action" value="mls_on_the_fly_update_global_filters_settings" />-->
<!---->
<!--        <div class="mls-on-the-fly-rendered-filters-wrapper"></div>-->
<!--        <textarea id="mls-on-the-fly-global-filters-input" name="mls-on-the-fly-global-filters-input" class="form-control" rows="5" disabled></textarea>-->
<!--        <textarea id="mls-on-the-fly-global-filters-input-value" name="mls-on-the-fly-global-filters-input" class="form-control" rows="5" style="display: none;"></textarea>-->
<!--        <textarea id="mls-on-the-fly-global-filters-input-json" name="mls-on-the-fly-global-filters-input-json" class="form-control" rows="5" style="display: none;">--><?php //echo $global_filters_json ?><!--</textarea>-->
<!--        <button id="mls-on-the-fly-update-settings" name="mls-on-the-fly-update-settings" class="mls-on-the-fly-btn mls-on-the-fly-btn-primary">--><?php //esc_html_e( 'Save Settings', 'realtyna-mls-on-the-fly' ); ?><!--</button>-->
<!--    </form>-->
<!--</div>-->
<!---->
<!--<div id="mls-on-the-fly-add-condition-modal" class="modal mls-on-the-fly-modal" tabindex="-1" role="dialog">-->
<!--    <div class="modal-dialog" role="document">-->
<!--        <div class="modal-content">-->
<!--            <form id="mls-on-the-fly-add-filter-modal-form" method="post">-->
<!--                <div class="modal-header">-->
<!--                    <h5 class="modal-title">--><?php //esc_html_e( 'Add Filter Set', 'realtyna-mls-on-the-fly' ); ?><!--</h5>-->
<!--                    <button type="button" class="mls-on-the-fly-close-modal-button" data-dismiss="modal" aria-label="Close">-->
<!--                        <span aria-hidden="true">&times;</span>-->
<!--                    </button>-->
<!--                </div>-->
<!--                <div class="modal-body">-->
<!--                    <div class="mls-on-the-fly-add-filter">-->
<!--                        --><?php
//                            SettingsField::select(array(
//                                'parent_name' => 'mls-on-the-fly-global-filters',
//                                'child_name' => 'field',
//                                'label' => __( 'RF Field', 'realtyna-mls-on-the-fly' ),
//                                'options' => $fields,
//                                'value' => '',
//                            ));
//
//                            SettingsField::select(array(
//                                'parent_name' => 'mls-on-the-fly-global-filters',
//                                'child_name' => 'odata_condition',
//                                'label' => __( 'OData Condition', 'realtyna-mls-on-the-fly' ),
//                                'options' => $OData_conditions,
//                                'value' => '',
//                            ));
//
//                            SettingsField::input(array(
//                                'parent_name' => 'mls-on-the-fly-global-filters',
//                                'child_name' => 'compared_value',
//                                'label' => __( 'Compared Value', 'realtyna-mls-on-the-fly' ),
//                                'value' => '',
//                            ));
//                        ?>
<!--                        <input type="hidden" name="mls-on-the-fly-global-filters[group]" value="" />-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="modal-footer">-->
<!--                    <button type="button" class="mls-on-the-fly-close-modal-button" class="btn btn-secondary" data-dismiss="modal">--><?php //esc_html_e( 'Close', 'realtyna-mls-on-the-fly' ); ?><!--</button>-->
<!--                    <button type="submit" id="mls-on-the-fly-add-filter" name="add_filter" class="mls-on-the-fly-btn mls-on-the-fly-btn-primary">--><?php //esc_html_e( 'Add Filter Set', 'realtyna-mls-on-the-fly' ); ?><!--</button>-->
<!--                </div>-->
<!--            </form>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->