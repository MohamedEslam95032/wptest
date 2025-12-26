<?php 
$currencies = Houzez_Currencies::get_form_fields(); 
$add_currency = Houzez_Currencies::currency_add_link();
?>

<!-- Statistics Cards -->
<div class="houzez-stats-grid">
	<div class="houzez-stat-card">
		<div class="houzez-stat-icon">
			<i class="dashicons dashicons-money-alt"></i>
		</div>
		<div class="houzez-stat-content">
			<h3><?php echo $currencies ? count($currencies) : 0; ?></h3>
			<p><?php esc_html_e('Total Currencies', 'houzez-theme-functionality'); ?></p>
		</div>
	</div>
	<div class="houzez-stat-card">
		<div class="houzez-stat-icon">
			<i class="dashicons dashicons-admin-site-alt3"></i>
		</div>
		<div class="houzez-stat-content">
			<h3><?php echo $currencies ? count(array_filter($currencies, function($currency) { return $currency->currency_position === 'before'; })) : 0; ?></h3>
			<p><?php esc_html_e('Before Position', 'houzez-theme-functionality'); ?></p>
		</div>
	</div>
	<div class="houzez-stat-card">
		<div class="houzez-stat-icon">
			<i class="dashicons dashicons-admin-site-alt"></i>
		</div>
		<div class="houzez-stat-content">
			<h3><?php echo $currencies ? count(array_filter($currencies, function($currency) { return $currency->currency_position === 'after'; })) : 0; ?></h3>
			<p><?php esc_html_e('After Position', 'houzez-theme-functionality'); ?></p>
		</div>
	</div>
</div>

<!-- Main Content Card -->
<div class="houzez-main-card">
	<div class="houzez-card-header">
		<h2>
			<i class="dashicons dashicons-money-alt"></i>
			<?php esc_html_e('Currencies Management', 'houzez-theme-functionality'); ?>
		</h2>
		<div class="houzez-status-badge houzez-status-success">
			<?php esc_html_e('Active', 'houzez-theme-functionality'); ?>
		</div>
	</div>
	
	<div class="houzez-card-body">
		<?php if($currencies && !empty($currencies)): ?>
		<div class="houzez-fields-table-wrapper">
			<table class="houzez-fields-table">
	<thead>
		<tr>
						<th class="currency-name-col">
							<i class="dashicons dashicons-tag"></i>
							<?php esc_html_e('Currency Name', 'houzez-theme-functionality'); ?>
						</th>
						<th class="currency-code-col">
							<i class="dashicons dashicons-admin-network"></i>
							<?php esc_html_e('Code', 'houzez-theme-functionality'); ?>
						</th>
						<th class="currency-symbol-col">
							<i class="dashicons dashicons-money-alt"></i>
							<?php esc_html_e('Symbol', 'houzez-theme-functionality'); ?>
						</th>
						<th class="currency-position-col">
							<i class="dashicons dashicons-admin-generic"></i>
							<?php esc_html_e('Position', 'houzez-theme-functionality'); ?>
						</th>
						<th class="currency-decimal-col">
							<i class="dashicons dashicons-editor-ol"></i>
							<?php esc_html_e('Decimals', 'houzez-theme-functionality'); ?>
						</th>
						<th class="currency-separators-col">
							<i class="dashicons dashicons-admin-tools"></i>
							<?php esc_html_e('Separators', 'houzez-theme-functionality'); ?>
						</th>
						<th class="currency-actions-col">
							<i class="dashicons dashicons-admin-tools"></i>
							<?php esc_html_e('Actions', 'houzez-theme-functionality'); ?>
						</th>
		</tr>
	</thead>
	<tbody>
		<?php
					foreach ( $currencies as $data ) { 
				$edit_link = Houzez_Currencies::currency_edit_link( $data->id );
				$delete_link = Houzez_Currencies::currency_delete_link( $data->id );
				?>
						<tr class="currency-row">
							<td class="currency-name">
								<div class="currency-info">
									<strong><?php echo esc_html($data->currency_name); ?></strong>
								</div>
							</td>
							<td class="currency-code">
								<div class="currency-code-wrapper">
									<input type="text" onfocus="this.select();" readonly="readonly" 
										   value="<?php echo esc_attr($data->currency_code); ?>" 
										   class="field-id-input">
									<button type="button" class="copy-btn" title="<?php esc_attr_e('Copy to clipboard', 'houzez-theme-functionality'); ?>">
										<i class="dashicons dashicons-admin-page"></i>
									</button>
								</div>
							</td>
							<td class="currency-symbol">
								<span class="currency-symbol-badge">
									<?php echo esc_html($data->currency_symbol); ?>
								</span>
							</td>
							<td class="currency-position">
								<span class="currency-position-badge currency-position-<?php echo esc_attr($data->currency_position); ?>">
									<?php echo esc_html(ucfirst($data->currency_position)); ?>
								</span>
							</td>
							<td class="currency-decimal">
								<span class="currency-decimal-count">
									<?php echo esc_html($data->currency_decimal); ?>
								</span>
							</td>
							<td class="currency-separators">
								<div class="separators-info">
									<small>
										<strong><?php esc_html_e('Decimal:', 'houzez-theme-functionality'); ?></strong> 
										<?php echo esc_html($data->currency_decimal_separator); ?>
										<br>
										<strong><?php esc_html_e('Thousands:', 'houzez-theme-functionality'); ?></strong> 
										<?php echo esc_html($data->currency_thousand_separator); ?>
									</small>
								</div>
							</td>
							<td class="currency-actions">
								<div class="action-buttons">
									<a href="<?php echo esc_url($edit_link); ?>" class="action-btn edit-btn"
									   title="<?php esc_attr_e('Edit currency', 'houzez-theme-functionality'); ?>">
										<i class="dashicons dashicons-edit"></i>
						</a>
									<a href="<?php echo esc_url($delete_link); ?>" class="action-btn delete-btn"
									   title="<?php esc_attr_e('Delete currency', 'houzez-theme-functionality'); ?>"
									   onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this currency?', 'houzez-theme-functionality'); ?>');">
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
				<i class="dashicons dashicons-money-alt"></i>
			</div>
			<h3><?php esc_html_e('No Currencies Found', 'houzez-theme-functionality'); ?></h3>
			<p><?php esc_html_e('Start adding currencies to support multiple currency options for your properties.', 'houzez-theme-functionality'); ?></p>
			<a href="<?php echo esc_url($add_currency); ?>" class="houzez-btn houzez-btn-primary">
				<i class="dashicons dashicons-plus"></i>
				<?php esc_html_e('Add Your First Currency', 'houzez-theme-functionality'); ?>
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
		var currencyCode = $input.val();
		
		// Create a temporary textarea to copy the text
		var $temp = $('<textarea>');
		$('body').append($temp);
		$temp.val(currencyCode).select();
		
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
