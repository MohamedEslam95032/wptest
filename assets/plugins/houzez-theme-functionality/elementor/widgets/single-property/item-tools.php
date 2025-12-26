<?php
namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Item_Tools extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
	
	public function get_name() {
		return 'houzez-property-tools';
	}

	public function get_title() {
		return __( 'Share, Favorite', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon eicon-favorite';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-listing')  {
            return ['houzez-single-property-builder']; 
        }

		return [ 'houzez-single-property' ];
	}

	public function get_keywords() {
		return [ 'houzez', 'share', 'favourite', 'favorite', 'property' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_share_content',
			[
				'label' => __( 'Content', 'houzez-theme-functionality' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
            'show_share',
            [
                'label' => esc_html__( 'Share Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_favorite',
            [
                'label' => esc_html__( 'Favorite Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_print',
            [
                'label' => esc_html__( 'Print Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );		

		$this->end_controls_section();

		$this->start_controls_section(
			'section_share_style',
			[
				'label' => __( 'Content', 'houzez-theme-functionality' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
            'buttons_bg_color',
            [
                'label'     => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_bg_color_hover',
            [
                'label'     => esc_html__( 'Background Color Hover', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_color_hover',
            [
                'label'     => esc_html__( 'Color Hover', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_border_color',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_border_color_hover',
            [
                'label'     => esc_html__( 'Border Color Hover', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );	
		
        $this->add_responsive_control(
			'share_align',
			[
				'label' => __( 'Alignment', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'right',
				'options' => [
					'flex-start' => [
                        'title' => __( 'Left', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => __( 'Right', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-right',
                    ],
				],
				'selectors' => [
					'{{WRAPPER}} ul.ele-item-tools' => 'justify-content: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();

	}

	protected function render() {
		
		global $post; 
		$settings = $this->get_settings();

        $this->single_property_preview_query(); // Only for preview

		$key = '';
		$userID      =   get_current_user_id();
		$fav_option = 'houzez_favorites-'.$userID;
		$fav_option = get_option( $fav_option );
		if( !empty($fav_option) ) {
		    $key = array_search($post->ID, $fav_option);
		}

		$icon = '';
		if( $key != false || $key != '' ) {
		    $icon = 'text-danger';
		}

		?>
		<ul class="ele-item-tools property-item-tools list-unstyled d-flex gap-1 m-0 p-0">

		    <?php if( $settings['show_favorite'] == 'yes' ) { ?>
		    <li class="item-tool text-center houzez-favorite">
		        <span class="add-favorite-js item-tool-favorite text-center" data-listid="<?php echo intval($post->ID)?>">
		            <i class="houzez-icon icon-love-it <?php echo esc_attr($icon); ?>"></i>
		        </span><!-- item-tool-favorite -->
		    </li><!-- item-tool -->
		    <?php } ?>

		    <?php if( $settings['show_share'] == 'yes' ) { ?>
		    <li class="item-tool text-center houzez-share">
		        <span class="item-tool-share text-center" data-bs-toggle="dropdown">
		            <i class="houzez-icon icon-share"></i>
		        </span><!-- item-tool-favorite -->
		        <div class="dropdown-menu dropdown-menu-end item-tool-dropdown-menu">
		            <?php get_template_part('property-details/partials/share'); ?>
		        </div>
		    </li><!-- item-tool -->
		    <?php } ?>
			
			<?php if( $settings['show_print'] == 'yes') {?>
    		<li class="item-tool text-center houzez-print" data-propid="<?php echo intval($post->ID); ?>">
        		<span class="item-tool-compare text-center">
            		<i class="houzez-icon icon-print-text"></i>
        		</span><!-- item-tool-compare -->
    		</li><!-- item-tool -->
    		<?php } ?>	
			
		</ul><!-- item-tools -->
		
		<?php
		$this->reset_preview_query(); // Only for preview
	}

	public function render_plain_content() {}
}
Plugin::instance()->widgets_manager->register( new Houzez_Item_Tools );