jQuery(document).ready(function ($) {
    const $renderedFilters = $('#rendered-filters');


    // Function to handle adding filter sets
    function addFilterSet(filter) {
        $renderedFilters.val($renderedFilters.val() ? $renderedFilters.val() + ' and ' : '');
        $renderedFilters.val($renderedFilters.val() + filter);
    }

    // Submit form to add filter sets
    $('#add-filter-form').submit(function (e) {
        e.preventDefault();

        const rfField = $('input[name="rf_field"]').val();
        const odataCondition = $('input[name="odata_condition"]').val();
        const comparedValue = $('input[name="compared_value"]').val();

        const filterSet = `(${rfField} ${odataCondition} ${comparedValue})`;
        addFilterSet(filterSet);

        $('input[name="rf_field"]').val('');
        $('input[name="odata_condition"]').val('');
        $('input[name="compared_value"]').val('');
    });

    // Implement drag-and-drop using jQuery UI
    $renderedFilters.sortable({
        cursor: 'move',
        update: function () {
            // Update the textarea content when filters are rearranged
            $renderedFilters.trigger('input');
        }
    });


    // Save Filter
    $('#save-filter-button').on('click', function () {
        const currentFilter = $renderedFilters.val();

        // AJAX request to save the filter to the database
        $.ajax({
            url: 'your_save_filter_endpoint.php', // Replace with your actual endpoint
            method: 'POST',
            data: { current_filter: currentFilter },
            success: function (response) {
                // Handle success (e.g., show a success message)
                alert('Filter saved successfully!');
            },
            error: function (xhr, status, error) {
                // Handle error (e.g., display an error message)
                alert('Error: ' + error);
            }
        });
    });


});
