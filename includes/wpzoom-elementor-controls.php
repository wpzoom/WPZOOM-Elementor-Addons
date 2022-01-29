<?php
/**
 * Custom Elementor Controls for WPZOOM.
 *
 * @package WPZOOM_Elementor_Addons
 * @since   1.2.0
 */

namespace WPZOOMElementorWidgets;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Instance the plugin.
WPZOOM_Elementor_Controls::instance();

/**
 * WPZOOM Elementor Controls Class
 *
 * @since 1.2.0
 */
class WPZOOM_Elementor_Controls {
	/**
	 * Instance
	 *
	 * @var WPZOOM_Elementor_Controls The single instance of the class.
	 * @since 1.2.0
	 * @access private
	 * @static
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.2.0
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
	 * @since 1.2.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'elementor/controls/register', array( $this, 'register_controls' ) );
	}

	/**
	 * Register custom controls.
	 *
	 * @since  1.2.0
	 * @access public
	 * @param  \Elementor\Controls_Manager $controls_manager Elementor controls manager.
	 */
	public function register_controls( $controls_manager ) {
		require_once plugin_dir_path( __FILE__ ) . 'controls/class-group-control-css-backdrop-filter.php';

		$controls_manager->add_group_control( Group_Control_Css_Backdrop_Filter::get_type(), new Group_Control_Css_Backdrop_Filter() );
	}
}
