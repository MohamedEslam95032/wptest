<?php
$key_mappings = isset($mapping_data["key_mappings"])
    ? $mapping_data["key_mappings"]
    : [];
$post_metas = isset($mapping_data["post_metas"])
    ? $mapping_data["post_metas"]
    : [];
$taxonomies = isset($mapping_data["taxonomies"])
    ? $mapping_data["taxonomies"]
    : [];
?>
<!--"key_mappings":-->
<table id="mls-on-the-fly-mapping-list-table" class="table table-striped table-bordered">
    <thead class="thead-light">
    <tr>
        <th>Key Mappings</th>
        <th class="field_name"><?php esc_html_e(
            "Field Name",
            "realtyna-mls-on-the-fly"
        ); ?></th>
        <th class="mapping"><?php esc_html_e(
            "Mapping",
            "realtyna-mls-on-the-fly"
        ); ?></th>
        <th class="actions"><button type="button" id="add-key-mapping-button" class="btn btn-primary"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e(
            " Add",
            "realtyna-mls-on-the-fly"
        ); ?></button>
            <button type="button" id="query-delete-all-checked-key_mappings-mappings" class="query-delete-all-checked-mappings" data-query="key_mappings"><?php
            echo mls_on_the_fly_kses_svg(mls_on_the_fly_get_svg("trash"));
            esc_html_e("Delete", "realtyna-mls-on-the-fly");
            ?></button></th>
    </tr>
    </thead>
    <tbody>

<?php
$i = 0;
foreach ($key_mappings as $field_name => $field_settings) {
    echo "<tr>";
    echo '<td class="mapping_input"><input type="checkbox" class="mls-on-the-fly-key-mapping-input-id" name="mls-on-the-fly-key-mapping-ids[' .
        esc_attr($field_name) .
        ']" value="' .
        esc_attr($field_name) .
        '" /><span class="row-number">' .
        esc_html(++$i) .
        "</span></td>";
    echo '<td class="field_name">' . $field_name . "</td>";
    echo '<td class="mapping">' .
        (is_array($field_settings) ? "" : $field_settings) .
        "</td>";
    echo '<td class="actions">';
    // Add edit and delete buttons using Bootstrap buttons
    echo '<a class="query-edit-button" data-query="key_mappings" data-type="' .
        $type .
        '" data-field="' .
        $field_name .
        '">' .
        mls_on_the_fly_kses_svg(mls_on_the_fly_get_svg("edit")) .
        "</a>";
    echo '<a class="query-delete-button" data-query="key_mappings" data-type="' .
        $type .
        '" data-field="' .
        $field_name .
        '">' .
        mls_on_the_fly_kses_svg(mls_on_the_fly_get_svg("trash")) .
        "</a>";
    echo "</td>";
    echo "</tr>";
}
?>

    </tbody>
</table><br>

<!--"post_metas":-->
<table id="mls-on-the-fly-mapping-list-table" class="table table-striped table-bordered">
    <thead class="thead-light">
    <tr>
        <th>Post Metas</th>
        <th class="field_name"><?php esc_html_e(
            "Field Name",
            "realtyna-mls-on-the-fly"
        ); ?></th>
        <th class="mapping"><?php esc_html_e(
            "Method",
            "realtyna-mls-on-the-fly"
        ); ?></th>
        <th class="mapping"><?php esc_html_e(
            "RF Field",
            "realtyna-mls-on-the-fly"
        ); ?></th>
        <th class="actions"><button type="button" id="add-post-metas-mapping-button" class="btn btn-primary"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e(
            " Add",
            "realtyna-mls-on-the-fly"
        ); ?></button>
            <button type="button" id="query-delete-all-checked-post_metas-mappings" class="query-delete-all-checked-mappings" data-query="post_metas"><?php
            echo mls_on_the_fly_kses_svg(mls_on_the_fly_get_svg("trash"));
            esc_html_e("Delete", "realtyna-mls-on-the-fly");
            ?></button></th>
    </tr>
    </thead>
    <tbody>
