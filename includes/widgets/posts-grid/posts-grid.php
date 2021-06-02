<?php
namespace WPZOOMElementorWidgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Schemes\Typography;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Image_Size;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * ZOOM Elementor Widgets - Posts Grid Widget.
 *
 * Elementor widget that inserts a customizable grid of posts.
 *
 * @since 1.0.0
 */
class Posts_Grid extends Widget_Base {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wpzoom-elementor-addons-css-frontend-posts-grid', plugins_url( 'frontend.css', __FILE__ ), [], WPZOOM_EL_ADDONS_VER );
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
		return 'wpzoom-elementor-addons-posts-grid';
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
		return __( 'Posts Grid', 'wpzoom-elementor-addons' );
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
		return 'eicon-posts-grid';
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
			'wpzoom-elementor-addons-css-frontend-posts-grid'
		];
	}

	/**
	 * Get All Post Categories.
	 *
	 * Returns a list of all post categories.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Array of categories.
	 */
	private function zew_get_all_post_categories( $post_type ) {
		$options = array();

		$taxonomy = 'category';

		if ( ! empty( $taxonomy ) ) {
			// Get categories for post type.
			$terms = get_terms(
				[
					'taxonomy'   => $taxonomy,
					'hide_empty' => false
				]
			);
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					if ( isset( $term ) ) {
						if ( isset( $term->slug ) && isset( $term->name ) ) {
							$options[ $term->slug ] = $term->name;
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
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	protected function _register_controls() {
		$this->zew_content_layout_options();
		$this->zew_content_query_options();

		$this->zew_style_layout_options();
		$this->zew_style_box_options();
		$this->zew_style_image_options();

		$this->zew_style_title_options();
		$this->zew_style_meta_options();
		$this->zew_style_content_options();
		$this->zew_style_readmore_options();
	}

	/**
	 * Content Layout Options.
	 *
	 * Registers the content layout controls.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	private function zew_content_layout_options() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Layout', 'wpzoom-elementor-addons' )
			]
		);

		$this->add_control(
			'grid_style',
			[
				'label' => __( 'Grid Style', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1' => esc_html__( 'Layout 1', 'wpzoom-elementor-addons' ),
					'2' => esc_html__( 'Layout 2', 'wpzoom-elementor-addons' ),
					'3' => esc_html__( 'Layout 3', 'wpzoom-elementor-addons' ),
					'4' => esc_html__( 'Layout 4', 'wpzoom-elementor-addons' ),
					'5' => esc_html__( 'Layout 5', 'wpzoom-elementor-addons' )
				]
			]
		);
		
		$this->add_responsive_control(
			'columns',
			[
				'label' => __( 'Columns', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4'
				],
				'prefix_class' => 'elementor-grid%s-',
				'frontend_available' => true,
				'selectors' => [
					'.elementor-msie {{WRAPPER}} .elementor-portfolio-item' => 'width: calc( 100% / {{SIZE}} )'
				]
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => __( 'Posts Per Page', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 3
			]
		);

		$this->add_control(
			'show_image',
			[
				'label' => __( 'Image', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'wpzoom-elementor-addons' ),
				'label_off' => __( 'Hide', 'wpzoom-elementor-addons' ),
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'post_thumbnail',
				'exclude' => [ 'custom' ],
				'default' => 'full',
				'prefix_class' => 'post-thumbnail-size-',
				'condition' => [
					'show_image' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_title',
			[
				'label' => __( 'Title', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'wpzoom-elementor-addons' ),
				'label_off' => __( 'Hide', 'wpzoom-elementor-addons' ),
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => __( 'Title HTML Tag', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p'
				],
				'default'   => 'h3',
				'condition' => [
					'show_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'meta_data',
			[
				'label' => __( 'Meta Data', 'wpzoom-elementor-addons' ),
				'label_block' => true,
				'type' => Controls_Manager::SELECT2,
				'default' => [ 'date', 'comments' ],
				'multiple' => true,
				'options' => [
					'author'     => __( 'Author', 'wpzoom-elementor-addons' ),
					'date'       => __( 'Date', 'wpzoom-elementor-addons' ),
					'categories' => __( 'Categories', 'wpzoom-elementor-addons' ),
					'comments'   => __( 'Comments', 'wpzoom-elementor-addons' )
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'meta_separator',
			[
				'label' => __( 'Separator Between', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => '/',
				'selectors' => [
					'{{WRAPPER}} .zew-grid-container .zew-post .post-grid-meta span + span:before' => 'content: "{{VALUE}}"'
				],
				'condition' => [
					'meta_data!' => []
				]
			]
		);

		$this->add_control(
			'show_excerpt',
			[
				'label'     => __( 'Excerpt', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'wpzoom-elementor-addons' ),
				'label_off' => __( 'Hide', 'wpzoom-elementor-addons' ),
				'default'   => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'excerpt_length',
			[
				'label' => __( 'Excerpt Length', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::NUMBER,
				/** This filter is documented in wp-includes/formatting.php */
				'default' => apply_filters( 'excerpt_length', 25 ),
				'condition' => [
					'show_excerpt' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_read_more',
			[
				'label'     => __( 'Read More', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'wpzoom-elementor-addons' ),
				'label_off' => __( 'Hide', 'wpzoom-elementor-addons' ),
				'default'   => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'read_more_text',
			[
				'label'     => __( 'Read More Text', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Read More Â»', 'wpzoom-elementor-addons' ),
				'condition' => [
					'show_read_more' => 'yes'
				]
			]
		);

		$this->add_control(
			'content_align',
			[
				'label' => __( 'Alignment', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'wpzoom-elementor-addons' ),
						'icon' => 'fa fa-align-left'
					],
					'center' => [
						'title' => __( 'Center', 'wpzoom-elementor-addons' ),
						'icon' => 'fa fa-align-center'
					],
					'right' => [
						'title' => __( 'Right', 'wpzoom-elementor-addons' ),
						'icon' => 'fa fa-align-right'
					]
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .post-grid-inner' => 'text-align: {{VALUE}};'
				],
				'separator' => 'before'
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Content Query Options.
	 *
	 * Registers the content query controls.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	private function zew_content_query_options() {
		$this->start_controls_section(
			'section_query',
			[
				'label' => __( 'Query', 'wpzoom-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT
			]
		);

		// Post categories
		$this->add_control(
			'post_categories',
			[
				'label'       => __( 'Categories', 'wpzoom-elementor-addons' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $this->zew_get_all_post_categories( 'post' )
			]
		);

		$this->add_control(
			'advanced',
			[
				'label' => __( 'Advanced', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::HEADING
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => __( 'Order By', 'wpzoom-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post_date',
				'options' => [
					'post_date'  => __( 'Date', 'wpzoom-elementor-addons' ),
					'post_title' => __( 'Title', 'wpzoom-elementor-addons' ),
					'rand'       => __( 'Random', 'wpzoom-elementor-addons' )
				]
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => __( 'Order', 'wpzoom-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'asc'  => __( 'ASC', 'wpzoom-elementor-addons' ),
					'desc' => __( 'DESC', 'wpzoom-elementor-addons' ),
				]
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style Layout Options.
	 *
	 * Registers the style layout controls.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	private function zew_style_layout_options() {
		// Layout.
		$this->start_controls_section(
			'section_layout_style',
			[
				'label' => __( 'Layout', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		// Columns margin.
		$this->add_control(
			'grid_style_columns_margin',
			[
				'label'     => __( 'Columns margin', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 15
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .zew-grid-container' => 'grid-column-gap: {{SIZE}}{{UNIT}}'
				]
			]
		);

		// Row margin.
		$this->add_control(
			'grid_style_rows_margin',
			[
				'label'     => __( 'Rows margin', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 30
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100
					]
				],
				'selectors' => [
					'{{WRAPPER}} .zew-grid-container' => 'grid-row-gap: {{SIZE}}{{UNIT}}'
				]
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style Box Options.
	 *
	 * Registers the style box controls.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	private function zew_style_box_options() {
		// Box.
		$this->start_controls_section(
			'section_box',
			[
				'label' => __( 'Box', 'wpzoom-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		// Image border radius.
		$this->add_control(
			'grid_box_border_width',
			[
				'label'      => __( 'Border Widget', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .zew-grid-container .zew-post' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}'
				],			
			]
		);

		// Border Radius.
		$this->add_control(
			'grid_style_border_radius',
			[
				'label'     => __( 'Border Radius', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 0
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200
					]
				],
				'selectors' => [
					'{{WRAPPER}} .zew-grid-container .zew-post' => 'border-radius: {{SIZE}}{{UNIT}}'
				]
			]
		);

		// Box internal padding.
		$this->add_responsive_control(
			'grid_items_style_padding',
			[
				'label'      => __( 'Padding', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .zew-grid-container .zew-post' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}'
				]
			]
		);

		$this->start_controls_tabs( 'grid_button_style' );

		// Normal tab.
		$this->start_controls_tab(
			'grid_button_style_normal',
			[
				'label' => __( 'Normal', 'wpzoom-elementor-addons' )
			]
		);

		// Normal background color.
		$this->add_control(
			'grid_button_style_normal_bg_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'wpzoom-elementor-addons' ),
				'separator' => '',
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .zew-grid-container .zew-post' => 'background-color: {{VALUE}};'
				]
			]
		);

		// Normal border color.
		$this->add_control(
			'grid_button_style_normal_border_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Border Color', 'wpzoom-elementor-addons' ),
				'separator' => '',
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .zew-grid-container .zew-post' => 'border-color: {{VALUE}};'
				]
			]
		);

		// Normal box shadow.
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'grid_button_style_normal_box_shadow',
				'selector' => '{{WRAPPER}} .zew-grid-container .zew-post'
			]
		);

		$this->end_controls_tab();

		// Hover tab.
		$this->start_controls_tab(
			'grid_button_style_hover',
			[
				'label' => __( 'Hover', 'wpzoom-elementor-addons' )
			]
		);

		// Hover background color.
		$this->add_control(
			'grid_button_style_hover_bg_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'wpzoom-elementor-addons' ),
				'separator' => '',
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .zew-grid-container .zew-post:hover' => 'background-color: {{VALUE}};'
				]
			]
		);

		// Hover border color.
		$this->add_control(
			'grid_button_style_hover_border_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Border Color', 'wpzoom-elementor-addons' ),
				'separator' => '',
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .zew-grid-container .zew-post:hover' => 'border-color: {{VALUE}};'
				]
			]
		);

		// Hover box shadow.
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'grid_button_style_hover_box_shadow',
				'selector' => '{{WRAPPER}} .zew-grid-container .zew-post:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Style Image Options.
	 *
	 * Registers the style image controls.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	private function zew_style_image_options() {
		// Box.
		$this->start_controls_section(
			'section_image',
			[
				'label' => __( 'Image', 'wpzoom-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		// Image border radius.
		$this->add_control(
			'grid_image_border_radius',
			[
				'label'      => __( 'Border Radius', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .post-grid-inner .post-grid-thumbnail img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'grid_style_image_margin',
			[
				'label'      => __( 'Margin', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .post-grid-inner .post-grid-thumbnail' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style > Title.
	 *
	 * Registers the style title controls.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	private function zew_style_title_options() {
		// Tab.
		$this->start_controls_section(
			'section_grid_title_style',
			[
				'label'     => __( 'Title', 'wpzoom-elementor-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE
			]
		);

		// Title typography.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'grid_title_style_typography',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .zew-grid-container .zew-post .title, {{WRAPPER}} .zew-grid-container .zew-post .title > a'
			]
		);

		$this->start_controls_tabs( 'grid_title_color_style' );

		// Normal tab.
		$this->start_controls_tab(
			'grid_title_style_normal',
			[
				'label' => esc_html__( 'Normal', 'wpzoom-elementor-addons' )
			]
		);

		// Title color.
		$this->add_control(
			'grid_title_style_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .zew-grid-container .zew-post .title, {{WRAPPER}} .zew-grid-container .zew-post .title > a' => 'color: {{VALUE}};'
				]
			]
		);

		$this->end_controls_tab();

		// Hover tab.
		$this->start_controls_tab(
			'grid_title_style_hover',
			[
				'label' => esc_html__( 'Hover', 'wpzoom-elementor-addons' )
			]
		);

		// Title hover color.
		$this->add_control(
			'grid_title_style_hover_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .zew-grid-container .zew-post .title, {{WRAPPER}} .zew-grid-container .zew-post .title > a:hover' => 'color: {{VALUE}};'
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		// Title margin.
		$this->add_responsive_control(
			'grid_title_style_margin',
			[
				'label'      => __( 'Margin', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .zew-grid-container .zew-post .title, {{WRAPPER}} .zew-grid-container .zew-post .title > a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style > Meta.
	 *
	 * Registers the style meta controls.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	private function zew_style_meta_options() {
		// Tab.
		$this->start_controls_section(
			'section_grid_meta_style',
			[
				'label'     => __( 'Meta', 'wpzoom-elementor-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE
			]
		);

		// Meta typography.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'grid_meta_style_typography',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .zew-grid-container .zew-post .post-grid-meta span'
			]
		);

		// Meta color.
		$this->add_control(
			'grid_meta_style_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .zew-grid-container .zew-post .post-grid-meta span'      => 'color: {{VALUE}};',
					'{{WRAPPER}} .zew-grid-container .zew-post .post-grid-meta span a' => 'color: {{VALUE}};'
				]
			]
		);

		// Meta margin.
		$this->add_responsive_control(
			'grid_meta_style_margin',
			[
				'label'      => __( 'Margin', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .zew-grid-container .zew-post .post-grid-meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style > Content.
	 *
	 * Registers the style content controls.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	private function zew_style_content_options() {
		// Tab.
		$this->start_controls_section(
			'section_grid_content_style',
			[
				'label' => __( 'Content', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		// Content typography.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'grid_content_style_typography',
				'scheme'    => Typography::TYPOGRAPHY_1,
				'selector'  => '{{WRAPPER}} .zew-grid-container .zew-post .post-grid-excerpt p'
			]
		);

		// Content color.
		$this->add_control(
			'grid_content_style_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .zew-grid-container .zew-post .post-grid-excerpt p' => 'color: {{VALUE}};'
				]
			]
		);

		// Content margin
		$this->add_responsive_control(
			'grid_content_style_margin',
			[
				'label'      => __( 'Margin', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .zew-grid-container .zew-post .post-grid-excerpt' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style > Readmore.
	 *
	 * Registers the style readmore controls.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	private function zew_style_readmore_options() {
		// Tab.
		$this->start_controls_section(
			'section_grid_readmore_style',
			[
				'label' => __( 'Read More', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		// Readmore typography.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'grid_readmore_style_typography',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .zew-grid-container .zew-post a.read-more-btn'
			]
		);

		$this->start_controls_tabs( 'grid_readmore_color_style' );

		// Normal tab.
		$this->start_controls_tab(
			'grid_readmore_style_normal',
			[
				'label' => esc_html__( 'Normal', 'wpzoom-elementor-addons' )
			]
		);

		// Readmore color.
		$this->add_control(
			'grid_readmore_style_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .zew-grid-container .zew-post a.read-more-btn' => 'color: {{VALUE}};'
				]
			]
		);

		$this->end_controls_tab();

		// Hover tab.
		$this->start_controls_tab(
			'grid_readmore_style_color_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'wpzoom-elementor-addons' )
			]
		);

		// Readmore hover color.
		$this->add_control(
			'grid_readmore_style_hover_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .zew-grid-container .zew-post a.read-more-btn:hover' => 'color: {{VALUE}};'
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		// Readmore margin
		$this->add_responsive_control(
			'grid_readmore_style_margin',
			[
				'label'      => __( 'Margin', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .zew-grid-container .zew-post a.read-more-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render the Widget.
	 *
	 * Renders the widget on the frontend.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	protected function render( $instance = [] ) {
		// Get settings.
		$settings = $this->get_settings();

		?>
		<div class="zew-grid">
			<?php 

			$columns_desktop = ( ! empty( $settings[ 'columns' ] ) ? 'zew-grid-desktop-' . $settings[ 'columns' ] : 'zew-grid-desktop-3' );

			$columns_tablet = ( ! empty( $settings[ 'columns_tablet' ] ) ? ' zew-grid-tablet-' . $settings[ 'columns_tablet' ] : ' zew-grid-tablet-2' );

			$columns_mobile = ( ! empty( $settings[ 'columns_mobile' ] ) ? ' zew-grid-mobile-' . $settings[ 'columns_mobile' ] : ' zew-grid-mobile-1' );

			$grid_style = $settings[ 'grid_style' ];

			$grid_class = '';

			if( 5 == $grid_style ){

				$grid_class = ' grid-meta-bottom';

			}

			?>
			<div class="zew-grid-container elementor-grid <?php echo $columns_desktop.$columns_tablet.$columns_mobile.$grid_class; ?>">
				<?php
				$posts_per_page = ( ! empty( $settings[ 'posts_per_page' ] ) ?  $settings[ 'posts_per_page' ] : 3 );

				$cats = is_array( $settings[ 'post_categories' ] ) ? implode( ',', $settings[ 'post_categories' ] ) : $settings[ 'post_categories' ];

				$query_args = array(
								'posts_per_page' 		=> absint( $posts_per_page ),
								'no_found_rows'  		=> true,
								'post__not_in'          => get_option( 'sticky_posts' ),
								'ignore_sticky_posts'   => true,
								'category_name' 		=> $cats
							);

				// Order by.
				if ( ! empty( $settings[ 'orderby' ] ) ) {
					$query_args[ 'orderby' ] = $settings[ 'orderby' ];
				}

				// Order .
				if ( ! empty( $settings[ 'order' ] ) ) {
					$query_args[ 'order' ] = $settings[ 'order' ];
				}

				$all_posts = new \WP_Query( $query_args );

				if ( $all_posts->have_posts() ) {
					if ( 5 == $grid_style ) {
						include( __DIR__ . '/layouts/layout-5.php' );
					} elseif( 4 == $grid_style ) {
						include( __DIR__ . '/layouts/layout-4.php' );
					} elseif( 3 == $grid_style ) {
						include( __DIR__ . '/layouts/layout-3.php' );
					} elseif( 2 == $grid_style ) {
						include( __DIR__ . '/layouts/layout-2.php' );
					} else {
						include( __DIR__ . '/layouts/layout-1.php' );
					}
				} ?>

			</div>			      						               
		</div>
		<?php
	}

	/**
	 * Filter Excerpt Length
	 *
	 * Filters the excerpt length to allow custom values.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Custom excerpt length.
	 */
	public function zew_filter_excerpt_length( $length ) {
		$settings = $this->get_settings();

		$excerpt_length = ( !empty( $settings[ 'excerpt_length' ] ) ) ? absint( $settings[ 'excerpt_length' ] ) : 25;

		return absint( $excerpt_length );
	}

	/**
	 * Filter Excerpt More.
	 *
	 * Filters the read more value at the end of excerpts.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Excerpt more.
	 */
	public function zew_filter_excerpt_more( $more ) {
		return '&hellip;';
	}

	/**
	 * Render Post Thumbnail.
	 *
	 * Outputs the markup for the post thumbnail.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	protected function render_thumbnail() {	
		$settings = $this->get_settings();

		$show_image = $settings[ 'show_image' ];

		if ( 'yes' !== $show_image ) {
			return;
		}

		$post_thumbnail_size = $settings[ 'post_thumbnail_size' ];
			
		if ( has_post_thumbnail() ) :  ?>
			<div class="post-grid-thumbnail">
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( $post_thumbnail_size ); ?>
				</a>
			</div>
		<?php endif;
	}

	/**
	 * Render Post Title.
	 *
	 * Outputs the markup for the post title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	protected function render_title() {	
		$settings = $this->get_settings();

		$show_title = $settings[ 'show_title' ];

		if ( 'yes' !== $show_title ) {
			return;
		}

		$title_tag = $settings[ 'title_tag' ];
			
		?>
		<<?php echo $title_tag; ?> class="title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</<?php echo $title_tag; ?>>
		<?php
	}

	/**
	 * Render Post Meta.
	 *
	 * Outputs the markup for the post meta.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	protected function render_meta() {
		$settings = $this->get_settings();

		$meta_data = $settings[ 'meta_data' ];

		if ( empty( $meta_data ) ) {
			return;
		}
		
		?>
		<div class="post-grid-meta">
			<?php
			if ( in_array( 'author', $meta_data ) ) { ?>

				<span class="post-author"><?php the_author(); ?></span>

				<?php 
			}

			if ( in_array( 'date', $meta_data ) ) { ?>

				<span class="post-author"><?php echo apply_filters( 'the_date', get_the_date(), get_option( 'date_format' ), '', '' ); ?></span>

				<?php
			}

			if ( in_array( 'categories', $meta_data ) ) {

				$categories_list = get_the_category_list( esc_html__( ', ', 'wpzoom-elementor-addons' ) ); 

				if ( $categories_list ) {
					printf( '<span class="post-categories">%s</span>', $categories_list ); // WPCS: XSS OK.
				}
				
			}

			if ( in_array( 'comments', $meta_data ) ) { ?>
				
				<span class="post-comments"><?php comments_number(); ?></span>

				<?php
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render Post Excerpt.
	 *
	 * Outputs the markup for the post excerpt.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	protected function render_excerpt() {
		$settings = $this->get_settings();

		$show_excerpt = $settings[ 'show_excerpt' ];

		if ( 'yes' !== $show_excerpt ) {
			return;
		}

		add_filter( 'excerpt_more', [ $this, 'zew_filter_excerpt_more' ], 20 );
		add_filter( 'excerpt_length', [ $this, 'zew_filter_excerpt_length' ], 9999 );

		?><div class="post-grid-excerpt"><?php the_excerpt(); ?></div><?php

		remove_filter( 'excerpt_length', [ $this, 'zew_filter_excerpt_length' ], 9999 );
		remove_filter( 'excerpt_more', [ $this, 'zew_filter_excerpt_more' ], 20 );
	}

	/**
	 * Render Post Read More.
	 *
	 * Outputs the markup for the readmore button.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	protected function render_readmore() {
		$settings = $this->get_settings();

		$show_read_more = $settings[ 'show_read_more' ];
		$read_more_text = $settings[ 'read_more_text' ];

		if ( 'yes' !== $show_read_more ) {
			return;
		}

		?><a class="read-more-btn" href="<?php the_permalink(); ?>"><?php echo esc_html( $read_more_text ); ?></a><?php
	}
}