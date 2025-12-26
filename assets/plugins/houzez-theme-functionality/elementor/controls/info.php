<?php

class Houzez_Info_note extends \Elementor\Base_Control {

	public function get_type() {
		return 'houzez-info-note';
	}


	protected function get_default_settings() {
		return [
			'label_block' => true,
		];
	}
	public function content_template() {
		?>
		<div class="houzez-info-control-wrap">
			<p style="font-size: 12px;
			line-height: 18px;
    font-style: italic;
    background-color: #f7f6d4;
    padding: 10px;
    border-left: 3px solid #e0c948;" class="houzez-info-control">{{{ data.label }}}</p>
		</div>
		<?php
	}

}