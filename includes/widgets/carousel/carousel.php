<?php
namespace WPZOOMElementorWidgets;

use \Elementor\Widget_Base;
use Elementor\Group_Control_Background;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Utils;
use Elementor\Icons_Manager;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * ZOOM Elementor Widgets - Carousel Widget.
 *
 * Elementor widget that inserts a customizable carousel.
 *
 * @since 1.0.0
 */
class Carousel extends Widget_Base {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		if ( ! wp_style_is( 'slick-slider', 'registered' ) ) {
			wp_register_style( 'slick-slider', plugins_url( 'assets/slick/slick.css', dirname( __FILE__, 2 ) ), null, '1.0.0' );
		}

		if ( ! wp_style_is( 'slick-slider-theme', 'registered' ) ) {
			wp_register_style( 'slick-slider-theme', plugins_url( 'assets/slick/slick-theme.css', dirname( __FILE__, 2 ) ), null, '1.0.0' );
		}

		wp_register_style( 'zoom-elementor-widgets-css-frontend-carousel', plugins_url( 'frontend.css', __FILE__ ), [ 'slick-slider', 'slick-slider-theme' ], '1.0.0' );

		if ( ! wp_script_is( 'jquery-slick-slider', 'registered' ) ) {
			wp_register_script( 'jquery-slick-slider', plugins_url( 'assets/slick/slick.min.js', dirname( __FILE__, 2 ) ), [ 'jquery' ], '1.0.0', true );
		}

