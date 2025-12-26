<?php
/**
 * Elementor autocomplete controls
 *
 */
/*namespace Houzez\Elementor\Controls;

use Elementor\Base_Data_Control;*/

/*class Houzez_Autocomplete extends Base_Data_Control {*/
class Houzez_Autocomplete extends \Elementor\Base_Data_Control {

	public function get_type() {
		return 'houzez_autocomplete';
	}


	protected function get_default_settings() {
		return [
			'label_block' => true,
			'post_type'   => false,
			'taxonomy'    => false,
			'multiple'    => false,
			'options'     => [],
			'callback'    => '',
		];
	}

	/**
	 * Enqueue control scripts and styles.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function enqueue() {

		$js_path = 'assets/frontend/js/';

		wp_enqueue_script( 'houzez-autocomplete-ele', HOUZEZ_PLUGIN_URL . $js_path . 'autocomplete.js', [ 'jquery' ], '1.0.0', false );
	}

	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<div class="elementor-control-field houzez-custom-autocomplete-field">
			<label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-title">{{{ data.label
				}}}</label>
			<div class="elementor-control-input-wrapper elementor-control-unit-5">
				<# var multiple = ( data.multiple ) ? 'multiple' : ''; #>
				<select id="<?php echo esc_attr( $control_uid ); ?>" class="elementor-select2" type="select2" {{ multiple }} data-setting="{{ data.name }}" data-post-type="{{ data.post_type }}" data-taxonomy="{{ data.taxonomy }}" data-placeholder="&nbsp&nbsp<?php echo esc_attr__( 'Search', 'houzez' ); ?>">
					<# _.each( data.options, function( option_title, option_value ) {
					var value = data.controlValue;
					if ( typeof value == 'string' ) {
						var selected = ( option_value === value ) ? 'selected' : '';
					} else if ( null !== value ) {
						var value = _.values( value );
						var selected = ( -1 !== value.indexOf( option_value ) ) ? 'selected' : '';
					}
					#>
					<option {{ selected }} value="{{ option_value }}">{{{ option_title }}}</option>
					<# } ); #>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

}