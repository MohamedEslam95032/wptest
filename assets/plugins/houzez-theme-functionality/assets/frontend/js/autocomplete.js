jQuery(window).on('elementor:init', function() {

	var houzezAutoComplete = elementor.modules.controls.BaseData.extend({
		
		isPostSearchReady: false,

		getPostsResults: function() { 
			var $this = this;
			var posts_slugs = this.getControlValue();

			if ( ! posts_slugs ) { return; }

			if ( !_.isArray(posts_slugs) ) {
				posts_slugs = [ posts_slugs ];
			}

			$this.addSpinnerControl();

			jQuery.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: $this.model.get('render_result'),
					post_slug: posts_slugs,
					post_type: $this.model.get('post_type'),
					taxonomy: $this.model.get('taxonomy'),
				},

				success: function(response) {
					$this.isPostSearchReady = true;
					$this.model.set('options', response);
					$this.render();
				},
			});
		},

		onReady: function() {
			var self = this;

			this.ui.select.select2({
				placeholder: 'Search',
				minimumInputLength: 1,
				allowClear: true,
	
				ajax: {
					url: ajaxurl,
					method: 'post',
					dataType: 'json',
					delay: 260,

					data: function( parameters ) {
						return {
							query: parameters.term, // search keyword
							post_type: self.model.get('post_type'),
							taxonomy: self.model.get('taxonomy'),
							action: self.model.get('make_search'),
						};
					},
					processResults: function( result ) {
						return {
							results: result,
						};
					},
					cache: true,
				},
			});

			if ( ! this.isPostSearchReady ) {
				this.getPostsResults();
			}
		},

		addSpinnerControl: function() {
			this.ui.select.prop('disabled', true);
			this.$el.find('.elementor-control-title').after('<span class="elementor-control-spinner">&nbsp;<i class="fa fa-spinner fa-spin"></i>&nbsp;</span>');
		},

		onBeforeDestroy: function() {
			if (this.ui.select.data('select2')) {
				this.ui.select.select2('destroy');
			}

			this.$el.remove();
		},
	});
	elementor.addControlView('houzez_autocomplete', houzezAutoComplete);
});