		wp_register_script( 'zoom-elementor-widgets-js-frontend-carousel', plugins_url( 'frontend.js', __FILE__ ), [ 'jquery', 'jquery-slick-slider' ], '1.0.0', true );
	}

	/**
	 * Get widget name.
	 *
	 * Retrieve widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'zoom-elementor-widgets-carousel';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Carousel', 'zoom-elementor-widgets' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-carousel';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'wpzoom-elementor-widgets' ];
	}

	/**
	 * Style Dependencies.
	 *
	 * Returns all the styles the widget depends on.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Style slugs.
	 */
	public function get_style_depends() {
		return [
			'slick-slider',
			'slick-slider-theme',
			'font-awesome-5-all',
			'font-awesome-4-shim',
			'zoom-elementor-widgets-css-frontend-carousel'
		];
	}

	/**
	 * Script Dependencies.
	 *
	 * Returns all the scripts the widget depends on.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Script slugs.
	 */
	public function get_script_depends() {
		return [
			'jquery',
			'jquery-slick-slider',
			'font-awesome-4-shim',
			'zoom-elementor-widgets-js-frontend-carousel'
		];
	}

	/**
	 * Register Controls.
	 *
	 * Registers all the controls for this widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	protected function _register_controls() {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	/**
	 * Register Content Controls.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	protected function register_content_controls() {
		$this->start_controls_section(
			'_section_slides',
			[
				'label' => __( 'Slides', 'zoom-elementor-widgets' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'type' => Controls_Manager::MEDIA,
				'label' => __( 'Image', 'zoom-elementor-widgets' ),
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'title',
			[
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'label' => __( 'Title', 'zoom-elementor-widgets' ),
				'placeholder' => __( 'Type title here', 'zoom-elementor-widgets' ),
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'subtitle',
			[
				'label' => __( 'Subtitle', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'placeholder' => __( 'Type subtitle here', 'zoom-elementor-widgets' ),
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __( 'Link', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => 'https://example.com',
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$placeholder = [
			'image' => [
				'url' => Utils::get_placeholder_image_src(),
			],
		];

		$this->add_control(
			'slides',
			[
				'show_label' => false,
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '<# print(title || "Carousel Item"); #>',
				'default' => array_fill( 0, 7, $placeholder )
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'medium_large',
				'separator' => 'before',
				'exclude' => [
					'custom'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_settings',
			[
				'label' => __( 'Settings', 'zoom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'animation_speed',
			[
				'label' => __( 'Animation Speed', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 100,
				'step' => 10,
				'max' => 10000,
				'default' => 300,
				'description' => __( 'Slide speed in milliseconds', 'zoom-elementor-widgets' ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => __( 'Autoplay?', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'zoom-elementor-widgets' ),
				'label_off' => __( 'No', 'zoom-elementor-widgets' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => __( 'Autoplay Speed', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 100,
				'step' => 100,
				'max' => 10000,
				'default' => 3000,
				'description' => __( 'Autoplay speed in milliseconds', 'zoom-elementor-widgets' ),
				'condition' => [
					'autoplay' => 'yes'
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => __( 'Infinite Loop?', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'zoom-elementor-widgets' ),
				'label_off' => __( 'No', 'zoom-elementor-widgets' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'center',
			[
				'label' => __( 'Center Mode?', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'zoom-elementor-widgets' ),
				'label_off' => __( 'No', 'zoom-elementor-widgets' ),
				'return_value' => 'yes',
				'description' => __( 'Best works with odd number of slides (Slides To Show) and loop (Infinite Loop)', 'zoom-elementor-widgets' ),
				'frontend_available' => true,
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'vertical',
			[
				'label' => __( 'Vertical Mode?', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'zoom-elementor-widgets' ),
				'label_off' => __( 'No', 'zoom-elementor-widgets' ),
				'return_value' => 'yes',
				'frontend_available' => true,
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'navigation',
			[
				'label' => __( 'Navigation', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => __( 'None', 'zoom-elementor-widgets' ),
					'arrow' => __( 'Arrow', 'zoom-elementor-widgets' ),
					'dots' => __( 'Dots', 'zoom-elementor-widgets' ),
					'both' => __( 'Arrow & Dots', 'zoom-elementor-widgets' ),
				],
				'default' => 'arrow',
				'frontend_available' => true,
				'style_transfer' => true,
			]
		);

		$this->add_responsive_control(
			'slides_to_show',
			[
				'label' => __( 'Slides To Show', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					1 => __( '1 Slide', 'zoom-elementor-widgets' ),
					2 => __( '2 Slides', 'zoom-elementor-widgets' ),
					3 => __( '3 Slides', 'zoom-elementor-widgets' ),
					4 => __( '4 Slides', 'zoom-elementor-widgets' ),
					5 => __( '5 Slides', 'zoom-elementor-widgets' ),
					6 => __( '6 Slides', 'zoom-elementor-widgets' ),
				],
				'desktop_default' => 4,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'frontend_available' => true,
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'arrow_prev_icon',
			[
				'label' => __( 'Previous Icon', 'zoom-elementor-widgets' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'default' => [
					'value' => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				],
				'condition' => [
					'navigation' => ['arrow', 'both']
				],
			]
		);

		$this->add_control(
			'arrow_next_icon',
			[
				'label' => __( 'Next Icon', 'zoom-elementor-widgets' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'default' => [
					'value' => 'fas fa-chevron-right',
					'library' => 'fa-solid',
				],
				'condition' => [
					'navigation' => ['arrow', 'both']
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register Style Controls.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	protected function register_style_controls() {
		$this->start_controls_section(
			'_section_style_item',
			[
				'label' => __( 'Carousel Item', 'zoom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'item_spacing',
			[
				'label' => __( 'Slide Spacing (px)', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .slick-slider:not(.slick-vertical) .slick-slide' => 'padding-right: {{SIZE}}{{UNIT}}; padding-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-slider.slick-vertical .slick-slide' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label' => __( 'Border Radius', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .zew-slick-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_style_content',
			[
				'label' => __( 'Slide Content', 'zoom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => __( 'Content Padding', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .zew-slick-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'content_background',
				'selector' => '{{WRAPPER}} .zew-slick-content',
				'exclude' => [
					 'image'
				]
			]
		);

		$this->add_control(
			'_heading_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Title', 'zoom-elementor-widgets' ),
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => __( 'Bottom Spacing', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .zew-slick-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Text Color', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-slick-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title',
				'label' => __( 'Typography', 'zoom-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .zew-slick-title',
				'scheme' => Scheme_Typography::TYPOGRAPHY_2,
			]
		);

		$this->add_control(
			'_heading_subtitle',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Subtitle', 'zoom-elementor-widgets' ),
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'subtitle_spacing',
			[
				'label' => __( 'Bottom Spacing', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .zew-slick-subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label' => __( 'Text Color', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-slick-subtitle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'subtitle',
				'label' => __( 'Typography', 'zoom-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .zew-slick-subtitle',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_style_arrow',
			[
				'label' => __( 'Navigation :: Arrow', 'zoom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'arrow_position_toggle',
			[
				'label' => __( 'Position', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'None', 'zoom-elementor-widgets' ),
				'label_on' => __( 'Custom', 'zoom-elementor-widgets' ),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'arrow_position_y',
			[
				'label' => __( 'Vertical', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'condition' => [
					'arrow_position_toggle' => 'yes'
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 500,
					],
					'%' => [
						'min' => -110,
						'max' => 110,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_position_x',
			[
				'label' => __( 'Horizontal', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'condition' => [
					'arrow_position_toggle' => 'yes'
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 500,
					],
					'%' => [
						'min' => -110,
						'max' => 110,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-prev' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-next' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_popover();

		$this->add_responsive_control(
			'arrow_size',
			[
				'label' => __( 'Size', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'arrow_border',
				'selector' => '{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next',
			]
		);

		$this->add_responsive_control(
			'arrow_border_radius',
			[
				'label' => __( 'Border Radius', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->start_controls_tabs( '_tabs_arrow' );

		$this->start_controls_tab(
			'_tab_arrow_normal',
			[
				'label' => __( 'Normal', 'zoom-elementor-widgets' ),
			]
		);

		$this->add_control(
			'arrow_color',
			[
				'label' => __( 'Color', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_bg_color',
			[
				'label' => __( 'Background Color', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_arrow_hover',
			[
				'label' => __( 'Hover', 'zoom-elementor-widgets' ),
			]
		);

		$this->add_control(
			'arrow_hover_color',
			[
				'label' => __( 'Color', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_hover_bg_color',
			[
				'label' => __( 'Background Color', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_hover_border_color',
			[
				'label' => __( 'Border Color', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'arrow_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_style_dots',
			[
				'label' => __( 'Navigation :: Dots', 'zoom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'dots_nav_position_y',
			[
				'label' => __( 'Vertical Position', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-dots' => 'bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dots_nav_spacing',
			[
				'label' => __( 'Spacing', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .slick-dots li' => 'margin-right: calc({{SIZE}}{{UNIT}} / 2); margin-left: calc({{SIZE}}{{UNIT}} / 2);',
				],
			]
		);

		$this->add_responsive_control(
			'dots_nav_align',
			[
				'label' => __( 'Alignment', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'zoom-elementor-widgets' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'zoom-elementor-widgets' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'zoom-elementor-widgets' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .slick-dots' => 'text-align: {{VALUE}}'
				]
			]
		);

		$this->start_controls_tabs( '_tabs_dots' );
		$this->start_controls_tab(
			'_tab_dots_normal',
			[
				'label' => __( 'Normal', 'zoom-elementor-widgets' ),
			]
		);

		$this->add_control(
			'dots_nav_size',
			[
				'label' => __( 'Size', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dots_nav_color',
			[
				'label' => __( 'Color', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_dots_hover',
			[
				'label' => __( 'Hover', 'zoom-elementor-widgets' ),
			]
		);

		$this->add_control(
			'dots_nav_hover_color',
			[
				'label' => __( 'Color', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:hover:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_dots_active',
			[
				'label' => __( 'Active', 'zoom-elementor-widgets' ),
			]
		);

		$this->add_control(
			'dots_nav_active_size',
			[
				'label' => __( 'Size', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .slick-dots li.slick-active button:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dots_nav_active_color',
			[
				'label' => __( 'Color', 'zoom-elementor-widgets' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-dots .slick-active button:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render the Widget.
	 *
	 * Renders the widget on the frontend.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['slides'] ) ) {
			return;
		}

		?><div class="zewjs-slick zew-slick zew-slick--carousel">

			<?php foreach ( $settings[ 'slides' ] as $slide ) :
				$image = wp_get_attachment_image_url( $slide[ 'image' ][ 'id' ], $settings[ 'thumbnail_size' ] );

				if ( ! $image ) {
					$image = $slide[ 'image' ][ 'url' ];
				}

				$item_tag = 'div';
				$id = 'zew-slick-item-' . $slide ['_id' ];

				$this->add_render_attribute( $id, 'class', 'zew-slick-item' );

				if ( isset( $slide[ 'link' ] ) && ! empty( $slide[ 'link' ][ 'url' ] ) ) {
					$item_tag = 'a';
					$this->add_link_attributes( $id, $slide[ 'link' ] );
				}
				?>

				<div class="zew-slick-slide">
					<<?php echo $item_tag; ?> <?php $this->print_render_attribute_string( $id ); ?>>
						<?php if ( $image ) : ?>
							<img class="zew-slick-img" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $slide[ 'title' ] ); ?>">
						<?php endif; ?>

						<?php if ( $slide[ 'title' ] || $slide[ 'subtitle' ] ) : ?>
							<div class="zew-slick-content">
								<?php if ( $slide[ 'title' ] ) : ?>
									<h2 class="zew-slick-title"><?php echo ZOOM_Elementor_Widgets::custom_kses( $slide[ 'title' ] ); ?></h2>
								<?php endif; ?>
								<?php if ( $slide[ 'subtitle' ] ) : ?>
									<p class="zew-slick-subtitle"><?php echo ZOOM_Elementor_Widgets::custom_kses( $slide[ 'subtitle' ] ); ?></p>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</<?php echo $item_tag; ?>>
				</div>

			<?php endforeach; ?>

		</div>

		<?php if ( ! empty( $settings[ 'arrow_prev_icon' ][ 'value' ] ) ) : ?>
			<button type="button" class="slick-prev"><?php Icons_Manager::render_icon( $settings[ 'arrow_prev_icon' ], [ 'aria-hidden' => 'true' ] ); ?></button>
		<?php endif; ?>

		<?php if ( ! empty( $settings[ 'arrow_next_icon' ][ 'value' ] ) ) : ?>
			<button type="button" class="slick-next"><?php Icons_Manager::render_icon( $settings[ 'arrow_next_icon' ], [ 'aria-hidden' => 'true' ] ); ?></button>
		<?php endif; ?>

		<?php
	}
}
