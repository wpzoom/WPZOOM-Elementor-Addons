<?php
/**
 * WPZOOM Elementor Addons License Management
 *
 * @package WPZOOM_Elementor_Addons
 */

namespace WPZOOM_Elementor_Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * License Management Class
 */
class License_Manager {

	/**
	 * License option name
	 */
	const LICENSE_KEY_OPTION = 'wpzoom_elementor_addons_license_key';
	const LICENSE_STATUS_OPTION = 'wpzoom_elementor_addons_license_status';
	const LICENSE_DATA_OPTION = 'wpzoom_elementor_addons_license_data';

	/**
	 * EDD Store URL
	 */
	const STORE_URL = 'https://www.wpzoom.com';

	/**
	 * Product ID for license validation
	 */
	const PRODUCT_ID = '815383';

	/**
	 * Instance
	 */
	private static $instance = null;

	/**
	 * Get instance
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_license_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'handle_license_actions' ) );
		add_action( 'admin_notices', array( $this, 'license_activation_notice' ) );
		add_action( 'wp_ajax_wpzoom_dismiss_license_notice', array( $this, 'dismiss_license_notice' ) );
		add_action( 'init', array( $this, 'schedule_license_check' ) );
		add_action( 'wpzoom_daily_license_check', array( $this, 'daily_license_check' ) );
		
		// Clean up scheduled events on plugin deactivation
		register_deactivation_hook( WPZOOM_EL_ADDONS__FILE__, array( $this, 'cleanup_scheduled_events' ) );
	}

	/**
	 * Add license page to admin menu
	 */
	public function add_license_page() {
		add_submenu_page(
			'options-general.php',
			__( 'WPZOOM Addons License', 'wpzoom-elementor-addons' ),
			__( 'Elementor Addons by WPZOOM', 'wpzoom-elementor-addons' ),
			'manage_options',
			'wpzoom-addons-license',
			array( $this, 'license_page' )
		);
	}

	/**
	 * Admin scripts and styles
	 */
	public function admin_enqueue_scripts( $hook ) {
		// Only load on our license page
		if ( 'settings_page_wpzoom-addons-license' !== $hook ) {
			return;
		}

		// Enqueue license page styles
		wp_enqueue_style(
			'wpzoom-elementor-addons-license',
			WPZOOM_EL_ADDONS_URL . 'assets/css/license-page.css',
			array(),
			WPZOOM_EL_ADDONS_VER
		);
	}

