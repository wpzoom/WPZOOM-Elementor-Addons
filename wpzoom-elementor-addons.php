<?php
/**
 * Plugin Name:       Elementor Addons by WPZOOM
 * Plugin URI:        https://www.wpzoom.com/plugins/wpzoom-elementor-addons/
 * Description:       A plugin that provides a collection of Elementor Templates and advanced widgets created by the WPZOOM team
 * Version:           1.1.53
 * Author:            WPZOOM
 * Author URI:        https://www.wpzoom.com/
 * Text Domain:       wpzoom-elementor-addons
 * License:           GNU General Public License v2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least: 6.0
 * Tested up to:      6.8
 * Elementor tested up to: 3.99
 * Elementor Pro tested up to: 3.99
 *
 * @package WPZOOM_Elementor_Addons
 */

namespace WPZOOM_Elementor_Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'WPZOOM_EL_ADDONS_VER' ) ) {
	define( 'WPZOOM_EL_ADDONS_VER', get_file_data( __FILE__, [ 'Version' ] )[0] ); // phpcs:ignore
}

define( 'WPZOOM_EL_ADDONS__FILE__', __FILE__ );
define( 'WPZOOM_EL_ADDONS_PLUGIN_BASE', plugin_basename( WPZOOM_EL_ADDONS__FILE__ ) );
define( 'WPZOOM_EL_ADDONS_PLUGIN_DIR', dirname( WPZOOM_EL_ADDONS_PLUGIN_BASE ) );

define( 'WPZOOM_EL_ADDONS_PATH', plugin_dir_path( WPZOOM_EL_ADDONS__FILE__ ) );
define( 'WPZOOM_EL_ADDONS_URL', plugin_dir_url( WPZOOM_EL_ADDONS__FILE__ ) );

// Instance the plugin
WPZOOM_Elementor_Addons::instance();

