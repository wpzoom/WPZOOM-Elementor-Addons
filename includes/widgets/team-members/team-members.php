<?php
namespace WPZOOMElementorWidgets;

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Repeater;
use Elementor\Core\Schemes\Typography;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * ZOOM Elementor Widgets - Team Members Widget.
 *
 * Elementor widget that inserts a customizable list of team members.
 *
 * @since 1.0.0
 */
class Team_Members extends Widget_Base {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wpzoom-elementor-addons-css-frontend-team-members', plugins_url( 'frontend.css', __FILE__ ), [], WPZOOM_EL_ADDONS_VER );
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
		return 'wpzoom-elementor-addons-team-members';
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
		return __( 'Team Members', 'wpzoom-elementor-addons' );
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
		return 'eicon-person';
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
			'wpzoom-elementor-addons-css-frontend-team-members',
			'font-awesome-5-all',
			'font-awesome-4-shim',
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
			'font-awesome-4-shim',
		];
	}

	/**
	 * Get profile names.
	 *
	 * Retrieve the list of profile names.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Profile names.
	 */
	protected static function get_profile_names() {
		return [
			'500px'          => __( '500px', 'wpzoom-elementor-addons' ),
			'apple'          => __( 'Apple', 'wpzoom-elementor-addons' ),
			'behance'        => __( 'Behance', 'wpzoom-elementor-addons' ),
			'bitbucket'      => __( 'BitBucket', 'wpzoom-elementor-addons' ),
			'codepen'        => __( 'CodePen', 'wpzoom-elementor-addons' ),
			'delicious'      => __( 'Delicious', 'wpzoom-elementor-addons' ),
			'deviantart'     => __( 'DeviantArt', 'wpzoom-elementor-addons' ),
			'digg'           => __( 'Digg', 'wpzoom-elementor-addons' ),
			'dribbble'       => __( 'Dribbble', 'wpzoom-elementor-addons' ),
			'email'          => __( 'Email', 'wpzoom-elementor-addons' ),
			'facebook'       => __( 'Facebook', 'wpzoom-elementor-addons' ),
			'flickr'         => __( 'Flicker', 'wpzoom-elementor-addons' ),
			'foursquare'     => __( 'FourSquare', 'wpzoom-elementor-addons' ),
			'github'         => __( 'Github', 'wpzoom-elementor-addons' ),
			'houzz'          => __( 'Houzz', 'wpzoom-elementor-addons' ),
			'instagram'      => __( 'Instagram', 'wpzoom-elementor-addons' ),
			'jsfiddle'       => __( 'JS Fiddle', 'wpzoom-elementor-addons' ),
			'linkedin'       => __( 'LinkedIn', 'wpzoom-elementor-addons' ),
			'medium'         => __( 'Medium', 'wpzoom-elementor-addons' ),
			'pinterest'      => __( 'Pinterest', 'wpzoom-elementor-addons' ),
			'product-hunt'   => __( 'Product Hunt', 'wpzoom-elementor-addons' ),
			'reddit'         => __( 'Reddit', 'wpzoom-elementor-addons' ),
			'slideshare'     => __( 'Slide Share', 'wpzoom-elementor-addons' ),
			'snapchat'       => __( 'Snapchat', 'wpzoom-elementor-addons' ),
			'soundcloud'     => __( 'SoundCloud', 'wpzoom-elementor-addons' ),
			'spotify'        => __( 'Spotify', 'wpzoom-elementor-addons' ),
			'stack-overflow' => __( 'StackOverflow', 'wpzoom-elementor-addons' ),
			'tripadvisor'    => __( 'TripAdvisor', 'wpzoom-elementor-addons' ),
			'tumblr'         => __( 'Tumblr', 'wpzoom-elementor-addons' ),
			'twitch'         => __( 'Twitch', 'wpzoom-elementor-addons' ),
			'twitter'        => __( 'Twitter', 'wpzoom-elementor-addons' ),
			'vimeo'          => __( 'Vimeo', 'wpzoom-elementor-addons' ),
			'vk'             => __( 'VK', 'wpzoom-elementor-addons' ),
			'website'        => __( 'Website', 'wpzoom-elementor-addons' ),
			'whatsapp'       => __( 'WhatsApp', 'wpzoom-elementor-addons' ),
			'wordpress'      => __( 'WordPress', 'wpzoom-elementor-addons' ),
			'xing'           => __( 'Xing', 'wpzoom-elementor-addons' ),
			'yelp'           => __( 'Yelp', 'wpzoom-elementor-addons' ),
			'youtube'        => __( 'YouTube', 'wpzoom-elementor-addons' ),
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
	 * Register Controls.
	 *
	 * Registers all the controls for this widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	protected function register_content_controls() {
		$this->start_controls_section(
			'_section_info',
			[
				'label' => __( 'Information', 'wpzoom-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Photo', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'large',
				'separator' => 'none',
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Name', 'wpzoom-elementor-addons' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => 'Team Member Name',
				'placeholder' => __( 'Team Member Name', 'wpzoom-elementor-addons' ),
				'separator' => 'before',
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$this->add_control(
			'job_title',
			[
				'label' => __( 'Job Title', 'wpzoom-elementor-addons' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Officer', 'wpzoom-elementor-addons' ),
				'placeholder' => __( 'Team Member Job Title', 'wpzoom-elementor-addons' ),
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$this->add_control(
			'bio',
			[
				'label' => __( 'Short Bio', 'wpzoom-elementor-addons' ),
				'description' => sprintf( __( 'This input field has support for the following HTML tags: %1$s', 'wpzoom-elementor-addons' ), '<code>' . esc_html( '<' . implode( '>,<', array_keys( WPZOOM_Elementor_Widgets::get_allowed_html_tags() ) ) . '>' ) . '</code>' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Write something about the team member', 'wpzoom-elementor-addons' ),
				'rows' => 5,
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => __( 'Title HTML Tag', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'h1'  => [
						'title' => __( 'H1', 'wpzoom-elementor-addons' ),
						'icon' => 'far fa-h1'
					],
					'h2'  => [
						'title' => __( 'H2', 'wpzoom-elementor-addons' ),
						'icon' => 'far fa-h2'
					],
					'h3'  => [
						'title' => __( 'H3', 'wpzoom-elementor-addons' ),
						'icon' => 'far fa-h3'
					],
					'h4'  => [
						'title' => __( 'H4', 'wpzoom-elementor-addons' ),
						'icon' => 'far fa-h4'
					],
					'h5'  => [
						'title' => __( 'H5', 'wpzoom-elementor-addons' ),
						'icon' => 'far fa-h5'
					],
					'h6'  => [
						'title' => __( 'H6', 'wpzoom-elementor-addons' ),
						'icon' => 'far fa-h6'
					]
				],
				'default' => 'h2',
				'toggle' => false,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'wpzoom-elementor-addons' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'wpzoom-elementor-addons' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'wpzoom-elementor-addons' ),
						'icon' => 'fa fa-align-right',
					],
					'justify' => [
						'title' => __( 'Justify', 'wpzoom-elementor-addons' ),
						'icon' => 'fa fa-align-justify',
					],
				],
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_social',
			[
				'label' => __( 'Social Profiles', 'wpzoom-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'name',
			[
				'label' => __( 'Profile Name', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'select2options' => [
					'allowClear' => false,
				],
				'options' => self::get_profile_names()
			]
		);

		$repeater->add_control(
			'link', [
				'label' => __( 'Profile Link', 'wpzoom-elementor-addons' ),
				'placeholder' => __( 'Add your profile link', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::URL,
				'label_block' => true,
				'autocomplete' => false,
				'show_external' => false,
				'condition' => [
					'name!' => 'email'
				],
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'email', [
				'label' => __( 'Email Address', 'wpzoom-elementor-addons' ),
				'placeholder' => __( 'Add your email address', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'input_type' => 'email',
				'condition' => [
					'name' => 'email'
				],
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'customize',
			[
				'label' => __( 'Want To Customize?', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off' => __( 'No', 'wpzoom-elementor-addons' ),
				'return_value' => 'yes',
				'style_transfer' => true,
			]
		);

		$repeater->start_controls_tabs(
			'_tab_icon_colors',
			[
				'condition' => ['customize' => 'yes']
			]
		);
		$repeater->start_controls_tab(
			'_tab_icon_normal',
			[
				'label' => __( 'Normal', 'wpzoom-elementor-addons' ),
			]
		);

		$repeater->add_control(
			'color',
			[
				'label' => __( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-member-links > {{CURRENT_ITEM}}' => 'color: {{VALUE}}',
				],
				'condition' => ['customize' => 'yes'],
				'style_transfer' => true,
			]
		);

		$repeater->add_control(
			'bg_color',
			[
				'label' => __( 'Background Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-member-links > {{CURRENT_ITEM}}' => 'background-color: {{VALUE}}',
				],
				'condition' => ['customize' => 'yes'],
				'style_transfer' => true,
			]
		);

		$repeater->end_controls_tab();
		$repeater->start_controls_tab(
			'_tab_icon_hover',
			[
				'label' => __( 'Hover', 'wpzoom-elementor-addons' ),
			]
		);

		$repeater->add_control(
			'hover_color',
			[
				'label' => __( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-member-links > {{CURRENT_ITEM}}:hover, {{WRAPPER}} .zew-member-links > {{CURRENT_ITEM}}:focus' => 'color: {{VALUE}}',
				],
				'condition' => ['customize' => 'yes'],
				'style_transfer' => true,
			]
		);

		$repeater->add_control(
			'hover_bg_color',
			[
				'label' => __( 'Background Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-member-links > {{CURRENT_ITEM}}:hover, {{WRAPPER}} .zew-member-links > {{CURRENT_ITEM}}:focus' => 'background-color: {{VALUE}}',
				],
				'condition' => ['customize' => 'yes'],
				'style_transfer' => true,
			]
		);

		$repeater->add_control(
			'hover_border_color',
			[
				'label' => __( 'Border Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-member-links > {{CURRENT_ITEM}}:hover, {{WRAPPER}} .zew-member-links > {{CURRENT_ITEM}}:focus' => 'border-color: {{VALUE}}',
				],
				'condition' => ['customize' => 'yes'],
				'style_transfer' => true,
			]
		);

		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();

		$this->add_control(
			'profiles',
			[
				'show_label' => false,
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '<# print(name.slice(0,1).toUpperCase() + name.slice(1)) #>',
				'default' => [
					[
						'link' => ['url' => 'https://facebook.com/'],
						'name' => 'facebook'
					],
					[
						'link' => ['url' => 'https://twitter.com/'],
						'name' => 'twitter'
					],
					[
						'link' => ['url' => 'https://linkedin.com/'],
						'name' => 'linkedin'
					]
				],
			]
		);

		$this->add_control(
			'show_profiles',
			[
				'label' => __( 'Show Profiles', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'wpzoom-elementor-addons' ),
				'label_off' => __( 'Hide', 'wpzoom-elementor-addons' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'separator' => 'before',
				'style_transfer' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_button',
			[
				'label' => __( 'Details Button', 'wpzoom-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_details_button',
			[
				'label' => __( 'Show Button', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'wpzoom-elementor-addons' ),
				'label_off' => __( 'Hide', 'wpzoom-elementor-addons' ),
				'return_value' => 'yes',
				'default' => '',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'button_position',
			[
				'label' => __( 'Position', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'after',
				'style_transfer' => true,
				'options' => [
					'before' => __( 'Before Social Icons', 'wpzoom-elementor-addons' ),
					'after' => __( 'After Social Icons', 'wpzoom-elementor-addons' ),
				],
				'condition' => [
					'show_details_button' => 'yes',
				]
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => __( 'Text', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Show Details', 'wpzoom-elementor-addons' ),
				'placeholder' => __( 'Type button text here', 'wpzoom-elementor-addons' ),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'show_details_button' => 'yes',
				]
			]
		);

		$this->add_control(
			'button_link',
			[
				'label' => __( 'Link', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::URL,
				'placeholder' => 'https://example.com',
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'show_details_button' => 'yes',
				],
				'default' => [
					'url' => '#',
				]
			]
		);

		$this->add_control(
			'button_icon',
			[
				'type' => Controls_Manager::ICONS,
				'label_block' => true,
				'show_label' => false,
				'condition' => [
					'show_details_button' => 'yes',
				]
			]
		);

		$this->add_control(
			'button_icon_position',
			[
				'label' => __( 'Icon Position', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'before' => [
						'title' => __( 'Before', 'wpzoom-elementor-addons' ),
						'icon' => 'fa-align-left',
					],
					'after' => [
						'title' => __( 'After', 'wpzoom-elementor-addons' ),
						'icon' => 'fa-align-right',
					],
				],
				'default' => 'after',
				'toggle' => false,
				'style_transfer' => true,
				'condition' => [
					'show_details_button' => 'yes',
					'button_icon[value]!' => ''
				]
			]
		);

		$this->add_control(
			'button_icon_spacing',
			[
				'label' => __( 'Icon Spacing', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10
				],
				'condition' => [
					'show_details_button' => 'yes',
					'button_icon[value]!' => ''
				],
				'selectors' => [
					'{{WRAPPER}} .zew-btn--icon-before .zew-btn-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .zew-btn--icon-after .zew-btn-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register Style Controls.
	 *
	 * Registers the style controls.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	protected function register_style_controls() {
		$this->start_controls_section(
			'_section_style_image',
			[
				'label' => __( 'Photo', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label' => __( 'Width', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%'],
				'range' => [
					'%' => [
						'min' => 20,
						'max' => 100,
					],
					'px' => [
						'min' => 100,
						'max' => 700,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .zew-member-figure' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_height',
			[
				'label' => __( 'Height', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 700,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .zew-member-figure' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing',
			[
				'label' => __( 'Bottom Spacing', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .zew-member-figure' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'image_padding',
			[
				'label' => __( 'Padding', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .zew-member-figure img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'image_border',
				'selector' => '{{WRAPPER}} .zew-member-figure img'
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label' => __( 'Border Radius', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .zew-member-figure img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .zew-member-figure img'
			]
		);

		$this->add_control(
			'image_bg_color',
			[
				'label' => __( 'Background Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-member-figure img' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_style_content',
			[
				'label' => __( 'Name, Job Title & Bio', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => __( 'Content Padding', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .zew-member-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'_heading_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Name', 'wpzoom-elementor-addons' ),
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => __( 'Bottom Spacing', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .zew-member-name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-member-name' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .zew-member-name',
				'scheme' => Typography::TYPOGRAPHY_2,
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'selector' => '{{WRAPPER}} .zew-member-name',
			]
		);

		$this->add_control(
			'_heading_job_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Job Title', 'wpzoom-elementor-addons' ),
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'job_title_spacing',
			[
				'label' => __( 'Bottom Spacing', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .zew-member-position' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'job_title_color',
			[
				'label' => __( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-member-position' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'job_title_typography',
				'selector' => '{{WRAPPER}} .zew-member-position',
				'scheme' => Typography::TYPOGRAPHY_3,
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'job_title_text_shadow',
				'selector' => '{{WRAPPER}} .zew-member-position',
			]
		);

		$this->add_control(
			'_heading_bio',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Short Bio', 'wpzoom-elementor-addons' ),
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'bio_spacing',
			[
				'label' => __( 'Bottom Spacing', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .zew-member-bio' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'bio_color',
			[
				'label' => __( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-member-bio' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'bio_typography',
				'selector' => '{{WRAPPER}} .zew-member-bio',
				'scheme' => Typography::TYPOGRAPHY_3,
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'bio_text_shadow',
				'selector' => '{{WRAPPER}} .zew-member-bio',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_style_social',
			[
				'label' => __( 'Social Icons', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'links_spacing',
			[
				'label' => __( 'Right Spacing', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .zew-member-links > a:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'links_padding',
			[
				'label' => __( 'Padding', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .zew-member-links > a' => 'padding: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'links_icon_size',
			[
				'label' => __( 'Icon Size', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .zew-member-links > a' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'links_border',
				'selector' => '{{WRAPPER}} .zew-member-links > a'
			]
		);

		$this->add_responsive_control(
			'links_border_radius',
			[
				'label' => __( 'Border Radius', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .zew-member-links > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( '_tab_links_colors' );
		$this->start_controls_tab(
			'_tab_links_normal',
			[
				'label' => __( 'Normal', 'wpzoom-elementor-addons' ),
			]
		);

		$this->add_control(
			'links_color',
			[
				'label' => __( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-member-links > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'links_bg_color',
			[
				'label' => __( 'Background Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-member-links > a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			'_tab_links_hover',
			[
				'label' => __( 'Hover', 'wpzoom-elementor-addons' ),
			]
		);

		$this->add_control(
			'links_hover_color',
			[
				'label' => __( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-member-links > a:hover, {{WRAPPER}} .zew-member-links > a:focus' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'links_hover_bg_color',
			[
				'label' => __( 'Background Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-member-links > a:hover, {{WRAPPER}} .zew-member-links > a:focus' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'links_hover_border_color',
			[
				'label' => __( 'Border Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-member-links > a:hover, {{WRAPPER}} .zew-member-links > a:focus' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'links_border_border!' => '',
				]
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_style_button',
			[
				'label' => __( 'Details Button', 'wpzoom-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'button_margin',
			[
				'label' => __( 'Margin', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .zew-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => __( 'Padding', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .zew-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .zew-btn',
				'scheme' => Typography::TYPOGRAPHY_4,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border',
				'selector' => '{{WRAPPER}} .zew-btn',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label' => __( 'Border Radius', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .zew-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .zew-btn',
			]
		);

		$this->add_control(
			'hr',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->start_controls_tabs( '_tabs_button' );

		$this->start_controls_tab(
			'_tab_button_normal',
			[
				'label' => __( 'Normal', 'wpzoom-elementor-addons' ),
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => __( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .zew-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => __( 'Background Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_button_hover',
			[
				'label' => __( 'Hover', 'wpzoom-elementor-addons' ),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label' => __( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zew-btn:hover, {{WRAPPER}} .zew-btn:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_bg_color',
			[
				'label' => __( 'Background Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zeww-btn:hover, {{WRAPPER}} .zew-btn:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label' => __( 'Border Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .zew-btn:hover, {{WRAPPER}} .zew-btn:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Overriding the parent method
	 *
	 * Add inline editing attributes.
	 *
	 * Define specific area in the element to be editable inline. The element can have several areas, with this method
	 * you can set the area inside the element that can be edited inline. You can also define the type of toolbar the
	 * user will see, whether it will be a basic toolbar or an advanced one.
	 *
	 * Note: When you use wysiwyg control use the advanced toolbar, with textarea control use the basic toolbar. Text
	 * control should not have toolbar.
	 *
	 * PHP usage (inside `Widget_Base::render()` method):
	 *
	 *    $this->add_inline_editing_attributes( 'text', 'advanced' );
	 *    echo '<div ' . $this->get_render_attribute_string( 'text' ) . '>' . $this->get_settings( 'text' ) . '</div>';
	 *
	 * @since 1.8.0
	 * @access protected
	 *
	 * @param string $key     Element key.
	 * @param string $toolbar Optional. Toolbar type. Accepted values are `advanced`, `basic` or `none`. Default is
	 *                        `basic`.
	 * @param string $setting_key Additional settings key in case $key != $setting_key
	 */
	protected function add_inline_editing_attributes( $key, $toolbar = 'basic', $setting_key = '' ) {
		if ( ! Plugin::instance()->editor->is_edit_mode() ) {
			return;
		}

		if ( empty( $setting_key ) ) {
			$setting_key = $key;
		}

		$this->add_render_attribute( $key, [
			'class' => 'elementor-inline-editing',
			'data-elementor-setting-key' => $setting_key,
		] );

		if ( 'basic' !== $toolbar ) {
			$this->add_render_attribute( $key, [
				'data-elementor-inline-editing-toolbar' => $toolbar,
			] );
		}
	}

	/**
	 * Render button with icon
	 *
	 * @since 1.0.0
	 * @access public
	 * @param array $args { old_icon, icon_pos, new_icon, text, link, class, text_class }
	 * @return void
	 */
	public function render_icon_button( $args = [] ) {
		$args = wp_parse_args( $args, [
			'old_icon'   => 'button_icon',
			'icon_pos'   => 'button_icon_position',
			'new_icon'   => 'button_selected_icon',
			'text'       => 'button_text',
			'link'       => 'button_link',
			'class'      => 'ha-btn ha-btn--link',
			'text_class' => 'ha-btn-text',
		] );

		$settings = $this->get_settings_for_display();
		$button_text = isset( $settings[ $args['text'] ] ) ? $settings[ $args['text'] ] : '';
		$has_new_icon = ( ! empty( $settings[ $args['new_icon'] ] ) && ! empty( $settings[ $args['new_icon'] ]['value'] ) ) ? true : false;
		$has_old_icon = ! empty( $settings[ $args['old_icon'] ] ) ? true : false;

		// Return as early as possible
		// Do not process anything if there is no icon and text
		if ( empty( $button_text ) && ! $has_new_icon && ! $has_old_icon ) {
			return;
		}

		$this->add_inline_editing_attributes( $args['text'], 'none' );
		$this->add_render_attribute( $args['text'], 'class', $args['text_class'] );

		$this->add_render_attribute( 'button', 'class', $args['class'] );
		$this->add_link_attributes( 'button', $settings[ $args['link'] ] );

		if ( $button_text && ( empty( $has_new_icon ) && empty( $has_old_icon ) ) ) :
			printf( '<a %1$s>%2$s</a>',
				$this->get_render_attribute_string( 'button' ),
				sprintf( '<span %1$s>%2$s</span>', $this->get_render_attribute_string( $args['text'] ), esc_html( $button_text ) )
			);
		elseif ( empty( $button_text ) && ( ! empty( $has_old_icon ) || ! empty( $has_new_icon ) ) ) : ?>
			<a <?php $this->print_render_attribute_string( 'button' ); ?>><?php Icons_Manager::render_icon( $settings[ $args['new_icon'] ] ); ?></a>
		<?php elseif ( $button_text && ( ! empty( $has_old_icon ) || ! empty( $has_new_icon ) ) ) :
			if ( $settings[ $args['icon_pos'] ] === 'before' ) :
				$this->add_render_attribute( 'button', 'class', 'ha-btn--icon-before' );
				$button_text = sprintf( '<span %1$s>%2$s</span>', $this->get_render_attribute_string( $args['text'] ), esc_html( $button_text ) );
				?>
				<a <?php $this->print_render_attribute_string( 'button' ); ?>><?php Icons_Manager::render_icon( $settings[ $args['new_icon'] ], ['class' => 'zew-btn-icon'] ); ?> <?php echo $button_text; ?></a>
				<?php
			else :
				$this->add_render_attribute( 'button', 'class', 'ha-btn--icon-after' );
				$button_text = sprintf( '<span %1$s>%2$s</span>', $this->get_render_attribute_string( $args['text'] ), esc_html( $button_text ) );
				?>
				<a <?php $this->print_render_attribute_string( 'button' ); ?>><?php echo $button_text; ?> <?php Icons_Manager::render_icon( $settings[ $args['new_icon'] ], ['class' => 'zew-btn-icon'] ); ?></a>
				<?php
			endif;
		endif;
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

		$button_position = ! empty( $settings['button_position'] ) ? $settings['button_position'] : 'after';

		$show_button = false;
		if ( ! empty( $settings['show_details_button'] ) && $settings['show_details_button'] === 'yes'  ) {
			$show_button = true;
		}

		$this->add_inline_editing_attributes( 'title', 'basic' );
		$this->add_render_attribute( 'title', 'class', 'zew-member-name' );

		$this->add_inline_editing_attributes( 'job_title', 'basic' );
		$this->add_render_attribute( 'job_title', 'class', 'zew-member-position' );

		$this->add_inline_editing_attributes( 'bio', 'intermediate' );
		$this->add_render_attribute( 'bio', 'class', 'zew-member-bio' );
		?>

		<?php if ( $settings['image']['url'] || $settings['image']['id'] ) :
			$settings['hover_animation'] = 'disable-animation'; // hack to prevent image hover animation
			?>
			<figure class="zew-member-figure">
				<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'image' ); ?>
			</figure>
		<?php endif; ?>

		<div class="zew-member-body">
			<?php if ( $settings['title'] ) :
				printf( '<%1$s %2$s>%3$s</%1$s>',
					tag_escape( $settings['title_tag'] ),
					$this->get_render_attribute_string( 'title' ),
					WPZOOM_Elementor_Widgets::custom_kses( $settings[ 'title' ] )
				);
			endif; ?>

			<?php if ( $settings['job_title' ] ) : ?>
				<div <?php $this->print_render_attribute_string( 'job_title' ); ?>><?php echo WPZOOM_Elementor_Widgets::custom_kses( $settings[ 'job_title' ] ); ?></div>
			<?php endif; ?>

			<?php if ( $settings['bio'] ) : ?>
				<div <?php $this->print_render_attribute_string( 'bio' ); ?>>
					<p><?php echo WPZOOM_Elementor_Widgets::custom_kses( $settings[ 'bio' ] ); ?></p>
				</div>
			<?php endif; ?>

			<?php
			if ( $show_button && $button_position === 'before' ) {
				$this->render_icon_button( [ 'new_icon' => 'button_icon', 'old_icon' => '' ] );
			}
			?>

			<?php if ( $settings[ 'show_profiles' ] && is_array( $settings[ 'profiles' ] ) ) : ?>
				<div class="zew-member-links">
					<?php
					foreach ( $settings['profiles'] as $profile ) :
						$icon = $profile['name'];
						$url = $profile['link']['url'];

						if ( $profile['name'] === 'website' ) {
							$icon = 'globe far';
						} elseif ( $profile['name'] === 'email' ) {
							$icon = 'envelope far';
							$url = 'mailto:' . antispambot( $profile['email'] );
						} else {
							$icon .= ' fab';
						}

						printf( '<a target="_blank" rel="noopener" href="%s" class="elementor-repeater-item-%s"><i class="fa fa-%s" aria-hidden="true"></i></a>',
							$url,
							esc_attr( $profile['_id'] ),
							esc_attr( $icon )
						);
					endforeach; ?>
				</div>
			<?php endif; ?>

			<?php
			if ( $show_button && $button_position === 'after' ) {
				$this->render_icon_button( [ 'new_icon' => 'button_icon', 'old_icon' => '' ] );
			}
			?>
		</div>
		<?php
	}

	/**
	 * JS Content Template.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	/*public function _content_template() {
		return '';
		?>
		<#
		view.addInlineEditingAttributes( 'title', 'basic' );
		view.addRenderAttribute( 'title', 'class', 'zew-member-name' );

		view.addInlineEditingAttributes( 'job_title', 'basic' );
		view.addRenderAttribute( 'job_title', 'class', 'zew-member-position' );

		view.addInlineEditingAttributes( 'bio', 'intermediate' );
		view.addRenderAttribute( 'bio', 'class', 'zew-member-bio' );

		if ( settings.image.url || settings.image.id ) {
			var image = {
				id: settings.image.id,
				url: settings.image.url,
				size: settings.thumbnail_size,
				dimension: settings.thumbnail_custom_dimension,
				model: view.getEditModel()
			};

			var image_url = elementor.imagesManager.getImageUrl( image );
			#>
			<figure class="zew-member-figure">
				<img src="{{ image_url }}">
			</figure>
		<# } #>
		<div class="zew-member-body">
			<# if (settings.title) { #>
				<{{ settings.title_tag }} {{{ view.getRenderAttributeString( 'title' ) }}}>{{ settings.title }}</{{ settings.title_tag }}>
			<# } #>
			<# if (settings.job_title) { #>
				<div {{{ view.getRenderAttributeString( 'job_title' ) }}}>{{ settings.job_title }}</div>
			<# } #>
			<# if (settings.bio) { #>
				<div {{{ view.getRenderAttributeString( 'bio' ) }}}>
					<p>{{{ settings.bio }}}</p>
				</div>
			<# } #>

			<# if ( !_.isUndefined( settings['button_position'] ) && settings['button_position'] === 'before' ) {
				print( haGetButtonWithIcon( view, {newIcon: 'button_icon', oldIcon: ''} ) );
			} #>

			<# if (settings.show_profiles && _.isArray(settings.profiles)) { #>
				<div class="zew-member-links">
					<# _.each(settings.profiles, function(profile, index) {
						var icon = profile.name,
							url = profile.link.url,
							linkKey = view.getRepeaterSettingKey( 'profile', 'profiles', index);

						if (profile.name === 'website') {
							icon = 'globe';
						} else if (profile.name === 'email') {
							icon = 'envelope'
							url = 'mailto:' + profile.email;
						}

						view.addRenderAttribute( linkKey, 'class', 'elementor-repeater-item-' + profile._id );
						view.addRenderAttribute( linkKey, 'href', url ); #>
						<a {{{view.getRenderAttributeString( linkKey )}}}><i class="fa fab fa-{{{icon}}}"></i></a>
					<# }); #>
				</div>
			<# } #>

			<# if ( !_.isUndefined( settings['button_position'] ) && settings['button_position'] === 'after' ) {
				print( haGetButtonWithIcon( view, {newIcon: 'button_icon', oldIcon: ''} ) );
			} #>
		</div>
		<?php
	}*/
}
