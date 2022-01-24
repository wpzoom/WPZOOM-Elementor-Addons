<?php
/**
 * Featured Category Widget.
 *
 * Elementor widget that shows a featured category.
 *
 * @package WPZOOM_Elementor_Addons
 * @since   1.2.0
 */

namespace WPZOOMElementorWidgets;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Typography;
use \Elementor\Group_Control_Text_Shadow;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Css_Filter;
use \Elementor\Core\Schemes\Typography;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * ZOOM Elementor Widgets - Featured Category Widget.
 *
 * Elementor widget that shows a featured category.
 *
 * @since 1.2.0
 */
class Featured_Category extends Widget_Base {
	/**
	 * Constructor.
	 *
	 * @since  1.2.0
	 * @access public
	 * @param  array      $data Widget data. Default is an empty array.
	 * @param  array|null $args Optional. Widget default arguments. Default is null.
	 * @return mixed
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_enqueue_style( 'wpzoom-elementor-addons-css-backend-featured-category', plugins_url( 'backend.css', __FILE__ ), array(), WPZOOM_EL_ADDONS_VER );
		wp_register_style( 'wpzoom-elementor-addons-css-frontend-featured-category', plugins_url( 'frontend.css', __FILE__ ), array(), WPZOOM_EL_ADDONS_VER );
	}

	/**
	 * Get widget name.
	 *
	 * Retrieve widget name.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wpzoom-elementor-addons-featured-category';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Featured Category', 'wpzoom-elementor-addons' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-featured-image';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'wpzoom-elementor-addons' );
	}

	/**
	 * Style Dependencies.
	 *
	 * Returns all the styles the widget depends on.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return array Style slugs.
	 */
	public function get_style_depends() {
		return array( 'wpzoom-elementor-addons-css-frontend-featured-category' );
	}

