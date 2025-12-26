var mls_on_the_fly_global_filters = (function($){

    var $rendered_filters_wrapper = $('#mls-on-the-fly-rendered-filters .mls-on-the-fly-rendered-filters-wrapper');
    var $add_condition_modal = $('#mls-on-the-fly-add-condition-modal');
    var $target_group;
    var $target_input;

    function render_groups( groups_data ) {

        var html = '';
        html += '<ul id="mls-on-the-fly-filter-groups">';
        html += render_add_filter_buttons('groups');
        $.each( groups_data, function(id, group_data){
            if ( 'object' === typeof group_data ) {
                html += render_group( id, group_data );
            } else {
                html += render_item( id, group_data );
            }
        });
        html += '</ul>';

        return html;
    }

    function render_group( group_id, group_data ) {

        var group_title = group_data['name'] ?? '';
        var items       = group_data['items'] ?? {};
        var relation    = group_data['relation'] ?? 'and';
        var html = '';
        html += '<li class="mls-on-the-fly-filter-group" data-relation="' + relation +'">'
                + '<div class="mls-on-the-fly-field-wrapper mls-on-the-fly-filter-group-title-wrapper">'
                    + '<div class="mls-on-the-fly-field-svg">' + mls_on_the_fly_global_filters_data.icons.folder + '</div>'
                    + '<input class="mls-on-the-fly-filter-group-title" type="text" value="' + group_title + '" />'
                    + render_actions_dropdown( 'group', relation )
                    + '<span class="mls-on-the-fly-filter-group-relation">'
                        + '<span class="mls-on-the-fly-filter-group-relation-and ' + ( 'and' === relation ? 'show' : '' ) + '">' + mls_on_the_fly_global_filters_data.i18n.and + '</span>'
                        + '<span class="mls-on-the-fly-filter-group-relation-or ' + ( 'or' === relation ? 'show' : '' ) + '">' + mls_on_the_fly_global_filters_data.i18n.or + '</span>'
                    + '</span>'
                + '</div>'
                + '<ul class="mls-on-the-fly-filter-group-items">';
                    html += render_add_filter_buttons('group');
                    $.each( items, function(item_id, item_data){

                        if ( 'object' === typeof item_data ) {
                            html += render_group( item_id, item_data );
                        } else {
                            html += render_item( item_id, item_data );
                        }
                    });
                html += '</ul>';

        html += '</li>';

        return html;
    }

    function get_item_data(value) {

        var data = {
            'key': '',
            'condition': '',
            'value': '',
        };

        value = value.split(' ');
        if( value.length ) {

            data['key'] = value[0];
            data['condition'] = value[1];
            delete(value[0]);
            delete(value[1]);
            var value = $.trim(value.join(' '));
            var text = value.length ? value.match("'(.*)'") : '';
            data['value'] = 'undefined' !== typeof text[1] ? text[1] : value;
        }

        return data;
    }

    function render_item( item_id, data ) {

        var values = get_item_data(data);
        var html = '';
        html += '<li class="mls-on-the-fly-field-wrapper mls-on-the-fly-input-wrapper mls-on-the-fly-filter-group-item-wrapper">'
                + '<input class="mls-on-the-fly-filter-group-item" type="text" value="' + data + '"  data-key="' + values['key'] + '" data-condition="' + values['condition'] + '" data-value="' + values['value'] + '"/>'
                + render_actions_dropdown('item')
            + '</li>';

        return html;
    }

    function render_actions_dropdown( $for = 'item', relation = 'and' ) {

        var html = '';
        html += '<span class="mls-on-the-fly-input-open-actions"></span>'
            + '<div class="mls-on-the-fly-item-actions">';
            if ( 'group' === $for ){
                html += '<div class="mls-on-the-fly-group-relation">'
                        + '<label><input type="radio" class="relation" value="and" ' + ( 'and' === relation ? 'checked' : '' ) + ' />' + mls_on_the_fly_global_filters_data.i18n.and + '</label>'
                        + '<label><input type="radio" class="relation" value="or" ' + ( 'or' === relation ? 'checked' : '' ) + ' />' + mls_on_the_fly_global_filters_data.i18n.or + '</label>'
                    + '</div>';
            }

            html += '<ul>';
                    if ( 'group' !== $for ){
                        html += '<li class="mls-on-the-fly-input-action-edit">' + mls_on_the_fly_global_filters_data.icons.edit + mls_on_the_fly_global_filters_data.i18n.edit + '</li>';
                    }
                    html += '<li class="mls-on-the-fly-input-action-duplicate">' + mls_on_the_fly_global_filters_data.icons.duplicate + mls_on_the_fly_global_filters_data.i18n.duplicate + '</li>'
                        + '<li class="mls-on-the-fly-input-action-ungroup">' + mls_on_the_fly_global_filters_data.icons.ungroup + mls_on_the_fly_global_filters_data.i18n.ungroup + '</li>'
                        + '<li class="mls-on-the-fly-input-action-trash">' + mls_on_the_fly_global_filters_data.icons.ungroup + mls_on_the_fly_global_filters_data.i18n.delete + '</li>'
                + '</ul>';
        html += '</div>';

        return html;
    }

    function render_add_filter_buttons( $for = 'item' ) {
        var html = '';
        html += '<div class="mls-on-the-fly-filter-buttons">'
                + '<a class="mls-on-the-fly-filter-add-group" href="#">'+ mls_on_the_fly_global_filters_data.icons.folder + mls_on_the_fly_global_filters_data.i18n.add_group +'</a>'
                + '<a class="mls-on-the-fly-filter-add-condition" href="#">'+ mls_on_the_fly_global_filters_data.icons.filter + mls_on_the_fly_global_filters_data.i18n.add_filter +'</a>'
            +'</div>';

        return html;
    }


    function get_rendered_group_items( group ) {

        var data = [];
        var $group_items = $(group).find(' > li');
        $.each($group_items,function(i,group_item){
            var id = $(group_item).attr('id');
            if($(group_item).hasClass('mls-on-the-fly-filter-group')) {
                data.push({
                    'name': $(group_item).find('.mls-on-the-fly-filter-group-title').val(),
                    'relation': $(group_item).data('relation'),
                    'items': get_rendered_group_items(
                        $(group_item).find(' > .mls-on-the-fly-filter-group-items')
                    )
                });
            }else if($(group_item).hasClass('mls-on-the-fly-filter-group-item-wrapper')){
                data.push(
                    $(group_item).find('.mls-on-the-fly-filter-group-item').val()
                );
            }
        });

        return data;
    }


    function get_filters_text( filters, relation = 'and' ) {
        var group_filter_values = [];
        $.each(filters,function(i,filter){

            if('undefined' !== typeof filter.items){
                group_filter_values.push(
                    get_filters_text(filter.items, filter.relation)
                );
            }else{

                group_filter_values.push("( " + filter + " )");
            }
        });

        return "(" + group_filter_values.join( " " + relation + " ") + ")";
    }

    function render_filters_init() {

        var $groups = $rendered_filters_wrapper.find('#mls-on-the-fly-filter-groups');
        var filters = get_rendered_group_items( $groups );
        var filters_text = get_filters_text(filters);

        $('#mls-on-the-fly-global-filters-input-json').val(JSON.stringify(filters));
        $('#mls-on-the-fly-global-filters-input').val(filters_text);
        $('#mls-on-the-fly-global-filters-input-value').val(filters_text);
    }

    function get_rendered_filters_parent_group_items_wrapper( el, level = 1 ) {

        $wrap = $(el).parents('.mls-on-the-fly-filter-group-items').get( level - 1 );
        return 'undefined' !== typeof $wrap ? $( $wrap ) : $(el).closest('#mls-on-the-fly-filter-groups');
    }

    function get_rendered_filters_item_wrapper ( el ) {

        return $(el).closest('.mls-on-the-fly-filter-group-item-wrapper').length
            ? $(el).closest('.mls-on-the-fly-filter-group-item-wrapper')
            : $(el).closest('.mls-on-the-fly-filter-group');
    }

    function get_group_titles(){

        var titles = [];
        $.each($('#mls-on-the-fly-filter-groups .mls-on-the-fly-filter-group-title'), function(i,input){
            titles.push($(input).val());
        });

        return titles;
    }

    function item_actions_handler(e){

        var $target = $(e.target);

        if( 0 === $target.closest('.mls-on-the-fly-item-actions').length && ! $target.hasClass('mls-on-the-fly-input-open-actions') ) {
            $('.mls-on-the-fly-field-wrapper.mls-on-the-fly-open-actions').removeClass('mls-on-the-fly-open-actions');
        }

        if( ! ( $target.closest('.mls-on-the-fly-item-actions').length || $target.closest('.mls-on-the-fly-filter-buttons').length || $target.hasClass('mls-on-the-fly-input-open-actions') ) ) {
            return;
        }

        if( ! ($target.is('input[type="radio"]') || $target.is('input[type="checkbox"]')) ){
            e.preventDefault();
        }

        var $wrap = get_rendered_filters_item_wrapper( $target );
        if( $target.hasClass('mls-on-the-fly-input-action-trash') ) {
            if( $wrap.length ) {
                $wrap.remove();
            }
        } else if( $target.hasClass('mls-on-the-fly-input-action-duplicate') ) {

            var $parent_items_wrap = get_rendered_filters_parent_group_items_wrapper($target);
            var item = $wrap.removeClass('mls-on-the-fly-open-actions').prop('outerHTML');
            if( $wrap.length && item.length ) {
                $parent_items_wrap.append(item);
            }
        } else if( $target.hasClass('mls-on-the-fly-input-action-ungroup') ) {
            var $parent_items_wrap = get_rendered_filters_parent_group_items_wrapper($target,2);
            var item = $wrap.removeClass('mls-on-the-fly-open-actions').prop('outerHTML');
            if( $wrap.length && item.length ) {
                $wrap.remove();
                $parent_items_wrap.append(item);
            }
        } else if( $target.hasClass('mls-on-the-fly-input-action-edit') ) {

            $target_group = '';
            var $input = $wrap.find('.mls-on-the-fly-filter-group-item');
            $target_input = $input;

            $add_condition_modal.find('[name="mls-on-the-fly-global-filters[condition]"]').val(
                $input.data('condition')
            ).trigger('change');

            $add_condition_modal.find('[name="mls-on-the-fly-global-filters[compared_value]"]').val(
                $input.data('value')
            ).trigger('change');

            $add_condition_modal.find('[name="mls-on-the-fly-global-filters[field]"]').val(
                $input.data('key')
            ).trigger('change');

            $add_condition_modal.modal('show');
        } else if( $target.hasClass('mls-on-the-fly-filter-add-group') ) {
            var $parent_items_wrap = get_rendered_filters_parent_group_items_wrapper($target);
            $parent_items_wrap.append(
                render_group('',{})
            );
            $([document.documentElement, document.body]).animate({
                scrollTop: $parent_items_wrap.find('.mls-on-the-fly-filter-group:last-child').offset().top
            }, 1000);
        } else if( $target.hasClass('mls-on-the-fly-filter-add-condition') ) {
            $target_input = '';
            $target_group = get_rendered_filters_parent_group_items_wrapper($target);
            $add_condition_modal.modal('show');
        } else if( $target.hasClass('relation') ) {

            var relation = $($target).val();
            $wrap.find('.mls-on-the-fly-group-relation input.relation').prop('checked',false)
            $($target).prop('checked',true);
            $($target).closest('.mls-on-the-fly-filter-group').data('relation', relation);

            $wrap.find('> .mls-on-the-fly-filter-group-title-wrapper .mls-on-the-fly-filter-group-relation > span').removeClass('show');
            $wrap.find('> .mls-on-the-fly-filter-group-title-wrapper .mls-on-the-fly-filter-group-relation > span.mls-on-the-fly-filter-group-relation-' + relation).addClass('show');
        } else if( $target.hasClass('mls-on-the-fly-input-open-actions') ) {
            var is_open = $($target).closest('.mls-on-the-fly-field-wrapper').hasClass('mls-on-the-fly-open-actions');
            $('.mls-on-the-fly-field-wrapper.mls-on-the-fly-open-actions').removeClass('mls-on-the-fly-open-actions');

            if(!is_open){
                $($target).closest('.mls-on-the-fly-field-wrapper').addClass('mls-on-the-fly-open-actions');
            }
        }

        render_filters_init();
    }

    function add_filter_form_handler(e) {
        e.preventDefault();

        var $form = $(this);
        var field = $('[name="mls-on-the-fly-global-filters[field]"]').val();
        var odata_condition = $('[name="mls-on-the-fly-global-filters[odata_condition]"]').val();
        var compared_value = $('[name="mls-on-the-fly-global-filters[compared_value]"]').val();

        var value = `${field} ${odata_condition} '${compared_value}'`;
        if( $target_input.length ) {
            $target_input.val(value).trigger('change');
        } else if( $target_group.length ) {
            $target_group.append(
                render_item('', value)
            );

            $([document.documentElement, document.body]).animate({
                scrollTop: $target_group.find('.mls-on-the-fly-filter-group-item-wrapper:last-child').offset().top
            }, 1000);
        }

        $form.closest('.mls-on-the-fly-modal').modal('hide');

        render_filters_init();
    }

    function change_input_handler(e){

        $input = $(e.currentTarget);
        if($input.hasClass('mls-on-the-fly-filter-group-item')){
            data = get_item_data($input.val());
            $input.data('key', data['key']);
            $input.data('condition', data['condition']);
            $input.data('value', data['value']);
        }

        render_filters_init();
    }

    function input_rf_field_change_handler(e){

        var $form = $(e.currentTarget).closest('form');
        var field_id = $form.find('[name="mls-on-the-fly-global-filters[field]"]').val();
        var condition = $form.find('[name="mls-on-the-fly-global-filters[odata_condition]"]').val();

        $form.css('opacity','.5');
        $.ajax({
            url: mlsOnTheFlyAjax.ajax_url,
            type: 'post',
            data: {
                action: 'mls_on_the_fly_get_rf_field_values',
                field_id: field_id,
                nonce: mlsOnTheFlyAjax.nonce
            },
            success: function (response) {
                if (response.success) {
                    if(response.data.items) {
                        var options = '';
                        $.each(response.data.items,function(key, value){
                            options += '<option value="'+key+'">'+ value +'</option>';
                        });

                        var $values_input = $form.find('[name="mls-on-the-fly-global-filters[compared_value]"]');
                        var old_value = $values_input.val();
                        if( options.length ) {

                            if($values_input.is('select')) {
                                $values_input.html(options);
                            }else{
                                $values_input.replaceWith('<select id="'+ $values_input.attr('id') +'" name="'+ $values_input.attr('name') +'">' + options + '</select>');
                                $('select[name="mls-on-the-fly-global-filters[compared_value]"]').select2();

                                var hidden_conditions = [
                                    'ne',
                                    'gt',
                                    'ge',
                                    'lt',
                                    'le',
                                ];

                                $.each(hidden_conditions,function(i,condition){
                                    $('[name="mls-on-the-fly-global-filters[odata_condition]"] option[value="'+ condition +'"]').addClass('hidden').prop('disabled',true);
                                });
                            }
                        }else{
                            $('select[name="mls-on-the-fly-global-filters[compared_value]"]').select2('destroy');
                            $values_input.replaceWith('<input type="text" id="'+ $values_input.attr('id') +'" name="'+ $values_input.attr('name') +'"/>' );

                            $('[name="mls-on-the-fly-global-filters[odata_condition]"] option.hidden').removeClass('hidden').prop('disabled',false);
                        }

                        $form.find('[name="mls-on-the-fly-global-filters[odata_condition]"]').select2();

                        if( $form.find('[name="mls-on-the-fly-global-filters[compared_value]"]').is('select') && $form.find('[name="mls-on-the-fly-global-filters[compared_value]"]').find('option[value="' + old_value + '"]').length ){
                            $form.find('[name="mls-on-the-fly-global-filters[compared_value]"]').val(old_value).trigger('change');
                        }
                    }

                    $form.css('opacity','1');
                } else {

                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // $message.html('<div class="notice notice-error is-dismissible"><p>Error: ' + textStatus + ' - ' + errorThrown + '</p></div>').show();
            }
        });
    }

    return {
        'init': function(options) {
            $('.mls-on-the-fly-rendered-filters-wrapper').html(
                render_groups(options)
            );

            render_filters_init();

            $rendered_filters_wrapper.on('click', item_actions_handler);
            $add_condition_modal.find('#mls-on-the-fly-add-filter-modal-form').on('submit',add_filter_form_handler);
            $add_condition_modal.find('[name="mls-on-the-fly-global-filters[field]"]').on('change', input_rf_field_change_handler);

            $rendered_filters_wrapper.on('change', '.mls-on-the-fly-filter-group-title, .mls-on-the-fly-filter-group-item', change_input_handler);
        },
    }
})(jQuery);


jQuery(document).ready(function ($) {

    var groups_data = $('#mls-on-the-fly-global-filters-input-json').length && $('#mls-on-the-fly-global-filters-input-json').val().length ? JSON.parse( $('#mls-on-the-fly-global-filters-input-json').val() ) : [];
    if($('#mls-on-the-fly-global-filters-input-json').length) {
        mls_on_the_fly_global_filters.init(groups_data);
    }
});
