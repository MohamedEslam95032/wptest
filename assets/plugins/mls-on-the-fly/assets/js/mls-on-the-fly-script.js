jQuery(document).ready(function ($) {
    /** Tabs Scripts Begin */
    $('.realtyna-base-plugin-tab-headers .realtyna-base-plugin-tab-link').on('click', function (e) {
        e.preventDefault();
        var $link_wrap = $(this).closest('.realtyna-base-plugin-tab-headers');
        var $content_wrap = $(this).closest('.realtyna-base-plugin-tabs-wrapper').find('.realtyna-base-plugin-tab-contents-wrapper');

        $link_wrap.find('.realtyna-base-plugin-tab-link.active').removeClass('active');
        $content_wrap.find('.realtyna-base-plugin-tab-content.active').removeClass('active');

        $(this).addClass('active');
        $content_wrap.find($(this).attr('href')).addClass('active');
    });
    /** Tabs Scripts End */

    var $globalSpinner = $('#global-spinner');

    $('form.delete-taxonomy-terms').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var taxonomy = $form.find('input[name="taxonomy_to_delete"]').val();
        var $message = $('#realtyna-base-plugin-message');
        var initialBatchSize = 200;
        var minBatchSize = 50;

        if (confirm('Are you sure you want to delete all terms in the taxonomy ' + taxonomy + '?')) {
            $globalSpinner.css('visibility', 'visible');
            deleteTerms(taxonomy, initialBatchSize);
        }

        function deleteTerms(taxonomy, batchSize) {
            $.ajax({
                url: mlsOnTheFlyAjax.ajax_url,
                type: 'post',
                data: {
                    action: 'delete_taxonomy_terms',
                    taxonomy: taxonomy,
                    nonce: mlsOnTheFlyAjax.nonce,
                    batch_size: batchSize
                },
                success: function (response) {
                    if (response.success) {
                        if (response.data.continue) {
                            deleteTerms(taxonomy, batchSize);
                        } else {
                            $globalSpinner.css('visibility', 'hidden');
                            $message.html('<div class="notice notice-success is-dismissible"><p>All terms in the taxonomy <strong>' + taxonomy + '</strong> have been deleted.</p></div>').show();
                            location.reload();
                        }
                    } else {
                        $globalSpinner.css('visibility', 'hidden');
                        $message.html('<div class="notice notice-error is-dismissible"><p>Error: ' + response.data.message + '</p></div>').show();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (batchSize > minBatchSize) {
                        batchSize -= 50;
                        deleteTerms(taxonomy, batchSize);
                    } else {
                        $globalSpinner.css('visibility', 'hidden');
                        $message.html('<div class="notice notice-error is-dismissible"><p>Error: ' + textStatus + ' - ' + errorThrown + '</p></div>').show();
                    }
                }
            });
        }
    });

    $('.update-last-update-time').on('click', function (e) {
        e.preventDefault();
        var $button = $(this);
        var taxonomy = $button.data('taxonomy');
        var $message = $('#realtyna-base-plugin-message');

        $globalSpinner.css('visibility', 'visible');

        $.ajax({
            url: mlsOnTheFlyAjax.ajax_url,
            type: 'post',
            data: {
                action: 'update_taxonomy_last_update_time',
                taxonomy: taxonomy,
                nonce: mlsOnTheFlyAjax.nonce
            },
            success: function (response) {
                $globalSpinner.css('visibility', 'hidden');
                if (response.success) {
                    $button.siblings('p').eq(1).text('Last Update: ' + response.data.last_update_time);
                    $message.html('<div class="notice notice-success is-dismissible"><p>Taxonomy <strong>' + response.data.taxonomy + '</strong> is scheduled for update.</p></div>').show();
                    location.reload();
                } else {
                    $message.html('<div class="notice notice-error is-dismissible"><p>Error: ' + response.data.message + '</p></div>').show();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $globalSpinner.css('visibility', 'hidden');
                $message.html('<div class="notice notice-error is-dismissible"><p>Error: ' + textStatus + ' - ' + errorThrown + '</p></div>').show();
            }
        });
    });

    $('.realtyna-base-plugin-modal .realtyna-base-plugin-close-modal-button').on('click', function () {
        $(this).closest('.realtyna-base-plugin-modal').modal('hide');
    });

    jQuery('.realtyna-core-field-wrapper select').select2();

});
