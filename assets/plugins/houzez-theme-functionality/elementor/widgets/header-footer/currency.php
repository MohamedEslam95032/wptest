<?php
namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Site Currency Switcher Widget.
 * @since 1.0.0
 */
class Houzez_Currency_Switcher extends Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'houzez_currency_switcher';
    }

    /**
     * Get widget title.
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Currency Switcher', 'houzez-theme-functionality' );
    }

    /**
     * Get widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'houzez-element-icon eicon-site-search';
    }

    public function get_keywords() {
        return [ 'Currency', 'Switcher' ];
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        // Check if the current post type is 'fts_builder'
        if (get_post_type() === 'fts_builder') {
            // Get the template type of the current post
            $template_type = htb_get_template_type(get_the_ID());

            // Check if the template type is 'tmp_header' or 'tmp_footer'
            if ($template_type === 'tmp_header' || $template_type === 'tmp_footer') {
                // Return the specific category for header and footer builders
                return ['houzez-header-footer-builder'];
            }
        }
        
        // Return the default categories
        return ['houzez-elements', 'houzez-header-footer'];
    }

    /**
     * Register widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
	protected function register_controls() {
		$this->register_general_controls();
	}

	

	/**
	 * Register Site Logo Controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_general_controls() {
		
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'houzez-theme-functionality' ),
			]
		);

        $this->add_control(
            'important_note',
            [
                'type' => 'raw_html',
                'raw' => esc_html__('You need enable it under Theme Options -> Top Bar -> Currency Switcher', 'houzez-theme-functionality'),
                'content_classes' => 'elementor-control-field-description',
            ]
        );

		$this->add_responsive_control(
            'padding_vertical_currency',
            [
                'label' => esc_html__( 'Vertical Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .currency-switcher-wrap' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'padding_horizontal_currency',
            [
                'label' => esc_html__( 'Horizontal Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .currency-switcher-wrap' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section( 'currency_toggle',
            [
                'label' => esc_html__( 'Currency', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'currency_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'exclude' => [],
                'selector' => '{{WRAPPER}} .currency-switcher-wrap button',
                'separator' => 'before',
            ]
        );

        $this->start_controls_tabs( 'tabs_currency_style' );

        $this->start_controls_tab(
            'tab_currency_style_normal',
            [
                'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'currency_color',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .currency-switcher-wrap button' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_currency_style_hover',
            [
                'label' => esc_html__( 'Hover', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'currency_color_hover',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .currency-switcher-wrap button:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'currency_dropdown_heading',
            [
                'label' => esc_html__( 'Currency Dropdown', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'dropdown_top_margin',
            [
                'label' => esc_html__( 'Position from Top', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'selectors' => [
                    '{{WRAPPER}} .switcher-wrap .dropdown-menu' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'currency_dropdown_typography',
                'exclude' => [],
                'selector' => '{{WRAPPER}} .currency-switcher-wrap ul li',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'cur_dropdown_bg_color',
            [
                'label' => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .switcher-wrap .dropdown-menu' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'cur_dropdowncolor',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .switcher-wrap ul.dropdown-menu li' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'cur_dropdowncolorhover',
            [
                'label' => esc_html__( 'Color :hover', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .switcher-wrap ul.dropdown-menu li:hover' => 'color: {{VALUE}}',
                ],
            ]
        );
	
		$this->end_controls_section();
	}

	

	/**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {
    	$settings = $this->get_settings_for_display();
		
        $currency_switcher_enable = houzez_option('currency_switcher_enable');
        $is_multi_currency = houzez_option('multi_currency');
        if( $currency_switcher_enable != 0 && $is_multi_currency != 1 ) {
            if (class_exists('FCC_Rates')) {

                $supported_currencies = houzez_get_list_of_supported_currencies();

                if (0 < count($supported_currencies)) {

                    $current_currency = houzez_get_wpc_current_currency();
                    ?>
                    <div class="switcher-wrap currency-switcher-wrap">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-bs-haspopup="true" aria-bs-expanded="true"><span><?php echo esc_attr($current_currency); ?></span>
                        </button>
                        <ul id="hz-currency-switcher-list" class="dropdown-menu" aria-bs-labelledby="dropdown">
                            <?php
                            foreach ($supported_currencies as $currency_code) {
                                echo '<li data-currency-code="' . esc_attr($currency_code) . '">' . esc_attr($currency_code) . '</li>';
                            }
                            ?>
                        </ul>
                        <input type="hidden" id="hz-switch-to-currency" value="<?php echo esc_attr($current_currency); ?>" />
                    </div><!-- currency-switcher-wrap -->
                    <?php
                }
            }
        } else {
            ?>
            <div class="switcher-wrap currency-switcher-wrap">
                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-bs-haspopup="true" aria-bs-expanded="true"><span><?php esc_html_e('Currency', 'houzez-theme-functionality'); ?></span>
                </button>
                <ul id="hz-currency-switcher-list" class="dropdown-menu" aria-bs-labelledby="dropdown">
                    <li><?php esc_html_e('You need enable it under Theme Options -> Top Bar -> Currency Switcher', 'houzez-theme-functionality'); ?></li>
                </ul>
            </div><!-- currency-switcher-wrap -->
            <?php
        }
		
    }

}
Plugin::instance()->widgets_manager->register( new Houzez_Currency_Switcher );