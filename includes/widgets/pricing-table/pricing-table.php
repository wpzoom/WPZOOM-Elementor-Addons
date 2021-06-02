<?php
namespace WPZOOMElementorWidgets;

use Elementor\Widget_Base;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Repeater;
use Elementor\Core\Schemes\Typography;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * ZOOM Elementor Widgets - Pricing Table Widget.
 *
 * Elementor widget that inserts a customizable pricing table.
 *
 * @since 1.0.0
 */
class Pricing_Table extends Widget_Base {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wpzoom-elementor-addons-css-frontend-pricing-table', plugins_url( 'frontend.css', __FILE__ ), [], WPZOOM_EL_ADDONS_VER );
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
		return 'wpzoom-elementor-addons-pricing-table';
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
		return __( 'Pricing Table', 'wpzoom-elementor-addons' );
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
		return 'eicon-price-list';
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
		return [ 'wpzoom-elementor-addons' ];
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
			'font-awesome-5-all',
			'font-awesome-4-shim',
			'wpzoom-elementor-addons-css-frontend-pricing-table'
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
			'_section_header',
			[
				'label' => __( 'Header', 'wpzoom-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __( 'Basic', 'wpzoom-elementor-addons' ),
				'dynamic' => [
					'active' => true
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_pricing',
			[
				'label' => __( 'Pricing', 'wpzoom-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT
			]
		);

		$this->add_control(
			'currency',
			[
				'label' => __( 'Currency', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					''             => __( 'None', 'wpzoom-elementor-addons' ),
					'baht'         => '&#3647; ' . _x( 'Baht', 'Currency Symbol', 'wpzoom-elementor-addons' ),
					'bdt'          => '&#2547; ' . _x( 'BD Taka', 'Currency Symbol', 'wpzoom-elementor-addons' ),
					'dollar'       => '&#36; ' . _x( 'Dollar', 'Currency Symbol', 'wpzoom-elementor-addons' ),
					'euro'         => '&#128; ' . _x( 'Euro', 'Currency Symbol', 'wpzoom-elementor-addons' ),
					'franc'        => '&#8355; ' . _x( 'Franc', 'Currency Symbol', 'wpzoom-elementor-addons' ),
					'guilder'      => '&fnof; ' . _x( 'Guilder', 'Currency Symbol', 'wpzoom-elementor-addons' ),
					'krona'        => 'kr ' . _x( 'Krona', 'Currency Symbol', 'wpzoom-elementor-addons' ),
					'lira'         => '&#8356; ' . _x( 'Lira', 'Currency Symbol', 'wpzoom-elementor-addons' ),
					'peseta'       => '&#8359 ' . _x( 'Peseta', 'Currency Symbol', 'wpzoom-elementor-addons' ),
					'peso'         => '&#8369; ' . _x( 'Peso', 'Currency Symbol', 'wpzoom-elementor-addons' ),
					'pound'        => '&#163; ' . _x( 'Pound Sterling', 'Currency Symbol', 'wpzoom-elementor-addons' ),
					'real'         => 'R$ ' . _x( 'Real', 'Currency Symbol', 'wpzoom-elementor-addons' ),
					'ruble'        => '&#8381; ' . _x( 'Ruble', 'Currency Symbol', 'wpzoom-elementor-addons' ),
					'rupee'        => '&#8360; ' . _x( 'Rupee', 'Currency Symbol', 'wpzoom-elementor-addons' ),
					'indian_rupee' => '&#8377; ' . _x( 'Rupee (Indian)', 'Currency Symbol', 'wpzoom-elementor-addons' ),
					'shekel'       => '&#8362; ' . _x( 'Shekel', 'Currency Symbol', 'wpzoom-elementor-addons' ),
					'won'          => '&#8361; ' . _x( 'Won', 'Currency Symbol', 'wpzoom-elementor-addons' ),
					'yen'          => '&#165; ' . _x( 'Yen/Yuan', 'Currency Symbol', 'wpzoom-elementor-addons' ),
					'custom'       => __( 'Custom', 'wpzoom-elementor-addons' )
				],
				'default' => 'dollar'
			]
		);

		$this->add_control(
			'currency_custom',
			[
				'label' => __( 'Custom Symbol', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'currency' => 'custom'
				],
				'dynamic' => [
					'active' => true
				]
			]
		);

		$this->add_control(
			'price',
			[
				'label' => __( 'Price', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => '9.99',
				'dynamic' => [
					'active' => true
				]
			]
		);

		$this->add_control(
			'period',
			[
				'label' => __( 'Period', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Per Month', 'wpzoom-elementor-addons' ),
				'dynamic' => [
					'active' => true
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_features',
			[
				'label' => __( 'Features', 'wpzoom-elementor-addons' )
			]
		);

		$this->add_control(
			'features_title',
			[
				'label' => __( 'Title', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Features', 'wpzoom-elementor-addons' ),
				'separator' => 'after',
				'label_block' => true,
				'dynamic' => [
					'active' => true
				]
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			[
				'label' => __( 'Text', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Exciting Feature', 'wpzoom-elementor-addons' ),
				'dynamic' => [
					'active' => true
				]
			]
		);

		$repeater->add_control(
			'selected_icon',
			[
				'label' => __( 'Icon', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-check',
					'library' => 'fa-solid'
				],
				'recommended' => [
					'fa-regular' => [
						'check-square',
						'window-close'
					],
					'fa-solid' => [
						'check'
					]
				]
			]
		);

		$this->add_control(
			'features_list',
			[
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'show_label' => false,
				'default' => [
					[
						'text' => __( 'Standard Feature', 'wpzoom-elementor-addons' ),
						'icon' => 'fa fa-check'
					],
					[
						'text' => __( 'Another Great Feature', 'wpzoom-elementor-addons' ),
						'icon' => 'fa fa-check'
					],
					[
						'text' => __( 'Obsolete Feature', 'wpzoom-elementor-addons' ),
						'icon' => 'fa fa-close'
					],
					[
						'text' => __( 'Exciting Feature', 'wpzoom-elementor-addons' ),
						'icon' => 'fa fa-check'
					]
				],
				'title_field' => '<# print(text); #>'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_footer',
			[
				'label' => __( 'Footer', 'wpzoom-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => __( 'Button Text', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Subscribe', 'wpzoom-elementor-addons' ),
				'placeholder' => __( 'Type button text here', 'wpzoom-elementor-addons' ),
				'label_block' => true,
				'dynamic' => [
					'active' => true
				]
			]
		);

		$this->add_control(
			'button_link',
			[
				'label' => __( 'Link', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => 'https://example.com',
				'dynamic' => [
					'active' => true
				],
				'default' => [
					'url' => '#'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_badge',
			[
				'label' => __( 'Badge', 'wpzoom-elementor-addons' )
			]
		);

		$this->add_control(
			'show_badge',
			[
				'label' => __( 'Show', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'wpzoom-elementor-addons' ),
				'label_off' => __( 'Hide', 'wpzoom-elementor-addons' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'style_transfer' => true
			]
		);

		$this->add_control(
			'badge_position',
			[
				'label' => __( 'Position', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-h-align-left'
					],
					'right' => [
						'title' => __( 'Right', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-h-align-right'
					]
				],
				'toggle' => false,
				'default' => 'left',
				'style_transfer' => true,
				'condition' => [
					'show_badge' => 'yes'
				]
			]
		);

		$this->add_control(
			'badge_text',
			[
				'label' => __( 'Badge Text', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Recommended', 'wpzoom-elementor-addons' ),
				'placeholder' => __( 'Type badge text', 'wpzoom-elementor-addons' ),
				'condition' => [
					'show_badge' => 'yes'
				],
				'dynamic' => [
					'active' => true
				]
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
			'_section_style_general',
			[
				'label' => __( 'General', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-title,'
					. '{{WRAPPER}} .zew-pricing-table-currency,'
					. '{{WRAPPER}} .zew-pricing-table-period,'
					. '{{WRAPPER}} .zew-pricing-table-features-title,'
					. '{{WRAPPER}} .zew-pricing-table-features-list li,'
					. '{{WRAPPER}} .zew-pricing-table-price-text' => 'color: {{VALUE}};'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_style_header',
			[
				'label' => __( 'Header', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => __( 'Bottom Spacing', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-title' => 'margin-bottom: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Title Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-title' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .zew-pricing-table-title',
				'scheme' => Typography::TYPOGRAPHY_2
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'selector' => '{{WRAPPER}} .zew-pricing-table-title'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_style_pricing',
			[
				'label' => __( 'Pricing', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'_heading_price',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Price', 'wpzoom-elementor-addons' )
			]
		);

		$this->add_responsive_control(
			'price_spacing',
			[
				'label' => __( 'Bottom Spacing', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-price-tag' => 'margin-bottom: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'price_color',
			[
				'label' => __( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-price-text' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'price_typography',
				'selector' => '{{WRAPPER}} .zew-pricing-table-price-text',
				'scheme' => Typography::TYPOGRAPHY_3
			]
		);

		$this->add_control(
			'_heading_currency',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Currency', 'wpzoom-elementor-addons' ),
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'currency_spacing',
			[
				'label' => __( 'Side Spacing', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-currency' => 'margin-right: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'currency_color',
			[
				'label' => __( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-currency' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'currency_typography',
				'selector' => '{{WRAPPER}} .zew-pricing-table-currency',
				'scheme' => Typography::TYPOGRAPHY_3
			]
		);

		$this->add_control(
			'_heading_period',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Period', 'wpzoom-elementor-addons' ),
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'period_spacing',
			[
				'label' => __( 'Bottom Spacing', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-price' => 'margin-bottom: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'period_color',
			[
				'label' => __( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-period' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'period_typography',
				'selector' => '{{WRAPPER}} .zew-pricing-table-period',
				'scheme' => Typography::TYPOGRAPHY_3
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_style_features',
			[
				'label' => __( 'Features', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_responsive_control(
			'features_container_spacing',
			[
				'label' => __( 'Container Bottom Spacing', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-body' => 'margin-bottom: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'_heading_features_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Title', 'wpzoom-elementor-addons' ),
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'features_title_spacing',
			[
				'label' => __( 'Bottom Spacing', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-features-title' => 'margin-bottom: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'features_title_color',
			[
				'label' => __( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-features-title' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'features_title_typography',
				'selector' => '{{WRAPPER}} .zew-pricing-table-features-title',
				'scheme' => Typography::TYPOGRAPHY_2
			]
		);

		$this->add_control(
			'_heading_features_list',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'List', 'wpzoom-elementor-addons' ),
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'features_list_spacing',
			[
				'label' => __( 'Spacing Between', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-features-list > li' => 'margin-bottom: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'features_list_color',
			[
				'label' => __( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-features-list > li' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'features_list_typography',
				'selector' => '{{WRAPPER}} .zew-pricing-table-features-list > li',
				'scheme' => Typography::TYPOGRAPHY_3
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_style_footer',
			[
				'label' => __( 'Footer', 'wpzoom-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'_heading_button',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Button', 'wpzoom-elementor-addons' )
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => __( 'Padding', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border',
				'selector' => '{{WRAPPER}} .zew-pricing-table-btn'
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label' => __( 'Border Radius', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .zew-pricing-table-btn'
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .zew-pricing-table-btn',
				'scheme' => Typography::TYPOGRAPHY_4
			]
		);

		$this->add_control(
			'hr',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick'
			]
		);

		$this->start_controls_tabs( '_tabs_button' );

		$this->start_controls_tab(
			'_tab_button_normal',
			[
				'label' => __( 'Normal', 'wpzoom-elementor-addons' )
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => __( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-btn' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => __( 'Background Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-btn' => 'background-color: {{VALUE}};'
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_button_hover',
			[
				'label' => __( 'Hover', 'wpzoom-elementor-addons' )
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label' => __( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-btn:hover, {{WRAPPER}} .zew-pricing-table-btn:focus' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'button_hover_bg_color',
			[
				'label' => __( 'Background Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-btn:hover, {{WRAPPER}} .zew-pricing-table-btn:focus' => 'background-color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label' => __( 'Border Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => ''
				],
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-btn:hover, {{WRAPPER}} .zew-pricing-table-btn:focus' => 'border-color: {{VALUE}};'
				]
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_controls_section(
			'_section_style_badge',
			[
				'label' => __( 'Badge', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_responsive_control(
			'badge_padding',
			[
				'label' => __( 'Padding', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label' => __( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-badge' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'badge_bg_color',
			[
				'label' => __( 'Background Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-badge' => 'background-color: {{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'badge_border',
				'selector' => '{{WRAPPER}} .zew-pricing-table-badge'
			]
		);

		$this->add_responsive_control(
			'badge_border_radius',
			[
				'label' => __( 'Border Radius', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .zew-pricing-table-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'badge_box_shadow',
				'selector' => '{{WRAPPER}} .zew-pricing-table-badge'
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'badge_typography',
				'label' => __( 'Typography', 'wpzoom-elementor-addons' ),
				'selector' => '{{WRAPPER}} .zew-pricing-table-badge',
				'scheme' => Typography::TYPOGRAPHY_3
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Get the currency symbol for the given name.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $symbol_name The name of the currency symbol to get.
	 * @return string The currency symbol for the given name.
	 */
	private static function get_currency_symbol( $symbol_name ) {
		$symbols = [
			'baht'         => '&#3647;',
			'bdt'          => '&#2547;',
			'dollar'       => '&#36;',
			'euro'         => '&#128;',
			'franc'        => '&#8355;',
			'guilder'      => '&fnof;',
			'indian_rupee' => '&#8377;',
			'pound'        => '&#163;',
			'peso'         => '&#8369;',
			'peseta'       => '&#8359',
			'lira'         => '&#8356;',
			'ruble'        => '&#8381;',
			'shekel'       => '&#8362;',
			'rupee'        => '&#8360;',
			'real'         => 'R$',
			'krona'        => 'kr',
			'won'          => '&#8361;',
			'yen'          => '&#165;'
		];

		return isset( $symbols[ $symbol_name ] ) ? $symbols[ $symbol_name ] : '';
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

		$this->add_render_attribute( 'badge_text', 'class',
			[
				'zew-pricing-table-badge',
				'zew-pricing-table-badge--' . $settings[ 'badge_position' ]
			]
		);

		$this->add_inline_editing_attributes( 'title', 'basic' );
		$this->add_render_attribute( 'title', 'class', 'zew-pricing-table-title' );

		$this->add_inline_editing_attributes( 'price', 'basic' );
		$this->add_render_attribute( 'price', 'class', 'zew-pricing-table-price-text' );

		$this->add_inline_editing_attributes( 'period', 'basic' );
		$this->add_render_attribute( 'period', 'class', 'zew-pricing-table-period' );

		$this->add_inline_editing_attributes( 'features_title', 'basic' );
		$this->add_render_attribute( 'features_title', 'class', 'zew-pricing-table-features-title' );

		$this->add_inline_editing_attributes( 'button_text', 'none' );
		$this->add_render_attribute( 'button_text', 'class', 'zew-pricing-table-btn' );

		$this->add_link_attributes( 'button_text', $settings[ 'button_link' ] );

		if ( $settings[ 'currency' ] === 'custom' ) {
			$currency = $settings[ 'currency_custom' ];
		} else {
			$currency = self::get_currency_symbol( $settings[ 'currency' ] );
		}
		?>

		<?php if ( $settings[ 'show_badge' ] ) : ?>
			<span <?php $this->print_render_attribute_string( 'badge_text' ); ?>><?php echo esc_html( $settings[ 'badge_text' ] ); ?></span>
		<?php endif; ?>

		<div class="zew-pricing-table-header">
			<?php if ( $settings[ 'title' ] ) : ?>
				<h2 <?php $this->print_render_attribute_string( 'title' ); ?>><?php echo ZOOM_Elementor_Widgets::custom_kses( $settings[ 'title' ] ); ?></h2>
			<?php endif; ?>
		</div>
		<div class="zew-pricing-table-price">
			<div class="zew-pricing-table-price-tag"><span class="zew-pricing-table-currency"><?php echo esc_html( $currency ); ?></span><span <?php $this->print_render_attribute_string( 'price' ); ?>><?php echo ZOOM_Elementor_Widgets::custom_kses( $settings[ 'price' ] ); ?></span></div>
			<?php if ( $settings[ 'period' ] ) : ?>
				<div <?php $this->print_render_attribute_string( 'period' ); ?>><?php echo ZOOM_Elementor_Widgets::custom_kses( $settings[ 'period' ] ); ?></div>
			<?php endif; ?>
		</div>
		<div class="zew-pricing-table-body">
			<?php if ( $settings[ 'features_title' ] ) : ?>
				<h3 <?php $this->print_render_attribute_string( 'features_title' ); ?>><?php echo ZOOM_Elementor_Widgets::custom_kses( $settings[ 'features_title' ] ); ?></h3>
			<?php endif; ?>

			<?php if ( is_array( $settings[ 'features_list' ] ) ) : ?>
				<ul class="zew-pricing-table-features-list">
					<?php foreach ( $settings[ 'features_list' ] as $index => $feature ) :
						$name_key = $this->get_repeater_setting_key( 'text', 'features_list', $index );
						$this->add_inline_editing_attributes( $name_key, 'intermediate' );
						$this->add_render_attribute( $name_key, 'class', 'zew-pricing-table-feature-text' );
						?>
						<li class="<?php echo esc_attr( 'elementor-repeater-item-' . $feature[ '_id' ] ); ?>">
							<?php if ( ! empty( $feature[ 'selected_icon' ][ 'value' ] ) ) :
								Icons_Manager::render_icon( $feature[ 'selected_icon' ], [] );
							endif; ?>
							<div <?php $this->print_render_attribute_string( $name_key ); ?>><?php echo ZOOM_Elementor_Widgets::custom_kses( $feature[ 'text' ] ); ?></div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<?php if ( $settings[ 'button_text' ] ) : ?>
			<a <?php $this->print_render_attribute_string( 'button_text' ); ?>><?php echo esc_html( $settings[ 'button_text' ] ); ?></a>
		<?php endif; ?>

		<?php
	}
}
