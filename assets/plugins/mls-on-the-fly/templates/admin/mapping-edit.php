<?php

use Realtyna\MlsOnTheFly\Boot\App;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\Mapping;

if (!defined("ABSPATH")) {
    exit();
}

try {
    $activeIntegration = App::get(IntegrationInterface::class);
    $type = $_GET["type"] ?? "post";

    /** @var Mapping $mapping */

    $mapping = App::get(Mapping::class);

    /** @var \Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\src\MappingConfig $mappingConfig */

    $mappingConfig = $mapping->mappingConfig;
    $mapping_data = $mappingConfig->getMappings($type);
} catch (ReflectionException $e) {
    return "something went wrong";
}

$mapping_types = [
    "post" => __("Post", "realtyna-mls-on-the-fly"),
    "meta" => __("Meta", "realtyna-mls-on-the-fly"),
    "query" => __("Query", "realtyna-mls-on-the-fly"),
];
?>
<form id="choose-mapping-type-form" action="<?php echo admin_url(
    "admin.php"
); ?>" method="GET">
    <input type="hidden" name="page" value="mls-on-the-fly-mapping">
    <div class="form-group">
        <label for="mapping-type"><?php esc_html_e(
            "Choose Mapping Type:",
            "realtyna-mls-on-the-fly"
        ); ?></label>
        <div id="mapping-type-wrapper">
            <?php foreach ($mapping_types as $mt_key => $mt_title): ?>
                <label><input type="radio" name="type" value="<?php echo esc_attr(
                    $mt_key
                ); ?>" <?php echo $type === $mt_key
    ? "checked"
    : ""; ?>><?php echo esc_html($mt_title); ?></label>
            <?php endforeach; ?>
        </div>
    </div>
</form>

<!-- Load mapping data -->

<div class="mls-otf-utility-buttons-container">
    <!-- Export Mapping button to download the mapping data as a json file -->
    <button id="export-mapping-button">
        <span class="dashicons dashicons-upload"></span>
        <?php esc_html_e(" Export Mappings", "realtyna-mls-on-the-fly"); ?>
    </button>

    <!-- Import Mapping button to download the mapping data as a json file -->
    <button id="import-mapping-button">
        <span class="dashicons dashicons-download"></span>
        <?php esc_html_e(" Import Mappings", "realtyna-mls-on-the-fly"); ?>
    </button>
    <!-- Reset Mapping button to reset the mapping data to default -->
    <button id="reset-mapping-button">
        <span class="dashicons dashicons-backup"></span>
        <?php esc_html_e("Reset To Default", "realtyna-mls-on-the-fly"); ?>
    </button>

</div>


<!-- Modal for importing mappings -->
<div id="import-mapping-modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e(
                    "Import Mappings",
                    "realtyna-mls-on-the-fly"
                ); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="import-mapping-form">
                    <input type="hidden" name="type" value="<?php echo $type; ?>">
                    <input type="hidden" id="application" name="application" value="<?php echo $activeIntegration->name; ?>">
                    <div class="form-group">
                        <label for="mapping_file_upload"><?php esc_html_e(
                            "Please select a Json file to import:",
                            "realtyna-mls-on-the-fly"
                        ); ?></label>
                        <input type="file" id="mapping_file_upload" name="mapping_file_upload" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="close-mapping-button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e(
                    "Close",
                    "realtyna-mls-on-the-fly"
                ); ?></button>
                <button type="button" id="import-mappings-button" class="btn btn-primary"><?php esc_html_e(
                    "Import Mappings",
                    "realtyna-mls-on-the-fly"
                ); ?></button>
            </div>
        </div>
    </div>
