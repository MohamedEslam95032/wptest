(function ($) {
    'use strict';

    var ajaxurl = Houzez_crm_vars.ajax_url;
    var delete_confirmation = Houzez_crm_vars.delete_confirmation;
    var email_confirmation = Houzez_crm_vars.email_confirmation;
    var processing_text = Houzez_crm_vars.processing_text;
    var delete_btn_text = Houzez_crm_vars.delete_btn_text;
    var confirm_btn_text = Houzez_crm_vars.confirm_btn_text;
    var cancel_btn_text = Houzez_crm_vars.cancel_btn_text;
    var map_fields_text = Houzez_crm_vars.map_fields_text;
    var error_import_text = Houzez_crm_vars.error_import;
    var select_text = Houzez_crm_vars.select_text;
    var import_text = Houzez_crm_vars.import_text;
    var houzez_date_language = Houzez_crm_vars.houzez_date_language;
    var execute_muticheck = false;
    var houzez_dashboard_loading = $('#houzez-dashboard-loading');

    var crm_processing_modal = function (msg) {
        var process_modal =
            '<div class="modal fade" id="fave_modal" tabindex="-1" role="dialog" aria-labelledby="faveModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-body houzez_messages_modal">' +
            msg +
            '</div></div></div></div></div>';
        jQuery('body').append(process_modal);
        jQuery('#fave_modal').modal('show');
    };

    var crm_processing_modal_close = function () {
        jQuery('#fave_modal').modal('hide');
    };

    /* ------------------------------------------------------------------------ */
    /* dashboard slide panel
    /* ------------------------------------------------------------------------ */
    $('.open-close-slide-panel').on('click', function (e) {
        $('.dashboard-slide-panel-wrap').toggleClass(
            'dashboard-slide-panel-wrap-visible'
        );
        $('.main-wrap').toggleClass('opacity-02');
    });

    $('.open-close-enquiry-panel').on('click', function (e) {
        $('.enquiry-panel-js').toggleClass(
            'dashboard-slide-panel-wrap-visible'
        );
        $('.main-wrap').toggleClass('opacity-02');
    });

    $('.open-close-deal-panel').on('click', function (e) {
        $('.deal-panel-wrap-js').toggleClass(
            'dashboard-slide-panel-wrap-visible'
        );
        $('.main-wrap').toggleClass('opacity-02');
    });

    /*-------------------------------------------------------------------------------
     * Note
     *------------------------------------------------------------------------------*/
    $('#enquiry_note').on('click', function (e) {
        e.preventDefault();

        //var $form = $('#lead-form');
        var $this = $(this);
        var $container = $('#notes-main-wrap');
        var note = $('#note').val();
        var note_type = $('#note_type').val();
        var belong_to = $('#belong_to').val();
        var Nonce = $('#note_security').val();

        $.ajax({
            type: 'post',
            url: ajaxurl,
            dataType: 'json',
            data: {
                action: 'houzez_crm_add_note',
                note: note,
                note_type: note_type,
                belong_to: belong_to,
                security: Nonce,
            },
            beforeSend: function () {
                $this.find('.houzez-loader-js').addClass('loader-show');
            },
            complete: function () {
                $this.find('.houzez-loader-js').removeClass('loader-show');
            },
            success: function (response) {
                if (response.success) {
                    $('#note').val('');
                    $container.prepend(response.noteHtml);
                    $('#note-' + response.note_id).slideDown('slow');
                    delete_note();
                } else {
                    alert(response.msg);
                }
            },
            error: function (xhr, status, error) {
                var err = eval('(' + xhr.responseText + ')');
                console.log(err.Message);
            },
        });
    });

    /*--------------------------------------------------------------------------
     *  Delete Leads CSV
     * -------------------------------------------------------------------------*/
    $('a.delete-lead-csv-js').on('click', function () {
        var $this = $(this);
        var file = $this.data('file');
        var nonce = $this.data('nonce'); // Get the nonce from the data attribute

        bootbox.confirm({
            message: '<strong>' + delete_confirmation + '</strong>',
            closeButton: false,
            buttons: {
                confirm: {
                    label: confirm_btn_text,
                    className: 'btn btn-primary',
                },
                cancel: {
                    label: cancel_btn_text,
                    className: 'btn btn-grey-outlined',
                },
            },
            callback: function (result) {
                if (result == true) {
                    crm_processing_modal(processing_text);

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: ajaxurl,
                        data: {
                            action: 'delete_leads_csv_file',
                            file_name: file,
                            _wpnonce: nonce, // Include the nonce in the request
                        },
                        success: function (data) {
                            if (data.success == true) {
                                window.location.reload();
                            } else {
                                jQuery('#fave_modal').modal('hide');
                                alert(data.reason);
                            }
                        },
                        error: function (errorThrown) {},
                    }); // $.ajax
                } // result
            }, // Callback
        });

        return false;
    });

    /*--------------------------------------------------------------------------
     *  Delete Note
     * -------------------------------------------------------------------------*/
    function delete_note() {
        $('a.delete_note').on('click', function (e) {
            e.preventDefault();

            var $this = $(this);
            var ID = $this.data('id');
            $this.parents('.note-block').slideUp('slow');

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                data: {
                    action: 'houzez_delete_note',
                    note_id: ID,
                },
                success: function (data) {
                    if (data.success == true) {
                        $this.parents('.note-block').remove();
                    }
                },
                error: function (errorThrown) {},
            }); // $.ajax
        });
    }
    delete_note();

    /*-------------------------------------------------------------------------------
     * Multi select
     *------------------------------------------------------------------------------*/
    var checkboxConfigs = [
        {
            selectAllId: 'enquiry_select_all',
            checkboxClass: 'enquiry_multi_delete',
        },
        {
            selectAllId: 'listing_viewed_select_all',
            checkboxClass: 'listing_viewed_multi_delete',
        },
        {
            selectAllId: 'listings_select_all',
            checkboxClass: 'listing_multi_id',
        },
        { selectAllId: 'leads_select_all', checkboxClass: 'lead-bulk-delete' },
        {
            selectAllId: 'activity_select_all',
            checkboxClass: 'activity-bulk-delete',
        },
    ];

    var execute_muticheck = false;
    var select_all, checkboxes;

    for (var config of checkboxConfigs) {
        if (document.getElementById(config.selectAllId)) {
            select_all = document.getElementById(config.selectAllId); //select all checkbox
            checkboxes = document.getElementsByClassName(config.checkboxClass); //checkbox items
            execute_muticheck = true;
            break; // Break the loop once the matching configuration is found
        }
    }

    if (execute_muticheck) {
        // Select all checkboxes
        select_all.addEventListener('change', function () {
            for (let checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });

        // Event listener for each checkbox
        Array.from(checkboxes).forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                // Uncheck "select all" if any checkbox is unchecked
                if (!this.checked) {
                    select_all.checked = false;
                }
                // Check "select all" if all checkboxes are checked
                else if (Array.from(checkboxes).every((chk) => chk.checked)) {
                    select_all.checked = true;
                }
            });
        });
    }

    /*-------------------------------------------------------------------------------
     * Delete button enable/disable
     *------------------------------------------------------------------------------*/
    function initializeDeleteButton(checkboxClass, buttonId) {
        var checkboxes = document.getElementsByClassName(checkboxClass);
        var button = document.getElementById(buttonId);

        if (!checkboxes.length || !button) {
            console.warn('Checkboxes or button not found!');
            return;
        }

        // Function to update the button based on checkbox state
        var updateButton = function () {
            var anyChecked = Array.from(checkboxes).some(function (checkbox) {
                return checkbox.checked;
            });

            if (anyChecked) {
                button.classList.remove('btn-grey-outlined');
                button.classList.add('btn-primary');
                button.disabled = false; // Enable the button
            } else {
                button.classList.remove('btn-primary');
                button.classList.add('btn-grey-outlined');
                button.disabled = true; // Disable the button
            }
        };

        // Attach event listener to each checkbox
        Array.from(checkboxes).forEach(function (checkbox) {
            checkbox.addEventListener('change', updateButton);
        });

        // Initialize button state
        updateButton();
    }

    // Usage
    document.addEventListener('DOMContentLoaded', function () {
        initializeDeleteButton('lead-bulk-delete', 'bulk-delete-leads');
        initializeDeleteButton(
            'enquiry_multi_delete',
            'enquiry_delete_multiple'
        );
        initializeDeleteButton('activity-bulk-delete', 'activity_bulk_apply');
    });

    /*--------------------------------------------------------------------------
     *  Delete property
     * -------------------------------------------------------------------------*/
    $('a.delete-lead').on('click', function () {
        var $this = $(this);
        var ID = $this.data('id');
        var Nonce = $this.data('nonce');

        bootbox.confirm({
            message: '<strong>' + delete_confirmation + '</strong>',
            closeButton: false,
            buttons: {
                confirm: {
                    label: confirm_btn_text,
                    className: 'btn btn-primary',
                },
                cancel: {
                    label: cancel_btn_text,
                    className: 'btn btn-grey-outlined',
                },
            },
            callback: function (result) {
                if (result == true) {
                    crm_processing_modal(processing_text);

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: ajaxurl,
                        data: {
                            action: 'houzez_delete_lead',
                            lead_id: ID,
                            security: Nonce,
                        },
                        success: function (data) {
                            if (data.success == true) {
                                window.location.reload();
                            } else {
                                jQuery('#fave_modal').modal('hide');
                                alert(data.reason);
                            }
                        },
                        error: function (errorThrown) {},
                    }); // $.ajax
                } // result
            }, // Callback
        });

        return false;
    });

    /*-------------------------------------------------------------------
     * Add Lead
     *------------------------------------------------------------------*/
    $('#add_new_lead').on('click', function (e) {
        e.preventDefault();

        var $form = $('#lead-form');
        var $this = $(this);
        var $messages = $('#lead-msgs');

        $.ajax({
            type: 'post',
            url: ajaxurl,
            dataType: 'json',
            data: $form.serialize(),
            beforeSend: function () {
                $this.find('.houzez-loader-js').addClass('loader-show');
            },
            complete: function () {
                $this.find('.houzez-loader-js').removeClass('loader-show');
            },
            success: function (response) {
                if (response.success) {
                    $messages
                        .empty()
                        .append(
                            '<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">' +
                                response.msg +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                        );
                    window.location.reload();
                } else {
                    $messages
                        .empty()
                        .append(
                            '<div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">' +
                                response.msg +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                        );
                }
            },
            error: function (xhr, status, error) {
                var err = eval('(' + xhr.responseText + ')');
                console.log(err.Message);
            },
        });
    });

    /*-------------------------------------------------------------------
     * Edit Lead
     *------------------------------------------------------------------*/
    $('.edit-lead').on('click', function (e) {
        e.preventDefault();

        var $form = $('#lead-form');
        var lead_id = $(this).data('id');

        $.ajax({
            type: 'post',
            url: ajaxurl,
            dataType: 'json',
            data: {
                action: 'get_single_lead',
                lead_id: lead_id,
                security: Houzez_crm_vars.get_lead_nonce,
            },
            beforeSend: function () {
                $('#lead_id').remove();
                houzez_dashboard_loading.show();
            },
            complete: function () {
                houzez_dashboard_loading.hide();
            },
            success: function (response) {
                if (response.success) {
                    var res = response.data;

                    $('#name').val(res.display_name);
                    $('#first_name').val(res.first_name);
                    $('#last_name').val(res.last_name);
                    $('#prefix').val(res.prefix).attr('selected', 'selected');
                    $('#user_type').val(res.type);
                    $('#email').val(res.email);
                    $('#mobile').val(res.mobile);
                    $('#home_phone').val(res.home_phone);
                    $('#work_phone').val(res.work_phone);
                    $('#address').val(res.address);
                    $('#country').val(res.country);
                    $('#city').val(res.city);
                    $('#state').val(res.state);
                    $('#zip').val(res.zip);
                    $('#source').val(res.source).attr('selected', 'selected');
                    $('#facebook').val(res.facebook_url);
                    $('#twitter').val(res.twitter_url);
                    $('#linkedin').val(res.linkedin_url);
                    $('#private_note').val(res.private_note);

                    $form.append(
                        '<input type="hidden" id="lead_id" name="lead_id" value="' +
                            res.lead_id +
                            '">'
                    );

                    $form.find('.selectpicker').selectpicker('refresh');
                }
            },
            error: function (xhr, status, error) {
                var err = eval('(' + xhr.responseText + ')');
                console.log(err.Message);
            },
        });
    });

    /*-------------------------------------------------------------------
     * Export leads
     *------------------------------------------------------------------*/
    $('#export-leads').on('click', function (e) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'houzez_crm_export_leads',
            },
            beforeSend: function () {
                $('.houzez-loader-js').addClass('loader-show');
            },
            complete: function () {
                $('.houzez-loader-js').removeClass('loader-show');
            },
            success: function (response) {
                // Create a Blob from the CSV data and trigger download
                var blob = new Blob([response], { type: 'text/csv' });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'leads.csv';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            },
        });
    });

    /*-------------------------------------------------------------------
     * upload leads CSV file
     *------------------------------------------------------------------*/
    $('#upload-leads-csv').on('click', function (e) {
        e.preventDefault();

        var formData = new FormData();
        formData.append('action', 'houzez_crm_upload_csv');
        formData.append('csv_import', $('input[type=file]')[0].files[0]);
        formData.append(
            'houzez_crm_leads_nonce_field',
            $('#houzez_crm_leads_nonce_field').val()
        );
        var message;

        $.ajax({
            url: ajaxurl, // ajaxurl is already defined in WordPress admin
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $('.houzez-loader-js').addClass('loader-show');
            },
            complete: function () {
                $('.houzez-loader-js').removeClass('loader-show');
            },
            success: function (response) {
                // Load field mapping interface
                if (response.success) {
                    message =
                        '<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">' +
                        response.data.message +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                    window.location.href = response.data.redirect_to;
                } else {
                    message =
                        '<div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">' +
                        response.data +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                }
                $('.dashboard-content-block-wrap').prepend(message);
            },
            error: function (response) {
                // Handle error
            },
        });
    });

    $('#fetch-data-btn').on('click', function () {
        var selectedFile = $('#uploaded-csv-files').val();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'get_leads_csv_headers',
                file_name: selectedFile,
            },
            success: function (response) {
                if (response.success) {
                    // Call a function to display the field mapping interface
                    displayMappingInterface(response.data);
                } else {
                    alert(response.data);
                }
            },
            error: function () {
                // Handle error
            },
        });
    });

    function displayMappingInterface(headers) {
        var mappingHtml = '<form id="csv-mapping-form">';

        // Predefined database column names
        var dbColumns = [
            'prefix',
            'display_name',
            'first_name',
            'last_name',
            'email',
            'mobile',
            'home_phone',
            'work_phone',
            'address',
            'country',
            'city',
            'state',
            'zipcode',
            'type',
            'source',
            'source_link',
            'linkedin_url',
            'facebook_url',
            'twitter_url',
            'private_note',
            'message',
        ];

        dbColumns.forEach(function (dbColumn) {
            mappingHtml +=
                '<div class="form-group mb-3"><label for="' +
                dbColumn +
                '">' +
                dbColumn.replace('_', ' ').charAt(0).toUpperCase() +
                dbColumn.replace('_', ' ').slice(1) +
                ' </label>';
            mappingHtml +=
                '<select name="field_mapping[' +
                dbColumn +
                ']" id="' +
                dbColumn +
                '" class="selectpicker form-control" data-live-search="true" title="' +
                select_text +
                '">';

            // Add options for each CSV header
            headers.forEach(function (header) {
                mappingHtml +=
                    '<option value="' + header + '">' + header + '</option>';
            });

            mappingHtml += '</select></div>';
        });

        mappingHtml +=
            '<button type="submit" class="btn btn-primary"><span class="btn-loader houzez-loader-js"></span>' +
            import_text +
            '</button></form>';

        // Append the form to a container in your HTML
        $('#mapping-container').html(mappingHtml);

        leads_csv_import();
        $('.selectpicker').selectpicker('refresh');
    }

    function leads_csv_import() {
        $('#csv-mapping-form').on('submit', function (e) {
            e.preventDefault();

            var $this = $(this);
            var allFieldsEmpty = true;
            $('#csv-mapping-form select').each(function () {
                if (this.value.trim() !== '') {
                    allFieldsEmpty = false;
                    return false; // Break the loop
                }
            });

            if (allFieldsEmpty) {
                alert(map_fields_text);
                return; // Prevent form submission
            }

            var formData = new FormData(this);
            formData.append('action', 'houzez_crm_process_field_mapping');
            formData.append(
                'selected_csv_file',
                $('#uploaded-csv-files').val()
            );

            $.ajax({
                url: ajaxurl, // ajaxurl is already defined in WordPress admin
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $this.find('.houzez-loader-js').addClass('loader-show');
                },
                complete: function () {
                    $this.find('.houzez-loader-js').removeClass('loader-show');
                },
                success: function (response) {
                    // Handle success
                    if (response.success) {
                        console.log(response.data.message);
                        window.location.href = response.data.redirect_to;
                    }
                    console.log(response.data.message);
                },
                error: function (response) {
                    // Handle error
                    alert(error_import_text);
                },
            });
        });
    }

    $('#uploaded-csv-files').on('change', function () {
        $('#mapping-container').html('');
    });

    /*-------------------------------------------------------------------
     * Delete Enquiries
     *------------------------------------------------------------------*/
    $('#bulk-delete-leads').on('click', function () {
        var $this = $(this);
        var ID = $this.data('id');
        var Nonce = $this.data('nonce');

        var checkboxVals = $('.lead-bulk-delete');

        var vals = $('.lead-bulk-delete:checked')
            .map(function () {
                return this.value;
            })
            .get()
            .join(',');

        if (vals == '') {
            return;
        }
        bootbox.confirm({
            message: '<strong>' + delete_confirmation + '</strong>',
            closeButton: false,
            buttons: {
                confirm: {
                    label: confirm_btn_text,
                    className: 'btn btn-primary',
                },
                cancel: {
                    label: cancel_btn_text,
                    className: 'btn btn-grey-outlined',
                },
            },
            callback: function (result) {
                if (result == true) {
                    crm_processing_modal(processing_text);

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: ajaxurl,
                        data: {
                            action: 'bulk_delete_leads',
                            ids: vals,
                        },
                        success: function (data) {
                            if (data.success == true) {
                                window.location.reload();
                            } else {
                                jQuery('#fave_modal').modal('hide');
                                alert(data.reason);
                            }
                        },
                        error: function (errorThrown) {},
                    }); // $.ajax
                } // result
            }, // Callback
        });

        return false;
    });

    /*-------------------------------------------------------------------
     * Edit Deal
     *------------------------------------------------------------------*/
    $('.crm-edit-deal-js').on('click', function (e) {
        e.preventDefault();

        var $form = $('#deal-form');
        var deal_id = $(this).data('id');
        var $messages = $('#deal-msgs');

        $.ajax({
            type: 'post',
            url: ajaxurl,
            dataType: 'json',
            data: {
                action: 'get_single_deal',
                deal_id: deal_id,
            },
            beforeSend: function () {
                $('#deal_id').remove();
                houzez_dashboard_loading.show();
            },
            complete: function () {
                houzez_dashboard_loading.hide();
            },
            success: function (response) {
                if (response.success) {
                    var res = response.data;

                    $('select[name="deal_group"]')
                        .val(res.deal_group)
                        .attr('selected', 'selected');
                    $('input[name="deal_title"]').val(res.title);
                    $('select[name="deal_contact"]')
                        .val(res.lead_id)
                        .attr('selected', 'selected');
                    $('select[name="deal_agent"]')
                        .val(res.agent_id)
                        .attr('selected', 'selected');
                    $('input[name="deal_value"]').val(res.deal_value);

                    $form.append(
                        '<input type="hidden" id="deal_id" name="deal_id" value="' +
                            res.deal_id +
                            '">'
                    );

                    $form.find('.selectpicker').selectpicker('refresh');
                }
            },
            error: function (xhr, status, error) {
                var err = eval('(' + xhr.responseText + ')');
                console.log(err.Message);
            },
        });
    });

    /*--------------------------------------------------------------------------
     *  Delete Activity
     * -------------------------------------------------------------------------*/
    $('.delete-activity-js').on('click', function (e) {
        e.preventDefault();

        var $this = $(this);
        var ID = $this.data('id');
        var Nonce = $this.data('nonce');

        bootbox.confirm({
            message: '<strong>' + delete_confirmation + '</strong>',
            closeButton: false,
            buttons: {
                confirm: {
                    label: confirm_btn_text,
                    className: 'btn btn-primary',
                },
                cancel: {
                    label: cancel_btn_text,
                    className: 'btn btn-grey-outlined',
                },
            },
            callback: function (result) {
                if (result == true) {
                    crm_processing_modal(processing_text);

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: ajaxurl,
                        data: {
                            action: 'houzez_delete_activity',
                            activity_id: ID,
                            security: Nonce,
                        },
                        success: function (data) {
                            if (data.success == true) {
                                window.location.reload();
                            } else {
                                jQuery('#fave_modal').modal('hide');
                                alert(data.reason);
                            }
                        },
                        error: function (errorThrown) {},
                    }); // $.ajax
                } // result
            }, // Callback
        });

        return false;
    });

    /*--------------------------------------------------------------------------
     *  Delete Deal
     * -------------------------------------------------------------------------*/
    $('a.delete-deal-js').on('click', function () {
        var $this = $(this);
        var ID = $this.data('id');
        var Nonce = $this.data('nonce');

        bootbox.confirm({
            message: '<strong>' + delete_confirmation + '</strong>',
            closeButton: false,
            buttons: {
                confirm: {
                    label: confirm_btn_text,
                    className: 'btn btn-primary',
                },
                cancel: {
                    label: cancel_btn_text,
                    className: 'btn btn-grey-outlined',
                },
            },
            callback: function (result) {
                if (result == true) {
                    crm_processing_modal(processing_text);

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: ajaxurl,
                        data: {
                            action: 'houzez_delete_deal',
                            deal_id: ID,
                            security: Nonce,
                        },
                        success: function (data) {
                            if (data.success == true) {
                                window.location.reload();
                            } else {
                                jQuery('#fave_modal').modal('hide');
                                alert(data.reason);
                            }
                        },
                        error: function (errorThrown) {},
                    }); // $.ajax
                } // result
            }, // Callback
        });

        return false;
    });

    /*-------------------------------------------------------------------
     * Add Deals
     *------------------------------------------------------------------*/
    $('#add_deal').on('click', function () {
        var $form = $('#deal-form');
        var $this = $(this);
        var $messages = $('#deal-msgs');

        $.ajax({
            type: 'post',
            url: ajaxurl,
            dataType: 'json',
            data: $form.serialize(),
            beforeSend: function () {
                $this.find('.houzez-loader-js').addClass('loader-show');
            },
            complete: function () {
                $this.find('.houzez-loader-js').removeClass('loader-show');
            },
            success: function (response) {
                if (response.success) {
                    $messages
                        .empty()
                        .append(
                            '<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">' +
                                response.msg +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                        );
                    window.location.reload();
                } else {
                    $messages
                        .empty()
                        .append(
                            '<div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">' +
                                response.msg +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                        );
                }
            },
            error: function (xhr, status, error) {
                var err = eval('(' + xhr.responseText + ')');
                console.log(err.Message);
            },
        });
    });

    /*-------------------------------------------------------------------
     * Add Enquiry
     *------------------------------------------------------------------*/
    $('#add_new_enquiry').on('click', function () {
        var $form = $('#enquiry-form');
        var $this = $(this);
        var $messages = $('#enquiry-msgs');

        $.ajax({
            type: 'post',
            url: ajaxurl,
            dataType: 'json',
            data: $form.serialize(),
            beforeSend: function () {
                $this.find('.houzez-loader-js').addClass('loader-show');
            },
            complete: function () {
                $this.find('.houzez-loader-js').removeClass('loader-show');
            },
            success: function (response) {
                if (response.success) {
                    $messages
                        .empty()
                        .append(
                            '<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">' +
                                response.msg +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                        );
                    window.location.reload();
                } else {
                    $messages
                        .empty()
                        .append(
                            '<div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">' +
                                response.msg +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                        );
                }
            },
            error: function (xhr, status, error) {
                var err = eval('(' + xhr.responseText + ')');
                console.log(err.Message);
            },
        });
    });

    /*-------------------------------------------------------------------
     * Delete Single Enquiry
     *------------------------------------------------------------------*/
    $('.delete_enquiry_js').on('click', function () {
        var $this = $(this);
        var ID = $this.data('id');

        bootbox.confirm({
            message: '<strong>' + delete_confirmation + '</strong>',
            closeButton: false,
            buttons: {
                confirm: {
                    label: confirm_btn_text,
                    className: 'btn btn-primary',
                },
                cancel: {
                    label: cancel_btn_text,
                    className: 'btn btn-grey-outlined',
                },
            },
            callback: function (result) {
                if (result == true) {
                    crm_processing_modal(processing_text);

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: ajaxurl,
                        data: {
                            action: 'houzez_delete_enquiry',
                            ids: ID,
                        },
                        success: function (data) {
                            if (data.success == true) {
                                window.location.reload();
                            } else {
                                jQuery('#fave_modal').modal('hide');
                                alert(data.reason);
                            }
                        },
                        error: function (errorThrown) {},
                    }); // $.ajax
                } // result
            }, // Callback
        });

        return false;
    });

    /*-------------------------------------------------------------------
     * Delete Enquiries Bulk
     *------------------------------------------------------------------*/
    $('#enquiry_delete_multiple').on('click', function () {
        var $this = $(this);
        var ID = $this.data('id');
        var Nonce = $this.data('nonce');

        var checkboxVals = $('.enquiry_multi_delete');

        var vals = $('.enquiry_multi_delete:checked')
            .map(function () {
                return this.value;
            })
            .get()
            .join(',');

        if (vals == '') {
            return;
        }
        bootbox.confirm({
            message: '<strong>' + delete_confirmation + '</strong>',
            closeButton: false,
            buttons: {
                confirm: {
                    label: confirm_btn_text,
                    className: 'btn btn-primary',
                },
                cancel: {
                    label: cancel_btn_text,
                    className: 'btn btn-grey-outlined',
                },
            },
            callback: function (result) {
                if (result == true) {
                    crm_processing_modal(processing_text);

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: ajaxurl,
                        data: {
                            action: 'houzez_delete_enquiry',
                            ids: vals,
                        },
                        success: function (data) {
                            if (data.success == true) {
                                window.location.reload();
                            } else {
                                jQuery('#fave_modal').modal('hide');
                                alert(data.reason);
                            }
                        },
                        error: function (errorThrown) {},
                    }); // $.ajax
                } // result
            }, // Callback
        });

        return false;
    });

    /*-------------------------------------------------------------------
     * Send select listings via email
     *------------------------------------------------------------------*/
    $('#inquiry-send-email').on('click', function () {
        var $this = $(this);

        var checkboxVals = $('.listing_multi_id');
        var email_to = $('#lead_email').val();

        var vals = $('.listing_multi_id:checked')
            .map(function () {
                return this.value;
            })
            .get()
            .join(',');

        if (vals == '' || email_to == '') {
            return;
        }

        bootbox.confirm({
            message: '<strong>' + email_confirmation + '</strong>',
            closeButton: false,
            buttons: {
                confirm: {
                    label: confirm_btn_text,
                    className: 'btn btn-primary',
                },
                cancel: {
                    label: cancel_btn_text,
                    className: 'btn btn-grey-outlined',
                },
            },
            callback: function (result) {
                if (result == true) {
                    crm_processing_modal(processing_text);

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: ajaxurl,
                        data: {
                            action: 'houzez_match_listing_email',
                            ids: vals,
                            email_to: email_to,
                        },
                        success: function (data) {
                            if (data.success) {
                                $('.email_messages')
                                    .empty()
                                    .append(
                                        '<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">' +
                                            data.msg +
                                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
                                    );
                            } else {
                                $('.email_messages')
                                    .empty()
                                    .append(
                                        '<div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">' +
                                            data.msg +
                                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
                                    );
                            }
                            jQuery('#fave_modal').modal('hide');

                            var checkboxes =
                                document.getElementsByClassName(
                                    'listing_multi_id'
                                ); //checkbox items

                            $('.listing_multi_id').prop('checked', false);
                        },
                        error: function (errorThrown) {},
                    }); // $.ajax
                } // result
            }, // Callback
        });

        return false;
    });

    /*-------------------------------------------------------------------
     * Edit Enquiry
     *------------------------------------------------------------------*/
    $('.edit_enquiry_js').on('click', function (e) {
        e.preventDefault();

        var $form = $('#enquiry-form');
        var enquiry_id = $(this).data('id');

        $.ajax({
            type: 'post',
            url: ajaxurl,
            dataType: 'json',
            data: {
                action: 'get_single_enquiry',
                enquiry_id: enquiry_id,
            },
            beforeSend: function () {
                $('#enquiry_id').remove();
                houzez_dashboard_loading.show();
            },
            complete: function () {
                houzez_dashboard_loading.hide();
            },
            success: function (response) {
                if (response.success) {
                    var res = response.data;
                    var meta = response.meta;

                    $('#lead_id').val(res.lead_id).attr('selected', 'selected');
                    $('#enquiry_type')
                        .val(res.enquiry_type)
                        .attr('selected', 'selected');

                    if (meta.property_type != undefined) {
                        $('#property_type')
                            .val(meta.property_type.slug)
                            .attr('selected', 'selected');
                    }
                    if (meta.property_status != undefined) {
                        $('#property_status')
                            .val(meta.property_status.slug)
                            .attr('selected', 'selected');
                    }
                    $('#private_note').val(res.private_note);
                    $('#min-price').val(meta.min_price);
                    $('#max-price').val(meta.max_price);
                    $('#min-beds').val(meta.min_beds);
                    $('#max-beds').val(meta.max_beds);
                    $('#min-baths').val(meta.min_baths);
                    $('#max-baths').val(meta.max_baths);
                    $('#min-area').val(meta.min_area);
                    $('#max-area').val(meta.max_area);
                    $('#zipcode').val(meta.zipcode);

                    if (meta.country != undefined) {
                        $('#country')
                            .val(meta.country.slug)
                            .attr('selected', 'selected');
                    }

                    if (meta.state != undefined) {
                        $('#state')
                            .val(meta.state.slug)
                            .attr('selected', 'selected');
                    }

                    if (meta.city != undefined) {
                        $('#city')
                            .val(meta.city.slug)
                            .attr('selected', 'selected');
                    }

                    if (meta.area != undefined) {
                        $('#area')
                            .val(meta.area.slug)
                            .attr('selected', 'selected');
                    }

                    $form.append(
                        '<input type="hidden" id="enquiry_id" name="enquiry_id" value="' +
                            res.enquiry_id +
                            '">'
                    );

                    $form.find('.selectpicker').selectpicker('refresh');
                }
            },
            error: function (xhr, status, error) {
                var err = eval('(' + xhr.responseText + ')');
                console.log(err.Message);
            },
        });
    });

    /*-------------------------------------------------------------------
     * Export inquiries
     *------------------------------------------------------------------*/
    $('#export-inquiries').on('click', function (e) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'crm_export_inquiries',
            },
            beforeSend: function () {
                $('.houzez-loader-js').addClass('loader-show');
            },
            complete: function () {
                $('.houzez-loader-js').removeClass('loader-show');
            },
            success: function (response) {
                // Create a Blob from the CSV data and trigger download
                var blob = new Blob([response], { type: 'text/csv' });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'inquiries.csv';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            },
        });
    });

    $('.deal_status').on('change', function () {
        var $this = $(this);
        var deal_id = $this.parents('tr').data('id');
        var deal_status = $this.val();

        updateDealData('crm_set_deal_status', deal_id, deal_status);
    });

    $('.deal_next_action').on('change', function () {
        var $this = $(this);
        var deal_id = $this.parents('tr').data('id');
        var deal_action = $this.val();

        updateDealData('crm_set_deal_next_action', deal_id, deal_action);
    });

    $(document).ready(function () {
        if ($('.deal_action_due').length > 0) {
            $('.deal_action_due')
                .datepicker({
                    orientation: 'bottom left',
                    format: 'yyyy-mm-dd',
                    clearBtn: true,
                    autoclose: true,
                    language: houzez_date_language,
                })
                .on('changeDate', function (e) {
                    var $this = $(this);
                    var deal_id = $this.parents('tr').data('id');

                    var current_date = $this.val();

                    updateDealData('crm_set_action_due', deal_id, current_date);
                    console.log(current_date);
                });
        }

        if ($('.deal_last_contact').length > 0) {
            $('.deal_last_contact')
                .datepicker({
                    orientation: 'bottom left',
                    format: 'yyyy-mm-dd',
                    clearBtn: true,
                    autoclose: true,
                    language: houzez_date_language,
                })
                .on('changeDate', function (e) {
                    var $this = $(this);
                    var deal_id = $this.parents('tr').data('id');

                    var current_date = $this.val();

                    updateDealData(
                        'crm_set_last_contact_date',
                        deal_id,
                        current_date
                    );
                    console.log(current_date);
                });
        }
    });

    function updateDealData(action, deal_id, data) {
        $.ajax({
            type: 'post',
            url: ajaxurl,
            dataType: 'json',
            data: {
                action: action,
                deal_id: deal_id,
                deal_data: data,
            },
            beforeSend: function () {},
            complete: function () {},
            success: function (response) {
                if (response.success) {
                }
            },
            error: function (xhr, status, error) {
                var err = eval('(' + xhr.responseText + ')');
                console.log(err.Message);
            },
        });
    }

    /*-------------------------------------------------------------------
     * Delete viewed listings
     *------------------------------------------------------------------*/
    $('#listing_viewed_delete').on('click', function () {
        var $this = $(this);

        var vals = $('.listing_viewed_multi_delete:checked')
            .map(function () {
                return this.value;
            })
            .get()
            .join(',');

        if (vals == '') {
            return;
        }

        bootbox.confirm({
            message: '<strong>' + delete_confirmation + '</strong>',
            closeButton: false,
            buttons: {
                confirm: {
                    label: confirm_btn_text,
                    className: 'btn btn-primary',
                },
                cancel: {
                    label: cancel_btn_text,
                    className: 'btn btn-grey-outlined',
                },
            },
            callback: function (result) {
                if (result == true) {
                    crm_processing_modal(processing_text);

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: ajaxurl,
                        data: {
                            action: 'houzez_delete_viewed_listings',
                            ids: vals,
                        },
                        success: function (data) {
                            if (data.success == true) {
                                window.location.reload();
                            } else {
                                jQuery('#fave_modal').modal('hide');
                                alert(data.reason);
                            }
                        },
                        error: function (errorThrown) {},
                    }); // $.ajax
                } // result
            }, // Callback
        });

        return false;
    });

    /*-----------------------------------------------------------------------------------*/
    /* Records per page
    /*-----------------------------------------------------------------------------------*/
    function insertParam_crm(key, value) {
        key = encodeURI(key);
        value = encodeURI(value);

        // get querystring , remove (?) and covernt into array
        var qrp = document.location.search.substr(1).split('&');

        // get qrp array length
        var i = qrp.length;
        var j;
        while (i--) {
            //covert query strings into array for check key and value
            j = qrp[i].split('=');

            // if find key and value then join
            if (j[0] == key) {
                j[1] = value;
                qrp[i] = j.join('=');
                break;
            }
        }

        if (i < 0) {
            qrp[qrp.length] = [key, value].join('=');
        }
        // reload the page
        document.location.search = qrp.join('&');
    }

    $('#crm-records-per-page').on('change', function () {
        var key = 'records';
        var value = $(this).val();
        insertParam_crm(key, value);
    });

    /*-------------------------------------------------------------------
     * Delete Activities Bulk
     *------------------------------------------------------------------*/
    $('#activity_bulk_apply').on('click', function () {
        var $this = $(this);
        var selected_action = $('#activity_bulk_action').val();

        if (selected_action === 'delete') {
            var vals = $('.activity-bulk-delete:checked')
                .map(function () {
                    return this.value;
                })
                .get()
                .join(',');

            if (vals === '') {
                return;
            }

            bootbox.confirm({
                message: '<strong>' + delete_confirmation + '</strong>',
                closeButton: false,
                buttons: {
                    confirm: {
                        label: confirm_btn_text,
                        className: 'btn btn-primary',
                    },
                    cancel: {
                        label: cancel_btn_text,
                        className: 'btn btn-grey-outlined',
                    },
                },
                callback: function (result) {
                    if (result === true) {
                        crm_processing_modal(processing_text);

                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: ajaxurl,
                            data: {
                                action: 'houzez_delete_activities_bulk',
                                activity_ids: vals,
                                security: Houzez_crm_vars.delete_activity_nonce,
                            },
                            success: function (data) {
                                if (data.success === true) {
                                    window.location.reload();
                                } else {
                                    jQuery('#fave_modal').modal('hide');
                                    alert(data.reason);
                                }
                            },
                            error: function (errorThrown) {},
                        }); // $.ajax
                    } // result
                }, // Callback
            });
        }

        return false;
    });
})(jQuery);