<?php
$i = 0;
foreach ($post_metas as $field_name => $field_settings) {
    echo "<tr>";
    echo '<td class="mapping_input"><input type="checkbox" data-query="post_metas" class="mls-on-the-fly-post-metas-mapping-input-id" name="mls-on-the-fly-post-metas-mapping-ids[' .
        esc_attr($field_name) .
        ']" value="' .
        esc_attr($field_name) .
        '" /><span class="row-number">' .
        esc_html(++$i) .
        "</span></td>";
    echo '<td class="field_name">' . $field_name . "</td>";
    echo '<td class="mapping">' . ($field_settings["method"] ?? "") . "</td>";
    echo '<td class="default">' . ($field_settings["rf_field"] ?? "") . "</td>";
    echo '<td class="actions">';
    // Add edit and delete buttons using Bootstrap buttons
    echo '<a class="query-edit-button" data-query="post_metas" data-type="' .
        $type .
        '" data-field="' .
        $field_name .
        '">' .
        mls_on_the_fly_kses_svg(mls_on_the_fly_get_svg("edit")) .
        "</a>";
    echo '<a class="query-delete-button" data-query="post_metas" data-type="' .
        $type .
        '" data-field="' .
        $field_name .
        '">' .
        mls_on_the_fly_kses_svg(mls_on_the_fly_get_svg("trash")) .
        "</a>";
    echo "</td>";
    echo "</tr>";
}
?>
    </tbody>
</table><br>
<!--"taxonomies":-->
<table id="mls-on-the-fly-mapping-list-table" class="table table-striped table-bordered">
    <thead class="thead-light">
    <tr>
        <th>Taxonomies</th>
        <th class="field_name"><?php esc_html_e(
            "Field Name",
            "realtyna-mls-on-the-fly"
        ); ?></th>
        <th class="mapping"><?php esc_html_e(
            "Mapping",
            "realtyna-mls-on-the-fly"
        ); ?></th>
        <th class="mapping"><?php esc_html_e(
            "Child",
            "realtyna-mls-on-the-fly"
        ); ?></th>
        <th class="actions"><button type="button" id="add-taxonomy-mapping-button" class="btn btn-primary"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e(
            " Add",
            "realtyna-mls-on-the-fly"
        ); ?></button>
            <button type="button" id="query-delete-all-checked-taxonomies-mappings" class="query-delete-all-checked-mappings" data-query="taxonomies"><?php
            echo mls_on_the_fly_kses_svg(mls_on_the_fly_get_svg("trash"));
            esc_html_e("Delete", "realtyna-mls-on-the-fly");
            ?></button></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $i = 0;
    foreach ($taxonomies as $field_name => $field_settings) {
        echo "<tr>";
        echo '<td class="mapping_input"><input type="checkbox" data-query="taxonomies" class="mls-on-the-fly-taxonomies-mapping-input-id" name="mls-on-the-fly-taxonomies-mapping-ids[' .
            esc_attr($field_name) .
            ']" value="' .
            esc_attr($field_name) .
            '" /><span class="row-number">' .
            esc_html(++$i) .
            "</span></td>";
        echo '<td class="field_name">' . $field_name . "</td>";
        echo '<td class="mapping">' .
            (is_array($field_settings["mapping"])
                ? implode(", ", $field_settings["mapping"])
                : $field_settings["mapping"]) .
            "</td>";
        echo '<td class="default">' .
            ($field_settings["child"] ?? "") .
            "</td>";
        echo '<td class="actions">';
        // Add edit and delete buttons using Bootstrap buttons
        echo '<a class="query-edit-button" data-query="taxonomies" data-type="' .
            $type .
            '" data-field="' .
            $field_name .
            '">' .
            mls_on_the_fly_kses_svg(mls_on_the_fly_get_svg("edit")) .
            "</a>";
        echo '<a class="query-delete-button" data-query="taxonomies" data-type="' .
            $type .
            '" data-field="' .
            $field_name .
            '">' .
            mls_on_the_fly_kses_svg(mls_on_the_fly_get_svg("trash")) .
            "</a>";
        echo "</td>";
        echo "</tr>";
    }
    ?>
    </tbody>