	/**
	 * License page content
	 */
	public function license_page() {
		$license_key = $this->get_license_key();
		$license_status = $this->get_license_status();
		$license_data = $this->get_license_data();
		$is_license_active = $this->is_license_active();
		
		?>
		<div class="wrap">
			<div class="wpzoom-elementor-addons-page-header">
				<div class="wpzoom-header-content">
					<h1 class="wpzoom-page-title">
						<span class="wpzoom-logo"><svg width="30" height="30" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 256C0 114.615 114.615 0 256 0V0C397.385 0 512 114.615 512 256V256C512 397.385 397.385 512 256 512V512C114.615 512 0 397.385 0 256V256Z" fill="#2858D1"/><path d="M172.55 385.45L109.55 188.05H85.05V152.7H183.05V188.05H152.6L190.05 309.15L247.1 175.1H268.1L325.15 309.15L360.15 188.05H328.65V152.7H426.65V188.05H402.15L340.2 385.45H318.15L256.55 243.7L194.6 385.45H172.55Z" fill="white"/></svg></span>
						<?php esc_html_e( 'WPZOOM Elementor Addons', 'wpzoom-elementor-addons' ); ?>
						<span class="wpzoom-pro-badge-header"><?php esc_html_e( 'PRO', 'wpzoom-elementor-addons' ); ?></span>
					</h1>
				</div>
			</div>

			<div class="wpzoom-elementor-addons-wrap wpzoom-elementor-addons-license">
				<div class="inner-wrap fit-max-content">
					<h2 class="section-title">
						<?php esc_html_e( 'Activate Your License Key', 'wpzoom-elementor-addons' ); ?>
					</h2>

					<p class="section-description">
						<?php
						printf(
							// translators: %1$s = WPZOOM Members Area URL.
							__( 'Enter your license key to unlock premium features. You can find your license in <a href="%1$s" target="_blank">WPZOOM Members Area &rarr; Licenses</a>.', 'wpzoom-elementor-addons' ), // phpcs:ignore WordPress.Security.EscapeOutput
							esc_url( 'https://www.wpzoom.com/account/licenses/' )
						);
						?>
					</p>

					<?php $this->display_license_messages(); ?>

					<form id="wpzoom-elementor-addons-license-form" method="POST" autocomplete="off">
						<?php wp_nonce_field( 'wpzoom_license_nonce', 'wpzoom_license_nonce' ); ?>

						<p class="wide-text-field">
							<label>
								<strong><?php esc_html_e( 'License Key', 'wpzoom-elementor-addons' ); ?></strong>
								<input
									type="text"
									name="license_key"
									id="wpzoom-elementor-addons-license-key"
									value="<?php echo esc_attr( $license_key ); ?>"
									minlength="32"
									maxlength="32"
									placeholder="<?php esc_attr_e( 'Enter your license key...', 'wpzoom-elementor-addons' ); ?>"
									class="regular-text"
									<?php
									if ( $is_license_active ) {
										echo sprintf(
											'title="%s" readonly',
											esc_html__( 'You must deactivate the current license before you can enter a new one.', 'wpzoom-elementor-addons' )
										);
									}
									?>
								/>
								<em class="help-text"><?php esc_html_e( 'Enter your license key for this plugin to unlock premium features.', 'wpzoom-elementor-addons' ); ?></em>
							</label>
						</p>

						<?php if ( ! empty( $license_key ) ) : ?>
							<div class="license-status-display">
								<?php if ( $license_status === 'valid' ) : ?>
									<div class="license-status-active">
										<span class="status-icon">✓</span>
										<div class="status-content">
											<strong><?php esc_html_e( 'License key is active!', 'wpzoom-elementor-addons' ); ?></strong>
											<?php if ( $license_data && isset( $license_data['expires'] ) ) : ?>
												<small>
													<?php
													if ( $license_data['expires'] === 'lifetime' ) {
														esc_html_e( '— Lifetime License', 'wpzoom-elementor-addons' );
													} else {
														printf(
															esc_html__( '— Expires %s', 'wpzoom-elementor-addons' ),
															date_i18n( get_option( 'date_format' ), strtotime( $license_data['expires'] ) )
														);
													}
													?>
												</small>
											<?php endif; ?>
										</div>
									</div>
								<?php else : ?>
									<div class="license-status-inactive">
										<span class="status-icon">⚠</span>
										<div class="status-content">
											<strong>
												<?php 
												switch ( $license_status ) {
													case 'expired':
														esc_html_e( 'License key has expired!', 'wpzoom-elementor-addons' );
														break;
													case 'invalid':
														esc_html_e( 'License key does not match!', 'wpzoom-elementor-addons' );
														break;
													case 'inactive':
													case 'site_inactive':
														esc_html_e( 'License is inactive. Click Activate to enable it.', 'wpzoom-elementor-addons' );
														break;
													case 'disabled':
														esc_html_e( 'License key is disabled!', 'wpzoom-elementor-addons' );
														break;
													default:
														esc_html_e( 'License is not active.', 'wpzoom-elementor-addons' );
														break;
												}
												?>
											</strong>
										</div>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<p class="submit-button">
							<?php if ( $is_license_active ) : ?>
								<input
									type="submit"
									name="deactivate_license"
									value="<?php esc_attr_e( 'Deactivate', 'wpzoom-elementor-addons' ); ?>"
									class="button button-secondary button-negative"
								/>
							<?php else : ?>
								<input
									type="submit"
									name="activate_license"
									value="<?php esc_attr_e( 'Activate', 'wpzoom-elementor-addons' ); ?>"
									class="button button-primary"
								/>
							<?php endif; ?>
							&ensp;
							<input
								type="submit"
								name="check_license"
								value="<?php esc_attr_e( 'Check Status', 'wpzoom-elementor-addons' ); ?>"
								class="button button-secondary"
							/>
							<?php if ( ! empty( $license_key ) ) : ?>
								&ensp;
								<input
									type="submit"
									name="clear_license"
									value="<?php esc_attr_e( 'Clear', 'wpzoom-elementor-addons' ); ?>"
									class="button button-secondary"
									onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to clear the license key?', 'wpzoom-elementor-addons' ); ?>');"
								/>
							<?php endif; ?>
						</p>
					</form>