/**
 * Main WPZOOM Elementor Addons Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
final class WPZOOM_Elementor_Addons {
	/**
	 * Minimum Elementor Version
	 *
	 * @var string Minimum Elementor version required to run the plugin.
	 * @since 1.0.0
	 */
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @var string Minimum PHP version required to run the plugin.
	 * @since 1.0.0
	 */
	const MINIMUM_PHP_VERSION = '7.0';

	/**
	 * Instance
	 *
	 * @var WPZOOM_Elementor_Addons The single instance of the class.
	 * @since 1.0.0
	 * @access private
	 * @static
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return WPZOOM_Elementor_Addons An instance of the class.
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
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		self::includes();

		add_action( 'init', array( $this, 'i18n' ) );
		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );

		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'plugin_css' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'plugin_css' ) );
		
		add_action( 'elementor/editor/footer', array( $this, 'plugin_scripts' ) );
		add_action( 'elementor/editor/footer', array( $this, 'insert_js_templates' ) );

		// Initialize Pro plugin promotion
		add_action( 'admin_notices', array( $this, 'pro_plugin_promotion_notice' ) );
		add_action( 'wp_ajax_wpzoom_dismiss_pro_notice', array( $this, 'dismiss_pro_notice' ) );

	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 *
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function i18n() {
		load_plugin_textdomain( 'wpzoom-elementor-addons', false, WPZOOM_EL_ADDONS_PLUGIN_DIR . '/languages' );
	}

	/**
	 * Includes files
	 * @method includes
	 *
	 * @return void
	 */
	public function includes() {

		include_once WPZOOM_EL_ADDONS_PATH . 'includes/wpzoom-elementor-controls.php';
		include_once WPZOOM_EL_ADDONS_PATH . 'includes/wpzoom-elementor-widgets.php';
		include_once WPZOOM_EL_ADDONS_PATH . 'includes/wpzoom-template-manager.php';
		include_once WPZOOM_EL_ADDONS_PATH . 'includes/wpzoom-elementor-ajax-posts-grid.php';

	}

	/**
	 * Get Editor Templates
	 *
	 * @return void
	 */
	public function insert_js_templates() {
		ob_start();
			require_once WPZOOM_EL_ADDONS_PATH . 'includes/editor-templates/templates.php';
		ob_end_flush();
	}

	/**
	 * Enqueue plugin styles.
	 */
	public function plugin_css() {	
		wp_enqueue_style( 'wpzoom-elementor-addons', WPZOOM_EL_ADDONS_URL . 'assets/css/wpzoom-elementor-addons.css', array(), WPZOOM_EL_ADDONS_VER );
		wp_enqueue_style( 'select2', WPZOOM_EL_ADDONS_URL . 'assets/vendors/select2/select2.css', array(), WPZOOM_EL_ADDONS_VER );
	}

	/**
	 * Enqueue plugin scripts.
	 */
	public function plugin_scripts() {
		wp_enqueue_script( 'select2', WPZOOM_EL_ADDONS_URL . 'assets/vendors/select2/select2.full.min.js', array( 'jquery' ), WPZOOM_EL_ADDONS_VER, true );
		wp_enqueue_script( 'wpzoom-elementor-addons', WPZOOM_EL_ADDONS_URL . 'assets/js/wpzoom-elementor-addons.js', array( 'jquery', 'wp-util', 'select2' ), WPZOOM_EL_ADDONS_VER, true );
		
		// Localize script with admin URL for Pro plugin links
		wp_localize_script( 'wpzoom-elementor-addons', 'wpzoom_admin_data', array(
			'admin_url' => admin_url(),
			'get_pro_url' => 'https://www.wpzoom.com/plugins/wpzoom-elementor-addons/'
		) );
	}

	/**
	 * On Plugins Loaded
	 *
	 * Checks if Elementor has loaded, and performs some compatibility checks.
	 * If All checks pass, inits the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function on_plugins_loaded() {
		if ( $this->is_compatible() ) {
			add_action( 'elementor/init', array( $this, 'init' ) );

			// Setup some extra stuff for specific widgets.
			require_once WPZOOM_EL_ADDONS_PATH . 'includes/widgets/featured-category/category-image.php';
			new \WPZOOMElementorWidgets\Featured_Category_Image();
		}
	}

	/**
	 * Compatibility Checks
	 *
	 * Checks if the installed version of Elementor meets the plugin's minimum requirement.
	 * Checks if the installed PHP version meets the plugin's minimum requirement.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_compatible() {
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
			return false;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
			return false;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );
			return false;
		}

		return true;
	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {
		// Add Plugin actions
		// Pro features are unlocked when WPZOOM Elementor Addons Pro plugin is active
	}


	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {


		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$plugin = 'elementor/elementor.php';
		$installed_plugins = get_plugins();
		$is_elementor_installed = isset( $installed_plugins[ $plugin ] );

		if ( $is_elementor_installed ) {
		
			$message = sprintf(
				/* translators: 1: Plugin name 2: Elementor */
				esc_html__( '"%1$s" requires "%2$s" to activated.', 'wpzoom-elementor-addons' ),
				'<strong>' . esc_html__( 'WPZOOM Elementor Addons', 'wpzoom-elementor-addons' ) . '</strong>',
				'<strong>' . esc_html__( 'Elementor', 'wpzoom-elementor-addons' ) . '</strong>'
			);

			$button_text = esc_html__( 'Activate Elementor', 'wpzoom-elementor-addons' );
			$button_link = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
		
		} else {

			$message = sprintf(
				/* translators: 1: Plugin name 2: Elementor */
				esc_html__( '"%1$s" requires "%2$s" to be installed.', 'wpzoom-elementor-addons' ),
				'<strong>' . esc_html__( 'WPZOOM Elementor Addons', 'wpzoom-elementor-addons' ) . '</strong>',
				'<strong>' . esc_html__( 'Elementor', 'wpzoom-elementor-addons' ) . '</strong>'
			);

			$button_text = esc_html__( 'Install Elementor', 'wpzoom-elementor-addons' );
			$button_link = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );

		}

		$button = sprintf(
			/* translators: 1: Button URL 2: Button text */
			'<a class="button button-primary" href="%1$s">%2$s</a>',
			esc_url( $button_link ),
			esc_html( $button_text )
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p> <p>%2$s</p></div>', $message, $button );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'wpzoom-elementor-addons' ),
			'<strong>' . esc_html__( 'Elementor Test Extension', 'wpzoom-elementor-addons' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'wpzoom-elementor-addons' ) . '</strong>',
			 self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'wpzoom-elementor-addons' ),
			'<strong>' . esc_html__( 'Elementor Test Extension', 'wpzoom-elementor-addons' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'wpzoom-elementor-addons' ) . '</strong>',
			 self::MINIMUM_PHP_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Pro plugin promotion notice
	 *
	 * Shows a notice promoting the Pro plugin when it's not active.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function pro_plugin_promotion_notice() {
		// Only show to administrators
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Don't show if Pro plugin is active
		if ( class_exists( 'WPZOOM_Elementor_Addons_Pro' ) ) {
			return;
		}

		// Don't show if WPZOOM premium theme is active (they get access anyway)
		if ( class_exists( 'WPZOOM' ) ) {
			return;
		}

		// Don't show on the Pro plugin license page 
		if ( isset( $_GET['page'] ) && $_GET['page'] === 'wpzoom-addons-pro' ) {
			return;
		}

		// Check if notice was dismissed
		if ( get_option( 'wpzoom_pro_notice_dismissed', false ) ) {
			return;
		}

		// Only show on relevant admin pages
		$screen = get_current_screen();
		$allowed_screens = [ 
			'dashboard', 
			'plugins', 
			'elementor_page_elementor-system-info',
			'toplevel_page_elementor',
			'edit-elementor_library'
		];
		
		if ( ! $screen || ! in_array( $screen->id, $allowed_screens ) ) {
			return;
		}

		?>
		<div class="notice notice-info is-dismissible" data-notice="wpzoom-pro">
			<div style="display: flex; align-items: center; padding: 10px 0;">
				<div style="margin-right: 15px; font-size: 24px;">🎬</div>
				<div>
					<h3 style="margin: 0 0 5px 0;"><?php esc_html_e( 'Video Slideshow Widget for Elementor now Available!', 'wpzoom-elementor-addons' ); ?></h3>
					<p style="margin: 0;">
						<?php esc_html_e( 'Purchase a WPZOOM Elementor Addons Pro license key to unlock the new Video Slideshow widget and access to all premium Elementor templates.', 'wpzoom-elementor-addons' ); ?>
					</p>
					<p style="margin: 10px 0 0 0;">
						<a href="https://www.wpzoom.com/plugins/wpzoom-elementor-addons/" target="_blank" class="button button-primary">
							<?php esc_html_e( 'Get Pro Plugin', 'wpzoom-elementor-addons' ); ?>
						</a>
						<a href="<?php echo esc_url( admin_url( 'options-general.php?page=wpzoom-addons-pro' ) ); ?>" class="button button-secondary" style="margin-left: 10px;">
							<?php esc_html_e( 'Enter License Key', 'wpzoom-elementor-addons' ); ?>
						</a>
					</p>
				</div>
			</div>
		</div>
		<script>
		jQuery(document).ready(function($) {
			$(document).on('click', '[data-notice="wpzoom-pro"] .notice-dismiss', function() {
				$.post(ajaxurl, {
					action: 'wpzoom_dismiss_pro_notice',
					nonce: '<?php echo wp_create_nonce( 'wpzoom_dismiss_pro_notice' ); ?>'
				});
			});
		});
		</script>
		<?php
	}

	/**
	 * Dismiss Pro notice
	 *
	 * AJAX handler for dismissing the Pro plugin promotion notice.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function dismiss_pro_notice() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'wpzoom_dismiss_pro_notice' ) ) {
			wp_die( 'Security check failed' );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Insufficient permissions' );
		}

		update_option( 'wpzoom_pro_notice_dismissed', true );
		wp_die();
	}
}