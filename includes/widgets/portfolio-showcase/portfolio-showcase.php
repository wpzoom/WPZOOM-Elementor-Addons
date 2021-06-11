<?php
namespace WPZOOMElementorWidgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Widget_Base;

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
		return __( 'Portfolio Showcase', 'wpzoom-elementor-addons' );
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
		return [];
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
	protected function _register_controls() {

		$this->start_controls_section(
			'section_portfolio_showcase',
			array(
				'label' => esc_html__( 'General Settings', 'wpzoom-elementor-addons' ),
			)
		);
		$this->add_control(
			'category',
			array(
				'label'    => esc_html__( 'Category', 'wpzoom-elementor-addons' ),
				'type'     => Controls_Manager::SELECT,
				'default'  => 0,
				'options'  => $this->get_portfolio_taxonomies(),
			)
		);
		$this->add_control(
			'show_categories',
			array(
				'label'       => __( 'Display Category Filter <br/>at the Top (Isotope Effect)', 'wpzoom-elementor-addons' ),
				'subtitle'    => __( 'Isotope Effect', 'wpzoom-elementor-addons' ),
				'description' => __( 'If you\'ve selected to display posts from All categories, then the filter will include top-level portfolio categories (no sub-categories). <br/><br/>If you selected to display posts from a specific category, then the filter will display its sub-categories.', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'no',
			)
		);
		$this->add_control(
			'show_count',
			array(
				'label'    => __( 'Number of Posts', 'wpzoom-elementor-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6
			)
		);
		$this->add_control(
			'enable_ajax_items_loading',
			array(
				'label'       => __( 'Load Dynamically <br/>New Posts in Each Category', 'wpzoom-elementor-addons' ),
				'description' => __( 'This option will try to display the same number of posts in each category as it\'s configured in the Number of Posts option above.', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'yes',
			)
		);

		$this->end_controls_section();
		
		//Design & Appearance
		$this->start_controls_section(
			'section_design_appearance',
			array(
				'label' => __( 'Design & Appearance', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT
			)
		);
		$this->add_control(
			'layout_type',
			array(
				'label'   => esc_html__( 'Layout:', 'wpzoom-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'full-width' => esc_html__( 'Full-width', 'wpzoom-elementor-addons' ),
					'narrow'     => esc_html__( 'Narrow', 'wpzoom-elementor-addons' ),
				),
				'default' => 'full-width',
			)
		);

		$this->add_responsive_control(
			'col_number',
			[
				'label' => __( 'Number of Columns', 'wpzoom-elementor-addons' ),
				'description' => __( 'The number of columns may vary depending on screen size', 'wpzoom-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '4',
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
			'show_masonry',
			array(
				'label'       => esc_html__( 'Display Posts in Masonry Layout', 'wpzoom-elementor-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__( 'Yes', 'wpzoom-elementor-addons' ),
				'label_off'   => esc_html__( 'No', 'wpzoom-elementor-addons' ),
				'default'     => 'no',
				'condition'   =>  array(
					'layout_type!' => 'narrow'
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
				'label'       => wp_kses_post( __( 'Add Margins <br/>between Posts (whitespace)', 'wpzoom-elementor-addons' ) ),
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
				'label' => __( 'Posts Settings', 'wpzoom-elementor-addons' ),
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
				'label' => __( 'Video Background', 'wpzoom-elementor-addons' ),
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
				'label' => __( 'Details to Show', 'wpzoom-elementor-addons' ),
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
				'default'     => 'yes',
				'condition'   =>  array(
					'layout_type!' => 'narrow',
					'show_popup!' => 'yes'
				),
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
				'label' => __( '"View All" or "Load More" Button at the Bottom', 'wpzoom-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT
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
				'placeholder' => __( 'https://your-link.com', 'wpzoom-elementor-addons' ),
				'default' => array(
					'url' => '#',
				),
			)
		);
		$this->end_controls_section();

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
					$portfolio_tax[ $taxonomy->slug ] = $taxonomy->name;
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

		// Get settings.
		$settings = $this->get_settings();

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

        $args = array(
            'post_type'      => 'portfolio_item',
            'posts_per_page' => $show_count,
            'orderby'        =>'menu_order date'
        );

        if ( $category ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'portfolio',
                    'terms'    => $category,
                    'field'    => 'term_id',
                )
            );
        }

		$wp_query = new \WP_Query( $args );

		$count = $wp_query->found_posts;
		
		if ( $wp_query->have_posts() ) :

			echo '<div class="portfolio-showcase">';

			if ( $show_masonry ) {
				echo '<div id="portfolio-masonry">';
			}

			if ( $show_categories ) {
				include( __DIR__ . '/view/filter.php' );
			}

			if ( 'narrow' == $layout_type ) {
				echo '<div class="inner-wrap portfolio_template_clean">';
			}
			?>
			<div <?php 
				echo( ! empty( $category ) ? 'data-subcategory="' . $category . '"' : '' ); ?>
				data-ajax-items-loading="<?php echo esc_attr( $enable_ajax_items_loading ) ?>"
				data-nonce="<?php echo esc_attr( wp_create_nonce( 'wpz_get_portfolio_items' ) ) ?>"
				data-count-nonce="<?php echo esc_attr( wp_create_nonce( 'wpz_count_portfolio_items' ) ) ?>"
				data-items-count="<?php echo $count ?>"
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
					'show_categories'              => $show_categories,
					'always_play_background_video' => $always_play_background_video
				) ) ) ?>"
				class="portfolio-grid <?php if ( $show_space == true ) { ?> portfolio_with_space<?php } ?> col_no_<?php echo $col_number; ?> <?php echo $always_play_background_video_class; ?>"
			>
				<?php
					$this->looper( $wp_query,
						array(
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
			</div><!-- // .portfolio-showcase -->

		<?php else: ?>

			<div class="inner-wrap" style="text-align:center;">
				<h3><?php esc_html__( 'No Portfolio Posts Found', 'wpzoom-elementor-addons' ) ?></h3>
				<p class="description"><?php printf( __( 'Please add a few Portfolio Posts first <a href="%1$s">here</a>.', 'wpzoom-elementor-addons' ), esc_url( admin_url( 'post-new.php?post_type=portfolio_item' ) ) ); ?></p>
			</div>

		<?php endif; ?>

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

        <?php if ( $view_all_enabled ) : ?>

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

		<?php
	}

	function looper( $wp_query, $settings ) {

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
            } elseif ($col_number == '1') {
                $size = 'portfolio_item-thumbnail_wide';
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
                    'poster="' . esc_attr( get_the_post_thumbnail_url( get_the_ID(), $size ) ) . '"'
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
                                data-show-caption="<?php echo $show_popup_caption ?>">
                                <!-- start lightbox -->
                                <?php if ( $popup_video_type === 'self_hosted' && $is_video_popup ): ?>
                                    <div id="zoom-popup-<?php echo the_ID(); ?>" class="mfp-hide"
                                         data-src="<?php echo $popup_final_external_src ?>">
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
                                    href="<?php echo $video_background_popup_url ?>"></a>
                                <?php else: ?>
                                    <?php if( has_post_thumbnail() && !\option::is_on('lightbox_video_only') ): ?>
                                        <a class="mfp-image portfolio-popup-video popup_image_insp"
                                           href="<?php echo $post_thumbnail ?>"></a>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <span class="portfolio_item-title" style="display: none;">
                                    <a href="<?php echo esc_url( get_permalink() ); ?>"
                                       title="<?php echo esc_attr( get_the_title() ); ?>"><?php the_title(); ?></a>
                                </span>

                            </div>
                        </div>

                        <?php if ( $is_video_background ): ?>
                            <video class="portfolio-gallery-video-background" <?php echo $video_atts ?>
                                   style=" width:100%; height:auto;vertical-align: middle; display:block;">
                                <source src="<?php echo $final_background_src ?>"
                                        type="<?php echo $filetype['type'] ?>">
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
                                <video class="portfolio-gallery-video-background" <?php echo $video_atts ?>
                                       style=" width:100%; height:auto;vertical-align: middle; display:block;">
                                    <source src="<?php echo $final_background_src ?>"
                                            type="<?php echo $filetype['type'] ?>">
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
                            <?php if ( $enable_director_name && $video_director) { ?>
                               <li><?php echo $video_director; ?></li>
                            <?php } ?>

                            <?php if ($enable_year && $video_year) { ?>
                               <li><?php echo $video_year; ?></li>
                            <?php } ?>

                            <?php if ( $enable_category ) : ?>
                                <li>
                                <?php if ( is_array( $tax_menu_items = get_the_terms( get_the_ID(), 'portfolio' ) ) ) : ?>
                                    <?php foreach ( $tax_menu_items as $tax_menu_item ) : ?>
                                        <a class="portfolio_sub_category"
                                           href="<?php echo get_term_link( $tax_menu_item, $tax_menu_item->taxonomy ); ?>"><?php echo $tax_menu_item->name; ?></a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                             </li>
                             <?php endif; ?>
                        </ul>
                    </div>

                    <?php the_title( sprintf( '<h3 class="portfolio_item-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' ); ?>


                </div>
            <?php } else {
                if ( $show_popup ) {
                    ?>

                    <div class="entry-thumbnail-popover">
                        <div class="entry-thumbnail-popover-content lightbox_popup_insp popover-content--animated"
                             data-show-caption="<?php echo $show_popup_caption ?>">
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
                                href="<?php echo $video_background_popup_url ?>"></a>
                            <?php else: ?>
                                <?php if( has_post_thumbnail() && !\option::is_on( 'lightbox_video_only' ) ) : ?>
                                    <a class="mfp-image portfolio-popup-video popup_image_insp"
                                       href="<?php echo $post_thumbnail ?>"></a>
                                <?php endif; ?>
                            <?php endif; ?>

                            <div class="entry-meta">

                                <h3 class="portfolio_item-title">
                                    <a href="<?php echo esc_url( get_permalink() ); ?>"
                                       title="<?php echo esc_attr( get_the_title() ); ?>"><?php the_title(); ?></a>
                                </h3>

                                <ul>
                                    <?php if ($enable_director_name && $video_director) { ?>
                                       <li><?php echo $video_director; ?></li>
                                    <?php } ?>

                                    <?php if ($enable_year && $video_year) { ?>
                                       <li><?php echo $video_year; ?></li>
                                    <?php } ?>

                                    <?php if ( $enable_category ) : ?><li>

                                         <?php if ( is_array( $tax_menu_items = get_the_terms( get_the_ID(), 'portfolio' ) ) ) : ?>
                                             <?php foreach ( $tax_menu_items as $tax_menu_item ) : ?>
                                                <?php echo $tax_menu_item->name; ?>
                                             <?php endforeach; ?>
                                         <?php endif; ?>
                                     </li>
                                     <?php endif; ?>
                                </ul>

                            </div>

                        </div>
                    </div>

                    <?php if ( $is_video_background ): ?>
                        <video class="portfolio-gallery-video-background" <?php echo $video_atts ?>
                               style=" width:100%; height:auto;vertical-align: middle; display:block;">
                            <source src="<?php echo $final_background_src ?>"
                                    type="<?php echo $filetype['type'] ?>">
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
                                        <?php if ($enable_director_name && $video_director) { ?>
                                           <li><?php echo $video_director; ?></li>
                                        <?php } ?>

                                        <?php if ($enable_year && $video_year) { ?>
                                           <li><?php echo $video_year; ?></li>
                                        <?php } ?>

                                        <?php if ( $enable_category ) : ?><li>

                                             <?php if ( is_array( $tax_menu_items = get_the_terms( get_the_ID(), 'portfolio' ) ) ) : ?>
                                                 <?php foreach ( $tax_menu_items as $tax_menu_item ) : ?>
                                                    <?php echo $tax_menu_item->name; ?>
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
                            <video class="portfolio-gallery-video-background" <?php echo $video_atts ?>
                                   style=" width:100%; height:auto;vertical-align: middle; display:block;">
                                <source src="<?php echo $final_background_src ?>"
                                        type="<?php echo $filetype['type'] ?>">
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