					<?php if ( $license_status === 'expired' ) : ?>
						<div class="wpzoom-license-help-section expired">
							<h3><?php esc_html_e( 'License Expired', 'wpzoom-elementor-addons' ); ?></h3>
							<p><?php esc_html_e( 'Your license has expired. Please renew it to continue receiving updates and support.', 'wpzoom-elementor-addons' ); ?></p>
							<a href="https://www.wpzoom.com/account/licenses/" target="_blank" class="button button-primary">
								<?php esc_html_e( 'Renew License', 'wpzoom-elementor-addons' ); ?>
							</a>
						</div>
					<?php elseif ( ! $is_license_active && empty( $license_key ) ) : ?>
						<div class="wpzoom-license-help-section">
							<h3><?php esc_html_e( 'Need a License?', 'wpzoom-elementor-addons' ); ?></h3>
							<p><?php esc_html_e( 'Purchase WPZOOM Elementor Addons Pro to get access to premium widgets, templates, and features.', 'wpzoom-elementor-addons' ); ?></p>
							<a href="https://www.wpzoom.com/plugins/wpzoom-elementor-addons/" target="_blank" class="button button-primary">
								<?php esc_html_e( 'Get License Key', 'wpzoom-elementor-addons' ); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Handle license actions
	 */
	public function handle_license_actions() {
		if ( ! isset( $_POST['wpzoom_license_nonce'] ) || ! wp_verify_nonce( $_POST['wpzoom_license_nonce'], 'wpzoom_license_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$license_key = sanitize_text_field( $_POST['license_key'] ?? '' );

		if ( isset( $_POST['activate_license'] ) ) {
			$this->activate_license( $license_key );
		} elseif ( isset( $_POST['deactivate_license'] ) ) {
			$this->deactivate_license();
		} elseif ( isset( $_POST['check_license'] ) ) {
			$this->check_license( $license_key );
		} elseif ( isset( $_POST['clear_license'] ) ) {
			$this->clear_license();
		}
	}

	/**
	 * Activate license
	 */
	private function activate_license( $license_key ) {
		if ( empty( $license_key ) ) {
			$this->set_license_message( __( 'Please enter a license key.', 'wpzoom-elementor-addons' ) );
			return;
		}

		// Always save the license key, even if activation fails (so user can edit/correct it)
		update_option( self::LICENSE_KEY_OPTION, $license_key );

		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license_key,
			'item_id'    => self::PRODUCT_ID,
			'url'        => home_url()
		);

		$response = wp_remote_post( self::STORE_URL, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params
		) );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$this->set_license_message( __( 'An error occurred, please try again.', 'wpzoom-elementor-addons' ) );
			return;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( false === $license_data['success'] ) {
			switch( $license_data['error'] ) {
				case 'expired' :
					// For expired licenses, still save the status but with a different message
					update_option( self::LICENSE_STATUS_OPTION, 'expired' );
					update_option( self::LICENSE_DATA_OPTION, $license_data );
					
					// Cache the expired status for 24 hours
					set_transient( 'wpzoom_elementor_addons_license_status_cache', 'expired', 24 * HOUR_IN_SECONDS );
					
					// Trigger action to clear template cache
					do_action( 'wpzoom_license_status_changed', 'expired' );
					
					$message = __( 'Your license has expired! Please renew it to receive Premium features.', 'wpzoom-elementor-addons' );
					$this->set_license_message( $message, 'success' );
					return; // Early return to avoid showing error message
				case 'disabled' :
				case 'revoked' :
					$message = __( 'Your license key has been disabled.', 'wpzoom-elementor-addons' );
					break;
				case 'missing' :
					$message = __( 'Invalid license key.', 'wpzoom-elementor-addons' );
					break;
				case 'invalid' :
				case 'site_inactive' :
					$message = __( 'Your license is not active for this URL.', 'wpzoom-elementor-addons' );
					break;
				case 'item_name_mismatch' :
					$message = __( 'This appears to be an invalid license key.', 'wpzoom-elementor-addons' );
					break;
				case 'no_activations_left':
					$message = __( 'Your license key has reached its activation limit.', 'wpzoom-elementor-addons' );
					break;
				default :
					$message = __( 'An error occurred, please try again.', 'wpzoom-elementor-addons' );
					break;
			}
			$this->set_license_message( $message );
		} else {
			update_option( self::LICENSE_STATUS_OPTION, $license_data['license'] );
			update_option( self::LICENSE_DATA_OPTION, $license_data );
			
			// Cache the successful activation for 24 hours
			set_transient( 'wpzoom_elementor_addons_license_status_cache', $license_data['license'], 24 * HOUR_IN_SECONDS );
			
			// Trigger action to clear template cache
			do_action( 'wpzoom_license_status_changed', $license_data['license'] );
			
			$this->set_license_message( __( 'License activated successfully!', 'wpzoom-elementor-addons' ), 'success' );
		}
	}

