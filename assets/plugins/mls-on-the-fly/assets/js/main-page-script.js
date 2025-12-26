jQuery(document).ready(function ($) {
    let currentStep = 0;
    const totalSteps = $('.mls-otf-wizard-step').length;

    // Function to navigate between steps
    function goToStep(step) {
        $('.mls-otf-wizard-step').removeClass('active');
        $('.progress-bar .step').removeClass('active');
        $('.mls-otf-wizard-step').hide();

        $('.mls-otf-wizard-step').eq(step).addClass('active');
        $('.progress-bar .step').eq(step).addClass('active');
        $('.mls-otf-wizard-step').eq(step).show();

        $('html, body').animate({scrollTop: $('.mls-otf-wizard-container').offset().top}, 'slow');
    }

    // Next button functionality
    $('.btn-next').click(function () {
        if (currentStep < totalSteps - 1) {
            currentStep++;
            goToStep(currentStep);
            logUserAction('Demo Wizard - Next Step', currentStep); // Log action
        }
    });

    // Previous button functionality
    $('.btn-prev').click(function () {
        if (currentStep > 0) {
            currentStep--;
            goToStep(currentStep);
            logUserAction('Demo Wizard - Previous Step', currentStep); // Log action
        }
    });

    // Close button functionality for demo wizard
    $('.btn-close').on('click', function () {
        logUserAction('Close Demo Wizard', currentStep); // Log close action with step
        currentStep = 0;
        $('#mls-otf-demo-wizard').hide();
        $('.mls-otf-main-page-content-wrapper').show();
    });

    // Submit form and handle AJAX request for API keys (Demo wizard)
    $('.submit-keys-btn').click(function (e) {
        e.preventDefault();

        const formData = {
            action: 'verify_api_keys',
            api_key: $('#mls-on-the-fly-settings-api-key').val(),
            client_id: $('#mls-on-the-fly-settings-client-id').val(),
            client_secret: $('#mls-on-the-fly-settings-client-secret').val(),
            nonce: mlsOnTheFlyAjax.nonce // Ensure nonce is available in your WordPress AJAX settings
        };

        $.ajax({
            url: mlsOnTheFlyAjax.ajax_url, // WordPress AJAX URL
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function (response) {
                if (response.success) {
                    // If successful, move to the final step
                    currentStep++;
                    goToStep(currentStep);
                    logUserAction('Demo Wizard - Submit API Keys', currentStep); // Log action
                } else {
                    // If failed, show the guide for submitting a ticket
                    $('#error-message').text(response.data.message).show();
                    $('#support-guide').show();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // Handle error
                $('#error-message').text('An error occurred. Please try again.').show();
                $('#support-guide').show();
            }
        });
    });

    // Show the wizard when the demo data button is clicked
    $('.demo-data-button').click(function () {
        logUserAction('Access Demo Data', 0); // Log opening real data wizard
        $('#mls-otf-demo-wizard').show();
        $('#mls-otf-realdata-wizard').hide();
        $('.mls-otf-main-page-content-wrapper').hide();
        goToStep(currentStep); // Show the first step
    });

    // Variables and functions for Real Data wizard
    let currentRealDataStep = 0;
    const totalRealDataSteps = $('#mls-otf-realdata-wizard .mls-otf-wizard-step').length;

    // Function to navigate between steps for real data wizard
    function goToRealDataStep(step) {
        $('#mls-otf-realdata-wizard .mls-otf-wizard-step').removeClass('active');
        $('#mls-otf-realdata-wizard .progress-bar .step').removeClass('active');
        $('#mls-otf-realdata-wizard .mls-otf-wizard-step').hide();

        $('#mls-otf-realdata-wizard .mls-otf-wizard-step').eq(step).addClass('active');
        $('#mls-otf-realdata-wizard .progress-bar .step').eq(step).addClass('active');
        $('#mls-otf-realdata-wizard .mls-otf-wizard-step').eq(step).show();

        $('html, body').animate({scrollTop: $('#mls-otf-realdata-wizard').offset().top}, 'slow');
    }

    // Next button functionality for real data wizard
    $('.realdata-btn-next').click(function () {
        if (currentRealDataStep < totalRealDataSteps - 1) {
            currentRealDataStep++;
            goToRealDataStep(currentRealDataStep);
            logUserAction('Real Data Wizard - Next Step', currentRealDataStep); // Log action
        }
    });

    // Previous button functionality for real data wizard
    $('.realdata-btn-prev').click(function () {
        if (currentRealDataStep > 0) {
            currentRealDataStep--;
            goToRealDataStep(currentRealDataStep);
            logUserAction('Real Data Wizard - Previous Step', currentRealDataStep); // Log action
        }
    });

    // Open real data wizard
    $('.real-data-button').on('click', function () {
        $('#mls-otf-realdata-wizard').show();
        $('.mls-otf-main-page-content-wrapper').hide();
        goToRealDataStep(0);
        logUserAction('Access Real Data', 0); // Log opening real data wizard
    });

    // Close button functionality for real data wizard
    $('.realdata-btn-close').on('click', function () {
        logUserAction('Close Real Data Wizard', currentRealDataStep); // Log close action with step
        currentRealDataStep = 0;
        $('#mls-otf-realdata-wizard').hide();
        $('.mls-otf-main-page-content-wrapper').show();
    });

    // Function to log user actions and send to WordPress
    // Function to log user actions and store them in localStorage
    function logUserAction(action, step) {
        const logData = {
            user_action: action,
            step: step,
            timestamp: new Date().toISOString()
        };

        // Get existing logs from localStorage
        let userActions = JSON.parse(localStorage.getItem('userActions')) || [];

        // Add the new action to the logs
        userActions.push(logData);

        // Save back to localStorage
        localStorage.setItem('userActions', JSON.stringify(userActions));
    }

    // Function to send batched logs to the server
    function sendBatchedLogs() {
        const userActions = JSON.parse(localStorage.getItem('userActions'));

        if (userActions && userActions.length > 0) {
            const logData = {
                action: 'log_user_action_batch',
                logs: userActions,
                nonce: mlsOnTheFlyAjax.nonce
            };

            $.ajax({
                url: mlsOnTheFlyAjax.ajax_url, // WordPress AJAX URL
                type: 'POST',
                dataType: 'json',
                data: logData,
                success: function (response) {
                    console.log('Batched user actions logged:', response);
                    // Clear the localStorage after successful logging
                    localStorage.removeItem('userActions');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Error logging batched user actions:', textStatus, errorThrown);
                }
            });
        }
    }

    // Periodically send logs every 10 minutes (600000 ms)
    setInterval(sendBatchedLogs, 20000);

    // Send logs when the user closes the browser/tab
    window.addEventListener('beforeunload', sendBatchedLogs);

    jQuery(document).ready(function ($) {
        const modal = $('#edit-notes-modal');
        const form = $('#edit-notes-form');
        const rowsContainer = form.find('.note-rows');

        // Open modal
        $('.edit-notes-btn').on('click', function () {

            const listingKey = $(this).data('listing-key');
            const originatingSystem = $(this).data('originating-system-name');
            const propertyAdditionalInfoKey = $(this).data('property-additional-info-key');
            const propertyAdditionalInfoPhoneNumber = $(this).data('property-additional-info-phone-number');
            const propertyAdditionalInfoAge = $(this).data('property-additional-info-age');
            const propertyAdditionalInfoName = $(this).data('property-additional-info-name');

            $('#edit-notes-listing-key').val(listingKey);
            $('#edit-notes-originating-system').val(originatingSystem);
            $('#property-additional-info-phone-number-value').val(propertyAdditionalInfoPhoneNumber);
            $('#property-additional-info-age-value').val(propertyAdditionalInfoAge);
            $('#property-additional-info-name-value').val(propertyAdditionalInfoName);
            $('#edit-notes-property-additional-info-key').val(propertyAdditionalInfoKey);
            modal.show();
        });

        // Close modal
        $('.close-modal').on('click', function () {
            modal.hide();
        });

        $('.delete-notes-btn').on('click', function () {
            if (!confirm('Are you sure you want to delete notes for this property?')) {
                return;
            }

            const $btn = $(this);
            const listingKey = $btn.data('listing-key');
            const originSystem = $btn.data('originating-system-name');
            const nonce = $btn.data('nonce');
            const propertyAdditionalInfoKey = $btn.data('property-additional-info-key');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'delete_property_notes',
                    security: nonce,
                    listing_key: listingKey,
                    originating_system_name: originSystem,
                    property_additional_info_key: propertyAdditionalInfoKey
                },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data?.message || 'Failed to delete notes.');
                    }
                },
                error: function () {
                    alert('AJAX request failed.');
                }
            });
        });



        // Submit form
        form.on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: form.serialize(),
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data?.message || 'Something went wrong.');
                    }
                },
                error: function () {
                    alert('AJAX request failed.');
                }
            });
        });
    });

});
