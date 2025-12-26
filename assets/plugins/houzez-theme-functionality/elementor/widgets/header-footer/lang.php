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
 * Site Language Switcher Widget.
 * @since 1.0.0
 */
class Houzez_Lang_Switcher extends Widget_Base {

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
        return 'houzez_language_switcher';
    }

    /**
     * Get widget title.
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Language', 'houzez' );
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
        return [ 'Language', 'Switcher', 'WPML' ];
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
				'label' => __( 'Content', 'houzez' ),
			]
		);

		$this->add_control(
			'important_note',
			[
				'type' => 'raw_html',
				'raw' => esc_html__('You need Polylang or WPML plugin for this to work', 'houzez-theme-functionality'),
				'content_classes' => 'elementor-control-field-description',
			]
		);

        $this->add_responsive_control(
            'padding_vertical_lang',
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
                    '{{WRAPPER}} .houzez-lang-ele' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'padding_horizontal_lang',
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
                    '{{WRAPPER}} .houzez-lang-ele' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'lang_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'exclude' => [],
                'selector' => '{{WRAPPER}} .houzez-lang-ele > a',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'lang_color',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .houzez-lang-ele > a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'lang_color_hover',
            [
                'label' => esc_html__( 'Color :hover', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .houzez-lang-ele > a:hover' => 'color: {{VALUE}}',
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

        $current_lang = 'Languages';
        $flag         = null;
        $languages    = null;

        // Poly Lang & WPML 
        if ( function_exists( 'pll_the_languages' ) ) {
            $languages = pll_the_languages( array( 'raw' => 1 ) );
            foreach ( $languages as $lang ) {
                if ( $lang['current_lang'] ) {
                    $flag         = '<i class="image-icon"><img src="' . $lang['flag'] . '" alt="' . $lang['name'] . '"/></i>';
                    $current_lang = $lang['name'];
                }
            }
        } elseif ( function_exists( 'icl_get_languages' ) ) {
            $languages = icl_get_languages();
            foreach ( $languages as $lang ) {
                if ( $lang['active'] ) {
                    $flag         = '<i class="image-icon"><img src="' . $lang['country_flag_url'] . '" alt="' . $lang['native_name'] . '"/></i>';
                    $current_lang = $lang['native_name'];
                }
            }
        }
		?>
		<div class="houzez-lang-ele">
            <a class="btn dropdown-toggle" href="#" role="button" id="languageDropdown" data-bs-toggle="dropdown" aria-bs-haspopup="true" aria-bs-expanded="false">
                <i class="houzez-icon houzez-icon-earth-1"></i> 
                <?php //echo $flag; ?>
                <?php echo $current_lang; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-bs-labelledby="languageDropdown">
                <?php
                if ( $languages && function_exists( 'pll_the_languages' ) ) { 
                    
                    foreach ( $languages as $lang ) {
                        if ( $lang['current_lang'] ) $current = 'class="active"';
                        echo '<a class="dropdown-item" href="' . $lang['url'] . '" hreflang="' . $lang['slug'] . '"><i class="icon-image"><img src="' . $lang['flag'] . '" alt="' . $lang['name'] . '"/></i> ' . $lang['name'] . '</a>';
                    }

                } elseif ( $languages && function_exists( 'icl_get_languages' ) ) { 
                    foreach ( $languages as $lang ) {
                        $current = '';
                        echo '<a class="dropdown-item" href="' . $lang['url'] . '" hreflang="' . $lang['language_code'] . '"><i class="icon-image"><img src="' . $lang['country_flag_url'] . '" alt="' . $lang['native_name'] . '"/></i> ' . $lang['native_name'] . '</a>';
                    }
                }
                if ( ! function_exists( 'pll_the_languages' ) && ! function_exists( 'icl_get_languages' ) ) {
                    echo '<a class="dropdown-item">You need Polylang or WPML plugin for this to work</a>';
                }
                ?>
            </div>
        </div>
		<?php
		
    }

}
Plugin::instance()->widgets_manager->register( new Houzez_Lang_Switcher );