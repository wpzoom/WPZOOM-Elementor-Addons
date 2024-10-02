<?php
namespace WPZOOMElementorWidgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Widget_Base;
use Elementor\Utils;

use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * WPZOOM Elementor Widgets - Portfolio Showcase Widget.
 *
 * Elementor widget that inserts a Porfolio Showcase.
 *
 * @since 1.0.0
 */
class Portfolio_Showcase extends Widget_Base {

	/**
	 * @var \WP_Query
	 */
	private $query = null;

	/**
	 * $post_type
	 * @var string
	 */
	private $post_type = 'portfolio_item';

	/**
	 * $taxonomies
	 * @var array
	 */
	private $taxonomies = array( 'portfolio' );

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
	
		wp_register_style( 'wpzoom-elementor-addons-css-frontend-portoflio-showcase', plugins_url( 'frontend.css', __FILE__ ), [], WPZOOM_EL_ADDONS_VER );
	
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
		return 'wpzoom-elementor-addons-portfolio-showcase';
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
		return esc_html__( 'Portfolio Showcase', 'wpzoom-elementor-addons' );
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
		return 'eicon-gallery-grid';
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
		return [ 'wpzoom-elementor-addons-inspiro' ];
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
			'wpzoom-elementor-addons-css-frontend-portoflio-showcase'
		];
	}

	/**
	 * Get the query
	 *
	 * Returns the current query.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return \WP_Query The current query.
	 */
	public function get_query() {
		return $this->query;
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
	protected function register_controls() {

		if ( !WPZOOM_Elementor_Widgets::is_supported_theme() ) {
			$this->register_restricted_controls();
		}
		else {
			$this->register_main_controls();
		}
	}

	/**
	 * Register restricted Controls.
	 *
	 * Registers all the controls for this widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	protected function register_restricted_controls() {

		$this->start_controls_section(
			'section_restricted_portfolio_showcase',
			array(
				'label' => esc_html__( 'Widget not available', 'wpzoom-elementor-addons' ),
			)
		);
		$this->add_control(
			'restricted_widget_text',
			[
				'raw' => wp_kses_post( __( 'This widget is supported only by the <a href="https://www.wpzoom.com/themes/inspiro/">"Inspiro"</a> and <a href="#">"Inspiro PRO"</a> themes', 'wpzoom-elementor-addons' ) ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->end_controls_section();
	
	}


	/**
	 * Register MAIN Controls.
	 *
	 * Registers all the controls for this widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	protected function register_main_controls() {

		$this->start_controls_section(
			'section_portfolio_showcase',
			array(
				'label' => esc_html__( 'General Settings', 'wpzoom-elementor-addons' ),
			)
		);

		$this->add_control(
			'widget_title',
			array(
				'label'       => esc_html__( 'Widget Title', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your widget title', 'wpzoom-elementor-addons' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				)
			)
		);
		$this->add_control(
			'widget_title_tag',
			array(
				'label'   => esc_html__( 'HTML Tag', 'wpzoom-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'default' => 'h2',
			)
		);
		$this->add_responsive_control(
			'widget_title_align',
			array(
				'label'   => esc_html__( 'Alignment', 'wpzoom-elementor-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justified', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-showcase .wpzoom-portfolio-showcase-widget-title' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'single_post',
			array(
				'label'       => esc_html__( 'Show a Single Portfolio Post', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'no',
				'separator'   => 'before',
			)
		);
		$this->add_control(
			'single_post_id',
			array(
				'label'    => esc_html__( 'Select a Post', 'wpzoom-elementor-addons' ),
				'type'     => Controls_Manager::SELECT,
				'default'  => 0,
				'options'  => $this->get_portfolio_posts(),
				'condition'   =>  array(
					'single_post' => 'yes'
				),
			)
		);
		$this->add_control(
			'category',
			array(
				'label'    => esc_html__( 'Category', 'wpzoom-elementor-addons' ),
				'type'     => Controls_Manager::SELECT,
				'default'  => 0,
				'options'  => $this->get_portfolio_taxonomies(),
				'condition'   =>  array(
					'single_post!' => 'yes'
				),
			)
		);
		$this->add_control(
			'show_categories',
			array(
				'label'       => esc_html__( 'Display Category Filter at the Top (Isotope Effect)', 'wpzoom-elementor-addons' ),
				'subtitle'    => esc_html__( 'Isotope Effect', 'wpzoom-elementor-addons' ),
				'description' => esc_html__( 'If you\'ve selected to display posts from All categories, then the filter will include all categories. If you selected to display posts from a specific category, then the filter will display its sub-categories.', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'yes',
				'condition'   =>  array(
					'single_post!' => 'yes'
				),
			)
		);
		$this->add_control(
			'hide_sub_categories',
			array(
				'label'       => esc_html__( 'Hide sub-categories in filter?', 'wpzoom-elementor-addons' ),
				'description' => esc_html__( 'If you select yes, filter will display only top level categories', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'no',
				'condition'   =>  array(
					'show_categories' => 'yes'
				),
			)
		);
		$this->add_control(
			'show_count',
			array(
				'label'    => esc_html__( 'Number of Posts', 'wpzoom-elementor-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
				'condition'   =>  array(
					'single_post!' => 'yes'
				),
			)
		);
		$this->add_control(
			'enable_ajax_items_loading',
			array(
				'label'       => esc_html__( 'Load Dynamically New Posts in Each Category', 'wpzoom-elementor-addons' ),
				'description' => esc_html__( 'This option will try to display the same number of posts in each category as it\'s configured in the Number of Posts option above.', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'yes',
				'condition'   =>  array(
					'single_post!' => 'yes'
				),
			)
		);

		$this->end_controls_section();
		
		//Design & Appearance
		$this->start_controls_section(
			'section_design_appearance',
			array(
				'label' => esc_html__( 'Design & Appearance', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT
			)
		);
		$this->add_control(
			'layout_type',
			array(
				'label'   => esc_html__( 'Items Style', 'wpzoom-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'full-width' => esc_html__( 'Title Overlay', 'wpzoom-elementor-addons' ),
					'narrow'     => esc_html__( 'Title Below', 'wpzoom-elementor-addons' ),
				),
				'default' => 'full-width',
			)
		);

		$this->add_control(
			'col_number',
			array(
				'label'       => esc_html__( 'Number of Columns', 'wpzoom-elementor-addons' ),
				'description' => esc_html__( 'The number of columns may vary depending on screen size', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4'
				),
				'default'     => '4',
			)
		);
		$this->add_control(
			'show_masonry',
			array(
				'label'       => esc_html__( 'Display Posts in Masonry Layout', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'no',
				'condition'   =>  array(
					'layout_type!' => 'narrow',
					'single_post!' => 'yes'

				),
			)
		);
		$this->add_control(
			'aspect_ratio',
			array(
				'label'   => esc_html__( 'Aspect Ratio', 'wpzoom-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'default'  => esc_html__( 'Landscape', 'wpzoom-elementor-addons' ),
					'cinema'   => esc_html__( 'Cinema', 'wpzoom-elementor-addons' ),
					'square'   => esc_html__( 'Square', 'wpzoom-elementor-addons' ),
					'portrait' => esc_html__( 'Portrait', 'wpzoom-elementor-addons' ),
					'original' => esc_html__( 'No Cropping', 'wpzoom-elementor-addons' ),
				),
				'default' => 'default',
			)
		);
		$this->add_control(
			'show_space',
			array(
				'label'       => wp_kses_post( esc_html__( 'Add Margins between Posts (whitespace)', 'wpzoom-elementor-addons' ) ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'no',			
			)
		);
		$this->end_controls_section();
	
		//Posts Settings
		$this->start_controls_section(
			'section_post_settings',
			array(
				'label' => esc_html__( 'Posts Settings', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT
			)
		);
		$this->add_control(
			'show_popup',
			array(
				'label'       => esc_html__( 'Enable Lightbox', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'yes',
			)
		);
		$this->add_control(
			'show_popup_caption',
			array(
				'label'       => esc_html__( 'Show Lightbox Caption', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'no',
			)
		);
		$this->add_control(
			'video_background_heading',
			array(
				'label' => esc_html__( 'Video Background', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			)
		);
		$this->add_control(
			'enable_background_video',
			array(
				'label'       => esc_html__( 'Enable Background Video on hover', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'yes',
			)
		);
		$this->add_control(
			'always_play_background_video',
			array(
				'label'       => esc_html__( 'Play Always Video Background', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'no',
			)
		);
		$this->add_control(
			'details_to_show_heading',
			array(
				'label' => esc_html__( 'Details to Show', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			)
		);
		$this->add_control(
			'enable_director_name',
			array(
				'label'       => esc_html__( 'Display Director Name', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'no',
			)
		);
		$this->add_control(
			'enable_year',
			array(
				'label'       => esc_html__( 'Display Year of Production', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'no',
			)
		);
		$this->add_control(
			'enable_category',
			array(
				'label'       => esc_html__( 'Display Category Name', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'no',
			)
		);
		$this->add_control(
			'show_excerpt',
			array(
				'label'       => esc_html__( 'Display Excerpts', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'no',
			)
		);
		$this->add_control(
			'view_all_btn',
			array(
				'label'       => esc_html__( 'Display Read More button', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'yes',
				'condition'   =>  array(
					'layout_type!' => 'narrow',
					'show_popup!' => 'yes'
				),
			)
		);
		$this->add_control(
			'readmore_text',
			array(
				'label'     => esc_html__( 'Text for Read More button', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Read More', 'wpzoom-elementor-addons' ),
				'condition' => array(
					'view_all_btn' => 'yes'
				),
			)
		);
		$this->end_controls_section();

		//"View All" or "Load More"
		$this->start_controls_section(
			'section_view_all_load_more_settings',
			array(
				'label' => esc_html__( '"View All" or "Load More" Button at the Bottom', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
				'condition'   =>  array(
					'single_post!' => 'yes'
				),
			)
		);
		$this->add_control(
			'view_all_enabled',
			array(
				'label'       => esc_html__( 'Display View All button', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'yes',
			)
		);
		$this->add_control(
			'view_all_ajax_loading',
			array(
				'label'       => esc_html__( 'Load new posts dynamically', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'no',
			)
		);
		$this->add_control(
			'view_all_text',
			array(
				'label'       => esc_html__( 'Text for View All button', 'wpzoom-elementor-addons' ),
				'description' => esc_html__( 'Change the text to something like "Load More" if you have enabled the option to load new posts dynamically.', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'View All', 'wpzoom' ),
			)
		);
		$this->add_control(
			'view_all_link',
			array(
				'label' => esc_html__( 'Link for View All button', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
				'placeholder' => esc_html__( 'https://your-link.com', 'wpzoom-elementor-addons' ),
				'default' => array(
					'url' => '#',
				),
			)
		);
		$this->add_responsive_control(
			'view_all_align',
			array(
				'label'   => esc_html__( 'Alignment', 'wpzoom-elementor-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-view_all-link' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'view_all_width',
			[
				'label' => esc_html__( 'Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range' => [
					'px' => [
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-view_all-link a.btn' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		//Style and Design Options
		//Portfolio Item Styling.
		$this->start_controls_section(
			'section_portfolio_item',
			array(
				'label' => esc_html__( 'Portfolio Item', 'wpzoom-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE
			)
		);

		//Portfolio Item border radius.
		$this->add_control(
			'portfolio_item_border_width',
			[
				'label'      => esc_html__( 'Border', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .portfolio-grid .portfolio_item' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}'
				],			
			]
		);

		// Border Radius.
		$this->add_control(
			'portfolio_item_style_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'wpzoom-elementor-addons' ),
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
					'{{WRAPPER}} .portfolio-grid .portfolio_item' => 'border-radius: {{SIZE}}{{UNIT}}'
				]
			]
		);

		$this->start_controls_tabs( 'portfolio_item_style' );

		// Normal tab.
		$this->start_controls_tab(
			'portfolio_item_style_normal',
			[
				'label' => esc_html__( 'Normal', 'wpzoom-elementor-addons' )
			]
		);

		// Normal border color.
		$this->add_control(
			'portfolio_item_style_normal_border_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Border Color', 'wpzoom-elementor-addons' ),
				'separator' => '',
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .portfolio-grid .portfolio_item' => 'border-color: {{VALUE}};'
				]
			]
		);

		// Normal box shadow.
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'grid_button_style_normal_box_shadow',
				'selector' => '{{WRAPPER}} .portfolio-grid .portfolio_item'
			]
		);

		$this->end_controls_tab();

		// Hover tab.
		$this->start_controls_tab(
			'portfolio_item_style_hover',
			[
				'label' => esc_html__( 'Hover', 'wpzoom-elementor-addons' )
			]
		);

		// Hover border color.
		$this->add_control(
			'portfolio_item_style_hover_border_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Border Color', 'wpzoom-elementor-addons' ),
				'separator' => '',
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .portfolio-grid .portfolio_item:hover' => 'border-color: {{VALUE}};'
				]
			]
		);

		// Hover box shadow.
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'portfolio_item_style_hover_box_shadow',
				'selector' => '{{WRAPPER}} .portfolio-grid .portfolio_item:hover',
			]
		);
		$this->add_control(
			'portfolio_item_overlay_bg_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Overlay Background', 'wpzoom-elementor-addons' ),
				'selectors' => array(
					'{{WRAPPER}} .portfolio-grid .portfolio_item:hover .entry-thumbnail-popover, .portfolio-showcase .portfolio_item:hover .entry-thumbnail-popover' => 'background-color: {{VALUE}};'
				),
			]
		);


		$this->end_controls_tab();
		$this->end_controls_tabs();

		// Box internal padding.
		$this->add_responsive_control(
			'portfolio_item_style_padding',
			[
				'label'      => esc_html__( 'Padding', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .portfolio-grid .portfolio_item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}'
				]
			]
		);

		$this->end_controls_section();

		//Widget Title Styles section
		$this->start_controls_section(
			'section_widget_title_style',
			array(
				'label' => esc_html__( 'Widget Title', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE
			)
		);

		// Title typography.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'widget_title_style_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .portfolio-showcase .wpzoom-portfolio-showcase-widget-title'
			)
		);

		// Title color.
		$this->add_control(
			'widget_title_style_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-showcase .wpzoom-portfolio-showcase-widget-title' => 'color: {{VALUE}};'
				)
			)
		);
		// Widget title margins.
		$this->add_responsive_control(
			'widget_title_style_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .portfolio-showcase .wpzoom-portfolio-showcase-widget-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}'
				]
			)
		);

		$this->end_controls_section();

		//Filter Styles
		$this->start_controls_section(
			'section_portfolio_filter_style',
			array(
				'label' => esc_html__( 'Filter', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_categories' => 'yes'
				)
			)
		);

		//Filter typography.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'portfolio_filter_style_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .portfolio-archive-taxonomies a'
			)
		);

		$this->start_controls_tabs( 'portfolio_filter_color_style' );

		// Normal tab.
		$this->start_controls_tab(
			'portfolio_filter_style_normal',
			array(
				'label' => esc_html__( 'Normal', 'wpzoom-elementor-addons' )
			)
		);

		//Filter color.
		$this->add_control(
			'portfolio_filter_style_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-archive-taxonomies a' => 'color: {{VALUE}};'
				)
			)
		);
		$this->end_controls_tab();

		// Hover tab.
		$this->start_controls_tab(
			'portfolio_filter_style_hover',
			array(
				'label' => esc_html__( 'Hover', 'wpzoom-elementor-addons' )
			)
		);

		//Filter hover color.
		$this->add_control(
			'portfolio_filter_style_hover_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-archive-taxonomies a:hover' => 'color: {{VALUE}};'
				)
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'filter_align',
			array(
				'label' => esc_html__( 'Alignment', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-left'
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-center'
					],
					'right' => [
						'title' => esc_html__( 'Right', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-right'
					]
				],
				'default' => 'right',
				'selectors' => [
					'{{WRAPPER}} .portfolio-archive-taxonomies ul' => 'text-align: {{VALUE}};'
				],
				'separator' => 'before'
			)
		);

		// Filter padding.
		$this->add_responsive_control(
			'filter_style_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .portfolio-archive-taxonomies ul' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important'
				]
			)
		);

		$this->end_controls_section();		

		//Portfolio Details box section
		$this->start_controls_section(
			'section_portfolio_info_style',
			array(
				'label' => esc_html__( 'Details Box', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'   =>  array(
					'layout_type' => 'narrow',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'portfolio_info_style_bg',
				'selector' => '{{WRAPPER}} .portfolio-grid .clean_skin_wrap_post'
			)
		);
		// Paddings.
		$this->add_responsive_control(
			'portfolio_info_style_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .portfolio-grid .clean_skin_wrap_post' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}'
				]
			)
		);

		$this->end_controls_section();

		//Title Styles section
		$this->start_controls_section(
			'section_portfolio_item_style',
			array(
				'label' => esc_html__( 'Title', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE
			)
		);

		// Title typography.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'portfolio_title_style_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .portfolio-grid .portfolio_item .portfolio_item-title, {{WRAPPER}} .portfolio-grid .portfolio_item .portfolio_item-title > a'
			)
		);

		$this->start_controls_tabs( 'portfolio_title_color_style' );

		// Normal tab.
		$this->start_controls_tab(
			'portfolio_title_style_normal',
			array(
				'label' => esc_html__( 'Normal', 'wpzoom-elementor-addons' )
			)
		);

		// Title color.
		$this->add_control(
			'portfolio_title_style_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-grid .portfolio_item .portfolio_item-title, {{WRAPPER}} .portfolio-grid .portfolio_item .portfolio_item-title > a' => 'color: {{VALUE}};'
				)
			)
		);
		$this->end_controls_tab();

		// Hover tab.
		$this->start_controls_tab(
			'portfolio_title_style_hover',
			array(
				'label' => esc_html__( 'Hover', 'wpzoom-elementor-addons' )
			)
		);

		// Title hover color.
		$this->add_control(
			'portfolio_title_style_hover_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-grid .portfolio_item .portfolio_item-title:hover, {{WRAPPER}} .portfolio-grid .portfolio_item .portfolio_item-title > a:hover' => 'color: {{VALUE}};'
				)
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'portfolio_item_title_align',
			array(
				'label'   => esc_html__( 'Alignment', 'wpzoom-elementor-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justified', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-showcase .portfolio_item-title' => 'text-align: {{VALUE}};',
				),
			)
		);
		// Title margins.
		$this->add_responsive_control(
			'portfolio_title_style_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .portfolio-showcase .portfolio_item-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}'
				]
			)
		);

		$this->end_controls_section();

		//Category Styles
		$this->start_controls_section(
			'section_portfolio_cat_style',
			array(
				'label' => esc_html__( 'Category', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'enable_category' => 'yes'
				)
			)
		);

		//Category typography.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'portfolio_cat_style_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .portfolio-grid .portfolio_item .entry-meta, {{WRAPPER}} .portfolio-grid .portfolio_item .entry-meta > a'
			)
		);

		$this->start_controls_tabs( 'portfolio_cat_color_style' );

		// Normal tab.
		$this->start_controls_tab(
			'portfolio_cat_style_normal',
			array(
				'label' => esc_html__( 'Normal', 'wpzoom-elementor-addons' )
			)
		);

		//Category color.
		$this->add_control(
			'portfolio_cat_style_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-grid .portfolio_item .entry-meta, {{WRAPPER}} .portfolio-grid .portfolio_item .entry-meta > a, .clean_skin_wrap_post a' => 'color: {{VALUE}};'
				)
			)
		);
		$this->end_controls_tab();

		// Hover tab.
		$this->start_controls_tab(
			'portfolio_cat_style_hover',
			array(
				'label' => esc_html__( 'Hover', 'wpzoom-elementor-addons' )
			)
		);

		//Category hover color.
		$this->add_control(
			'portfolio_cat_style_hover_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-grid .portfolio_item .entry-meta:hover, {{WRAPPER}} .portfolio-grid .portfolio_item .entry-meta > a:hover,.clean_skin_wrap_post a:hover' => 'color: {{VALUE}};'
				)
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'portfolio_cat_align',
			array(
				'label'   => esc_html__( 'Alignment', 'wpzoom-elementor-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justified', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-grid .portfolio_item .entry-meta' => 'text-align: {{VALUE}};',
				),
			)
		);
		// Title margins.
		$this->add_responsive_control(
			'portfolio_cat_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .portfolio-grid .portfolio_item .entry-meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}'
				]
			)
		);

		$this->end_controls_section();

		//Excerpt Styles
		$this->start_controls_section(
			'section_portfolio_excerpt_style',
			array(
				'label' => esc_html__( 'Excerpts', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_excerpt' => 'yes'
				)
			)
		);

		//Excerpt typography.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'portfolio_excerpt_style_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .portfolio-grid .portfolio_item p'
			)
		);

		$this->start_controls_tabs( 'portfolio_excerpt_color_style' );

		// Normal tab.
		$this->start_controls_tab(
			'portfolio_excerpt_style_normal',
			array(
				'label' => esc_html__( 'Normal', 'wpzoom-elementor-addons' )
			)
		);

		//Excerpt color.
		$this->add_control(
			'portfolio_excerpt_style_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-grid .portfolio_item p' => 'color: {{VALUE}};'
				)
			)
		);
		$this->end_controls_tab();

		// Hover tab.
		$this->start_controls_tab(
			'portfolio_excerpt_style_hover',
			array(
				'label' => esc_html__( 'Hover', 'wpzoom-elementor-addons' )
			)
		);

		//Excerpt hover color.
		$this->add_control(
			'portfolio_excerpt_style_hover_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-grid .portfolio_item p:hover' => 'color: {{VALUE}};'
				)
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'portfolio_excerpt_align',
			array(
				'label'   => esc_html__( 'Alignment', 'wpzoom-elementor-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justified', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-grid .portfolio_item p' => 'text-align: {{VALUE}};',
				),
			)
		);
		// Title margins.
		$this->add_responsive_control(
			'portfolio_excerpt_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .portfolio-grid .portfolio_item p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}'
				]
			)
		);

		$this->end_controls_section();

		//ReadMore Styles
		$this->start_controls_section(
			'section_portfolio_readmore_style',
			array(
				'label' => esc_html__( 'Read More Button', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'view_all_btn' => 'yes'
				)
			)
		);

		//ReadMore typography.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'portfolio_readmore_style_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .portfolio-grid .portfolio_item span.btn'
			)
		);

		$this->start_controls_tabs( 'portfolio_readmore_color_style' );

		//ReadMore tab.
		$this->start_controls_tab(
			'portfolio_readmore_style_normal',
			array(
				'label' => esc_html__( 'Normal', 'wpzoom-elementor-addons' )
			)
		);

		//ReadMore color.
		$this->add_control(
			'portfolio_readmore_style_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-grid .portfolio_item span.btn' => 'color: {{VALUE}};'
				)
			)
		);
		$this->end_controls_tab();

		// Hover tab.
		$this->start_controls_tab(
			'portfolio_readmore_style_hover',
			array(
				'label' => esc_html__( 'Hover', 'wpzoom-elementor-addons' )
			)
		);

		//ReadMore hover color.
		$this->add_control(
			'portfolio_readmore_style_hover_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-grid .portfolio_item span.btn:hover' => 'color: {{VALUE}};'
				)
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		//ReadMore border radius.
		$this->add_control(
			'portfolio_readmore_border_width',
			array(
				'label'      => esc_html__( 'Border', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .portfolio-grid .portfolio_item span.btn' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}'
				],			
			)
		);

		//ReadMore Border Radius.
		$this->add_control(
			'portfolio_readmore_border_radius',
			array(
				'label'     => esc_html__( 'Border Radius', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 100
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200
					]
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-grid .portfolio_item span.btn' => 'border-radius: {{SIZE}}{{UNIT}}'
				]
			)
		);

		$this->start_controls_tabs( 'portfolio_readmore_border_color_style' );

		//ReadMore tab.
		$this->start_controls_tab(
			'portfolio_readmore_border_style_normal',
			array(
				'label' => esc_html__( 'Normal', 'wpzoom-elementor-addons' )
			)
		);

		//ReadMore color.
		$this->add_control(
			'portfolio_readmore_border_style_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Border Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-grid .portfolio_item span.btn' => 'border-color: {{VALUE}};'
				)
			)
		);
		$this->end_controls_tab();

		// Hover tab.
		$this->start_controls_tab(
			'portfolio_readmore_border_style_hover',
			array(
				'label' => esc_html__( 'Hover', 'wpzoom-elementor-addons' )
			)
		);
		//ReadMore hover color.
		$this->add_control(
			'portfolio_readmore_border_style_hover_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Border Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-grid .portfolio_item span.btn:hover' => 'border-color: {{VALUE}};'
				)
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		//View All or Load More Button Styles
		$this->start_controls_section(
			'section_portfolio_viewall_style',
			array(
				'label' => esc_html__( 'View All/Load More Button', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'view_all_enabled' => 'yes'
				)
			)
		);

		//View All or Load More typography.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'portfolio_viewall_style_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .portfolio-view_all-link a.btn'
			)
		);

		$this->start_controls_tabs( 'portfolio_viewall_color_style' );

		//View All or Load More tab.
		$this->start_controls_tab(
			'portfolio_viewall_style_normal',
			array(
				'label' => esc_html__( 'Normal', 'wpzoom-elementor-addons' )
			)
		);

		//View All or Load More color.
		$this->add_control(
			'portfolio_viewall_style_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-view_all-link a.btn' => 'color: {{VALUE}};'
				)
			)
		);
		$this->end_controls_tab();

		// Hover tab.
		$this->start_controls_tab(
			'portfolio_viewall_style_hover',
			array(
				'label' => esc_html__( 'Hover', 'wpzoom-elementor-addons' )
			)
		);

		//View All or Load More hover color.
		$this->add_control(
			'portfolio_viewall_style_hover_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-view_all-link a.btn:hover' => 'color: {{VALUE}};'
				)
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		//View All or Load More border radius.
		$this->add_control(
			'portfolio_viewall_border_width',
			array(
				'label'      => esc_html__( 'Border', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .portfolio-view_all-link a.btn' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}'
				],			
			)
		);

		//View All or Load MoreBorder Radius.
		$this->add_control(
			'portfolio_viewall_border_radius',
			array(
				'label'     => esc_html__( 'Border Radius', 'wpzoom-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 100
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200
					]
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-view_all-link a.btn' => 'border-radius: {{SIZE}}{{UNIT}}'
				]
			)
		);

		$this->start_controls_tabs( 'portfolio_viewall_border_color_style' );

		//View All or Load More tab.
		$this->start_controls_tab(
			'portfolio_viewall_border_style_normal',
			array(
				'label' => esc_html__( 'Normal', 'wpzoom-elementor-addons' )
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'portfolio_viewall_normal_style_background',
				'label' => esc_html__( 'Background', 'wpzoom-elementor-addons' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .portfolio-view_all-link a.btn',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'global' => [
							'default' => '',
						],
					],
				],
			]
		);

		//View All or Load More color.
		$this->add_control(
			'portfolio_viewall_border_style_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Border Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-view_all-link a.btn' => 'border-color: {{VALUE}};'
				)
			)
		);
		$this->end_controls_tab();

		// Hover tab.
		$this->start_controls_tab(
			'portfolio_viewall_border_style_hover',
			array(
				'label' => esc_html__( 'Hover', 'wpzoom-elementor-addons' )
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'portfolio_viewall_hover_style_background',
				'label' => esc_html__( 'Background', 'wpzoom-elementor-addons' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .portfolio-view_all-link a.btn:hover',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'global' => [
							'default' => '',
						],
					],
				],
			]
		);
		//View All or Load More hover color.
		$this->add_control(
			'portfolio_viewall_border_style_hover_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Border Color', 'wpzoom-elementor-addons' ),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .portfolio-view_all-link a.btn:hover' => 'border-color: {{VALUE}};'
				)
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'portfolio_viewall_style_box_shadow',
				'selector' => '{{WRAPPER}} .portfolio-view_all-link a.btn',
			]
		);

		$this->add_responsive_control(
			'portfolio_viewall_style_text_padding',
			[
				'label' => esc_html__( 'Padding', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .portfolio-view_all-link a.btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		// Viewall button margin
		$this->add_responsive_control(
			'portfolio_viewall_style_margin',
			[
				'label'      => esc_html__( 'Margin', 'wpzoom-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .portfolio-view_all-link a.btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Get portfolio posts.
	 *
	 * Retrieve a list of all portfolio posts.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array All portfolio posts.
	 */
	protected function get_portfolio_posts() {

		$portfolio_posts = array(
			esc_html__( 'Select a Post', 'wpzoom-elementor-addons' ),
		);

		$args = array(
			'post_type'   => 'portfolio_item',
			'post_status' => array( 'publish' ),
			'numberposts' => -1
		);

		$posts = get_posts( $args );

		if ( !empty( $posts ) && !is_wp_error( $posts ) ) {
			foreach ( $posts as $key => $post ) {
				if ( is_object( $post ) && property_exists( $post, 'ID' ) ) {
					$portfolio_posts[ $post->ID ] = get_the_title( $post );
				}
			}
		}

		return $portfolio_posts;

	}


	/**
	 * Get portfolio taxonomies.
	 *
	 * Retrieve a list of all portfolio taxonomies.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array All portfolio taxonomies.
	 */
	protected function get_portfolio_taxonomies() {

		$portfolio_tax = array(
			'0' => esc_html__( 'All', 'wpzoom-elementor-addons' )
		);

		$tax_args = array(
			'orderby'    => 'name',
			'order'      => 'asc',
			'hide_empty' => false,
		);

		$terms = get_terms( 'portfolio', $tax_args );

		if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
			foreach ( $terms as $key => $taxonomy ) {
				if ( is_object( $taxonomy ) && property_exists( $taxonomy, 'slug' ) && property_exists( $taxonomy, 'name' ) ) {
					$portfolio_tax[ $taxonomy->slug ] = $taxonomy->name . ' (' . $taxonomy->count . ')';
				}
			}
		}

		return $portfolio_tax;

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
	public function render() {

		if ( !WPZOOM_Elementor_Widgets::is_supported_theme() ) {
			if( current_user_can('editor') || current_user_can('administrator') ) {
				echo '<h3>' . esc_html__( 'Widget not available', 'wpzoom-elementor-addons' ) . '</h3>';
				echo wp_kses_post( __( 'This widget is supported only by the <a href="https://www.wpzoom.com/themes/inspiro/">"Inspiro"</a> and <a href="https://www.wpzoom.com/themes/inspiro-pro/">"Inspiro PRO"</a> themes', 'wpzoom-elementor-addons' ) );
			}
			return;			
		}

		// Get settings.
		$settings = $this->get_settings();

		$widget_title                       = $settings['widget_title'];

		$single_post                        = ( 'yes' == $settings['single_post'] ? true : false );
		$single_post_id                     = ( '0' !== $settings['single_post_id'] ? intval( $settings['single_post_id'] ) : -1 );
		$category                           = $settings['category'];
		$show_count                         = $settings['show_count'];
		$col_number                         = $settings['col_number'];
		$layout_type                        = $settings['layout_type'];
		$aspect_ratio                       = $settings['aspect_ratio'];
		$show_masonry                       = ( 'yes' == $settings['show_masonry'] ? true : false );
		$show_popup                         = ( 'yes' == $settings['show_popup'] ? true : false );
		$show_popup_caption                 = ( 'yes' == $settings['show_popup_caption'] ? true : false ) ;
		$show_space                         = ( 'yes' == $settings['show_space'] ? true : false );
		$show_categories                    = ( 'yes' == $settings['show_categories'] ? true : false );
		$hide_subcategories                 = ( 'yes' == $settings['hide_sub_categories'] ? true : false );
		$background_video                   = ( 'yes' == $settings['enable_background_video'] ? true : false );
		$enable_director_name               = ( 'yes' == $settings['enable_director_name'] ? true : false );
		$enable_year                        = ( 'yes' == $settings['enable_year'] ? true : false );
		$enable_category                    = ( 'yes' == $settings['enable_category'] ? true : false );
		$always_play_background_video       = ( 'yes' == $settings['always_play_background_video'] ? true : false );
		$always_play_background_video_class = ( 'yes' == $settings['always_play_background_video'] ? ' always-play-background-video' : '' );
		$enable_ajax_items_loading          = ( 'yes' == $settings['enable_ajax_items_loading'] ? true : false );
		$show_excerpt                       = ( 'yes' == $settings['show_excerpt'] ? true : false );
		$view_all_btn                       = ( 'yes' == $settings['view_all_btn'] ? true : false );
		$view_all_ajax_loading              = ( 'yes' == $settings['view_all_ajax_loading'] ? true : false );
		$view_all_enabled                   = ( 'yes' == $settings['view_all_enabled'] ? true : false );
		$readmore_text                      = $settings['readmore_text'];
		$view_all_text                      = $settings['view_all_text'];
		$view_all_link                      = !empty( $settings['view_all_link']['url'] ) ? $settings['view_all_link']['url'] : $settings['view_all_link']['url'] = get_page_link( \option::get( 'portfolio_url' ) );

		if ( ! empty( $view_all_link ) ) {
			$this->add_link_attributes( 'button', $settings['view_all_link'] );
			$this->add_render_attribute( 'button', 'class', 'btn' );
		}
		if( ! empty( $view_all_text ) ) {
			$this->add_render_attribute( 'button', 'title', esc_attr( $view_all_text ) );
		}
		if( $view_all_ajax_loading ) {
			$this->add_render_attribute( 'button', 'data-ajax-loading', '1' );
		}

		if ( $single_post ) {
			$args = array(
				'post_type' => 'portfolio_item',
				'p'         => $single_post_id
			);
		} else {
			$args = array(
				'post_type'      => 'portfolio_item',
				'post_status'    => array( 'publish' ),
				'posts_per_page' => $show_count,
				'orderby'        =>'menu_order date'
			);
		}

        if ( $category ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'portfolio',
                    'terms'    => $category,
                    'field'    => 'slug',
                )
            );
        }

		echo '<div class="portfolio-showcase">';

		$wp_query = new \WP_Query( $args );

		$count = $wp_query->found_posts;
		
		if ( $wp_query->have_posts() ) :

			echo '<div class="portfolio-showcase-header">';

			if( ! empty( $widget_title ) ) {

				$this->add_render_attribute( 'widget_title', 'class', 'wpzoom-portfolio-showcase-widget-title' );
				$this->add_inline_editing_attributes( 'widget_title' );
				
				printf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag( $settings['widget_title_tag'] ), $this->get_render_attribute_string( 'widget_title' ), $widget_title );
				
			}

			if ( !$single_post ) {
				include( __DIR__ . '/view/filter.php' );
			}

			echo '</div><!-- // .portfolio-showcase-header -->';

			if ( $show_masonry ) {
				echo '<div class="portfolio-masonry">';
			}

			if ( 'narrow' == $layout_type ) {
				echo '<div class="inner-wrap portfolio_template_clean">';
			}
			?>
			<div <?php 
				echo( ! empty( $category ) ? 'data-subcategory="' . esc_attr( $category ) . '"' : '' ); ?>
				data-ajax-items-loading="<?php echo esc_attr( $enable_ajax_items_loading ) ?>"
				data-nonce="<?php echo esc_attr( wp_create_nonce( 'wpz_get_portfolio_items' ) ) ?>"
				data-count-nonce="<?php echo esc_attr( wp_create_nonce( 'wpz_count_portfolio_items' ) ) ?>"
				data-items-count="<?php echo esc_attr( $count ) ?>"
				data-instance="<?php echo esc_attr( wp_json_encode( array(
					'layout_type'                  => $layout_type,
					'col_number'                   => $col_number,
					'aspect_ratio'                 => $aspect_ratio,
					'background_video'             => $background_video,
					'show_masonry'                 => $show_masonry,
					'show_popup'                   => $show_popup,
					'show_popup_caption'           => $show_popup_caption,
					'show_excerpt'                 => $show_excerpt,
					'view_all_btn'                 => $view_all_btn,
					'readmore_text'                => $readmore_text,
					'enable_director_name'         => $enable_director_name,
					'enable_year'                  => $enable_year,
					'enable_category'              => $enable_category,
					'show_count'                   => $show_count,
					'show_categories'              => true,
					'always_play_background_video' => $always_play_background_video
				) ) ) ?>"
				class="portfolio-grid <?php if ( $show_space ) { ?> portfolio_with_space<?php } ?> col_no_<?php echo esc_attr( $col_number ); ?> <?php echo esc_attr( $always_play_background_video_class ); // WPCS: XSS OK. ?>"
			>
				<?php
					$this->looper( $wp_query,
						array(
							'single_post'                  => $single_post,
							'layout_type'                  => $layout_type,
							'col_number'                   => $col_number,
							'aspect_ratio'                 => $aspect_ratio,
							'background_video'             => $background_video,
							'show_masonry'                 => $show_masonry,
							'show_popup'                   => $show_popup,
							'show_popup_caption'           => $show_popup_caption,
							'enable_director_name'         => $enable_director_name,
							'enable_year'                  => $enable_year,
							'enable_category'              => $enable_category,
							'hide_subcategories'           => $hide_subcategories,
							'show_excerpt'                 => $show_excerpt,
							'view_all_btn'                 => $view_all_btn,
							'readmore_text'                => $readmore_text,
							'always_play_background_video' => $always_play_background_video
						)
					);
				?>

			</div><!-- // .portfolio-grid -->

			<?php
				if ( 'narrow' == $layout_type ) { 
					echo '</div>';
				}  

				if ( $show_masonry ) { 
					echo '</div>';
				} 
			?>				

		<?php else: ?>

			<div class="inner-wrap" style="text-align:center;">
				<h3><?php esc_html__( 'No Portfolio Posts Found', 'wpzoom-elementor-addons' ) ?></h3>
				<p class="description"><?php printf( __( 'Please add a few Portfolio Posts first <a href="%1$s">here</a>.', 'wpzoom-elementor-addons' ), esc_url( admin_url( 'post-new.php?post_type=portfolio_item' ) ) ); ?></p>
			</div>

		<?php endif; ?>
		
		<?php if ( ! $single_post ) : ?>
			<div class="portfolio-preloader">
				<div id="loading-39x">
					<div class="spinner">
						<div class="rect1"></div> 
						<div class="rect2"></div> 
						<div class="rect3"></div> 
						<div class="rect4"></div> 
						<div class="rect5"></div>
					</div>
				</div>
			</div>
		<?php endif; ?>

        <?php if ( ! $single_post && $view_all_enabled ) : ?>

            <?php if ( 'narrow' == $layout_type ) { ?>
                <div class="inner-wrap">
            <?php } ?>

            <div class="portfolio-view_all-link">
				<a <?php echo $this->get_render_attribute_string( 'button' ); ?>>
					<?php echo esc_html( $view_all_text ); ?>
				</a>
            </div><!-- .portfolio-view_all-link -->
			<?php
				if ( 'narrow' == $layout_type ) { 
					echo '</div>';
				} 
			?>

        <?php endif; ?>

		</div><!-- // .portfolio-showcase -->

		<?php
	}

	function looper( $wp_query, $settings ) {

		$single_post                  = wp_validate_boolean( $settings['single_post'] );
        $show_masonry                 = wp_validate_boolean( $settings['show_masonry'] );
        $show_popup         		  = wp_validate_boolean( $settings['show_popup'] );
        $col_number                   = $settings['col_number'];
        $layout_type        		  = $settings['layout_type'];
        $aspect_ratio                 = $settings['aspect_ratio'];
        $show_popup_caption           = wp_validate_boolean( $settings['show_popup_caption'] );
        $show_excerpt                 = wp_validate_boolean( $settings['show_excerpt'] );
        $view_all_btn                 = wp_validate_boolean( $settings['view_all_btn'] );
        $readmore_text                = $settings['readmore_text'];
        $background_video             = wp_validate_boolean( $settings['background_video'] );
        $enable_director_name         = wp_validate_boolean( $settings['enable_director_name'] );
        $enable_year                  = wp_validate_boolean( $settings['enable_year'] );
        $enable_category              = wp_validate_boolean( $settings['enable_category'] );
		$hide_subcategories           = wp_validate_boolean( $settings['hide_subcategories'] );
        $always_play_background_video = wp_validate_boolean( $settings['always_play_background_video'] );

        while ( $wp_query->have_posts() ) : $wp_query->the_post();

            $post_thumbnail                    = get_the_post_thumbnail_url( get_the_ID() );
            $video_background_popup_url        = get_post_meta( get_the_ID(), 'wpzoom_portfolio_video_popup_url', true );
            $background_popup_url              = ! empty( $video_background_popup_url ) ? $video_background_popup_url : $post_thumbnail;
            $video_background_popup_url_mp4    = get_post_meta( get_the_ID(), 'wpzoom_portfolio_video_popup_url_mp4', true );
            $video_background_popup_url_webm   = get_post_meta( get_the_ID(), 'wpzoom_portfolio_video_popup_url_webm', true );
            $video_background_popup_video_type = get_post_meta( get_the_ID(), 'wpzoom_portfolio_popup_video_type', true );
            $popup_video_type                  = ! empty( $video_background_popup_video_type ) ? $video_background_popup_video_type : 'external_hosted';
            $is_video_popup                    = $video_background_popup_url_mp4 || $video_background_popup_url_webm;

            #giphy start
            $instance = function_exists( 'init_video_background_on_hover_module' ) ? init_video_background_on_hover_module() : array();
            $final_background_src = $instance->get_data( get_the_ID() );
            $is_video_background = $background_video && !empty( $final_background_src );
            #giphy end

            $video_director = get_post_meta( get_the_ID(), 'su_portfolio_item_director', true );
            $video_year = get_post_meta( get_the_ID(), 'su_portfolio_item_year', true );

            $popup_final_external_src = ! empty( $video_background_popup_url_mp4 ) ? $video_background_popup_url_mp4 : $video_background_popup_url_webm;

            $articleClass = ( ! has_post_thumbnail() && ! $is_video_background ) ? 'no-thumbnail ' : '';

            $portfolios = wp_get_post_terms( get_the_ID(), 'portfolio' );

            // $size = ( $show_masonry == true ) ? "portfolio_item-masonry" : "portfolio_item-thumbnail";

            if ( $show_masonry == true ) {
                $size = 'portfolio_item-masonry';
            } elseif ($col_number == '1' && $aspect_ratio == 'default') {
                $size = 'portfolio_item-thumbnail_wide';
            } elseif ($col_number == '1' && $aspect_ratio == 'cinema') {
                $size = 'portfolio_item-thumbnail_wide_cinema';
            } elseif ($col_number == '1' && $aspect_ratio == 'original') {
               $size = 'entry-cover';
            } elseif ($aspect_ratio == 'cinema' && $col_number != '1' && $show_masonry != true ) {
                $size = 'portfolio_item-thumbnail_cinema';
            } elseif ($aspect_ratio == 'square' && $col_number != '1' && $show_masonry != true) {
                $size = 'portfolio_item-thumbnail_square';
            } elseif ($aspect_ratio == 'portrait' && $col_number != '1' && $show_masonry != true) {
                $size = 'portfolio_item-thumbnail_portrait';
            } elseif ($aspect_ratio == 'original' && $col_number != '1' && $show_masonry != true) {
                $size = 'portfolio_item-masonry';
            } else {
                $size ='portfolio_item-thumbnail';
            }

            if ( $is_video_background ) {
                $filetype = wp_check_filetype( $final_background_src );

                $video_atts = array(
                    'loop',
                    'muted',
                    // 'preload="none"',
                    'playsinline',
                    'poster=' . esc_attr( get_the_post_thumbnail_url( get_the_ID(), $size ) ) . ''
                );

                if ($always_play_background_video) {
                    $video_atts[] = 'autoplay';
                }

                $video_atts           = implode( ' ', $video_atts );
                $is_video_popup_class = $is_video_background ? ' is-portfolio-gallery-video-background' : '';
                $articleClass .= $is_video_popup_class;
            }


            if ( is_array( $portfolios ) ) {
                foreach ( $portfolios as $portfolio ) {
                    $articleClass .= ' portfolio_' . $portfolio->term_id . '_item ';
					if( isset( $portfolio->parent ) && $hide_subcategories ) {
						$articleClass .= ' portfolio_' . $portfolio->parent . '_item ';
					}
                }
            }

            if ( wp_doing_ajax() ) {
                $articleClass .= ' ' . get_post_type( get_the_ID() );
            }
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( $articleClass . ' portfolio_item' ); ?>>
            <?php if ( $layout_type == 'narrow' ) { ?>

                <div class="portfolio_item_top_wrap">

                    <?php if ( $show_popup ) { ?>

                        <div class="entry-thumbnail-popover">
                            <div
                                class="entry-thumbnail-popover-content lightbox_popup_insp popover-content--animated"
                                data-show-caption="<?php echo esc_attr( $show_popup_caption ); ?>">
                                <!-- start lightbox -->
                                <?php if ( $popup_video_type === 'self_hosted' && $is_video_popup ): ?>
                                    <div id="zoom-popup-<?php echo the_ID(); ?>" class="mfp-hide"
                                         data-src="<?php echo esc_url( $popup_final_external_src ); ?>">
                                        <div class="mfp-iframe-scaler">
                                            <?php
                                            echo wp_video_shortcode(
                                                array(
                                                    'src'     => $popup_final_external_src,
                                                    'preload' => 'none',
                                                    //'autoplay' => 'on'
                                                ) );
                                            ?>
                                            <?php if ( $show_popup_caption ): ?>
                                                <div class="mfp-bottom-bar">
                                                    <div class="mfp-title">
                                                        <a href="<?php echo esc_url( get_permalink() ); ?>"
                                                           title="<?php echo esc_attr( get_the_title() ); ?>">
                                                            <?php the_title(); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                    </div>
                                    <a href="#zoom-popup-<?php echo the_ID(); ?>"
                                       class="mfp-inline portfolio-popup-video"></a>
                                <?php elseif ( ! empty( $video_background_popup_url ) ): ?><a
                                    class="mfp-iframe portfolio-popup-video"
                                    href="<?php echo esc_url( $video_background_popup_url ); ?>"></a>
                                <?php else: ?>
                                    <?php if( has_post_thumbnail() && !\option::is_on('lightbox_video_only') ): ?>
                                        <a class="mfp-image portfolio-popup-video popup_image_insp"
                                           href="<?php echo esc_url( $post_thumbnail ); ?>"></a>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <span class="portfolio_item-title" style="display: none;">
                                    <a href="<?php echo esc_url( get_permalink() ); ?>"
                                       title="<?php echo esc_attr( get_the_title() ); ?>"><?php the_title(); ?></a>
                                </span>

                            </div>
                        </div>

                        <?php if ( $is_video_background ): ?>
                            <video class="portfolio-gallery-video-background" <?php echo esc_attr( $video_atts ) // WPCS: XSS OK. ?>
                                   style=" width:100%; height:auto;vertical-align: middle; display:block;">
                                <source src="<?php echo esc_url( $final_background_src ) ?>"
                                        type="<?php echo esc_attr( $filetype['type'] ) ?>">
                            </video>

                            <?php the_post_thumbnail( $size ); ?>

                        <?php elseif ( has_post_thumbnail() ): ?>

                            <?php the_post_thumbnail ($size ); ?>

                        <?php else: ?>

                            <img width="600" height="400"
                                 src="<?php echo get_template_directory_uri() . '/images/portfolio_item-placeholder.gif'; ?>">

                        <?php endif; ?>

                    <?php } else { ?>

                        <a href="<?php echo esc_url( get_permalink() ); ?>"
                           title="<?php echo esc_attr( get_the_title() ); ?>">

                            <?php if ( $is_video_background ): ?>
                                <video class="portfolio-gallery-video-background" <?php echo esc_attr( $video_atts ) // WPCS: XSS OK. ?>
                                       style=" width:100%; height:auto;vertical-align: middle; display:block;">
                                    <source src="<?php echo esc_url( $final_background_src ); ?>"
                                            type="<?php echo esc_attr( $filetype['type'] ) ?>">
                                </video>

                                <?php the_post_thumbnail( $size ); ?>

                            <?php elseif ( has_post_thumbnail() ): ?>

                                <?php the_post_thumbnail( $size ); ?>

                            <?php else: ?>

                                <img width="600" height="400"
                                     src="<?php echo get_template_directory_uri() . '/images/portfolio_item-placeholder.gif'; ?>">

                            <?php endif; ?>

                        </a>

                    <?php } ?>

                </div>

                <div class="clean_skin_wrap_post">

                    <div class="entry-meta">
                        <ul>
                            <?php if ( $enable_director_name && $video_director ) { ?>
                               <li><?php echo esc_html( $video_director ); ?></li>
                            <?php } ?>

                            <?php if ( $enable_year && $video_year ) { ?>
                               <li><?php echo esc_html( $video_year ); ?></li>
                            <?php } ?>

                            <?php if ( $enable_category ) : ?>
                                <li>
                                <?php if ( is_array( $tax_menu_items = get_the_terms( get_the_ID(), 'portfolio' ) ) ) : ?>
                                    <?php foreach ( $tax_menu_items as $tax_menu_item ) : ?>
                                        <a class="portfolio_sub_category"
                                           href="<?php echo get_term_link( $tax_menu_item, $tax_menu_item->taxonomy ); ?>"><?php echo esc_html( $tax_menu_item->name ); ?></a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                             </li>
                             <?php endif; ?>
                        </ul>
                    </div>
                    <?php the_title( sprintf( '<h3 class="portfolio_item-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' ); ?>
					<?php if ( $show_excerpt == true ) : ?>
						<?php the_excerpt(); ?>
					<?php endif; ?>

                </div>
            <?php } else {
                if ( $show_popup ) {
                    ?>

                    <div class="entry-thumbnail-popover">
                        <div class="entry-thumbnail-popover-content lightbox_popup_insp popover-content--animated" data-show-caption="<?php echo esc_attr( $show_popup_caption ) ?>">

                            <a class="wpz-portfolio-link" href="<?php echo esc_url( get_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"></a>

                            <div class="entry-meta">

                                <!-- start lightbox --><?php if ( $popup_video_type === 'self_hosted' && $is_video_popup ): ?>
                                    <div id="zoom-popup-<?php echo the_ID(); ?>" class="animated slow mfp-hide">

                                        <div class="mfp-iframe-scaler">

                                            <?php
                                            echo wp_video_shortcode(
                                                array(
                                                    'src'     => $popup_final_external_src,
                                                    'preload' => 'none',
                                                    //'autoplay' => 'on'
                                                ) );
                                            ?>
                                            <?php if ( $show_popup_caption ): ?>
                                                <div class="mfp-bottom-bar">
                                                    <div class="mfp-title">
                                                        <a href="<?php echo esc_url( get_permalink() ); ?>"
                                                           title="<?php echo esc_attr( get_the_title() ); ?>">
                                                            <?php the_title(); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <a href="#zoom-popup-<?php echo the_ID(); ?>"
                                       class="mfp-inline portfolio-popup-video"></a>
                                <?php elseif ( ! empty( $video_background_popup_url ) ): ?><a
                                    class="mfp-iframe portfolio-popup-video"
                                    href="<?php echo esc_url( $video_background_popup_url ); ?>"></a>
                                <?php else: ?>
                                    <?php if( has_post_thumbnail() && !\option::is_on( 'lightbox_video_only' ) ) : ?>
                                        <a class="mfp-image portfolio-popup-video popup_image_insp"
                                           href="<?php echo esc_url( $post_thumbnail ); ?>"></a>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <h3 class="portfolio_item-title">
                                    <a href="<?php echo esc_url( get_permalink() ); ?>"
                                       title="<?php echo esc_attr( get_the_title() ); ?>"><?php the_title(); ?></a>
                                </h3>

                                <ul>
                                    <?php if ( $enable_director_name && $video_director ) { ?>
                                       <li><?php echo esc_html( $video_director ); ?></li>
                                    <?php } ?>

                                    <?php if ( $enable_year && $video_year ) { ?>
                                       <li><?php echo esc_html( $video_year ); ?></li>
                                    <?php } ?>

                                    <?php if ( $enable_category ) : ?><li>

                                         <?php if ( is_array( $tax_menu_items = get_the_terms( get_the_ID(), 'portfolio' ) ) ) : ?>
                                             <?php foreach ( $tax_menu_items as $tax_menu_item ) : ?>
                                                <?php echo esc_html( $tax_menu_item->name ); ?>
                                             <?php endforeach; ?>
                                         <?php endif; ?>
                                     </li>
                                     <?php endif; ?>
                                </ul>

                            </div>

                        </div>
                    </div>

                    <?php if ( $is_video_background ): ?>
                        <video class="portfolio-gallery-video-background" <?php echo esc_attr( $video_atts ) // WPCS: XSS OK. ?>
                               style=" width:100%; height:auto;vertical-align: middle; display:block;">
                            <source src="<?php echo esc_url( $final_background_src ) ?>"
                                    type="<?php echo esc_attr( $filetype['type'] ) ?>">
                        </video>

                        <?php the_post_thumbnail( $size ); ?>

                    <?php elseif ( has_post_thumbnail() ): ?>

                        <?php the_post_thumbnail( $size ); ?>

                    <?php else: ?>

                        <img width="600" height="400"
                             src="<?php echo get_template_directory_uri() . '/images/portfolio_item-placeholder.gif'; ?>">

                    <?php endif; ?>


                <?php } else { ?>

                    <a href="<?php echo esc_url( get_permalink() ); ?>"
                       title="<?php echo esc_attr( get_the_title() ); ?>">

                        <div class="entry-thumbnail-popover">
                            <div class="entry-thumbnail-popover-content popover-content--animated">
                                <?php the_title( '<h3 class="portfolio_item-title">', '</h3>' ); ?>

                                <div class="entry-meta">
                                    <ul>
                                        <?php if ( $enable_director_name && $video_director ) { ?>
                                           <li><?php echo esc_html( $video_director ); ?></li>
                                        <?php } ?>

                                        <?php if ( $enable_year && $video_year ) { ?>
                                           <li><?php echo esc_html( $video_year ); ?></li>
                                        <?php } ?>

                                        <?php if ( $enable_category ) : ?><li>

                                             <?php if ( is_array( $tax_menu_items = get_the_terms( get_the_ID(), 'portfolio' ) ) ) : ?>
                                                 <?php foreach ( $tax_menu_items as $tax_menu_item ) : ?>
                                                    <?php echo esc_html( $tax_menu_item->name ); ?>
                                                 <?php endforeach; ?>
                                             <?php endif; ?>
                                         </li>
                                         <?php endif; ?>
                                    </ul>
                                </div>

								<?php if ( $show_excerpt == true ) : ?>

									<?php the_excerpt(); ?>

								<?php endif; ?>

                                <?php if ( $view_all_btn == true ) : ?>

                                    <span class="btn"><?php echo esc_html( $readmore_text ); ?></span>

                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ( $is_video_background ): ?>
                            <video class="portfolio-gallery-video-background" <?php echo esc_attr( $video_atts ) // WPCS: XSS OK. ?>
                                   style=" width:100%; height:auto;vertical-align: middle; display:block;">
                                <source src="<?php echo esc_url( $final_background_src ) ?>"
                                        type="<?php echo esc_attr( $filetype['type'] ) ?>">
                            </video>

                            <?php the_post_thumbnail( $size ); ?>

                        <?php elseif ( has_post_thumbnail() ): ?>

                            <?php the_post_thumbnail( $size ); ?>

                        <?php else: ?>

                            <img width="600" height="400"
                                 src="<?php echo get_template_directory_uri() . '/images/portfolio_item-placeholder.gif'; ?>">

                        <?php endif; ?>
                    </a>
                <?php } ?>
            <?php } ?></article><?php endwhile; ?><?php

            wp_reset_postdata();

    }

}