</div>
<?php if ($type === "query") {
    include REALTYNA_MLS_ON_THE_FLY_TEMPLATES_PATH . "/admin/query-edit.php";
} else {
     ?>

<!-- Add Mapping button to open the modal -->
<button id="add-mapping-button"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e(
    " Add Mapping",
    "realtyna-mls-on-the-fly"
); ?></button>

<!-- Modal for editing or adding a mapping -->
<div id="mapping-modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e(
                    "Edit Mapping",
                    "realtyna-mls-on-the-fly"
                ); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="mapping-form">
                    <input type="hidden" name="type" value="<?php echo $type; ?>">
                    <input type="hidden" id="application" name="application" value="<?php echo $activeIntegration->name; ?>">
                    <div class="form-group">
                        <label for="field_name"><?php esc_html_e(
                            "Field Name",
                            "realtyna-mls-on-the-fly"
                        ); ?></label>
                        <input type="text" id="field_name" name="field_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="mapping"><?php esc_html_e(
                            "Mapping",
                            "realtyna-mls-on-the-fly"
                        ); ?></label>
                        <input type="text" id="mapping" name="mapping" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="default"><?php esc_html_e(
                            "Default",
                            "realtyna-mls-on-the-fly"
                        ); ?></label>
                        <input type="text" id="default" name="default" class="form-control">
                    </div>
                    <div class="form-group" id="add-search-replace-label">
                        <h5><?php esc_html_e(
                            "Search and Replace (Optional)",
                            "realtyna-mls-on-the-fly"
                        ); ?></h5>
                    </div>
                    <div id="search-replace-fields">
                        <!-- Search and Replace fields will be added here -->
                    </div>
                    <button id="add-search-replace" class="btn"><?php esc_html_e(
                        "Add Search & Replace",
                        "realtyna-mls-on-the-fly"
                    ); ?></button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="close-mapping-button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e(
                    "Close",
                    "realtyna-mls-on-the-fly"
                ); ?></button>
                <button type="button" id="save-mapping-button" class="btn btn-primary"><?php esc_html_e(
                    "Save Mapping",
                    "realtyna-mls-on-the-fly"
                ); ?></button>
            </div>
        </div>
    </div>
</div>
<table id="mls-on-the-fly-mapping-list-table" class="table table-striped table-bordered">
    <thead class="thead-light">
        <tr>
            <th><input type="checkbox" class="mls-on-the-fly-check-all" /></th>
            <th class="field_name"><?php esc_html_e(
                "Field Name",
                "realtyna-mls-on-the-fly"
            ); ?></th>
            <th class="mapping"><?php esc_html_e(
                "Mapping",
                "realtyna-mls-on-the-fly"
            ); ?></th>
            <th class="default"><?php esc_html_e(
                "Default",
                "realtyna-mls-on-the-fly"
            ); ?></th>
            <th class="replaces"><?php esc_html_e(
                "Replaced",
                "realtyna-mls-on-the-fly"
            ); ?></th>
            <th class="actions"><button type="button" id="delete-all-checked-mappings"><?php
            echo mls_on_the_fly_kses_svg(mls_on_the_fly_get_svg("trash"));
            esc_html_e("Delete", "realtyna-mls-on-the-fly");
            ?></button></th>
        </tr>
    </thead>
    <tbody>
    <?php
    $i = 0;
    // Loop through $mapping_data and generate table rows
    foreach ($mapping_data as $field_name => $field_settings) {
        echo "<tr>";
        echo '<td class="mapping_input"><input type="checkbox" class="mls-on-the-fly-mapping-input-id" name="mls-on-the-fly-mapping-ids[' .
            esc_attr($field_name) .
            ']" value="' .
            esc_attr($field_name) .
            '" /><span class="row-number">' .
            esc_html(++$i) .
            "</span></td>";
        echo '<td class="field_name">' . $field_name . "</td>";
        echo '<td class="mapping">' .
            htmlspecialchars($field_settings["mapping"] ?? "") .
            "</td>";
        echo '<td class="default">' .
            ($field_settings["default"] ?? "") .
            "</td>";
        echo '<td class="replaces">' .
            (isset($field_settings["replaces"]) &&
            !empty($field_settings["replaces"])
                ? "Yes"
                : "No") .
            "</td>";
        echo '<td class="actions">';
        // Add edit and delete buttons using Bootstrap buttons
        echo '<a class="edit-button" data-type="' .
            $type .
            '" data-field="' .
            $field_name .
            '">' .
            mls_on_the_fly_kses_svg(mls_on_the_fly_get_svg("edit")) .
            "</a>";
        echo '<a class="delete-button" data-type="' .
            $type .
            '" data-field="' .
            $field_name .
            '">' .
            mls_on_the_fly_kses_svg(mls_on_the_fly_get_svg("trash")) .
            "</a>";
        echo "</td>";
        echo "</tr>";
    }

} ?>
    </tbody>
</table>
