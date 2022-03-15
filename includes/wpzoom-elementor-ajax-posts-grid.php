<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Instance the plugin
WPZOOM_Elementor_Ajax_Post_Grid::instance();

/**
 * WPZOOM Elementor Ajax Post Grid Class
 *
 * @since 1.1.5
 */
class WPZOOM_Elementor_Ajax_Post_Grid {


	/**
	 * Settings
	 *
	 * @var settings.
	 * @since 1.1.5
	 * @access private
	 * @static
	 */
	private static $settings = array();

	/**
	 * Instance
	 *
	 * @var WPZOOM_Elementor_Ajax_Post_Grid The single instance of the class.
	 * @since 1.1.5
	 * @access private
	 * @static
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.1.5
	 * @access public
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * @since 1.1.5
	 * @access public
	 */
	public function __construct() {

		add_action( 'wp_ajax_wpz_posts_grid_load_more', array( $this, 'ajax_post_grid_load_more' ) );
		add_action( 'wp_ajax_nopriv_wpz_posts_grid_load_more', array( $this, 'ajax_post_grid_load_more' ) );
		
	}

	public function ajax_post_grid_load_more() {

		if ( check_ajax_referer( 'wpz_posts_grid_load_more', 'nonce' ) && wp_verify_nonce( $_POST['nonce'], 'wpz_posts_grid_load_more' ) ) :

			$offset = sanitize_text_field( $_POST['offset'] );
			$data   = sanitize_text_field( $_POST['posts_data'] );
			$data   = json_decode( stripslashes( $data ), true );

			self::$settings = $data;

			$args = array(
				'posts_per_page' 	  => absint( $data['posts_per_page'] ),
				'post__not_in'        => get_option( 'sticky_posts' ),
				'ignore_sticky_posts' => true,
			);

			if ( ! empty( $data[ 'category_name' ] ) ) {
				$args[ 'category_name' ] = $data[ 'category_name' ];
			}

			if ( ! empty( $data[ 'category__not_in' ] ) ) {
				$args[ 'category__not_in' ] = $data[ 'category__not_in' ];
			}

			// Order by.
			if ( ! empty( $data[ 'orderby' ] ) ) {
				$args[ 'orderby' ] = $data[ 'orderby' ];
			}

			// Order .
			if ( ! empty( $data[ 'order' ] ) ) {
				$args[ 'order' ] = $data[ 'order' ];
			}

			// Offset .
			if ( ! empty( $offset ) ) {
				$args[ 'offset' ] = $offset;
			}

			$grid_style = isset( $data[ 'grid_style' ] ) ? $data[ 'grid_style' ] : '1';

			// Post Query
			$all_posts = new WP_Query( $args );

			if ( $all_posts->have_posts() ) {

				$layout_file = WPZOOM_EL_ADDONS_PATH . 'includes/widgets/posts-grid/layouts/layout-' . $grid_style . '.php';
				
				?>
				<div class="wpzoom-ajax-posts">
					<?php include $layout_file; ?>
				</div>
				<?php
			}
		
		endif; //wp_verify_nonce


		wp_die();
	}

	protected function get_settings() {

		$settings = self::$settings;

		return $settings;

	}

	/**
	 * Filter Excerpt Length
	 *
	 * Filters the excerpt length to allow custom values.
	 *
	 * @since 1.1.5
	 * @access public
	 * @return array Custom excerpt length.
	 */
	public function wpz_filter_excerpt_length( $length ) {
		
		$settings = $this->get_settings();
		$excerpt_length = ( !empty( $settings[ 'excerpt_length' ] ) ) ? absint( $settings[ 'excerpt_length' ] ) : 25;

		return absint( $excerpt_length );
	}

	/**
	 * Filter Excerpt More.
	 *
	 * Filters the read more value at the end of excerpts.
	 *
	 * @since 1.1.5
	 * @access public
	 * @return array Excerpt more.
	 */
	public function wpz_filter_excerpt_more( $more ) {
		return '&hellip;';
	}

	/**
	 * Render Post Thumbnail.
	 *
	 * Outputs the markup for the post thumbnail.
	 *
	 * @since 1.1.5
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
	 * @since 1.1.5
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
		<<?php echo $title_tag; // WPCS: XSS OK. ?> class="title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</<?php echo $title_tag; // WPCS: XSS OK. ?>>
		<?php
	}

	/**
	 * Render Post Meta.
	 *
	 * Outputs the markup for the post meta.
	 *
	 * @since 1.1.5
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
	 * @since 1.1.5
	 * @access public
	 */
	protected function render_excerpt() {
		$settings = $this->get_settings();

		$show_excerpt = $settings[ 'show_excerpt' ];

		if ( 'yes' !== $show_excerpt ) {
			return;
		}

		add_filter( 'excerpt_more', [ $this, 'wpz_filter_excerpt_more' ], 20 );
		add_filter( 'excerpt_length', [ $this, 'wpz_filter_excerpt_length' ], 9999 );

		?><div class="post-grid-excerpt"><?php the_excerpt(); ?></div><?php

		remove_filter( 'excerpt_length', [ $this, 'wpz_filter_excerpt_length' ], 9999 );
		remove_filter( 'excerpt_more', [ $this, 'wpz_filter_excerpt_more' ], 20 );
	}

	/**
	 * Render Post Read More.
	 *
	 * Outputs the markup for the readmore button.
	 *
	 * @since 1.1.5
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