	/**
	 * Get All Post Categories.
	 *
	 * Returns a list of all post categories.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return array Array of categories.
	 */
	private function get_all_categories() {
		$options = array();

		$taxonomy = 'category';

		if ( ! empty( $taxonomy ) ) {
			// Get categories for post type.
			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
				)
			);

			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					if ( isset( $term ) ) {
						if ( isset( $term->slug ) && isset( $term->name ) ) {
							$options[ $term->slug ] = $term->name . ' (' . $term->count . ')';
						}
					}
				}
			}
		}

		return $options;
	}

	/**
	 * Register Controls.
	 *
	 * Registers all the controls for this widget.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return void
	 */
	protected function _register_controls() {
		$this->content_options();
		$this->style_options();
	}

	/**
	 * Content Options.
	 *
	 * Registers the content controls.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return void
	 */
	private function content_options() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Content', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'category',
			array(
				'label'       => esc_html__( 'Category', 'wpzoom-elementor-addons' ),
				'label_block' => true,
				'description' => esc_html__( 'Select the category to display.', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => false,
				'options'     => $this->get_all_categories(),
			)
		);

		$this->add_control(
			'hr_1',
			array(
				'type' => Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'        => 'category_image_size',
				'label'       => esc_html__( 'Category Image Scale', 'wpzoom-elementor-addons' ),
				'label_block' => true,
				'description' => esc_html__( 'Select the size at which to display the category image.', 'wpzoom-elementor-addons' ),
				'default'     => 'large',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style Options.
	 *
	 * Registers the style controls.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return void
	 */
	private function style_options() {
		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Style', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'style_transition_duration',
			array(
				'label'     => esc_html__( 'Transition Duration', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 0.3,
				),
				'range'     => array(
					'px' => array(
						'max'  => 3,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a' => 'transition-duration: {{SIZE}}s',
				),
			)
		);

		$this->add_control(
			'hr_2',
			array(
				'type' => Controls_Manager::DIVIDER,
			)
		);

		$this->start_controls_tabs( 'section_style_tabs' );

		$this->start_controls_tab(
			'section_style_tabs_normal',
			array(
				'label' => esc_html__( 'Normal', 'wpzoom-elementor-addons' ),
			)
		);

		$this->add_control(
			'section_style_normal_heading_font',
			array(
				'label'     => esc_html__( 'Text', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'style_normal_font',
				'label'    => esc_html__( 'Typography', 'wpzoom-elementor-addons' ),
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a > span',
			)
		);

		$this->add_control(
			'style_normal_font_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a > span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'style_normal_font_shadow',
				'label'    => esc_html__( 'Shadow', 'wpzoom-elementor-addons' ),
				'selector' => '{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a > span',
			)
		);

		$this->add_control(
			'style_normal_font_align',
			array(
				'label'                => esc_html__( 'Alignment', 'wpzoom-elementor-addons' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'top_left'      => array(
						'title' => esc_html__( 'Top Left', 'wpzoom-elementor-addons' ),
						'icon'  => 'fa fa-arrow-up',
					),
					'top_center'    => array(
						'title' => esc_html__( 'Top Center', 'wpzoom-elementor-addons' ),
						'icon'  => 'fa fa-arrow-up',
					),
					'top_right'     => array(
						'title' => esc_html__( 'Top Right', 'wpzoom-elementor-addons' ),
						'icon'  => 'fa fa-arrow-up',
					),
					'center_left'   => array(
						'title' => esc_html__( 'Center Left', 'wpzoom-elementor-addons' ),
						'icon'  => 'fa fa-arrow-left',
					),
					'center_center' => array(
						'title' => esc_html__( 'Center Center', 'wpzoom-elementor-addons' ),
						'icon'  => 'fa fa-plus',
					),
					'center_right'  => array(
						'title' => esc_html__( 'Center Right', 'wpzoom-elementor-addons' ),
						'icon'  => 'fa fa-arrow-right',
					),
					'bottom_left'   => array(
						'title' => esc_html__( 'Bottom Left', 'wpzoom-elementor-addons' ),
						'icon'  => 'fa fa-arrow-down',
					),
					'bottom_center' => array(
						'title' => esc_html__( 'Bottom Center', 'wpzoom-elementor-addons' ),
						'icon'  => 'fa fa-arrow-down',
					),
					'bottom_right'  => array(
						'title' => esc_html__( 'Bottom Right', 'wpzoom-elementor-addons' ),
						'icon'  => 'fa fa-arrow-down',
					),
				),
				'default'              => 'center_center',
				'selectors_dictionary' => array(
					'top_left'      => 'align-items: flex-start; justify-content: flex-start',
					'top_center'    => 'align-items: flex-start; justify-content: center',
					'top_right'     => 'align-items: flex-start; justify-content: flex-end',
					'center_left'   => 'align-items: center; justify-content: flex-start',
					'center_center' => 'align-items: center; justify-content: center',
					'center_right'  => 'align-items: center; justify-content: flex-end',
					'bottom_left'   => 'align-items: flex-end; justify-content: flex-start',
					'bottom_center' => 'align-items: flex-end; justify-content: center',
					'bottom_right'  => 'align-items: flex-end; justify-content: flex-end',
				),
				'selectors'            => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a' => '{{VALUE}}',
				),
				'toggle'               => true,
				'classes'              => 'wpzoom-elementor-addons-backend-align-control',
			)
		);

		$this->add_control(
			'section_style_normal_heading_background',
			array(
				'label'     => esc_html__( 'Background', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'style_normal_background_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'style_normal_background_position',
			array(
				'label'     => esc_html__( 'Position', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center center',
				'options'   => array(
					'top left'      => esc_html__( 'Top Left', 'wpzoom-elementor-addons' ),
					'top center'    => esc_html__( 'Top Center', 'wpzoom-elementor-addons' ),
					'top right'     => esc_html__( 'Top Right', 'wpzoom-elementor-addons' ),
					'center left'   => esc_html__( 'Center Left', 'wpzoom-elementor-addons' ),
					'center center' => esc_html__( 'Center Center', 'wpzoom-elementor-addons' ),
					'center right'  => esc_html__( 'Center Right', 'wpzoom-elementor-addons' ),
					'bottom left'   => esc_html__( 'Bottom Left', 'wpzoom-elementor-addons' ),
					'bottom center' => esc_html__( 'Bottom Center', 'wpzoom-elementor-addons' ),
					'bottom right'  => esc_html__( 'Bottom Right', 'wpzoom-elementor-addons' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a' => 'background-position: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'style_normal_background_repeat',
			array(
				'label'     => esc_html__( 'Repeat', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'no-repeat',
				'options'   => array(
					'no-repeat' => esc_html__( 'Don&rsquo;t Repeat', 'wpzoom-elementor-addons' ),
					'repeat-x'  => esc_html__( 'Repeat Horizontally', 'wpzoom-elementor-addons' ),
					'repeat-y'  => esc_html__( 'Repeat Vertically', 'wpzoom-elementor-addons' ),
					'repeat'    => esc_html__( 'Repeat Both', 'wpzoom-elementor-addons' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a' => 'background-repeat: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'style_normal_background_attachment',
			array(
				'label'     => esc_html__( 'Attachment', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'scroll',
				'options'   => array(
					'scroll' => esc_html__( 'Scroll', 'wpzoom-elementor-addons' ),
					'fixed'  => esc_html__( 'Fixed', 'wpzoom-elementor-addons' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a' => 'background-attachment: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'style_normal_background_size',
			array(
				'label'     => esc_html__( 'Size', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'auto',
				'options'   => array(
					'auto'    => esc_html__( 'Auto', 'wpzoom-elementor-addons' ),
					'cover'   => esc_html__( 'Cover', 'wpzoom-elementor-addons' ),
					'contain' => esc_html__( 'Contain', 'wpzoom-elementor-addons' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a' => 'background-size: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'section_style_normal_heading_padding',
			array(
				'label'     => esc_html__( 'Padding', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'style_normal_padding',
			array(
				'label'      => esc_html__( 'Size', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'default'    => array(
					'top'      => 2,
					'right'    => 2,
					'bottom'   => 2,
					'left'     => 2,
					'unit'     => 'rem',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'section_style_normal_heading_border',
			array(
				'label'     => esc_html__( 'Border', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'style_normal_border',
				'label'    => esc_html__( 'Border', 'wpzoom-elementor-addons' ),
				'selector' => '{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a',
			)
		);

		$this->add_control(
			'style_normal_border_radius',
			array(
				'label'      => esc_html__( 'Radius', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'default'    => array(
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'section_style_normal_heading_shadow',
			array(
				'label'     => esc_html__( 'Shadow', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'style_normal_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wpzoom-elementor-addons' ),
				'selector' => '{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a',
			)
		);

		$this->add_control(
			'section_style_normal_heading_filters',
			array(
				'label'     => esc_html__( 'Filters', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'style_normal_filters',
				'selector' => '{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a',
			)
		);

		$this->add_control(
			'section_style_normal_heading_opacity',
			array(
				'label'     => esc_html__( 'Opacity', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'style_normal_opacity',
			array(
				'label'     => esc_html__( 'Value', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'section_style_tabs_hover',
			array(
				'label' => esc_html__( 'Hover', 'wpzoom-elementor-addons' ),
			)
		);

		$this->add_control(
			'section_style_hover_heading_font',
			array(
				'label'     => esc_html__( 'Text', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'style_hover_font',
				'label'    => esc_html__( 'Typography', 'wpzoom-elementor-addons' ),
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a:hover > span',
			)
		);

		$this->add_control(
			'style_hover_font_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a:hover > span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'style_hover_font_shadow',
				'label'    => esc_html__( 'Shadow', 'wpzoom-elementor-addons' ),
				'selector' => '{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a:hover > span',
			)
		);

		$this->add_control(
			'style_hover_font_align',
			array(
				'label'                => esc_html__( 'Alignment', 'wpzoom-elementor-addons' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'top_left'      => array(
						'title' => esc_html__( 'Top Left', 'wpzoom-elementor-addons' ),
						'icon'  => 'fa fa-arrow-up',
					),
					'top_center'    => array(
						'title' => esc_html__( 'Top Center', 'wpzoom-elementor-addons' ),
						'icon'  => 'fa fa-arrow-up',
					),
					'top_right'     => array(
						'title' => esc_html__( 'Top Right', 'wpzoom-elementor-addons' ),
						'icon'  => 'fa fa-arrow-up',
					),
					'center_left'   => array(
						'title' => esc_html__( 'Center Left', 'wpzoom-elementor-addons' ),
						'icon'  => 'fa fa-arrow-left',
					),
					'center_center' => array(
						'title' => esc_html__( 'Center Center', 'wpzoom-elementor-addons' ),
						'icon'  => 'fa fa-plus',
					),
					'center_right'  => array(
						'title' => esc_html__( 'Center Right', 'wpzoom-elementor-addons' ),
						'icon'  => 'fa fa-arrow-right',
					),
					'bottom_left'   => array(
						'title' => esc_html__( 'Bottom Left', 'wpzoom-elementor-addons' ),
						'icon'  => 'fa fa-arrow-down',
					),
					'bottom_center' => array(
						'title' => esc_html__( 'Bottom Center', 'wpzoom-elementor-addons' ),
						'icon'  => 'fa fa-arrow-down',
					),
					'bottom_right'  => array(
						'title' => esc_html__( 'Bottom Right', 'wpzoom-elementor-addons' ),
						'icon'  => 'fa fa-arrow-down',
					),
				),
				'default'              => 'center_center',
				'selectors_dictionary' => array(
					'top_left'      => 'align-items: flex-start; justify-content: flex-start',
					'top_center'    => 'align-items: flex-start; justify-content: center',
					'top_right'     => 'align-items: flex-start; justify-content: flex-end',
					'center_left'   => 'align-items: center; justify-content: flex-start',
					'center_center' => 'align-items: center; justify-content: center',
					'center_right'  => 'align-items: center; justify-content: flex-end',
					'bottom_left'   => 'align-items: flex-end; justify-content: flex-start',
					'bottom_center' => 'align-items: flex-end; justify-content: center',
					'bottom_right'  => 'align-items: flex-end; justify-content: flex-end',
				),
				'selectors'            => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a:hover' => '{{VALUE}}',
				),
				'toggle'               => true,
				'classes'              => 'wpzoom-elementor-addons-backend-align-control',
			)
		);

		$this->add_control(
			'section_style_hover_heading_background',
			array(
				'label'     => esc_html__( 'Background', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'style_hover_background_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'style_hover_background_position',
			array(
				'label'     => esc_html__( 'Position', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center center',
				'options'   => array(
					'top left'      => esc_html__( 'Top Left', 'wpzoom-elementor-addons' ),
					'top center'    => esc_html__( 'Top Center', 'wpzoom-elementor-addons' ),
					'top right'     => esc_html__( 'Top Right', 'wpzoom-elementor-addons' ),
					'center left'   => esc_html__( 'Center Left', 'wpzoom-elementor-addons' ),
					'center center' => esc_html__( 'Center Center', 'wpzoom-elementor-addons' ),
					'center right'  => esc_html__( 'Center Right', 'wpzoom-elementor-addons' ),
					'bottom left'   => esc_html__( 'Bottom Left', 'wpzoom-elementor-addons' ),
					'bottom center' => esc_html__( 'Bottom Center', 'wpzoom-elementor-addons' ),
					'bottom right'  => esc_html__( 'Bottom Right', 'wpzoom-elementor-addons' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a:hover' => 'background-position: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'style_hover_background_repeat',
			array(
				'label'     => esc_html__( 'Repeat', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'no-repeat',
				'options'   => array(
					'no-repeat' => esc_html__( 'Don&rsquo;t Repeat', 'wpzoom-elementor-addons' ),
					'repeat-x'  => esc_html__( 'Repeat Horizontally', 'wpzoom-elementor-addons' ),
					'repeat-y'  => esc_html__( 'Repeat Vertically', 'wpzoom-elementor-addons' ),
					'repeat'    => esc_html__( 'Repeat Both', 'wpzoom-elementor-addons' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a:hover' => 'background-repeat: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'style_hover_background_attachment',
			array(
				'label'     => esc_html__( 'Attachment', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'scroll',
				'options'   => array(
					'scroll' => esc_html__( 'Scroll', 'wpzoom-elementor-addons' ),
					'fixed'  => esc_html__( 'Fixed', 'wpzoom-elementor-addons' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a:hover' => 'background-attachment: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'style_hover_background_size',
			array(
				'label'     => esc_html__( 'Size', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'auto',
				'options'   => array(
					'auto'    => esc_html__( 'Auto', 'wpzoom-elementor-addons' ),
					'cover'   => esc_html__( 'Cover', 'wpzoom-elementor-addons' ),
					'contain' => esc_html__( 'Contain', 'wpzoom-elementor-addons' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a:hover' => 'background-size: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'section_style_hover_heading_padding',
			array(
				'label'     => esc_html__( 'Padding', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'style_hover_padding',
			array(
				'label'      => esc_html__( 'Size', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'default'    => array(
					'top'      => 2,
					'right'    => 2,
					'bottom'   => 2,
					'left'     => 2,
					'unit'     => 'rem',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'section_style_hover_heading_border',
			array(
				'label'     => esc_html__( 'Border', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'style_hover_border',
				'label'    => esc_html__( 'Border', 'wpzoom-elementor-addons' ),
				'selector' => '{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a:hover',
			)
		);

		$this->add_control(
			'style_hover_border_radius',
			array(
				'label'      => esc_html__( 'Radius', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'default'    => array(
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'section_style_hover_heading_shadow',
			array(
				'label'     => esc_html__( 'Shadow', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'style_hover_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wpzoom-elementor-addons' ),
				'selector' => '{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a:hover',
			)
		);

		$this->add_control(
			'section_style_hover_heading_filters',
			array(
				'label'     => esc_html__( 'Filters', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'style_hover_filters',
				'selector' => '{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a:hover',
			)
		);

		$this->add_control(
			'section_style_hover_heading_opacity',
			array(
				'label'     => esc_html__( 'Opacity', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'style_hover_opacity',
			array(
				'label'     => esc_html__( 'Value', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wpzoom-elementor-addons-featured-category > h3 > a:hover' => 'opacity: {{SIZE}};',
				),
			)
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
	 * @since  1.2.0
	 * @access public
	 * @param  array $instance The instance variables.
	 * @return void
	 */
	protected function render( $instance = array() ) {
		// Get settings.
		$settings = $this->get_settings_for_display();

		$cat = trim( $settings['category'] );

		// Exit early if no category is selected.
		if ( ! in_array( $cat, array_keys( array_filter( $this->get_all_categories() ) ), true ) ) {
			return;
		}

		$cat_data = get_category_by_slug( $cat );

		// Exit early if the given category is invalid.
		if ( false === $cat_data ) {
			return;
		}

		$term_meta    = get_term_meta( $cat_data->term_id, 'wpz_cover_image_id', true );
		$image_id     = false !== $term_meta ? absint( $term_meta ) : 0;
		$image_url    = '';
		$image_height = -1;

		if ( $image_id > 0 ) {
			$image_size = isset( $settings['category_image_size'] ) ? trim( $settings['category_image_size'] ) : 'large';
			$attachment = wp_get_attachment_image_src( $image_id, $image_size );

			if ( false !== $attachment && is_array( $attachment ) && count( $attachment ) > 2 ) {
				$image_url    = trim( $attachment[0] );
				$image_height = absint( $attachment[2] );
			}
		}

		$term_pos       = get_term_meta( $cat_data->term_id, 'wpz_cover_image_pos', true );
		$image_position = false !== $term_pos ? trim( $term_pos ) : '';

		$attrs = '';
		$style = '';

		if ( ! empty( $image_url ) ) {
			$attrs .= ' class="has-image"';
			$style .= sprintf( 'background-image:url(\'%s\');', esc_url( $image_url ) );
		}

		if ( ! empty( $image_position ) ) {
			$style .= sprintf( 'background-position:%s;', esc_attr( $image_position ) );
		}

		if ( $image_height > 0 ) {
			$style .= sprintf( 'height:%spx;', absint( $image_height ) );
		}

		if ( ! empty( $style ) ) {
			$style  = sprintf( ' style="%s"', $style );
			$attrs .= $style;
		}

		// phpcs:disable WordPress.Security.EscapeOutput
		?>
		<div class="wpzoom-elementor-addons-featured-category">
			<h3>
				<a href="<?php echo esc_url( get_category_link( $cat_data ) ); ?>"<?php echo $attrs; ?>>
					<span>
						<?php echo esc_html( $cat_data->name ); ?>
					</span>
				</a>
			</h3>
		</div><!-- //.wpzoom-elementor-addons-featured-category -->
		<?php
		// phpcs:enable WordPress.Security.EscapeOutput
	}
}
