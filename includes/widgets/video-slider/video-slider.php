<?php
namespace WPZOOMElementorWidgets;

use Elementor\Widget_Base;
use Elementor\Group_Control_Background;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Embed;
use Elementor\Icons_Manager;

use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use Elementor\Modules\DynamicTags\Module as TagsModule;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * ZOOM Elementor Widgets - Slider Widget.
 *
 * Elementor widget that inserts a customizable slider.
 *
 * @since 1.0.0
 */
class Video_Slider extends Widget_Base {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		if ( ! wp_style_is( 'slick-slider', 'registered' ) ) {
			wp_register_style( 'slick-slider', WPZOOM_EL_ADDONS_URL . 'assets/vendors/slick/slick.css', null, WPZOOM_EL_ADDONS_VER );
		}

		if ( ! wp_style_is( 'slick-slider-theme', 'registered' ) ) {
			wp_register_style( 'slick-slider-theme', WPZOOM_EL_ADDONS_URL . 'assets/vendors/slick/slick-theme.css', null, WPZOOM_EL_ADDONS_VER );
		}

		wp_register_style( 'wpzoom-elementor-addons-css-frontend-video-slider', plugins_url( 'frontend.css', __FILE__ ), [ 'slick-slider', 'slick-slider-theme' ], WPZOOM_EL_ADDONS_VER );

		if ( ! wp_script_is( 'jquery-slick-slider', 'registered' ) ) {
			wp_register_script( 'jquery-slick-slider', WPZOOM_EL_ADDONS_URL . 'assets/vendors/slick/slick.min.js', [ 'jquery' ], WPZOOM_EL_ADDONS_VER, true );
		}

