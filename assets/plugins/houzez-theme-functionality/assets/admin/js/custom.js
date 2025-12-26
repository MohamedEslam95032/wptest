(function ($) {
    'use strict';

    var ajaxurl = Houzez_admin_vars.ajax_url;
    var processing_text = Houzez_admin_vars.processing_text;
    var delete_btn_text = Houzez_admin_vars.delete_btn_text;
    var confirm_btn_text = Houzez_admin_vars.confirm_btn_text;
    var cancel_btn_text = Houzez_admin_vars.cancel_btn_text;
    var map_fields_text = Houzez_admin_vars.map_fields_text;
    var error_import_text = Houzez_admin_vars.error_import;
    var select_text = Houzez_admin_vars.select_text;
    var import_text = Houzez_admin_vars.import_text;

    // Import progress tracking variables
    var importInProgress = false;
    var currentBatch = 0;
    var totalRows = 0;

    $(function () {
        $('.houzez-clone').cloneya();

        $('.houzez-fbuilder-js-on-change').change(function () {
            var field_type = $(this).val();
            $('.houzez-clone').cloneya();

            if (field_type == 'select' || field_type == 'multiselect') {
                // Get the field ID if we're editing an existing field
                var field_id = $('input[name="hz_fbuilder[id]"]').val() || '';

                $.post(
                    ajaxurl,
                    {
                        action: 'houzez_load_select_options',
                        type: field_type,
                        field_id: field_id,
                    },
                    function (response) {
                        $('.houzez_select_options_loader_js').html(response);
                        $('.houzez-clone').cloneya();
                    }
                );
            } else if (field_type == 'checkbox_list' || field_type == 'radio') {
                $('.houzez_multi_line_js').show();
                $('.houzez_select_options_loader_js').html('');
            } else {
                $('.houzez_select_options_loader_js').html('');
                $('.houzez_multi_line_js').hide();
            }
        });

        $(window).on('load', function () {
            var current_option = $('.houzez-fbuilder-js-on-change').attr(
                'value'
            );

            if (
                current_option == 'checkbox_list' ||
                current_option == 'radio'
            ) {
                $('.houzez_multi_line_js').show();
            }
        });
    });

    function HouzezStringToSlug(str) {
        // Trim the string
        str = str.replace(/^\s+|\s+$/g, '');
        str = str.toLowerCase();

        // Remove accents
        var from = 'àáäâèéëêìíïîòóöôùúüûñç·/_,:;',
            to = 'aaaaeeeeiiiioooouuuunc------',
            i,
            l;

        for (i = 0, l = from.length; i < l; i++) {
            str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
        }

        str = str
            .replace(/[^a-z0-9 -]/g, '') // remove invalid chars
            .replace(/\s+/g, '-') // collapse whitespace and replace by -
            .replace(/-+/g, '-'); // collapse dashes

        return str;
    }

    $(document).ready(function () {
        $('#fetch-locations-csv').on('click', function (e) {
            e.preventDefault();
            var selectedFile = $('#locations-csv-file').val();
            var $this = $(this);

            var $success = $('#locations-locations-success');
            var $error = $('#locations-locations-error');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_locations_csv_headers',
                    file_name: selectedFile,
                    nonce: Houzez_admin_vars.import_nonce,
                },
                beforeSend: function () {
                    $this.addClass('updating-message');
                    $success.html('');
                    $error.html('');
                },
                complete: function () {
                    $this.removeClass('updating-message');
                },
                success: function (response) {
                    if (response.success) {
                        // Call a function to display the field mapping interface
                        displayMappingInterface(response.data);
                    } else {
                        $error.html(response.data);
                    }
                },
                error: function () {
                    // Handle error
                },
            });
        });

        function displayMappingInterface(headers) {
            // Enhanced mapping interface with new Houzez admin UI design
            var mappingHtml =
                '<form id="locations-csv-form" class="houzez-fields-form">';
            mappingHtml += '<div class="houzez-form-grid">';

            var dbColumns = ['country', 'state', 'city', 'area'];
            var columnIcons = {
                country: 'admin-site-alt3',
                state: 'admin-multisite',
                city: 'building',
                area: 'location',
            };

            dbColumns.forEach(function (dbColumn) {
                var columnLabel =
                    dbColumn.charAt(0).toUpperCase() + dbColumn.slice(1);
                if (dbColumn === 'state') columnLabel = 'State/County';

                mappingHtml += '<div class="houzez-form-group">';
                mappingHtml +=
                    '<label class="houzez-form-label" for="' + dbColumn + '">';
                mappingHtml +=
                    '<i class="dashicons dashicons-' +
                    columnIcons[dbColumn] +
                    '"></i>';
                mappingHtml += columnLabel + '</label>';
                mappingHtml +=
                    '<select name="field_mapping[' +
                    dbColumn +
                    ']" id="' +
                    dbColumn +
                    '" class="houzez-form-select">';
                mappingHtml += '<option value="">Select CSV Column...</option>';

                headers.forEach(function (header) {
                    mappingHtml +=
                        '<option value="' +
                        header +
                        '">' +
                        header +
                        '</option>';
                });

                mappingHtml += '</select>';
                mappingHtml +=
                    '<p class="houzez-form-help">Map this field to a column in your CSV file</p>';
                mappingHtml += '</div>';
            });

            mappingHtml += '</div>';
            mappingHtml += '<div class="houzez-form-actions">';
            mappingHtml += '<div class="houzez-form-actions-left">';
            mappingHtml +=
                '<button type="button" class="houzez-btn houzez-btn-outline" onclick="$(\'#locations-mapping-section\').hide();">';
            mappingHtml += '<i class="dashicons dashicons-arrow-left-alt"></i>';
            mappingHtml += 'Back to Upload</button>';
            mappingHtml += '</div>';
            mappingHtml += '<div class="houzez-form-actions-right">';
            mappingHtml +=
                '<button id="run-locations-import" type="button" class="houzez-btn houzez-btn-primary">';
            mappingHtml +=
                '<i class="dashicons dashicons-database-import"></i>';
            mappingHtml += 'Start Import</button>';
            mappingHtml += '</div>';
            mappingHtml += '</div>';
            mappingHtml += '</form>';

            // Append the form to the container
            $('#locations-mapping-container').html(mappingHtml);

            // Show the mapping section with animation
            $('#locations-mapping-section').show();

            // Update the status badge
            $('#locations-mapping-section .houzez-status-badge')
                .removeClass('houzez-status-warning')
                .addClass('houzez-status-success')
                .text('Ready to Map');

            // Scroll to mapping section
            $('html, body').animate(
                {
                    scrollTop:
                        $('#locations-mapping-section').offset().top - 100,
                },
                500
            );
        }

        // Use event delegation to handle the import button click
        $(document).on('click', '#run-locations-import', function (e) {
            e.preventDefault();

            if (importInProgress) {
                return; // Prevent multiple imports
            }

            var $success = $('#locations-locations-success');
            var $error = $('#locations-locations-error');

            $success.html('');
            $error.html('');

            var $this = $(this);
            var allFieldsEmpty = true;
            var mappedFields = [];

            // Primary validation: Check using name attribute
            $('#locations-csv-form select[name^="field_mapping"]').each(
                function () {
                    var fieldValue = $(this).val();
                    var fieldName = $(this).attr('name');

                    if (fieldValue && fieldValue.trim() !== '') {
                        allFieldsEmpty = false;
                        mappedFields.push(fieldName + ': ' + fieldValue);
                    }
                }
            );

            // Fallback validation: Check using class selector
            if (allFieldsEmpty) {
                $('#locations-csv-form .houzez-form-select').each(function () {
                    var fieldValue = $(this).val();
                    var fieldName = $(this).attr('name') || $(this).attr('id');

                    if (fieldValue && fieldValue.trim() !== '') {
                        allFieldsEmpty = false;
                        mappedFields.push(fieldName + ': ' + fieldValue);
                    }
                });
            }

            // Final fallback: Direct check by IDs
            if (allFieldsEmpty) {
                var directFields = ['country', 'state', 'city', 'area'];
                directFields.forEach(function (fieldId) {
                    var fieldValue = $('#' + fieldId).val();

                    if (fieldValue && fieldValue.trim() !== '') {
                        allFieldsEmpty = false;
                        mappedFields.push(fieldId + ': ' + fieldValue);
                    }
                });
            }

            if (allFieldsEmpty) {
                showImportResults(
                    false,
                    'Please map at least one field before starting the import.'
                );
                return;
            }

            // Start the import process
            startImportProcess($this);
        });

        function import_locations_csv() {
            // This function is no longer needed as we're using event delegation
            // Keeping it for backward compatibility but it's empty
        }

        function startImportProcess($button) {
            importInProgress = true;
            currentBatch = 0;

            // Disable the import button
            $button.prop('disabled', true).addClass('updating-message');

            // Collect form data BEFORE showing progress section
            var manualData = {
                action: 'locations_process_field_mapping',
                selected_csv_file: $('#locations-csv-file').val(),
                field_mapping: {},
                nonce: Houzez_admin_vars.import_nonce,
            };

            var mappingCount = 0;

            // Collect field mappings while form still exists
            $('#locations-csv-form select[name^="field_mapping"]').each(
                function () {
                    var fieldName = $(this).attr('name');
                    var fieldValue = $(this).val();

                    if (fieldName && fieldValue && fieldValue.trim() !== '') {
                        var matches = fieldName.match(
                            /field_mapping\[([^\]]+)\]/
                        );
                        if (matches && matches[1]) {
                            manualData.field_mapping[matches[1]] = fieldValue;
                            mappingCount++;
                        }
                    }
                }
            );

            // Fallback: try direct IDs if no mappings found
            if (mappingCount === 0) {
                var directFields = ['country', 'state', 'city', 'area'];
                directFields.forEach(function (fieldId) {
                    var fieldElement = $('#' + fieldId);
                    if (fieldElement.length > 0) {
                        var fieldValue = fieldElement.val();
                        if (fieldValue && fieldValue.trim() !== '') {
                            manualData.field_mapping[fieldId] = fieldValue;
                            mappingCount++;
                        }
                    }
                });
            }

            if (mappingCount === 0) {
                importInProgress = false;
                $button.prop('disabled', false).removeClass('updating-message');
                showImportResults(
                    false,
                    'No field mappings found. Please map at least one field.'
                );
                return;
            }

            // Show progress section after collecting data
            showProgressSection();

            // Start the import
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: manualData,
                success: function (response) {
                    if (response.success) {
                        totalRows = response.data.total_rows;
                        updateProgress(0, totalRows, 0, 0, 0);
                        processBatch();
                    } else {
                        importInProgress = false;
                        $button
                            .prop('disabled', false)
                            .removeClass('updating-message');
                        showImportResults(false, response.data);
                    }
                },
                error: function (xhr, status, error) {
                    importInProgress = false;
                    $button
                        .prop('disabled', false)
                        .removeClass('updating-message');
                    showImportResults(
                        false,
                        'An error occurred during import initialization: ' +
                            error
                    );
                },
            });
        }

        function processBatch() {
            if (!importInProgress) return;

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'locations_batch_import',
                    batch: currentBatch,
                    nonce: Houzez_admin_vars.import_nonce,
                },
                success: function (response) {
                    if (response.success) {
                        var data = response.data;

                        // Update progress
                        updateProgress(
                            data.total_processed,
                            data.total_rows,
                            data.total_successful,
                            data.total_errors,
                            data.progress_percentage
                        );

                        // Show batch results
                        if (
                            data.batch_errors > 0 &&
                            data.error_messages.length > 0
                        ) {
                            appendBatchErrors(data.error_messages);
                        }

                        if (data.is_complete) {
                            // Import completed
                            importInProgress = false;
                            $('#run-locations-import')
                                .prop('disabled', false)
                                .removeClass('updating-message');
                            showCompletionMessage(data.completion_message);

                            // Auto-reload after 3 seconds
                            setTimeout(function () {
                                location.reload();
                            }, 3000);
                        } else {
                            // Continue with next batch
                            currentBatch++;
                            setTimeout(processBatch, 500); // Small delay between batches
                        }
                    } else {
                        importInProgress = false;
                        $('#run-locations-import')
                            .prop('disabled', false)
                            .removeClass('updating-message');
                        showImportResults(false, response.data);
                    }
                },
                error: function (xhr, status, error) {
                    importInProgress = false;
                    $('#run-locations-import')
                        .prop('disabled', false)
                        .removeClass('updating-message');
                    showImportResults(
                        false,
                        'Batch processing error: ' + error
                    );
                },
            });
        }

        function showProgressSection() {
            var progressHtml = `
                <div id="import-progress-section" class="houzez-import-progress">
                    <div class="progress-header">
                        <h3><i class="dashicons dashicons-update spinning"></i> Import in Progress</h3>
                        <p>Please don't close this page while import is running...</p>
                    </div>
                    
                    <div class="progress-stats">
                        <div class="stat-item">
                            <span class="stat-number" id="processed-count">0</span>
                            <span class="stat-label">Processed</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" id="successful-count">0</span>
                            <span class="stat-label">Successful</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" id="error-count">0</span>
                            <span class="stat-label">Errors</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" id="total-count">0</span>
                            <span class="stat-label">Total</span>
                        </div>
                    </div>
                    
                    <div class="houzez-progress-bar medium">
                        <div class="houzez-progress-fill default animated" id="progress-fill" style="width: 0%"></div>
                    </div>
                    <div class="houzez-progress-text">
                        <span id="progress-percentage" class="houzez-progress-percentage">0%</span>
                        <span id="progress-status" class="houzez-progress-status">Initializing...</span>
                    </div>
                    
                    <div id="batch-errors" class="batch-errors" style="display: none;">
                        <h4>Recent Errors:</h4>
                        <ul id="error-list"></ul>
                    </div>
                </div>
            `;

            $('#locations-mapping-container').html(progressHtml);
        }

        function updateProgress(
            processed,
            total,
            successful,
            errors,
            percentage
        ) {
            $('#processed-count').text(processed.toLocaleString());
            $('#successful-count').text(successful.toLocaleString());
            $('#error-count').text(errors.toLocaleString());
            $('#total-count').text(total.toLocaleString());
            $('#progress-percentage').text(percentage + '%');
            $('#progress-fill').css('width', percentage + '%');

            var status = 'Processing batch ' + (currentBatch + 1) + '...';
            if (percentage === 100) {
                status = 'Finalizing import...';
            }
            $('#progress-status').text(status);
        }

        function appendBatchErrors(errorMessages) {
            var $batchErrors = $('#batch-errors');
            var $errorList = $('#error-list');

            errorMessages.forEach(function (error) {
                $errorList.append('<li>' + error + '</li>');
            });

            $batchErrors.show();
        }

        function showCompletionMessage(message) {
            var completionHtml = `
                <div class="import-completion">
                    <div class="completion-icon">
                        <i class="dashicons dashicons-yes-alt"></i>
                    </div>
                    <h3>Import Completed Successfully!</h3>
                    <p>${message}</p>
                    <p><small>Page will reload automatically in 3 seconds...</small></p>
                </div>
            `;

            $('#import-progress-section').html(completionHtml);
        }

        // Enhanced success/error message display
        function showImportResults(success, message) {
            var resultsSection = $('#locations-results-section');
            var successDiv = $('#locations-locations-success');
            var errorDiv = $('#locations-locations-error');

            successDiv.html('');
            errorDiv.html('');

            if (success) {
                successDiv.html(
                    '<div class="houzez-notification success"><span class="dashicons dashicons-yes-alt"></span>' +
                        message +
                        '</div>'
                );
            } else {
                errorDiv.html(
                    '<div class="houzez-notification error"><span class="dashicons dashicons-warning"></span>' +
                        message +
                        '</div>'
                );
            }

            resultsSection.show();

            // Scroll to results
            $('html, body').animate(
                {
                    scrollTop: resultsSection.offset().top - 100,
                },
                500
            );
        }

        // Enhanced file upload with status update
        $('#upload-locations-csv').click(function (e) {
            e.preventDefault();

            var mediaUploader;

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose a CSV File',
                button: {
                    text: 'Choose CSV File',
                },
                library: {
                    type: 'text/csv',
                },
                multiple: false,
            });

            mediaUploader.on('select', function () {
                var attachment = mediaUploader
                    .state()
                    .get('selection')
                    .first()
                    .toJSON();
                $('#locations-csv-file').val(attachment.url);
                $('#locations-mapping-container').html('');
                $('#locations-mapping-section').hide();
                $('#locations-results-section').hide();

                // Reset import state
                importInProgress = false;
                currentBatch = 0;
                totalRows = 0;

                // Update status after file selection
                if (typeof updateCSVStatus === 'function') {
                    updateCSVStatus();
                }
            });

            mediaUploader.open();
        });
    });
})(jQuery);
