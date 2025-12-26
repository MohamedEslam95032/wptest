(function ($) {
    'use strict';

    window.houzezValidateElementor = function (form) {
        if ($.fn.validate && $.fn.ajaxSubmit) {
            var $form = $(form),
                submitButton = $form.find('.houzez-submit-button'),
                messageContainer = $form.find('.ele-form-messages'),
                errorContainer = $form.find('.error-container'),
                ajaxLoader = $form.find('.houzez-loader-js'),
                formOptions = {
                    beforeSubmit: function () {
                        ajaxLoader.addClass('loader-show');
                        submitButton.attr('disabled', 'disabled');
                        messageContainer.fadeOut('fast');
                        errorContainer.fadeOut('fast');
                    },
                    success: function (response, statusText, xhr, $form) {
                        response = $.parseJSON(response);
                        ajaxLoader.removeClass('loader-show');
                        submitButton.removeAttr('disabled');

                        if (response.success) {
                            $form.resetForm();
                            messageContainer
                                .html(
                                    '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                        '<i class="houzez-icon icon-check-circle-1 me-1"></i> ' +
                                        response.msg +
                                        '</div>'
                                )
                                .fadeIn('fast');

                            if (houzez_vars.houzez_reCaptcha == 1) {
                                $form.find('.g-recaptcha-response').remove();
                                if (houzez_vars.g_recaptha_version == 'v3') {
                                    houzezReCaptchaLoad();
                                } else {
                                    houzezReCaptchaReset();
                                }
                            }

                            if (response.redirect_to != '') {
                                setTimeout(function () {
                                    window.location.replace(
                                        response.redirect_to
                                    );
                                }, 500);
                            }
                        } else {
                            messageContainer
                                .html(
                                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                        '<i class="houzez-icon icon-remove-circle me-1"></i> ' +
                                        response.msg +
                                        '</div>'
                                )
                                .fadeIn('fast');
                        }
                    },
                };

            $form.validate({
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid').removeClass('is-valid');

                    // For checkboxes, also add/remove class for control__indicator
                    if (
                        $(element).is(':checkbox') &&
                        $(element).closest('.control--checkbox').length
                    ) {
                        $(element)
                            .closest('.control--checkbox')
                            .find('.control__indicator')
                            .addClass('is-invalid')
                            .removeClass('is-valid');
                    }
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid').addClass('is-valid');

                    // For checkboxes, also add/remove class for control__indicator
                    if (
                        $(element).is(':checkbox') &&
                        $(element).closest('.control--checkbox').length
                    ) {
                        $(element)
                            .closest('.control--checkbox')
                            .find('.control__indicator')
                            .removeClass('is-invalid')
                            .addClass('is-valid');
                    }
                },
                errorPlacement: function (error, element) {
                    // Do nothing with the error, effectively hiding it
                    return true;
                },
                submitHandler: function (form) {
                    $(form).ajaxSubmit(formOptions);
                },
            });
        } // end if jQuery.validate
    }; // end houzezValidateElementor

    // Initialize the validation when the document is ready
    $(document).ready(function () {
        $('.houzez-ele-form-js').each(function () {
            houzezValidateElementor(this);
        });
    });

    // Re-initialize the validation when an Elementor popup is opened
    $(document).on('elementor/popup/show', function () {
        $('.houzez-ele-form-js').each(function () {
            houzezValidateElementor(this);
        });
    });
})(jQuery);