		wp_register_script( 'wpzoom-elementor-addons-js-frontend-video-slider', plugins_url( 'frontend.js', __FILE__ ), [ 'jquery', 'jquery-slick-slider' ], WPZOOM_EL_ADDONS_VER, true );
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
		return 'wpzoom-elementor-addons-video-slider';
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
		return esc_html__( 'Video Slider', 'wpzoom-elementor-addons' );
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
		return 'eicon-slides';
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
			'slick-slider',
			'slick-slider-theme',
			'font-awesome-5-all',
			'font-awesome-4-shim',
			'wpzoom-elementor-addons-css-frontend-video-slider'
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
			'wpzoom-elementor-addons-js-frontend-video-slider'
		];
	}

	/**
	 * Get All Post Types.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array An array of all currently-registered post types.
	 */
	public function get_post_types() {
		$post_types = get_post_types( [ 'public' => true, 'show_in_nav_menus' => true ], 'objects' );
		$post_types = wp_list_pluck( $post_types, 'label', 'name' );

		return array_diff_key( $post_types, [ 'elementor_library', 'attachment' ] );
	}

	/**
	 * Get All Posts of Type.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param  string $post_type The post type to get all posts for.
	 * @param  int    $limit     Limit to the number of posts returned.
	 * @return array             An array of all posts in the given post type.
	 */
	public function get_post_list( $post_type = 'any', $limit = -1 ) {
		global $wpdb;

		$where = '';
		$data = [];

		if ( -1 == $limit ) {
			$limit = '';
		} elseif ( 0 == $limit ) {
			$limit = "limit 0,1";
		} else {
			$limit = $wpdb->prepare( " limit 0,%d", esc_sql( $limit ) );
		}

		if ( 'any' === $post_type ) {
			$in_search_post_types = get_post_types( [ 'exclude_from_search' => false ] );

			if ( empty( $in_search_post_types ) ) {
				$where .= ' AND 1=0 ';
			} else {
				$where .= " AND {$wpdb->posts}.post_type IN ('" . join( "', '", array_map( 'esc_sql', $in_search_post_types ) ) . "')";
			}
		} elseif ( ! empty( $post_type ) ) {
			$where .= $wpdb->prepare( " AND {$wpdb->posts}.post_type = %s", esc_sql( $post_type ) );
		}

		if ( ! empty( $search ) ) {
			$where .= $wpdb->prepare( " AND {$wpdb->posts}.post_title LIKE %s", '%' . esc_sql( $search ) . '%' );
		}

		$query = "select post_title,ID  from $wpdb->posts where post_status = 'publish' $where $limit";
		$results = $wpdb->get_results( $query );

		if ( ! empty( $results ) ) {
			foreach ( $results as $row ) {
				$data[ $row->ID ] = $row->post_title;
			}
		}

		return $data;
	}

	/**
	 * Get All Authors.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array An array of all registered authors.
	 */
	public function get_authors_list() {
		$users = get_users( [
			'capability'          => 'edit_posts',
			'has_published_posts' => true,
			'fields' => [
				'ID',
				'display_name'
			]
		] );

		if ( ! empty( $users ) ) {
			return wp_list_pluck( $users, 'display_name', 'ID' );
		}

		return [];
	}

	/**
	 * Post Order-By Options.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array An array of all Order-By options for querying posts.
	 */
	public function get_post_orderby_options() {
		return [
			'ID'            => esc_html__( 'Post ID', 'wpzoom-elementor-addons' ),
			'author'        => esc_html__( 'Post Author', 'wpzoom-elementor-addons' ),
			'title'         => esc_html__( 'Title', 'wpzoom-elementor-addons' ),
			'date'          => esc_html__( 'Date', 'wpzoom-elementor-addons' ),
			'modified'      => esc_html__( 'Last Modified Date', 'wpzoom-elementor-addons' ),
			'parent'        => esc_html__( 'Parent ID', 'wpzoom-elementor-addons' ),
			'rand'          => esc_html__( 'Random', 'wpzoom-elementor-addons' ),
			'comment_count' => esc_html__( 'Comment Count', 'wpzoom-elementor-addons' ),
			'menu_order'    => esc_html__( 'Menu Order', 'wpzoom-elementor-addons' )
		];
	}

	/**
	 * Get Post Terms.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array An array of all terms in the given taxonomy.
	 */
	public function get_terms_list( $taxonomy = 'category', $key = 'term_id' ) {
		$options = [];
		$terms = get_terms( [
			'taxonomy' => $taxonomy,
			'hide_empty' => true
		] );

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->{$key} ] = $term->name;
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
	protected function register_controls() {
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
				'label' => esc_html__( 'Slides', 'wpzoom-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'slides_source',
			[
				'label' => esc_html__( 'Source', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'custom',
				'options' => [
					'custom' => esc_html__( 'Custom', 'wpzoom-elementor-addons' ),
					'posts' => esc_html__( 'WordPress Posts', 'wpzoom-elementor-addons' )
				],
				'separator' => 'after'
			]
		);

		$repeater = new Repeater();

		// Background Type Control
		$repeater->add_control(
			'background_type',
			[
				'label' => esc_html__( 'Background Type', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'image' => [
						'title' => esc_html__( 'Image', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-image',
					],
					'video' => [
						'title' => esc_html__( 'Video', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-video-camera',
					],
				],
				'default' => 'image',
				'toggle' => false,
				'frontend_available' => true,
			]
		);

		// Image Background Controls
		$repeater->add_control(
			'image',
			[
				'type' => Controls_Manager::MEDIA,
				'label' => esc_html__( 'Image', 'wpzoom-elementor-addons' ),
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'background_type' => 'image',
				],
			]
		);

		// Video Background Controls
		$repeater->add_control(
			'video_link',
			[
				'label' => esc_html__( 'Video Link', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY
					]
				],
				'placeholder' => esc_html__( 'YouTube/Vimeo link, or link to video file (mp4 is recommended).', 'wpzoom-elementor-addons' ),
				'label_block' => true,
				'condition' => [
					'background_type' => 'video',
				],
				'frontend_available' => true,
			]
		);

		$repeater->add_control(
			'video_start_time',
			[
				'label' => esc_html__( 'Start Time', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::NUMBER,
				'description' => esc_html__( 'Specify a start time (in seconds)', 'wpzoom-elementor-addons' ),
				'condition' => [
					'background_type' => 'video',
					'video_link!' => '',
				],
				'frontend_available' => true,
			]
		);

		$repeater->add_control(
			'video_end_time',
			[
				'label' => esc_html__( 'End Time', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::NUMBER,
				'description' => esc_html__( 'Specify an end time (in seconds)', 'wpzoom-elementor-addons' ),
				'condition' => [
					'background_type' => 'video',
					'video_link!' => '',
				],
				'frontend_available' => true,
			]
		);

		$repeater->add_control(
			'video_play_once',
			[
				'label' => esc_html__( 'Play Once', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'background_type' => 'video',
					'video_link!' => '',
				],
				'frontend_available' => true,
			]
		);

		$repeater->add_control(
			'video_play_on_mobile',
			[
				'label' => esc_html__( 'Play On Mobile', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'background_type' => 'video',
					'video_link!' => '',
				],
				'frontend_available' => true,
			]
		);

		$repeater->add_control(
			'video_privacy_mode',
			[
				'label' => esc_html__( 'Privacy Mode', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'Enable privacy mode for YouTube videos. YouTube won\'t store information about visitors unless they play the video.', 'wpzoom-elementor-addons' ),
				'condition' => [
					'background_type' => 'video',
					'video_link!' => '',
				],
				'frontend_available' => true,
			]
		);

		$repeater->add_control(
			'background_fallback',
			[
				'label' => esc_html__( 'Background Fallback', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::MEDIA,
				'description' => esc_html__( 'This cover image will replace the background video in case that the video could not be loaded.', 'wpzoom-elementor-addons' ),
				'condition' => [
					'background_type' => 'video',
				],
				'dynamic' => [
					'active' => true,
				],
				'frontend_available' => true,
			]
		);

		$repeater->add_control(
			'title',
			[
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'label' => esc_html__( 'Title', 'wpzoom-elementor-addons' ),
				'placeholder' => esc_html__( 'Type title here', 'wpzoom-elementor-addons' ),
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'subtitle',
			[
				'label' => esc_html__( 'Subtitle', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'placeholder' => esc_html__( 'Type subtitle here', 'wpzoom-elementor-addons' ),
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => 'https://example.com',
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'show_button',
			[
				'label' => esc_html__( 'Show Button', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off' => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$repeater->add_control(
			'button_text',
			[
				'label' => esc_html__( 'Button Text', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Learn More', 'wpzoom-elementor-addons' ),
				'condition' => [
					'show_button' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'button_link',
			[
				'label' => esc_html__( 'Button Link', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::URL,
				'placeholder' => 'https://example.com',
				'condition' => [
					'show_button' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'show_video_lightbox',
			[
				'label' => esc_html__( 'Show Video Lightbox', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off' => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$repeater->add_control(
			'lightbox_video_url',
			[
				'label' => esc_html__( 'Lightbox Video URL', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'YouTube or Vimeo URL', 'wpzoom-elementor-addons' ),
				'condition' => [
					'show_video_lightbox' => 'yes',
				],
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

		// Sample slides with different configurations
		$sample_slides = [
			// Slide 1: Self-hosted MP4 video background with button
			[
				'_id' => 'slide_1',
				'background_type' => 'video',
				'video_link' => 'https://wpzoom.s3.amazonaws.com/inspiro-blocks-pro/video/video.mp4',
				'video_play_on_mobile' => 'yes',
				'background_fallback' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'title' => 'Self-Hosted Video Background',
				'subtitle' => 'Experience smooth video playback with our self-hosted MP4 video support. Perfect for showcasing your content with reliable performance and fast loading times.',
				'show_button' => 'yes',
				'button_text' => 'Learn More',
				'button_link' => [
					'url' => '#',
					'is_external' => false,
					'nofollow' => false,
				],
			],
			// Slide 2: Vimeo video background with video lightbox
			[
				'_id' => 'slide_2',
				'background_type' => 'video',
				'video_link' => 'https://vimeo.com/729485552',
				'video_play_on_mobile' => '',
				'background_fallback' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'title' => 'Vimeo Video Integration',
				'subtitle' => 'Seamlessly integrate Vimeo videos as background elements. Enjoy professional video hosting with advanced customization options.',
				'show_video_lightbox' => 'yes',
				'lightbox_video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
			],
			// Slide 3: Image background with link
			[
				'_id' => 'slide_3',
				'background_type' => 'image',
				'image' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'title' => 'Professional Video Solutions',
				'subtitle' => 'Experience seamless video integration with support for multiple formats and advanced customization options.',
				'link' => [
					'url' => '#',
					'is_external' => false,
					'nofollow' => false,
				],
			],
		];

		$this->add_control(
			'slides',
			[
				'show_label' => false,
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '<# print(title || "Slider Item"); #>',
				'default' => $sample_slides,
				'condition' => [
					'slides_source' => 'custom'
				]
			]
		);

		$post_types = $this->get_post_types();
		$post_types[ 'by_id' ] = esc_html__( 'Manual Selection', 'wpzoom-elementor-addons' );
		$post_list = $this->get_post_list();
		$author_list = $this->get_authors_list();
		$taxonomies = get_taxonomies( [], 'objects' );
		$orderby_options = $this->get_post_orderby_options();

		$this->add_control(
			'posts_type',
			[
				'label' => esc_html__( 'Posts Source', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => $post_types,
				'default' => key( $post_types ),
				'condition' => [
					'slides_source' => 'posts'
				]
			]
		);

		$this->add_control(
			'posts_ids',
			[
				'label' => esc_html__( 'Search & Select', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $post_list,
				'label_block' => true,
				'multiple' => true,
				'condition' => [
					'slides_source' => 'posts',
					'posts_type' => 'by_id'
				]
			]
		);

		$this->add_control(
			'posts_authors', [
				'label' => esc_html__( 'Author', 'wpzoom-elementor-addons' ),
				'label_block' => true,
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'default' => [],
				'options' => $author_list,
				'condition' => [
					'slides_source' => 'posts',
					'posts_type!' => [ 'by_id' ]
				]
			]
		);

		foreach ( $taxonomies as $taxonomy => $object ) {
			if ( ! isset( $object->object_type[0] ) || ! in_array( $object->object_type[0], array_keys( $post_types ) ) ) {
				continue;
			}

			$this->add_control(
				'posts_' . $taxonomy . '_ids',
				[
					'label' => $object->label,
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'object_type' => $taxonomy,
					'options' => wp_list_pluck( get_terms( $taxonomy ), 'name', 'term_id' ),
					'condition' => [
						'slides_source' => 'posts',
						'posts_type' => $object->object_type
					]
				]
			);
		}

		$this->add_control(
			'posts__not_in',
			[
				'label' => esc_html__( 'Exclude', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $post_list,
				'label_block' => true,
				'post_type' => '',
				'multiple' => true,
				'condition' => [
					'slides_source' => 'posts',
					'posts_type!' => [ 'by_id' ]
				]
			]
		);

		$this->add_control(
			'posts_offset',
			[
				'label' => esc_html__( 'Offset', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '0',
				'condition' => [
					'slides_source' => 'posts'
				]
			]
		);

		$this->add_control(
			'posts_orderby',
			[
				'label' => esc_html__( 'Order By', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => $orderby_options,
				'default' => 'date',
				'condition' => [
					'slides_source' => 'posts'
				]
			]
		);

		$this->add_control(
			'posts_order',
			[
				'label' => esc_html__( 'Order', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'asc' => esc_html__( 'Ascending', 'wpzoom-elementor-addons' ),
					'desc' => esc_html__( 'Descending', 'wpzoom-elementor-addons' ),
				],
				'default' => 'desc',
				'condition' => [
					'slides_source' => 'posts'
				]
			]
		);

		$this->add_control(
			'posts_amount',
			[
				'label' => esc_html__( 'Amount', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'step' => 1,
				'max' => 100,
				'default' => 5,
				'condition' => [
					'slides_source' => 'posts'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'full',
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
				'label' => esc_html__( 'Settings', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'settings_slides',
			[
				'label' => esc_html__( 'Slides', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::HEADING
			]
		);

		$this->add_control(
			'animation_speed',
			[
				'label' => esc_html__( 'Animation Speed', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 100,
				'step' => 10,
				'max' => 10000,
				'default' => 300,
				'description' => esc_html__( 'Slide speed in milliseconds', 'wpzoom-elementor-addons' ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => esc_html__( 'Autoplay?', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off' => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'return_value' => 'yes',
				'default' => '',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => esc_html__( 'Autoplay Speed', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 100,
				'step' => 100,
				'max' => 10000,
				'default' => 3000,
				'description' => esc_html__( 'Autoplay speed in milliseconds', 'wpzoom-elementor-addons' ),
				'condition' => [
					'autoplay' => 'yes'
				],
				'frontend_available' => true
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => esc_html__( 'Infinite Loop?', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off' => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'frontend_available' => true
			]
		);

		$this->add_control(
			'center',
			[
				'label' => esc_html__( 'Center Mode?', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off' => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'return_value' => 'yes',
				'description' => esc_html__( 'Best works with odd number of slides (Slides To Show) and loop (Infinite Loop)', 'wpzoom-elementor-addons' ),
				'frontend_available' => true,
				'style_transfer' => true
			]
		);

		$this->add_control(
			'vertical',
			[
				'label' => esc_html__( 'Vertical Mode?', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off' => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'return_value' => 'yes',
				'frontend_available' => true,
				'style_transfer' => true
			]
		);

		$this->add_responsive_control(
			'slides_to_show',
			[
				'label' => esc_html__( 'Slides To Show', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					1 => esc_html__( '1 Slide', 'wpzoom-elementor-addons' ),
					2 => esc_html__( '2 Slides', 'wpzoom-elementor-addons' ),
					3 => esc_html__( '3 Slides', 'wpzoom-elementor-addons' ),
					4 => esc_html__( '4 Slides', 'wpzoom-elementor-addons' ),
					5 => esc_html__( '5 Slides', 'wpzoom-elementor-addons' ),
					6 => esc_html__( '6 Slides', 'wpzoom-elementor-addons' )
				],
				'desktop_default' => 1,
				'tablet_default' => 1,
				'mobile_default' => 1,
				'frontend_available' => true,
				'style_transfer' => true
			]
		);

		$this->add_control(
			'settings_navigation',
			[
				'label' => esc_html__( 'Navigation', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'navigation',
			[
				'label' => esc_html__( 'Navigation', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => esc_html__( 'None', 'wpzoom-elementor-addons' ),
					'arrow' => esc_html__( 'Arrow', 'wpzoom-elementor-addons' ),
					'dots' => esc_html__( 'Dots', 'wpzoom-elementor-addons' ),
					'both' => esc_html__( 'Arrow & Dots', 'wpzoom-elementor-addons' )
				],
				'default' => 'arrow',
				'frontend_available' => true,
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'arrow_prev_icon',
			[
				'label' => esc_html__( 'Previous Icon', 'wpzoom-elementor-addons' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'default' => [
					'value' => 'fas fa-chevron-left',
					'library' => 'fa-solid'
				],
				'condition' => [
					'navigation' => [ 'arrow', 'both' ]
				],
			]
		);

		$this->add_control(
			'arrow_next_icon',
			[
				'label' => esc_html__( 'Next Icon', 'wpzoom-elementor-addons' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'default' => [
					'value' => 'fas fa-chevron-right',
					'library' => 'fa-solid'
				],
				'condition' => [
					'navigation' => [ 'arrow', 'both' ]
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
			'_section_style_slider',
			[
				'label' => esc_html__( 'Slider Height', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'auto_height',
			[
				'label' => esc_html__( 'Custom Height', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no'
			]
		);

		$this->add_responsive_control(
			'auto_height_max',
			[
				'label' => esc_html__( 'Automatic Height Maximum', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vh' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
                    'vh' => [
                        'min' => 1,
                        'max' => 100,
                    ]
				],
				'default' => [
					'unit' => 'vh',
					'size' => 100
				],
				'desktop_default' => [
					'unit' => 'vh',
					'size' => 100
				],
				'tablet_default' => [
					'unit' => 'vh',
					'size' => 100
				],
				'mobile_default' => [
					'unit' => 'vh',
					'size' => 100
				],
				'selectors' => [
					'{{WRAPPER}} .slick-slider' => 'height: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'auto_height' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_style_item',
			[
				'label' => esc_html__( 'Slider Item', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'item_spacing',
			[
				'label' => esc_html__( 'Slide Spacing (px)', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'unit' => 'px',
					'size' => 0,
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
				'label' => esc_html__( 'Border Radius', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .wpz-slick-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_style_content',
			[
				'label' => esc_html__( 'Slide Content', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => esc_html__( 'Content Padding', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .wpz-slick-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_width',
			[
				'label' => esc_html__( 'Content Width', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px', 'vw' ],
				'range' => [
					'%' => [
						'min' => 10,
						'max' => 100,
						'step' => 1,
					],
					'px' => [
						'min' => 100,
						'max' => 1200,
						'step' => 10,
					],
					'vw' => [
						'min' => 10,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 80,
				],
				'selectors' => [
					'{{WRAPPER}} .wpz-slick-content' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_horizontal_position',
			[
				'label' => esc_html__( 'Horizontal Position', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-h-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .wpz-slick-item' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_vertical_position',
			[
				'label' => esc_html__( 'Vertical Position', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Top', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Middle', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-v-align-middle',
					],
					'flex-end' => [
						'title' => esc_html__( 'Bottom', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .wpz-slick-item' => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_text_align',
			[
				'label' => esc_html__( 'Text Align', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .wpz-slick-content' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'content_background',
				'selector' => '{{WRAPPER}} .wpz-slick-content',
				'exclude' => [
					 'image'
				]
			]
		);

		$this->add_control(
			'_heading_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Title', 'wpzoom-elementor-addons' ),
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => esc_html__( 'Bottom Spacing', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .wpz-slick-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpz-slick-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title',
				'label' => esc_html__( 'Typography', 'wpzoom-elementor-addons' ),
				'selector' => '{{WRAPPER}} .wpz-slick-title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->add_control(
			'_heading_subtitle',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Subtitle', 'wpzoom-elementor-addons' ),
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'subtitle_spacing',
			[
				'label' => esc_html__( 'Bottom Spacing', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .wpz-slick-subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label' => esc_html__( 'Text Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpz-slick-subtitle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'subtitle',
				'label' => esc_html__( 'Typography', 'wpzoom-elementor-addons' ),
				'selector' => '{{WRAPPER}} .wpz-slick-subtitle',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_style_arrow',
			[
				'label' => esc_html__( 'Navigation :: Arrow', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'arrow_position_toggle',
			[
				'label' => esc_html__( 'Position', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => esc_html__( 'None', 'wpzoom-elementor-addons' ),
				'label_on' => esc_html__( 'Custom', 'wpzoom-elementor-addons' ),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'arrow_position_y',
			[
				'label' => esc_html__( 'Vertical', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
				'label' => esc_html__( 'Horizontal', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
					'size' => 25,
				],
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
				'label' => esc_html__( 'Size', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'unit' => 'px',
					'size' => 40,
				],
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
				'label' => esc_html__( 'Border Radius', 'wpzoom-elementor-addons' ),
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
				'label' => esc_html__( 'Normal', 'wpzoom-elementor-addons' ),
			]
		);

		$this->add_control(
			'arrow_color',
			[
				'label' => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
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
				'label' => esc_html__( 'Background Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#00000000',
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_arrow_hover',
			[
				'label' => esc_html__( 'Hover', 'wpzoom-elementor-addons' ),
			]
		);

		$this->add_control(
			'arrow_hover_color',
			[
				'label' => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_hover_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'wpzoom-elementor-addons' ),
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
				'label' => esc_html__( 'Navigation :: Dots', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'dots_nav_position_y',
			[
				'label' => esc_html__( 'Vertical Position', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
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
				'label' => esc_html__( 'Spacing', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .slick-dots li' => 'margin-right: calc({{SIZE}}{{UNIT}} / 2); margin-left: calc({{SIZE}}{{UNIT}} / 2);',
				],
			]
		);

		$this->add_responsive_control(
			'dots_nav_align',
			[
				'label' => esc_html__( 'Alignment', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'wpzoom-elementor-addons' ),
						'icon' => 'eicon-text-align-right',
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
				'label' => esc_html__( 'Normal', 'wpzoom-elementor-addons' ),
			]
		);

		$this->add_control(
			'dots_nav_size',
			[
				'label' => esc_html__( 'Size', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dots_nav_color',
			[
				'label' => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
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
				'label' => esc_html__( 'Hover', 'wpzoom-elementor-addons' ),
			]
		);

		$this->add_control(
			'dots_nav_hover_color',
			[
				'label' => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
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
				'label' => esc_html__( 'Active', 'wpzoom-elementor-addons' ),
			]
		);

		$this->add_control(
			'dots_nav_active_size',
			[
				'label' => esc_html__( 'Size', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .slick-dots li.slick-active button:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dots_nav_active_color',
			[
				'label' => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slick-dots .slick-active button:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_style_overlay',
			[
				'label' => esc_html__( 'Background Overlay', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'background_overlay_enable',
			[
				'label' => esc_html__( 'Enable Overlay', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off' => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'selectors' => [
					'{{WRAPPER}} .wpz-slick-item::before' => 'display: block;',
				],
			]
		);

		$this->add_control(
			'background_overlay_color',
			[
				'label' => esc_html__( 'Overlay Color', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .wpz-slick-item::before' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'background_overlay_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'background_overlay_opacity',
			[
				'label' => esc_html__( 'Overlay Opacity', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.01,
					],
				],
				'default' => [
					'size' => 0.5,
				],
				'selectors' => [
					'{{WRAPPER}} .wpz-slick-item::before' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'background_overlay_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'background_overlay_blend_mode',
			[
				'label' => esc_html__( 'Blend Mode', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'normal' => esc_html__( 'Normal', 'wpzoom-elementor-addons' ),
					'multiply' => esc_html__( 'Multiply', 'wpzoom-elementor-addons' ),
					'screen' => esc_html__( 'Screen', 'wpzoom-elementor-addons' ),
					'overlay' => esc_html__( 'Overlay', 'wpzoom-elementor-addons' ),
					'darken' => esc_html__( 'Darken', 'wpzoom-elementor-addons' ),
					'lighten' => esc_html__( 'Lighten', 'wpzoom-elementor-addons' ),
					'color-dodge' => esc_html__( 'Color Dodge', 'wpzoom-elementor-addons' ),
					'color-burn' => esc_html__( 'Color Burn', 'wpzoom-elementor-addons' ),
					'hard-light' => esc_html__( 'Hard Light', 'wpzoom-elementor-addons' ),
					'soft-light' => esc_html__( 'Soft Light', 'wpzoom-elementor-addons' ),
					'difference' => esc_html__( 'Difference', 'wpzoom-elementor-addons' ),
					'exclusion' => esc_html__( 'Exclusion', 'wpzoom-elementor-addons' ),
					'hue' => esc_html__( 'Hue', 'wpzoom-elementor-addons' ),
					'saturation' => esc_html__( 'Saturation', 'wpzoom-elementor-addons' ),
					'color' => esc_html__( 'Color', 'wpzoom-elementor-addons' ),
					'luminosity' => esc_html__( 'Luminosity', 'wpzoom-elementor-addons' ),
				],
				'default' => 'normal',
				'selectors' => [
					'{{WRAPPER}} .wpz-slick-item::before' => 'mix-blend-mode: {{VALUE}};',
				],
				'condition' => [
					'background_overlay_enable' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Get All Query Arguments from Settings.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param  array  $settings  The settings array that contains the query arguments.
	 * @param  string $post_type The post type that the query will be run for.
	 * @return array             Array of all the query arguments in the given settings array.
	 */
	public function get_query_args( $settings = [], $post_type = 'post' ) {
		$settings = wp_parse_args(
			$settings,
			[
				'posts_type' => $post_type,
				'posts_ids' => [],
				'posts_orderby' => 'date',
				'posts_order' => 'desc',
				'posts_amount' => 5,
				'posts_offset' => 0,
				'posts__not_in' => []
			]
		);

		$args = [
			'orderby' => $settings[ 'posts_orderby' ],
			'order' => $settings[ 'posts_order' ],
			'ignore_sticky_posts' => 1,
			'post_status' => 'publish',
			'posts_per_page' => intval( $settings[ 'posts_amount' ] ),
			'offset' => intval( $settings[ 'posts_offset' ] )
		];

		if ( 'by_id' === $settings[ 'posts_type' ] ) {
			$args[ 'post_type' ] = 'any';
			$args[ 'post__in' ] = empty( $settings[ 'posts_ids' ] ) ? [0] : $settings[ 'posts_ids' ];
		} else {
			$args[ 'post_type' ] = $settings[ 'posts_type' ];
			$args[ 'tax_query' ] = [];

			$taxonomies = get_object_taxonomies( $settings[ 'posts_type' ], 'objects' );

			foreach ( $taxonomies as $object ) {
				$setting_key = 'posts_' . $object->name . '_ids';

				if ( ! empty( $settings[ $setting_key ] ) ) {
					$args[ 'tax_query' ][] = [
						'taxonomy' => $object->name,
						'field' => 'term_id',
						'terms' => $settings[ $setting_key ]
					];
				}
			}

			if ( ! empty( $args[ 'tax_query' ] ) ) {
				$args[ 'tax_query' ][ 'relation' ] = 'AND';
			}
		}

		if ( ! empty( $settings[ 'posts_authors' ] ) ) {
			$args[ 'author__in' ] = $settings[ 'posts_authors' ];
		}

		if ( ! empty( $settings[ 'posts__not_in' ] ) ) {
			$args[ 'post__not_in' ] = $settings[ 'posts__not_in' ];
		}

		return $args;
	}

	/**
	 * Detect video type from URL.
	 *
	 * @param string $url The video URL to analyze.
	 * @since 1.0.0
	 * @access private
	 * @return string|false The video type (youtube, vimeo, hosted) or false if not detected.
	 */
	private function detect_video_type( $url ) {
		if ( empty( $url ) ) {
			return false;
		}

		// YouTube detection
		if ( preg_match( '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url ) ) {
			return 'youtube';
		}

		// Vimeo detection
		if ( preg_match( '/(?:vimeo\.com\/)([0-9]+)/', $url ) ) {
			return 'vimeo';
		}

		// Check if it's a direct video file
		$video_extensions = [ 'mp4', 'webm', 'ogg', 'mov', 'avi' ];
		$url_parts = parse_url( $url );
		if ( isset( $url_parts['path'] ) ) {
			$path_info = pathinfo( $url_parts['path'] );
			if ( isset( $path_info['extension'] ) && in_array( strtolower( $path_info['extension'] ), $video_extensions ) ) {
				return 'hosted';
			}
		}

		return false;
	}

	/**
	 * Get video ID from URL.
	 *
	 * @param string $url The video URL.
	 * @param string $type The video type.
	 * @since 1.0.0
	 * @access private
	 * @return string|false The video ID or false if not found.
	 */
	private function get_video_id( $url, $type ) {
		switch ( $type ) {
			case 'youtube':
				preg_match( '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches );
				return isset( $matches[1] ) ? $matches[1] : false;

			case 'vimeo':
				preg_match( '/(?:vimeo\.com\/)([0-9]+)/', $url, $matches );
				return isset( $matches[1] ) ? $matches[1] : false;

			default:
				return false;
		}
	}

	/**
	 * Build video embed URL with parameters.
	 *
	 * @param array $slide The slide data.
	 * @param string $video_type The video type.
	 * @param string $video_id The video ID.
	 * @since 1.0.0
	 * @access private
	 * @return string The embed URL.
	 */
	private function build_video_embed_url( $slide, $video_type, $video_id ) {
		$params = [];

		switch ( $video_type ) {
			case 'youtube':
				$base_url = $slide['video_privacy_mode'] ? 'https://www.youtube-nocookie.com/embed/' : 'https://www.youtube.com/embed/';
				$embed_url = $base_url . $video_id;

				$params['autoplay'] = '1';
				$params['mute'] = '1';
				$params['controls'] = '0';
				$params['rel'] = '0';
				$params['modestbranding'] = '1';
				$params['playsinline'] = '1';

				if ( ! empty( $slide['video_start_time'] ) ) {
					$params['start'] = $slide['video_start_time'];
				}

				if ( ! empty( $slide['video_end_time'] ) ) {
					$params['end'] = $slide['video_end_time'];
				}

				if ( empty( $slide['video_play_once'] ) ) {
					$params['loop'] = '1';
					$params['playlist'] = $video_id;
				}

				break;

			case 'vimeo':
				$embed_url = 'https://player.vimeo.com/video/' . $video_id;

				$params['autoplay'] = '1';
				$params['muted'] = '1';
				$params['controls'] = '0';
				$params['title'] = '0';
				$params['byline'] = '0';
				$params['portrait'] = '0';
				$params['playsinline'] = '1';
				$params['background'] = '1';

				if ( ! empty( $slide['video_start_time'] ) ) {
					$params['t'] = $slide['video_start_time'] . 's';
				}

				if ( empty( $slide['video_play_once'] ) ) {
					$params['loop'] = '1';
				}

				break;

			default:
				return '';
		}

		if ( ! empty( $params ) ) {
			$embed_url .= '?' . http_build_query( $params );
		}

		return $embed_url;
	}

	/**
	 * Render video background.
	 *
	 * @param array $slide The slide data.
	 * @since 1.0.0
	 * @access private
	 * @return void
	 */
	private function render_video_background( $slide ) {
		if ( empty( $slide['video_link'] ) ) {
			return;
		}

		$video_type = $this->detect_video_type( $slide['video_link'] );

		if ( ! $video_type ) {
			return;
		}

		$video_attrs = [
			'class' => 'wpz-video-bg',
			'data-video-type' => $video_type,
			'data-video-url' => esc_url( $slide['video_link'] ),
		];

		if ( ! empty( $slide['video_play_on_mobile'] ) ) {
			$video_attrs['data-play-on-mobile'] = 'true';
		}

		if ( ! empty( $slide['video_start_time'] ) ) {
			$video_attrs['data-start-time'] = intval( $slide['video_start_time'] );
		}

		if ( ! empty( $slide['video_end_time'] ) ) {
			$video_attrs['data-end-time'] = intval( $slide['video_end_time'] );
		}

		if ( ! empty( $slide['video_play_once'] ) ) {
			$video_attrs['data-play-once'] = 'true';
		}

		if ( ! empty( $slide['video_privacy_mode'] ) ) {
			$video_attrs['data-privacy-mode'] = 'true';
		}

		?>
		<div <?php echo Utils::render_html_attributes( $video_attrs ); ?>>
			<?php if ( 'hosted' === $video_type ) : ?>
				<video
					autoplay
					muted
					playsinline
					<?php echo empty( $slide['video_play_once'] ) ? 'loop' : ''; ?>
					<?php echo empty( $slide['video_play_on_mobile'] ) ? 'data-mobile-disabled="true"' : ''; ?>
				>
					<source src="<?php echo esc_url( $slide['video_link'] ); ?>" type="video/mp4">
				</video>
			<?php else :
				$video_id = $this->get_video_id( $slide['video_link'], $video_type );
				if ( $video_id ) :
					$embed_url = $this->build_video_embed_url( $slide, $video_type, $video_id );
			?>
				<iframe
					src="<?php echo esc_url( $embed_url ); ?>"
					frameborder="0"
					allow="autoplay; fullscreen; picture-in-picture"
					allowfullscreen>
				</iframe>
			<?php
				endif;
			endif; ?>
		</div>
		<?php
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
		$slides = [];

		if ( 'posts' == $settings[ 'slides_source' ] ) {
			$args = $this->get_query_args( $settings );
			$query = new \WP_Query( $args );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();

					$slides[] = [
						'_id' => get_the_ID(),
						'title' => get_the_title(),
						'subtitle' => get_the_excerpt(),
						'background_type' => 'image',
						'image' => [ 'id' => get_post_thumbnail_id(), 'url' => false ],
						'link' => [ 'url' => get_permalink() ]
					];
				}

				wp_reset_postdata();
			}
		} else {
			$slides = $settings[ 'slides' ];
		}

		if ( empty( $slides ) ) {
			return;
		}

		?><div class="wpzjs-slick wpzjs-slick-video wpz-slick wpz-slick--slider">

			<?php foreach ( $slides as $slide ) :

				// Set default background type if not set
				$background_type = isset( $slide['background_type'] ) ? $slide['background_type'] : 'image';

				$item_tag = 'div';
				$id = 'wpz-slick-item-' . $slide ['_id' ];

				$this->add_render_attribute( $id, 'class', 'wpz-slick-item' );
				$this->add_render_attribute( $id, 'class', 'wpz-slick-item--' . $background_type );

				// Only make the slide a link if no button or lightbox is shown and a link is provided
				$has_button = !empty($slide['show_button']) && $slide['show_button'] === 'yes';
				$has_lightbox = !empty($slide['show_video_lightbox']) && $slide['show_video_lightbox'] === 'yes';
				$has_slide_link = isset( $slide[ 'link' ] ) && ! empty( $slide[ 'link' ][ 'url' ] );

				if ( $has_slide_link && !$has_button && !$has_lightbox ) {
					$item_tag = 'a';
					$this->add_link_attributes( $id, $slide[ 'link' ] );
				}
				?>

				<div class="wpz-slick-slide">

					<<?php echo $item_tag; // WPCS: XSS OK. ?> <?php $this->print_render_attribute_string( $id ); ?>>

						<?php if ( 'video' === $background_type ) : ?>
							<!-- Video Background -->
							<?php $this->render_video_background( $slide ); ?>

							<!-- Fallback Image for Video -->
							<?php if ( ! empty( $slide['background_fallback']['url'] ) ) : ?>
								<img class="wpz-slick-img wpz-video-fallback" src="<?php echo esc_url( $slide['background_fallback']['url'] ); ?>" alt="<?php echo esc_attr( $slide[ 'title' ] ); ?>">
							<?php endif; ?>

						<?php else : ?>
							<!-- Image Background -->
							<?php
							$image = wp_get_attachment_image_url( $slide[ 'image' ][ 'id' ], $settings[ 'thumbnail_size' ] );
							if ( ! $image ) {
								$image = $slide[ 'image' ][ 'url' ];
							}
							?>
							<?php if ( $image ) : ?>
								<img class="wpz-slick-img" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $slide[ 'title' ] ); ?>">
							<?php endif; ?>
						<?php endif; ?>

						<?php if ( $slide[ 'title' ] || $slide[ 'subtitle' ] || $has_button || $has_lightbox ) : ?>
							<div class="wpz-slick-content">
								<?php if ( $slide[ 'title' ] ) : ?>
									<h2 class="wpz-slick-title"><?php echo WPZOOM_Elementor_Widgets::custom_kses( $slide[ 'title' ] ); ?></h2>
								<?php endif; ?>
								<?php if ( $slide[ 'subtitle' ] ) : ?>
									<p class="wpz-slick-subtitle"><?php echo WPZOOM_Elementor_Widgets::custom_kses( $slide[ 'subtitle' ] ); ?></p>
								<?php endif; ?>

								<?php if ( $has_button || $has_lightbox ) : ?>
									<div class="wpz-slick-actions">
										<?php if ( $has_button && !empty( $slide['button_text'] ) ) : ?>
											<a href="<?php echo esc_url( $slide['button_link']['url'] ?? '#' ); ?>"
											   class="wpz-slick-button elementor-button"
											   <?php echo ( $slide['button_link']['is_external'] ?? false ) ? 'target="_blank"' : ''; ?>
											   <?php echo ( $slide['button_link']['nofollow'] ?? false ) ? 'rel="nofollow"' : ''; ?>>
												<?php echo esc_html( $slide['button_text'] ); ?>
											</a>
										<?php endif; ?>

										<?php if ( $has_lightbox && !empty( $slide['lightbox_video_url'] ) ) : ?>
											<a href="<?php echo esc_url( $slide['lightbox_video_url'] ); ?>"
											   class="wpz-slick-lightbox-trigger"
											   title="<?php esc_attr_e( 'Play Video', 'wpzoom-elementor-addons' ); ?>">
                                               <svg height="32px" version="1.1" viewBox="0 0 512 512" width="512px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M405.2,232.9L126.8,67.2c-3.4-2-6.9-3.2-10.9-3.2c-10.9,0-19.8,9-19.8,20H96v344h0.1c0,11,8.9,20,19.8,20  c4.1,0,7.5-1.4,11.2-3.4l278.1-165.5c6.6-5.5,10.8-13.8,10.8-23.1C416,246.7,411.8,238.5,405.2,232.9z" fill="#fff"/></svg>
												<span class="elementor-screen-only"><?php esc_html_e( 'Play Video', 'wpzoom-elementor-addons' ); ?></span>
											</a>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>

					</<?php echo $item_tag; // WPCS: XSS OK. ?>>

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
