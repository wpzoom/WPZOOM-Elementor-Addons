<?php
namespace WPZOOMElementorWidgets;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Typography;
use \Elementor\Utils;
use \Elementor\Widget_Base;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * ZOOM Elementor Widgets - Testimonial Widget.
 *
 * Elementor widget that inserts a customizable customer testimonial.
 *
 * @since 1.0.0
 */
class Testimonial extends Widget_Base {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wpzoom-elementor-addons-css-frontend-testimonial', plugins_url( 'frontend.css', __FILE__ ), [], WPZOOM_EL_ADDONS_VER );
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
		return 'wpzoom-elementor-addons-testimonial';
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
		return __( 'Testimonial', 'wpzoom-elementor-addons' );
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
		return 'eicon-testimonial';
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
			'wpzoom-elementor-addons-css-frontend-testimonial',
			'font-awesome-5-all',
			'font-awesome-4-shim'
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
			'font-awesome-4-shim'
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
  		$this->start_controls_section(
  			'zew_section_testimonial_image',
  			[
  				'label' => esc_html__( 'Testimonial Image', 'wpzoom-elementor-addons' )
  			]
  		);

		$this->add_control(
			'zew_testimonial_enable_avatar',
			[
				'label' => esc_html__( 'Display Avatar?', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Testimonial Avatar', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'zew_testimonial_enable_avatar' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'		=> 'image',
				'default'	=> 'thumbnail',
				'condition' => [
					'image[url]!' => '',
					'zew_testimonial_enable_avatar' => 'yes',
				],
			]
		);

		$this->end_controls_section();

  		$this->start_controls_section(
  			'zew_section_testimonial_content',
  			[
  				'label' => esc_html__( 'Testimonial Content', 'wpzoom-elementor-addons' )
  			]
  		);

		$this->add_control(
			'zew_testimonial_name',
			[
				'label' => esc_html__( 'User Name', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'John Doe', 'wpzoom-elementor-addons' ),
				'dynamic' => [ 'active' => true ]
			]
		);

		$this->add_control(
			'zew_testimonial_company_title',
			[
				'label' => esc_html__( 'Company Name', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Codetic', 'wpzoom-elementor-addons' ),
				'dynamic' => [ 'active' => true ]
			]
		);

		$this->add_control(
			'zew_testimonial_description',
			[
				'label' => esc_html__( 'Testimonial Description', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Add testimonial description here. Edit and place your own text.', 'wpzoom-elementor-addons' ),
			]
		);

		$this->add_control(
			'content_height',
			[
				'label' => esc_html__( 'Description Height', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units'	=> ['px', '%', 'em'],
				'range' => [
					'px' => [ 'max' => 300 ],
					'%'	=> [ 'max'	=> 100 ]
				],
				'selectors' => [
					'{{WRAPPER}} .zew-testimonial-content' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'zew_testimonial_enable_rating',
			[
				'label' => esc_html__( 'Display Rating?', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
		  'zew_testimonial_rating_number',
		  [
			 'label'       => __( 'Rating Number', 'wpzoom-elementor-addons' ),
			 'type' => Controls_Manager::SELECT,
			 'default' => 'rating-five',
			 'options' => [
			 	'rating-one'  => __( '1', 'wpzoom-elementor-addons' ),
			 	'rating-two' => __( '2', 'wpzoom-elementor-addons' ),
			 	'rating-three' => __( '3', 'wpzoom-elementor-addons' ),
			 	'rating-four' => __( '4', 'wpzoom-elementor-addons' ),
			 	'rating-five'   => __( '5', 'wpzoom-elementor-addons' ),
			 ],
			'condition' => [
				'zew_testimonial_enable_rating' => 'yes',
			],
		  ]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'zew_section_testimonial_styles_general',
			[
				'label' => esc_html__( 'Testimonial Styles', 'wpzoom-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'zew_testimonial_style',
			[
				'label'		=> __( 'Select Style', 'wpzoom-elementor-addons' ),
				'type'		=> Controls_Manager::SELECT,
				'default'	=> 'default-style',
				'options'	=> [
					'default-style'						=> __( 'Default', 'wpzoom-elementor-addons' ),
					'classic-style'						=> __( 'Classic', 'wpzoom-elementor-addons' ),
					'middle-style'						=> __( 'Content | Icon/Image | Bio', 'wpzoom-elementor-addons' ),
					'icon-img-left-content'				=> __( 'Icon/Image | Content', 'wpzoom-elementor-addons' ),
					'icon-img-right-content'			=> __( 'Content | Icon/Image', 'wpzoom-elementor-addons' ),
					'content-top-icon-title-inline'		=> __( 'Content Top | Icon Title Inline', 'wpzoom-elementor-addons' ),
					'content-bottom-icon-title-inline'	=> __( 'Content Bottom | Icon Title Inline', 'wpzoom-elementor-addons' )
				]
			]
		);

		$this->add_control(
			'zew_testimonial_is_gradient_background',
			[
				'label' => __( 'Use Gradient Background', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'essential-addons-elementor' ),
				'label_off' => __( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'zew_testimonial_background',
			[
				'label' => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .zew-testimonial-item' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'zew_testimonial_is_gradient_background' => ''
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'zew_testimonial_gradient_background',
				'label' => __( 'Gradient Background', 'essential-addons-elementor' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .zew-testimonial-item',
				'condition' => [
					'zew_testimonial_is_gradient_background' => 'yes'
				]
			]
		);

		$this->add_control(
			'zew_testimonial_alignment',
			[
				'label' => esc_html__( 'Layout Alignment', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options' => [
					'default' => [
						'title' => __( 'Default', 'wpzoom-elementor-addons' ),
						'icon' => 'fa fa-ban',
					],
					'left' => [
						'title' => esc_html__( 'Left', 'wpzoom-elementor-addons' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wpzoom-elementor-addons' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'wpzoom-elementor-addons' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'default',
				'selectors' => [
					'{{WRAPPER}} .zew-testimonial-content' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .zew-testimonial-image' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'zew_testimonial_user_display_block',
			[
				'label' => esc_html__( 'Display User & Company Block?', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'zew_section_testimonial_image_styles',
			[
				'label' => esc_html__( 'Testimonial Image Style', 'wpzoom-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'zew_testimonial_enable_avatar'	=> 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'zew_testimonial_image_width',
			[
				'label' => esc_html__( 'Image Width', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 150,
					'unit' => 'px',
				],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'size_units' => [ '%', 'px' ],
				'selectors' => [
					'{{WRAPPER}} .zew-testimonial-image figure > img' => 'width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'zew_testimonial_max_image_width',
			[
				'label' => esc_html__( 'Image Max Width', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ '%' ],
				'selectors' => [
					'{{WRAPPER}} .zew-testimonial-image' => 'max-width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'zew_testimonial_image_margin',
			[
				'label' => esc_html__( 'Margin', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .zew-testimonial-image img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'zew_testimonial_image_padding',
			[
				'label' => esc_html__( 'Padding', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .zew-testimonial-image img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'zew_testimonial_image_border',
				'label' => esc_html__( 'Border', 'wpzoom-elementor-addons' ),
				'selector' => '{{WRAPPER}} .zew-testimonial-image img',
			]
		);

		$this->add_control(
			'zew_testimonial_image_rounded',
			[
				'label' => esc_html__( 'Rounded Avatar?', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'testimonial-avatar-rounded',
				'default' => '',
			]
		);

		$this->add_control(
			'zew_testimonial_image_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .zew-testimonial-image img' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
				'condition' => [
					'zew_testimonial_image_rounded!' => 'testimonial-avatar-rounded',
				],
			]
		);

		$this->end_controls_section();

		// color, Typography & Spacing
		$this->start_controls_section(
			'zew_section_testimonial_typography',
			[
				'label' => esc_html__( 'Color, Typography &amp; Spacing', 'wpzoom-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'zew_testimonial_name_heading',
			[
				'label' => __( 'User Name', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'zew_testimonial_name_color',
			[
				'label' => esc_html__( 'User Name Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#272727',
				'selectors' => [
					'{{WRAPPER}} .zew-testimonial-content .zew-testimonial-user' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'zew_testimonial_name_typography',
				'selector' => '{{WRAPPER}} .zew-testimonial-content .zew-testimonial-user',
			]
		);

		$this->add_control(
			'zew_testimonial_name_margin',
			[
				'label' => esc_html__( 'Margin', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .zew-testimonial-content .zew-testimonial-user' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'zew_testimonial_company_heading',
			[
				'label' 	=> __( 'Company Name', 'wpzoom-elementor-addons' ),
				'type' 		=> Controls_Manager::HEADING,
				'separator'	=> 'before'
			]
		);

		$this->add_control(
			'zew_testimonial_company_color',
			[
				'label' => esc_html__( 'Company Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#272727',
				'selectors' => [
					'{{WRAPPER}} .zew-testimonial-content .zew-testimonial-user-company' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'zew_testimonial_position_typography',
				'selector' => '{{WRAPPER}} .zew-testimonial-content .zew-testimonial-user-company',
			]
		);

		$this->add_control(
			'zew_testimonial_company_margin',
			[
				'label' => esc_html__( 'Margin', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .zew-testimonial-content .zew-testimonial-user-company' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'zew_testimonial_description_heading',
			[
				'label' => __( 'Testimonial Text', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator'	=> 'before'
			]
		);

		$this->add_control(
			'zew_testimonial_description_color',
			[
				'label' => esc_html__( 'Testimonial Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#7a7a7a',
				'selectors' => [
					'{{WRAPPER}} .zew-testimonial-content .zew-testimonial-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			 	'name' => 'zew_testimonial_description_typography',
				'selector' => '{{WRAPPER}} .zew-testimonial-content .zew-testimonial-text',
			]
		);

		$this->add_control(
			'zew_testimonial_description_margin',
			[
				'label' => esc_html__( 'Margin', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .zew-testimonial-content .zew-testimonial-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'zew_testimonial_rating_heading',
			[
				'label' => __( 'Rating', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator'	=> 'before'
			]
		);

		$this->add_control(
			'zew_testimonial_rating_item_distance',
			[
				'label' => esc_html__( 'Distance Between Rating Item', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .zew-testimonial-content .testimonial-star-rating li' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'zew_testimonial_rating_margin',
			[
				'label' => esc_html__( 'Margin', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .zew-testimonial-content .testimonial-star-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'zew_section_testimonial_quotation_typography',
			[
				'label' => esc_html__( 'Quotation Style', 'wpzoom-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'zew_testimonial_quotation_color',
			[
				'label' => esc_html__( 'Quotation Mark Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(0,0,0,0.15)',
				'selectors' => [
					'{{WRAPPER}} .zew-testimonial-quote' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'zew_testimonial_quotation_typography',
				'selector' => '{{WRAPPER}} .zew-testimonial-quote',
			]
		);

		$this->add_responsive_control(
			'zew_testimonial_quotation_top',
			[
				'label' => esc_html__( 'Quotation Postion From Top', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 5,
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					]
				],
				'size_units' => [ '%' ],
				'selectors' => [
					'{{WRAPPER}} span.zew-testimonial-quote' => 'top:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'zew_testimonial_quotation_right',
			[
				'label' => esc_html__( 'Quotation Postion From Right', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 5,
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					]
				],
				'size_units' => [ '%' ],
				'selectors' => [
					'{{WRAPPER}} span.zew-testimonial-quote' => 'right:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render Testimonial Image.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	protected function render_testimonial_image() {
		$settings = $this->get_settings();
		$image = Group_Control_Image_Size::get_attachment_image_html( $settings );

		if( ! empty( $image ) && ! empty( $settings[ 'zew_testimonial_enable_avatar' ] ) ) {
			ob_start();

			?>
			<div class="zew-testimonial-image">
				<?php if ( 'yes' == $settings[ 'zew_testimonial_enable_avatar' ] ) : ?>
					<figure><?php echo Group_Control_Image_Size::get_attachment_image_html( $settings ); ?></figure>
				<?php endif; ?>
			</div>
			<?php

			echo ob_get_clean();
		}
	}

	/**
	 * Render Testimonial Rating.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	protected function render_testimonial_rating() {
		$settings = $this->get_settings_for_display( 'zew_testimonial_enable_rating' );

		if ( $settings == 'yes' ) :
			ob_start();

			?>
			<ul class="testimonial-star-rating">
				<li><i class="fas fa-star" aria-hidden="true"></i></li>
				<li><i class="fas fa-star" aria-hidden="true"></i></li>
				<li><i class="fas fa-star" aria-hidden="true"></i></li>
				<li><i class="fas fa-star" aria-hidden="true"></i></li>
				<li><i class="fas fa-star" aria-hidden="true"></i></li>
			</ul>
			<?php

			echo ob_get_clean();
		endif;
	}

	/**
	 * Render User Name and Company.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	protected function render_user_name_and_company() {
		$settings = $this->get_settings_for_display();

		if ( ! empty( $settings[ 'zew_testimonial_name' ] ) ) :
			?><p <?php echo $this->get_render_attribute_string( 'zew_testimonial_user' ); ?>><?php echo $settings[ 'zew_testimonial_name' ]; ?></p><?php
		endif;

		if ( ! empty( $settings[ 'zew_testimonial_company_title' ] ) ) :
			?><p class="zew-testimonial-user-company"><?php echo $settings[ 'zew_testimonial_company_title' ]; ?></p><?php
		endif;
	}

	/**
	 * Render Testimonial Quote.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	protected function testimonial_quote() {
		echo '<span class="zew-testimonial-quote"></span>';
	}

	/**
	 * Render Testimonial Description.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	protected function testimonial_desc() {
		$settings = $this->get_settings_for_display();

		echo '<div class="zew-testimonial-text">' . wpautop( $settings[ 'zew_testimonial_description' ] ) . '</div>';
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
		$rating = $this->get_settings_for_display( 'zew_testimonial_enable_rating' );

		$this->add_render_attribute(
			'zew_testimonial_wrap',
			[
				'id'	=> 'zew-testimonial-' . esc_attr( $this->get_id() ),
				'class'	=> [
					'zew-testimonial-item',
					'clearfix',
					$this->get_settings( 'zew_testimonial_image_rounded' ),
					esc_attr( $settings[ 'zew_testimonial_style' ] ),
				]
			]
		);

		if ( $rating == 'yes' ) {
			$this->add_render_attribute( 'zew_testimonial_wrap', 'class', $this->get_settings( 'zew_testimonial_rating_number' ) );
		}

		$this->add_render_attribute('zew_testimonial_user', 'class', 'zew-testimonial-user');

		if ( ! empty( $settings[ 'zew_testimonial_user_display_block' ] ) ) {
			$this->add_render_attribute( 'zew_testimonial_user', 'style', 'display: block; float: none;' );
		}

		?><div <?php echo $this->get_render_attribute_string( 'zew_testimonial_wrap' ); ?>>

			<?php if ( 'classic-style' == $settings[ 'zew_testimonial_style' ] ) { ?>
				<div class="zew-testimonial-content">
					<?php $this->testimonial_desc(); ?>

					<div class="clearfix">
						<?php $this->render_user_name_and_company(); ?>
					</div>

					<?php $this->render_testimonial_rating( $settings ); ?>
				</div>

				<?php $this->render_testimonial_image(); ?>
			<?php } ?>

			<?php if ( 'middle-style' == $settings[ 'zew_testimonial_style' ] ) { ?>
				<div class="zew-testimonial-content">
					<?php $this->testimonial_desc(); ?>

					<?php $this->render_testimonial_image(); ?>

					<div class="clearfix">
						<?php $this->render_user_name_and_company(); ?>
					</div>

					<?php $this->render_testimonial_rating( $settings ); ?>
				</div>
			<?php } ?>

			<?php if ( 'default-style' == $settings[ 'zew_testimonial_style' ] ) { ?>
				<?php $this->render_testimonial_image(); ?>
				<div class="zew-testimonial-content">
					<?php
						$this->testimonial_desc();
						$this->render_testimonial_rating( $settings );
						$this->render_user_name_and_company();
					?>
				</div>
			<?php } ?>

			<?php if ( 'icon-img-left-content' == $settings[ 'zew_testimonial_style' ] ) { ?>
				<?php $this->render_testimonial_image(); ?>

				<div class="zew-testimonial-content">
					<?php
						$this->testimonial_desc();
						$this->render_testimonial_rating( $settings );
					?>

					<div class="bio-text clearfix">
						<?php $this->render_user_name_and_company(); ?>
					</div>
				</div>
			<?php } ?>

			<?php if ( 'icon-img-right-content' == $settings[ 'zew_testimonial_style' ] ) { ?>
				<?php $this->render_testimonial_image(); ?>

				<div class="zew-testimonial-content">
					<?php
						$this->testimonial_desc();
						$this->render_testimonial_rating( $settings );
					?>

					<div class="bio-text-right"><?php $this->render_user_name_and_company(); ?></div>
				</div>
			<?php } ?>

			<?php if ( 'content-top-icon-title-inline' == $settings[ 'zew_testimonial_style' ] ) { ?>
				<div class="zew-testimonial-content zew-testimonial-inline-bio">
					<?php $this->render_testimonial_image(); ?>

					<div class="bio-text"><?php $this->render_user_name_and_company(); ?></div>

					<?php $this->render_testimonial_rating( $settings ); ?>
				</div>

				<div class="zew-testimonial-content">
					<?php $this->testimonial_desc(); ?>
				</div>
			<?php } ?>

			<?php if ( 'content-bottom-icon-title-inline' == $settings[ 'zew_testimonial_style' ] ) { ?>
				<div class="zew-testimonial-content">
					<?php $this->testimonial_desc(); ?>
				</div>

				<div class="zew-testimonial-content zew-testimonial-inline-bio">
					<?php $this->render_testimonial_image(); ?>

					<div class="bio-text"><?php $this->render_user_name_and_company(); ?></div>

					<?php $this->render_testimonial_rating( $settings ); ?>
				</div>
			<?php } ?>

			<?php $this->testimonial_quote(); ?>

		</div><?php
	}

	/**
	 * Content Template.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	protected function content_template() {}
}
