<?php $fields = Houzez_Fields_Builder::get_form_fields(); ?>

<!-- Statistics Cards -->
<div class="houzez-stats-grid">
	<div class="houzez-stat-card">
		<div class="houzez-stat-icon">
			<i class="dashicons dashicons-admin-settings"></i>
		</div>
		<div class="houzez-stat-content">
			<h3><?php echo $fields ? count($fields) : 0; ?></h3>
			<p><?php esc_html_e('Custom Fields', 'houzez-theme-functionality'); ?></p>
		</div>
	</div>
	<div class="houzez-stat-card">
		<div class="houzez-stat-icon">
			<i class="dashicons dashicons-search"></i>
		</div>
		<div class="houzez-stat-content">
			<h3><?php echo $fields ? count(array_filter($fields, function($field) { return $field->is_search === 'yes'; })) : 0; ?></h3>
			<p><?php esc_html_e('Search Fields', 'houzez-theme-functionality'); ?></p>
		</div>
	</div>
</div>

<!-- Main Content Card -->
<div class="houzez-main-card">
	<div class="houzez-card-header">
		<h2>
			<i class="dashicons dashicons-admin-settings"></i>
			<?php esc_html_e('Custom Fields Management', 'houzez-theme-functionality'); ?>
		</h2>
		<div class="houzez-status-badge houzez-status-success">
			<?php esc_html_e('Active', 'houzez-theme-functionality'); ?>
		</div>
	</div>
	
	<div class="houzez-card-body">
		<?php if($fields && !empty($fields)): ?>
		<div class="houzez-fields-table-wrapper">
			<table class="houzez-fields-table">
	<thead>
		<tr>
						<th class="field-name-col">
							<i class="dashicons dashicons-tag"></i>
							<?php esc_html_e('Field Name', 'houzez-theme-functionality'); ?>
						</th>
						<th class="field-id-col">
							<i class="dashicons dashicons-admin-network"></i>
							<?php esc_html_e('Field ID', 'houzez-theme-functionality'); ?>
						</th>
						<th class="field-type-col">
							<i class="dashicons dashicons-admin-generic"></i>
							<?php esc_html_e('Type', 'houzez-theme-functionality'); ?>
						</th>
						<th class="field-search-col">
							<i class="dashicons dashicons-search"></i>
							<?php esc_html_e('Search', 'houzez-theme-functionality'); ?>
						</th>
						<th class="field-actions-col">
							<i class="dashicons dashicons-admin-tools"></i>
							<?php esc_html_e('Actions', 'houzez-theme-functionality'); ?>
						</th>
		</tr>
	</thead>
	<tbody>
		<?php
					foreach ( $fields as $data ) { 
				$edit_link = Houzez_Fields_Builder::field_edit_link( $data->id );
				$delete_link = Houzez_Fields_Builder::field_delete_link( $data->id );
				$field_title = stripslashes($data->label);
				$field_title = houzez_wpml_translate_single_string($field_title);	
						$field_types = Houzez_Fields_Builder::get_field_types();
						?>
						<tr class="field-row">
							<td class="field-name">
								<div class="field-info">
									<strong><?php echo esc_html($field_title); ?></strong>
									<?php if(!empty($data->placeholder)): ?>
										<div class="field-placeholder">
											<small><?php echo esc_html($data->placeholder); ?></small>
										</div>
									<?php endif; ?>
								</div>
							</td>
							<td class="field-id">
								<div class="field-id-wrapper">
									<input type="text" onfocus="this.select();" readonly="readonly" 
										   value="<?php echo 'fave_'.esc_attr($data->field_id); ?>" 
										   class="field-id-input">
									<button type="button" class="copy-btn" title="<?php esc_attr_e('Copy to clipboard', 'houzez-theme-functionality'); ?>">
										<i class="dashicons dashicons-admin-page"></i>
									</button>
								</div>
							</td>
							<td class="field-type">
								<span class="field-type-badge field-type-<?php echo esc_attr($data->type); ?>">
									<?php echo isset($field_types[$data->type]) ? esc_html($field_types[$data->type]) : esc_html(ucfirst($data->type)); ?>
								</span>
					</td>
							<td class="field-search">
								<?php if($data->is_search === 'yes'): ?>
									<span class="search-enabled">
										<i class="dashicons dashicons-yes-alt"></i>
									</span>
								<?php else: ?>
									<span class="search-disabled">
										<i class="dashicons dashicons-dismiss"></i>
									</span>
								<?php endif; ?>
					</td>
							<td class="field-actions">
								<div class="action-buttons">
									<a href="<?php echo esc_url($edit_link); ?>" class="action-btn edit-btn"
									   title="<?php esc_attr_e('Edit field', 'houzez-theme-functionality'); ?>">
										<i class="dashicons dashicons-edit"></i>
						</a>
									<a href="<?php echo esc_url($delete_link); ?>" class="action-btn delete-btn"
									   title="<?php esc_attr_e('Delete field', 'houzez-theme-functionality'); ?>"
									   onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this field?', 'houzez-theme-functionality'); ?>');">
										<i class="dashicons dashicons-trash"></i>
						</a>
								</div>
					</td>
				</tr>
				<?php		
		}
		?>
	</tbody>
</table>
		</div>
		<?php else: ?>
		<div class="houzez-empty-state">
			<div class="empty-state-icon">
				<i class="dashicons dashicons-admin-settings"></i>
			</div>
			<h3><?php esc_html_e('No Custom Fields Found', 'houzez-theme-functionality'); ?></h3>
			<p><?php esc_html_e('Start building your custom fields to enhance your property listings.', 'houzez-theme-functionality'); ?></p>
			<a href="<?php echo esc_url(Houzez_Fields_Builder::field_add_link()); ?>" class="houzez-btn houzez-btn-primary">
				<i class="dashicons dashicons-plus"></i>
				<?php esc_html_e('Create Your First Field', 'houzez-theme-functionality'); ?>
			</a>
		</div>
		<?php endif; ?>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	// Copy to clipboard functionality
	$('.copy-btn').on('click', function(e) {
		e.preventDefault();
		
		var $button = $(this);
		var $input = $button.siblings('.field-id-input');
		var fieldId = $input.val();
		
		// Create a temporary textarea to copy the text
		var $temp = $('<textarea>');
		$('body').append($temp);
		$temp.val(fieldId).select();
		
		try {
			// Copy to clipboard
			document.execCommand('copy');
			
			// Show success feedback
			var originalIcon = $button.find('.dashicons').attr('class');
			$button.find('.dashicons').removeClass().addClass('dashicons dashicons-yes-alt');
			$button.css('background', '#28a745');
			
			// Show tooltip
			$button.attr('title', '<?php esc_attr_e('Copied!', 'houzez-theme-functionality'); ?>');
			
			// Reset after 2 seconds
			setTimeout(function() {
				$button.find('.dashicons').removeClass().addClass(originalIcon);
				$button.css('background', '');
				$button.attr('title', '<?php esc_attr_e('Copy to clipboard', 'houzez-theme-functionality'); ?>');
			}, 2000);
			
		} catch (err) {
			// Fallback: select the text for manual copy
			$input.select();
			$input.focus();
			
			// Show fallback message
			$button.attr('title', '<?php esc_attr_e('Please copy manually', 'houzez-theme-functionality'); ?>');
		}
		
		// Remove temporary textarea
		$temp.remove();
	});
	
	// Also allow clicking on the input field to select all text
	$('.field-id-input').on('click', function() {
		$(this).select();
	});
});
</script>