</table>

<!--add key mapping modal-->

<div id="key-mapping-modal" class="modal" tabindex="-1" role="dialog">
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
                <form id="key_mappings-mapping-form">
                    <input type="hidden" name="type" value="<?php echo $type; ?>">
                    <input type="hidden" id="application" name="application" value="<?php echo $activeIntegration->name; ?>">
                    <div class="form-group">
                        <label for="field_name"><?php esc_html_e(
                            "Field Name",
                            "realtyna-mls-on-the-fly"
                        ); ?></label>
                        <input type="text" id="field_name_key_mappings" name="field_name_key_mappings" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="mapping"><?php esc_html_e(
                            "Mapping",
                            "realtyna-mls-on-the-fly"
                        ); ?></label>
                        <input type="text" id="mapping_key_mappings" name="mapping_key_mappings" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="close-mapping-button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e(
                    "Close",
                    "realtyna-mls-on-the-fly"
                ); ?></button>
                <button type="button" id="query-key_mappings-save-mapping-button" data-query="key_mappings" class="btn btn-primary"><?php esc_html_e(
                    "Save Mapping",
                    "realtyna-mls-on-the-fly"
                ); ?></button>
            </div>
        </div>
    </div>
</div>

<!--add post metas mapping modal-->

<div id="post-metas-mapping-modal" class="modal" tabindex="-1" role="dialog">
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
                <form id="post_metas-mapping-form">
                    <input type="hidden" name="type" value="<?php echo $type; ?>">
                    <input type="hidden" id="application" name="application" value="<?php echo $activeIntegration->name; ?>">
                    <div class="form-group">
                        <label for="field_name"><?php esc_html_e(
                            "Field Name",
                            "realtyna-mls-on-the-fly"
                        ); ?></label>
                        <input type="text" id="field_name_post_metas" name="field_name_post_metas" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="mapping"><?php esc_html_e(
                            "Mapping",
                            "realtyna-mls-on-the-fly"
                        ); ?></label>
                        <input type="text" id="mapping_post_metas" name="mapping_post_metas" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="method"><?php esc_html_e(
                            "Method",
                            "realtyna-mls-on-the-fly"
                        ); ?></label>
                        <input type="text" id="method_post_metas" name="method_post_metas" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="close-mapping-button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e(
                    "Close",
                    "realtyna-mls-on-the-fly"
                ); ?></button>
                <button type="button" id="query-post_metas-save-mapping-button" data-query="post_metas" class="btn btn-primary"><?php esc_html_e(
                    "Save Mapping",
                    "realtyna-mls-on-the-fly"
                ); ?></button>
            </div>
        </div>
    </div>
</div>

<!--add taxonomy mapping modal-->

<div id="taxonomy-mapping-modal" class="modal" tabindex="-1" role="dialog">
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
                <form id="taxonomies-mapping-form">
                    <input type="hidden" name="type" value="<?php echo $type; ?>">
                    <input type="hidden" id="application" name="application" value="<?php echo $activeIntegration->name; ?>">
                    <div class="form-group">
                        <label for="field_name"><?php esc_html_e(
                            "Field Name",
                            "realtyna-mls-on-the-fly"
                        ); ?></label>
                        <input type="text" id="field_name_taxonomies" name="field_name_taxonomies" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="mapping"><?php esc_html_e(
                            "Mapping",
                            "realtyna-mls-on-the-fly"
                        ); ?></label>
                        <input type="text" id="mapping_taxonomies" name="mapping_taxonomies" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="child"><?php esc_html_e(
                            "Child",
                            "realtyna-mls-on-the-fly"
                        ); ?></label>
                        <input type="text" id="child_taxonomies" name="child_taxonomies" class="form-control">
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
                <button type="button" id="query-taxonomies-save-mapping-button" data-query="taxonomies" class="btn btn-primary"><?php esc_html_e(
                    "Save Mapping",
                    "realtyna-mls-on-the-fly"
                ); ?></button>
            </div>
        </div>
    </div>
</div>