	/**
	 * Deactivate license
	 */
	private function deactivate_license() {
		$license_key = $this->get_license_key();

		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license_key,
			'item_id'    => self::PRODUCT_ID,
			'url'        => home_url()
		);

		$response = wp_remote_post( self::STORE_URL, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params
		) );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$this->set_license_message( __( 'An error occurred, please try again.', 'wpzoom-elementor-addons' ) );
			return;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ), true );

		if( $license_data['license'] == 'deactivated' ) {
			delete_option( self::LICENSE_KEY_OPTION );
			delete_option( self::LICENSE_STATUS_OPTION );
			delete_option( self::LICENSE_DATA_OPTION );
			
			// Clear the license status cache
			delete_transient( 'wpzoom_elementor_addons_license_status_cache' );
			
			// Trigger action to clear template cache
			do_action( 'wpzoom_license_status_changed', 'deactivated' );
			
			$this->set_license_message( __( 'License deactivated successfully!', 'wpzoom-elementor-addons' ), 'success' );
		}
	}

	/**
	 * Clear license
	 */
	private function clear_license() {
		delete_option( self::LICENSE_KEY_OPTION );
		delete_option( self::LICENSE_STATUS_OPTION );
		delete_option( self::LICENSE_DATA_OPTION );
		
		// Clear the license status cache
		delete_transient( 'wpzoom_elementor_addons_license_status_cache' );
		
		// Trigger action to clear template cache
		do_action( 'wpzoom_license_status_changed', 'cleared' );
		
		$this->set_license_message( __( 'License cleared successfully!', 'wpzoom-elementor-addons' ), 'success' );
	}

	/**
	 * Check license status
	 */
	private function check_license( $license_key = '' ) {
		if ( empty( $license_key ) ) {
			$license_key = $this->get_license_key();
		}

		if ( empty( $license_key ) ) {
			$this->set_license_message( __( 'Please enter a license key.', 'wpzoom-elementor-addons' ) );
			return;
		}

		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $license_key,
			'item_id'    => self::PRODUCT_ID,
			'url'        => home_url()
		);

		$response = wp_remote_post( self::STORE_URL, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params
		) );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$this->set_license_message( __( 'An error occurred, please try again.', 'wpzoom-elementor-addons' ) );
			return;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ), true );

		update_option( self::LICENSE_STATUS_OPTION, $license_data['license'] );
		update_option( self::LICENSE_DATA_OPTION, $license_data );

		// Update the cache with fresh data
		set_transient( 'wpzoom_elementor_addons_license_status_cache', $license_data['license'], 24 * HOUR_IN_SECONDS );

		// Trigger action to clear template cache
		do_action( 'wpzoom_license_status_changed', $license_data['license'] );

		if ( $license_data['license'] === 'valid' ) {
			$this->set_license_message( __( 'License is valid and active!', 'wpzoom-elementor-addons' ), 'success' );
		} elseif ( $license_data['license'] === 'expired' ) {
			$this->set_license_message( __( 'License has expired! Please renew it to unlock Premium features.', 'wpzoom-elementor-addons' ), 'success' );
		} else {
			$this->set_license_message( __( 'License is not valid.', 'wpzoom-elementor-addons' ) );
		}
	}

	/**
	 * Get license key
	 */
	public function get_license_key() {
		return get_option( self::LICENSE_KEY_OPTION, '' );
	}

	/**
	 * Get license status
	 */
	public function get_license_status() {
		return get_option( self::LICENSE_STATUS_OPTION, '' );
	}

	/**
	 * Get license data
	 */
	public function get_license_data() {
		return get_option( self::LICENSE_DATA_OPTION, array() );
	}

	/**
	 * Check if license is valid
	 */
	public function is_license_valid() {
		// First check if we have a cached status that's still valid
		$cached_status = get_transient( 'wpzoom_elementor_addons_license_status_cache' );
		if ( $cached_status !== false ) {
			// Allow both 'valid' and 'expired' licenses to access premium features
			return in_array( $cached_status, [ 'valid', 'expired' ] );
		}

		// Fall back to stored status if no cache
		$stored_status = $this->get_license_status();
		// Allow both 'valid' and 'expired' licenses to access premium features
		return in_array( $stored_status, [ 'valid', 'expired' ] );
	}

	/**
	 * Check if license is valid and active (for imports and other strict checks)
	 */
	public function is_license_active() {
		// First check if we have a cached status that's still valid
		$cached_status = get_transient( 'wpzoom_elementor_addons_license_status_cache' );
		if ( $cached_status !== false ) {
			// Only allow 'valid' licenses for imports
			return $cached_status === 'valid';
		}

		// Fall back to stored status if no cache
		$stored_status = $this->get_license_status();
		// Only allow 'valid' licenses for imports
		return $stored_status === 'valid';
	}

	/**
	 * Get detailed license status for user messaging
	 */
	public function get_license_restriction_message() {
		$license_status = $this->get_license_status();
		$has_premium_theme = class_exists( 'WPZOOM' );
		
		if ( $has_premium_theme ) {
			return ''; // No restriction if premium theme is active
		}
		
		switch ( $license_status ) {
			case 'valid':
				return ''; // No restriction
				
			case 'expired':
				return esc_html__( 'Your license has expired. Please renew your license to import PRO templates.', 'wpzoom-elementor-addons' );
				
			case 'inactive':
			case 'site_inactive':
				return esc_html__( 'Your license is inactive. Please activate your license to import PRO templates.', 'wpzoom-elementor-addons' );
				
			case 'invalid':
				return esc_html__( 'Your license key is invalid. Please enter a valid license key to import PRO templates.', 'wpzoom-elementor-addons' );
				
			case 'disabled':
				return esc_html__( 'Your license has been disabled. Please contact support for assistance.', 'wpzoom-elementor-addons' );
				
			default:
				return esc_html__( 'This template requires WPZOOM Elementor Addons Pro license. Please activate your license key to import PRO templates.', 'wpzoom-elementor-addons' );
		}
	}

	/**
	 * Check if license is expired specifically
	 */
	public function is_license_expired() {
		// First check if we have a cached status
		$cached_status = get_transient( 'wpzoom_elementor_addons_license_status_cache' );
		if ( $cached_status !== false ) {
			return $cached_status === 'expired';
		}

		// Fall back to stored status if no cache
		return $this->get_license_status() === 'expired';
	}

	/**
	 * Check if a premium WPZOOM theme is active
	 */
	private function has_premium_theme() {
		return class_exists( 'WPZOOM' );
	}

	/**
	 * Schedule daily license check
	 */
	public function schedule_license_check() {
		if ( ! wp_next_scheduled( 'wpzoom_daily_license_check' ) ) {
			wp_schedule_event( time(), 'daily', 'wpzoom_daily_license_check' );
		}
	}

	/**
	 * Daily license check via cron
	 */
	public function daily_license_check() {
		$license_key = $this->get_license_key();
		
		// Only check if we have a license key
		if ( empty( $license_key ) ) {
			return;
		}

		// Perform the license check and cache the result
		$this->check_license_and_cache( $license_key );
	}

	/**
	 * Check license and cache the result
	 */
	private function check_license_and_cache( $license_key ) {
		if ( empty( $license_key ) ) {
			return false;
		}

		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $license_key,
			'item_id'    => self::PRODUCT_ID,
			'url'        => home_url()
		);

		$response = wp_remote_post( self::STORE_URL, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params
		) );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			// On error, extend the current cache for a shorter period (6 hours)
			$current_status = $this->get_license_status();
			set_transient( 'wpzoom_elementor_addons_license_status_cache', $current_status, 6 * HOUR_IN_SECONDS );
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $license_data['license'] ) ) {
			// Update stored status
			update_option( self::LICENSE_STATUS_OPTION, $license_data['license'] );
			update_option( self::LICENSE_DATA_OPTION, $license_data );
			
			// Cache the status for 24 hours
			set_transient( 'wpzoom_elementor_addons_license_status_cache', $license_data['license'], 24 * HOUR_IN_SECONDS );
			
			// Trigger action to clear template cache
			do_action( 'wpzoom_license_status_changed', $license_data['license'] );
			
			return $license_data['license'] === 'valid';
		}

		return false;
	}

	/**
	 * Display license messages
	 */
	private function display_license_messages() {
		$message = get_transient( 'wpzoom_license_message' );
		if ( $message ) {
			$type = $message['type'] === 'success' ? 'notice-success' : 'notice-error';
			echo '<div class="notice ' . esc_attr( $type ) . ' is-dismissible"><p>' . esc_html( $message['text'] ) . '</p></div>';
			delete_transient( 'wpzoom_license_message' );
		}
	}

	/**
	 * Set license message
	 */
	private function set_license_message( $text, $type = 'error' ) {
		set_transient( 'wpzoom_license_message', array(
			'text' => $text,
			'type' => $type
		), 30 );
	}

	/**
	 * Show license activation notice
	 */
	public function license_activation_notice() {
		// Only show on admin pages and if license is not valid and no premium theme is active
		if ( ! current_user_can( 'manage_options' ) || $this->is_license_valid() || $this->has_premium_theme() ) {
			return;
		}

		// Don't show on the license page itself
		if ( isset( $_GET['page'] ) && $_GET['page'] === 'wpzoom-addons-license' ) {
			return;
		}

		// Check if notice was dismissed
		if ( get_option( 'wpzoom_license_notice_dismissed', false ) ) {
			return;
		}

		?>
		<div class="notice notice-info is-dismissible" data-notice="wpzoom-license">
			<div style="display: flex; align-items: center; padding: 10px 0;">
				<div style="margin-right: 15px; font-size: 24px;">🎬</div>
				<div>
					<h3 style="margin: 0 0 5px 0;"><?php esc_html_e( 'Video Slideshow Widget for Elementor now Available!', 'wpzoom-elementor-addons' ); ?></h3>
					<p style="margin: 0;">
						<?php esc_html_e( 'Purchase a WPZOOM Elementor Addons Pro license key to unlock the new Video Slideshow widget and other premium features.', 'wpzoom-elementor-addons' ); ?>
					</p>
					<p style="margin: 10px 0 0 0;">
						<a href="<?php echo esc_url( admin_url( 'options-general.php?page=wpzoom-addons-license' ) ); ?>" class="button button-primary">
							<?php esc_html_e( 'Enter License Key', 'wpzoom-elementor-addons' ); ?>
						</a>
						<a href="https://www.wpzoom.com/plugins/wpzoom-elementor-addons/" target="_blank" class="button button-secondary" style="margin-left: 10px;">
							<?php esc_html_e( 'Get License Key', 'wpzoom-elementor-addons' ); ?>
						</a>
					</p>
				</div>
			</div>
		</div>
		<script>
		jQuery(document).ready(function($) {
			$(document).on('click', '[data-notice="wpzoom-license"] .notice-dismiss', function() {
				$.post(ajaxurl, {
					action: 'wpzoom_dismiss_license_notice',
					nonce: '<?php echo wp_create_nonce( 'wpzoom_dismiss_notice' ); ?>'
				});
			});
		});
		</script>
		<?php
	}

	/**
	 * Dismiss license notice
	 */
	public function dismiss_license_notice() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'wpzoom_dismiss_notice' ) ) {
			wp_die( 'Security check failed' );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Insufficient permissions' );
		}

		update_option( 'wpzoom_license_notice_dismissed', true );
		wp_die();
	}

	/**
	 * Clean up scheduled events on plugin deactivation
	 */
	public function cleanup_scheduled_events() {
		wp_clear_scheduled_hook( 'wpzoom_daily_license_check' );
		delete_transient( 'wpzoom_elementor_addons_license_status_cache' );
	}
} 