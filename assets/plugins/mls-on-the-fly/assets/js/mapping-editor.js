jQuery(document).ready(function ($) {
    // Variables for mapping modal and form
    var mappingModal = $('#mapping-modal');
    var importMappingModal = $("#import-mapping-modal");
    var type = $('#mapping-type-wrapper [name="type"]:checked').val();
    var mappingForm = $('#mapping-form');
    var application = $('#application').val();

    $('#mapping-type-wrapper [name="type"]').on('change', function(e){
        $(this).closest('form#choose-mapping-type-form').trigger('submit');
    });

    // Event handler for opening the modal
    $('#add-mapping-button').on('click', function () {
        mappingForm.trigger('reset'); // Reset the form
        mappingModal.modal('show');
    });

    $('#mapping-modal .close, #mapping-modal #close-mapping-button').on('click', function () {
        mappingModal.modal('hide');
    });

    $('#mls-on-the-fly-mapping-list-table .mls-on-the-fly-check-all').on('change',function(e){
        var $checkboxes = $(this).closest('#mls-on-the-fly-mapping-list-table').find('.mls-on-the-fly-mapping-input-id');
        if($(this).is(':checked')) {
            $checkboxes.prop('checked', true);
        }else{
            $checkboxes.prop('checked', false);
        }
    });

    // Event handler for opening the modal for editing a mapping
    $('.edit-button').on('click', function () {
        var type = $(this).data('type');
        var field = $(this).data('field');

        // Make an AJAX request to the API endpoint to get the mapping field data
        $.ajax({
            url: mlsOnTheFlyMappingAjax.siteUrl + '/wp-json/realtyna/mls-on-the-fly/v1/mappings/' + application + '/' + type + '/' + field,
            method: 'GET',
            headers: {
                'X-WP-Nonce': mlsOnTheFlyMappingAjax.nonce // Use the localized nonce
            },
            success: function (data) {
                if (data) {
                    // Populate the form fields with the retrieved data
                    $('#field_name').val(field);
                    $('#mapping').val(data.mapping);
                    $('#default').val(data.default);

                    // Populate the search and replace fields if applicable
                    var searchReplaceFields = $('#search-replace-fields');
                    searchReplaceFields.empty(); // Clear existing fields

                    if (data.replaces) {
                        // Populate search and replace fields if 'replaces' data exists
                        data.replaces.forEach(function (replaceData) {
                            addSearchReplaceFields(replaceData.search, replaceData.replace);
                        });
                    }
                } else {
                    displayMessage('Failed to retrieve mapping data for editing.', 'error');
                }
                mappingModal.modal('show');
            },
            error: function () {
                displayMessage('Failed to retrieve mapping data for editing.', 'error');
            }
        });
    });

    // Helper function to add search and replace fields
    function addSearchReplaceFields(searchValue, replaceValue) {
        var searchReplaceFields = $('#search-replace-fields');
        var searchReplaceField = $('<div class="form-row">');
        searchReplaceField.append('<div class="col"><input type="text" class="form-control" name="search[]" placeholder="Search" value="' + searchValue + '" required></div>');
        searchReplaceField.append('<div class="col"><input type="text" class="form-control" name="replace[]" placeholder="Replace" value="' + replaceValue + '" required></div>');
        searchReplaceField.append('<div class="col"><button class="btn btn-danger remove-search-replace">Delete</button></div>');
        searchReplaceFields.append(searchReplaceField);

        // Event handler to remove the search and replace field
        searchReplaceField.find('.remove-search-replace').on('click', function () {
            searchReplaceField.remove();
        });
    }

    // Event handler for saving the mapping
    $('#save-mapping-button').on('click', function (e) {
        e.preventDefault();

        // Gather form data
        var formData = mappingForm.serializeArray();

        // Make an AJAX request to the API endpoint to update or create a mapping field
        var type = formData.find(item => item.name === 'type').value;
        var field = formData.find(item => item.name === 'field_name').value;
        $.ajax({
            url: mlsOnTheFlyMappingAjax.siteUrl + '/wp-json/realtyna/mls-on-the-fly/v1/mappings/' + application + '/' + type + '/' + field,
            method: 'PUT',
            data: formData,
            headers: {
                'X-WP-Nonce': mlsOnTheFlyMappingAjax.nonce // Use the localized nonce
            },
            success: function (data) {
                // Handle success (e.g., update the table with the new data)
                // Reload the page to reflect the changes
                displayMessage('Mapping data saved successfully.', 'success');
                location.reload();
            },
            error: function () {
                displayMessage('Failed to save mapping data.', 'error');
            }
        });
    });

    // Event handler for deleting a mapping field
    $('.delete-button').on('click', function () {
        var type = $(this).data('type');
        var field = $(this).data('field');

        // Confirm the delete action with the user
        if (confirm('Are you sure you want to delete this mapping field?')) {
            // Make an AJAX request to the API endpoint to delete the mapping field
            $.ajax({
                url: mlsOnTheFlyMappingAjax.siteUrl + '/wp-json/realtyna/mls-on-the-fly/v1/mappings/' + application + '/' + type + '/' + field,
                method: 'DELETE',
                headers: {
                    'X-WP-Nonce': mlsOnTheFlyMappingAjax.nonce // Use the localized nonce
                },
                success: function () {
                    // Handle success (e.g., remove the row from the table)
                    // Reload the page to reflect the changes
                    displayMessage('Mapping data deleted successfully.', 'success');
                    location.reload();
                },
                error: function () {
                    displayMessage('Failed to delete mapping data.', 'error');
                }
            });
        }
    });

    $('#delete-all-checked-mappings').on('delete-map-item', function(e){

        var items = $('#delete-all-checked-mappings').data('items');
        $('#mls-on-the-fly-mapping-list-table').css('opacity', '.7');
        if( ! items.length ) {
            $('#mls-on-the-fly-mapping-list-table').css('opacity', '1');
            location.reload();
            return;
        }
        var item = items.pop();

        $.ajax({
            url: mlsOnTheFlyMappingAjax.siteUrl + '/wp-json/realtyna/mls-on-the-fly/v1/mappings/' + application + '/' + item.type + '/' + item.field,
            method: 'DELETE',
            headers: {
                'X-WP-Nonce': mlsOnTheFlyMappingAjax.nonce // Use the localized nonce
            },
            success: function () {
                // Handle success (e.g., remove the row from the table)
                // Reload the page to reflect the changes
                $('#delete-all-checked-mappings').trigger('delete-map-item');
                jQuery('[name^="mls-on-the-fly-mapping-ids['+ item.field +']"]').closest('tr').fadeOut(500);
            },
        });
    });

    $('#delete-all-checked-mappings').on('click', function () {

        // Confirm the delete action with the user
        if (confirm('Are you sure you want to delete this mapping fields?')) {
            // Make an AJAX request to the API endpoint to delete the mapping field

            var ids = jQuery('[name^="mls-on-the-fly-mapping-ids["]').serializeArray();
            var fields = [];
            $.each(ids, function(i,data){

                var $el = $('[name="'+data.name+'"]');
                if(!$el.is(':checked')){
                    return;
                }

                var $btn = $el.closest('tr').find('.delete-button');
                var type = $btn.data('type');
                var field = $btn.data('field');

                var ajax_requests = $('#delete-all-checked-mappings').data('ajax_requests');
                if('undefined' !== typeof ajax_requests ) {
                    ajax_requests = 0;
                }

                if(field.length) {

                    fields.push({
                        'type': type,
                        'field': field
                    });
                }
            });

            $('#delete-all-checked-mappings').data('items',fields);
            $('#delete-all-checked-mappings').trigger('delete-map-item');
        }
    });

    // Event handler for adding search and replace fields
    $('#add-search-replace').on('click', function () {
        var searchReplaceFields = $('#search-replace-fields');

        var searchReplaceField = '<div class="form-row">';
        searchReplaceField += '<div class="col">';
        searchReplaceField += '<input type="text" class="form-control" name="search[]" placeholder="Search" required>';
        searchReplaceField += '</div>';
        searchReplaceField += '<div class="col">';
        searchReplaceField += '<input type="text" class="form-control" name="replace[]" placeholder="Replace" required>';
        searchReplaceField += '</div>';
        searchReplaceField += '<div class="col">';
        searchReplaceField += '<button class="btn btn-danger remove-search-replace">Remove</button>';
        searchReplaceField += '</div>';
        searchReplaceField += '</div>';

        searchReplaceFields.append(searchReplaceField);

        // Event handler to remove the search and replace field
        $('.remove-search-replace').on('click', function () {
            $(this).closest('.form-row').remove();
        });
    });

    // Function to display success or error messages
    function displayMessage(message, type) {
        var messageContainer = $('#message-container');
        messageContainer.html('<div class="alert alert-' + type + '">' + message + '</div>');
        messageContainer.fadeIn(200);

        setTimeout(function () {
            messageContainer.fadeOut(200);
        }, 3000);
    }
    /************************************************************************************************************/

  var keyMappingModal = $("#key-mapping-modal");
  var postMetasMappingModal = $("#post-metas-mapping-modal");
  var taxonomyMappingModal = $("#taxonomy-mapping-modal");

  // Event handler for opening the import modal
  $("#import-mapping-button").on("click", function () {
    importMappingModal.modal("show");
  });
  // Event handler for closing the import modal
  $(
    "#import-mapping-modal .close, #import-mapping-modal #close-mapping-button",
  ).on("click", function () {
    importMappingModal.modal("hide");
  });

  // Event handler for exporting mappings
  $("#export-mapping-button").on("click", function () {
    // Make an AJAX request to the API endpoint to export the mappings as json
    $.ajax({
      url:
        "/wp-json/realtyna/mls-on-the-fly/v1/mappings/" +
        application +
        "/" +
        type,
      method: "GET",
      headers: {
        "X-WP-Nonce": mlsOnTheFlyMappingAjax.nonce, // Use the localized nonce
      },
      success: function (data) {
        if (data) {
          // Create a temporary link element
          const link = document.createElement("a");
          // If data.url is valid, use it, otherwise create a Blob
          if (data.url) {
            link.href = data.url; // Use the provided URL
          } else {
            const blob = new Blob([JSON.stringify(data)], {
              type: "application/json",
            });
            link.href = URL.createObjectURL(blob); // Create a Blob URL
          }
          link.download = "mappings" + "-" + application + "-" + type + ".json"; // Set the default file name
          document.body.appendChild(link);
          link.click(); // Trigger the download
          document.body.removeChild(link); // Clean up
          // If a Blob URL was created, revoke it
          if (!data.url) {
            URL.revokeObjectURL(link.href);
          }
        } else {
          displayMessage("Failed to export mappings.", "error");
        }
      },

      error: function () {
        displayMessage("Failed to export mappings.", "error");
      },
    });
  });

  // Event handler for importing the mappings
  $("#import-mappings-button").on("click", async function (e) {
    e.preventDefault();
    // Get the file data
    var file = $("#mapping_file_upload").prop("files")[0];

    async function parseJsonFile(file) {
      return new Promise((resolve, reject) => {
        const fileReader = new FileReader();
        fileReader.onload = (event) => resolve(JSON.parse(event.target.result));
        fileReader.onerror = (error) => reject(error);
        fileReader.readAsText(file);
      });
    }

    const object = await parseJsonFile(file);
    var formData = new FormData();
    var serializedData = JSON.stringify(object);
    formData.append("mapping_data", serializedData);
    // Make an AJAX request to the API endpoint to import the mappings
    $.ajax({
      url:
        "/wp-json/realtyna/mls-on-the-fly/v1/mappings/" +
        application +
        "/" +
        type,
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
      headers: {
        "X-WP-Nonce": mlsOnTheFlyMappingAjax.nonce, // Use the localized nonce
      },
      success: function (data) {
        console.log("suc");
        if (data) {
          // Handle success (e.g., update the table with the new data)
          // Reload the page to reflect the changes
          displayMessage("Mapping data imported successfully.", "success");
          location.reload();
        } else {
          displayMessage("Failed to import mappings.", "error");
        }
      },
      error: function () {
        displayMessage("Failed to import mappings.", "error");
      },
    });
    importMappingModal.modal("hide");
  });

  // Event handler for opening the add key mapping modal
  $("#add-key-mapping-button").on("click", function () {
    console.log("add key mapping button clicked");
    keyMappingModal.modal("show");
  });
  $("#key-mapping-modal .close, #key-mapping-modal #close-mapping-button").on(
    "click",
    function () {
      keyMappingModal.modal("hide");
    },
  );
  // Event handler for opening the add post metas mapping modal
  $("#add-post-metas-mapping-button").on("click", function () {
    console.log("add key mapping button clicked");
    postMetasMappingModal.modal("show");
  });
  $(
    "#post-metas-mapping-modal .close, #post-metas-mapping-modal #close-mapping-button",
  ).on("click", function () {
    postMetasMappingModal.modal("hide");
  });
  // Event handler for opening the add taxonomy modal
  $("#add-taxonomy-mapping-button").on("click", function () {
    console.log("add key mapping button clicked");
    taxonomyMappingModal.modal("show");
  });
  $(
    "#taxonomy-mapping-modal .close, #taxonomy-mapping-modal #close-mapping-button",
  ).on("click", function () {
    taxonomyMappingModal.modal("hide");
  });

  // Event handler for deleting a mapping field
  $(".query-delete-button").on("click", function () {
    var type = $(this).data("type");
    var query = $(this).data("query");
    var field = $(this).data("field");

    // Confirm the delete action with the user
    if (confirm("Are you sure you want to delete this query mapping field?")) {
      // Make an AJAX request to the API endpoint to delete the mapping field
      $.ajax({
        url:
          "/wp-json/realtyna/mls-on-the-fly/v1/mappings/" +
          application +
          "/" +
          type +
          "/" +
          query +
          "/" +
          field,
        method: "DELETE",
        headers: {
          "X-WP-Nonce": mlsOnTheFlyMappingAjax.nonce, // Use the localized nonce
        },
        success: function () {
          // Handle success (e.g., remove the row from the table)
          // Reload the page to reflect the changes
          displayMessage("Mapping data deleted successfully.", "success");
          //console.log("query mapping data deleted successfully")
          location.reload();
        },
        error: function () {
          displayMessage("Failed to delete mapping data.", "error");
        },
      });
    }
  });

  // Event handler for opening the modal for editing a mapping
  $(".query-edit-button").on("click", function () {
    var type = $(this).data("type");
    var field = $(this).data("field");
    var query = $(this).data("query");

    // Make an AJAX request to the API endpoint to get the mapping field data
    $.ajax({
      url:
        "/wp-json/realtyna/mls-on-the-fly/v1/mappings/" +
        application +
        "/" +
        type +
        "/" +
        query +
        "/" +
        field,
      method: "GET",
      headers: {
        "X-WP-Nonce": mlsOnTheFlyMappingAjax.nonce, // Use the localized nonce
      },
      success: function (data) {
        if (data) {
          // Populate the form fields with the retrieved data
          if (query == "key_mappings") {
            $("#field_name_key_mappings").val(field);
            $("#mapping_key_mappings").val(data);
          }
          if (query == "post_metas") {
            $("#field_name_post_metas").val(field);
            $("#mapping_post_metas").val(data.rf_field);
            $("#method_post_metas").val(data.method);
          }
          if (query == "taxonomies") {
            $("#field_name_taxonomies").val(field);
            $("#mapping_taxonomies").val(data.mapping);
            $("#child_taxonomies").val(data.child);
          }

          // Populate the search and replace fields if applicable
          var searchReplaceFields = $("#search-replace-fields");
          searchReplaceFields.empty(); // Clear existing fields

          if (data.replaces) {
            // Populate search and replace fields if 'replaces' data exists
            data.replaces.forEach(function (replaceData) {
              addSearchReplaceFields(replaceData.search, replaceData.replace);
            });
          }
        } else {
          displayMessage(
            "Failed to retrieve mapping data for editing.",
            "error",
          );
        }
        if (query === "key_mappings") {
          keyMappingModal.modal("show");
        }
        if (query === "post_metas") {
          postMetasMappingModal.modal("show");
        }
        if (query === "taxonomies") {
          taxonomyMappingModal.modal("show");
        }
      },
      error: function () {
        displayMessage("Failed to retrieve mapping data for editing.", "error");
      },
    });
  });

  // Event handler for saving the mapping
  $(
    "#query-key_mappings-save-mapping-button, #query-post_metas-save-mapping-button, #query-taxonomies-save-mapping-button",
  ).on("click", function (e) {
    e.preventDefault();
    var query = $(this).data("query");
    var formData;
    var type;
    var field;
    if (query === "key_mappings") {
      mappingForm = $("#key_mappings-mapping-form");
      formData = mappingForm.serializeArray();
      // Make an AJAX request to the API endpoint to update or create a mapping field
      type = formData.find((item) => item.name === "type").value;
      field = formData.find(
        (item) => item.name === "field_name_key_mappings",
      ).value;
    }
    if (query === "post_metas") {
      mappingForm = $("#post_metas-mapping-form");
      formData = mappingForm.serializeArray();
      // Make an AJAX request to the API endpoint to update or create a mapping field
      type = formData.find((item) => item.name === "type").value;
      field = formData.find(
        (item) => item.name === "field_name_post_metas",
      ).value;
    }
    if (query === "taxonomies") {
      mappingForm = $("#taxonomies-mapping-form");
      formData = mappingForm.serializeArray();
      // Make an AJAX request to the API endpoint to update or create a mapping field
      type = formData.find((item) => item.name === "type").value;
      field = formData.find(
        (item) => item.name === "field_name_taxonomies",
      ).value;
    }

    // Gather form data

    $.ajax({
      url:
        "/wp-json/realtyna/mls-on-the-fly/v1/mappings/" +
        application +
        "/" +
        type +
        "/" +
        query +
        "/" +
        field,
      method: "PUT",
      data: formData,
      headers: {
        "X-WP-Nonce": mlsOnTheFlyMappingAjax.nonce, // Use the localized nonce
      },
      success: function (data) {
        // Handle success (e.g., update the table with the new data)
        // Reload the page to reflect the changes
        displayMessage("Mapping data saved successfully.", "success");
        location.reload();
      },
      error: function () {
        displayMessage("Failed to save mapping data.", "error");
      },
    });
  });

  $(".query-delete-all-checked-mappings").on(
    "query-delete-map-item",
    function (e) {
      var items = $("#query-delete-all-checked-taxonomies-mappings").data(
        "items",
      );
      if (items.length === 0) {
        location.reload();
        return;
      }
      var item = items.pop();

      $.ajax({
        url:
          "/wp-json/realtyna/mls-on-the-fly/v1/mappings/" +
          application +
          "/" +
          item.type +
          "/" +
          item.query +
          "/" +
          item.field,
        method: "DELETE",
        headers: {
          "X-WP-Nonce": mlsOnTheFlyMappingAjax.nonce, // Use the localized nonce
        },
        success: function () {
          // Handle success (e.g., remove the row from the table)
          // Reload the page to reflect the changes

          if (item.query === "key_mappings") {
            jQuery(
              '[name^="mls-on-the-fly-key-mapping-ids[' + item.field + ']"]',
            )
              .closest("tr")
              .fadeOut(500);
          }
          if (item.query === "post_metas") {
            jQuery(
              '[name^="mls-on-the-fly-post-metas-mapping-ids[' +
                item.field +
                ']"]',
            )
              .closest("tr")
              .fadeOut(500);
          }
          if (item.query === "taxonomies") {
            jQuery(
              '[name^="mls-on-the-fly-taxonomies-mapping-ids[' +
                item.field +
                ']"]',
            )
              .closest("tr")
              .fadeOut(500);
          }
          $(this).trigger("query-delete-map-item");
        },
      });
    },
  );

  $(".query-delete-all-checked-mappings").on("click", function () {
    // Confirm the delete action with the user
    if (confirm("Are you sure you want to delete this mapping fields?")) {
      // Make an AJAX request to the API endpoint to delete the mapping field
      var query = $(this).data("query");
      var ids;
      if (query === "key_mappings") {
        ids = jQuery(
          '[name^="mls-on-the-fly-key-mapping-ids["]',
        ).serializeArray();
      }
      if (query === "post_metas") {
        ids = jQuery(
          '[name^="mls-on-the-fly-post-metas-mapping-ids["]',
        ).serializeArray();
      }
      if (query === "taxonomies") {
        ids = jQuery(
          '[name^="mls-on-the-fly-taxonomies-mapping-ids["]',
        ).serializeArray();
      }

      var fields = [];
      $.each(ids, function (i, data) {
        var $el = $('[name="' + data.name + '"]');
        if (!$el.is(":checked")) {
          return;
        }

        var $btn = $el.closest("tr").find(".query-delete-button");
        var type = $btn.data("type");
        var field = $btn.data("field");

        var ajax_requests = $(".query-delete-all-checked-mappings").data(
          "ajax_requests",
        );
        if ("undefined" !== typeof ajax_requests) {
          ajax_requests = 0;
        }

        if (field.length) {
          fields.push({
            type: type,
            field: field,
            query: query,
          });
        }
      });

      $("#query-delete-all-checked-taxonomies-mappings").data("items", fields);
      $(".query-delete-all-checked-mappings").trigger("query-delete-map-item");
    }
  });

  // Event handler for Resetting the mappings
  $("#reset-mapping-button").on("click", function () {
    // Use template literals for confirmation dialog with the correct variable references
    if (
      confirm(`Are you sure you want to reset ${application} ${type} mappings?`)
    ) {
      // Make an AJAX request to the API endpoint to reset the mappings
      $.ajax({
        url:
          "/wp-json/realtyna/mls-on-the-fly/v1/mappings/" +
          application +
          "/" +
          type,
        method: "DELETE", // Change to DELETE if you want to reset
        headers: {
          "X-WP-Nonce": mlsOnTheFlyMappingAjax.nonce, // Use the localized nonce
        },
        success: function (data) {
          if (data) {
            // Handle success (e.g., update the table with the new data)
            displayMessage("Mapping data reset successfully.", "success");
            location.reload();
          } else {
            displayMessage("Resetting mappings failed.", "error");
          }
        },
        error: function () {
          displayMessage("Failed to reset mappings.", "error");
        },
      });
    }
  });
});

