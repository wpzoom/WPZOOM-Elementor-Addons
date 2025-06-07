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
			__( 'WPZOOM Addons License', 'wpzoom-elementor-addons' ),
			'manage_options',
			'wpzoom-addons-license',
			array( $this, 'license_page' )
		);
	}

	/**
	 * License page content
	 */
	public function license_page() {
		$license_key = $this->get_license_key();
		$license_status = $this->get_license_status();
		$license_data = $this->get_license_data();
		
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'WPZOOM Addons License', 'wpzoom-elementor-addons' ); ?></h1>
			
			<?php $this->display_license_messages(); ?>
			
			<div class="wpzoom-license-container" style="max-width: 800px;">
				<div class="wpzoom-license-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 8px; margin-bottom: 30px;">
					<h2 style="margin: 0 0 10px 0; color: white;"><?php esc_html_e( 'Unlock Premium Features', 'wpzoom-elementor-addons' ); ?></h2>
					<p style="margin: 0; opacity: 0.9;"><?php esc_html_e( 'Enter your license key to unlock the Video Slideshow widget and other premium features.', 'wpzoom-elementor-addons' ); ?></p>
				</div>

				<form method="post" action="">
					<?php wp_nonce_field( 'wpzoom_license_nonce', 'wpzoom_license_nonce' ); ?>
					
					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row">
									<label for="license_key"><?php esc_html_e( 'License Key', 'wpzoom-elementor-addons' ); ?></label>
								</th>
								<td>
									<input 
										type="text" 
										id="license_key" 
										name="license_key" 
										value="<?php echo esc_attr( $license_key ); ?>" 
										class="regular-text" 
										placeholder="<?php esc_attr_e( 'Enter your license key...', 'wpzoom-elementor-addons' ); ?>"
									/>
									<p class="description">
										<?php esc_html_e( 'Enter the license key you received when purchasing WPZOOM Elementor Addons Pro.', 'wpzoom-elementor-addons' ); ?>
									</p>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'License Status', 'wpzoom-elementor-addons' ); ?></th>
								<td>
									<?php if ( $license_status === 'valid' ) : ?>
										<span style="color: #46b450; font-weight: 600;">
											✓ <?php esc_html_e( 'Active', 'wpzoom-elementor-addons' ); ?>
										</span>
										<?php if ( $license_data && isset( $license_data['expires'] ) ) : ?>
											<br>
											<small style="color: #666;">
												<?php 
												if ( $license_data['expires'] === 'lifetime' ) {
													esc_html_e( 'Lifetime license', 'wpzoom-elementor-addons' );
												} else {
													printf( 
														esc_html__( 'Expires: %s', 'wpzoom-elementor-addons' ), 
														date_i18n( get_option( 'date_format' ), strtotime( $license_data['expires'] ) )
													);
												}
												?>
											</small>
										<?php endif; ?>
									<?php elseif ( $license_status === 'expired' ) : ?>
										<span style="color: #dc3232; font-weight: 600;">
											⚠ <?php esc_html_e( 'Expired', 'wpzoom-elementor-addons' ); ?>
										</span>
									<?php elseif ( $license_status === 'invalid' ) : ?>
										<span style="color: #dc3232; font-weight: 600;">
											✗ <?php esc_html_e( 'Invalid', 'wpzoom-elementor-addons' ); ?>
										</span>
									<?php else : ?>
										<span style="color: #666;">
											<?php esc_html_e( 'Not activated', 'wpzoom-elementor-addons' ); ?>
										</span>
									<?php endif; ?>
								</td>
							</tr>
						</tbody>
					</table>

					<p class="submit">
						<?php if ( $license_status === 'valid' ) : ?>
							<input type="submit" name="deactivate_license" class="button-secondary" value="<?php esc_attr_e( 'Deactivate License', 'wpzoom-elementor-addons' ); ?>" />
						<?php else : ?>
							<input type="submit" name="activate_license" class="button-primary" value="<?php esc_attr_e( 'Activate License', 'wpzoom-elementor-addons' ); ?>" />
						<?php endif; ?>
						
						<input type="submit" name="check_license" class="button-secondary" value="<?php esc_attr_e( 'Check License Status', 'wpzoom-elementor-addons' ); ?>" style="margin-left: 10px;" />
						
						<?php if ( ! empty( $license_key ) ) : ?>
							<input type="submit" name="clear_license" class="button-secondary" value="<?php esc_attr_e( 'Clear License', 'wpzoom-elementor-addons' ); ?>" style="margin-left: 10px;" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to clear the license key?', 'wpzoom-elementor-addons' ); ?>');" />
						<?php endif; ?>
					</p>
				</form>

				<div class="wpzoom-license-info" style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-top: 30px;">
					<?php if ( $license_status === 'expired' ) : ?>
						<h3><?php esc_html_e( 'License Expired', 'wpzoom-elementor-addons' ); ?></h3>
						<p style="color: #d54e21; margin-bottom: 15px;">
							<strong><?php esc_html_e( 'Your license has expired! Please renew it to unlock Premium features.', 'wpzoom-elementor-addons' ); ?></strong>
						</p>
						<p><?php esc_html_e( 'You can continue using the free version, however, you\'ll need an active license to unlock the Premium features.', 'wpzoom-elementor-addons' ); ?></p>
						<a href="https://www.wpzoom.com/account/licenses/" target="_blank" class="button button-primary">
							<?php esc_html_e( 'Renew License', 'wpzoom-elementor-addons' ); ?>
						</a>
					<?php else : ?>
						<h3><?php esc_html_e( 'Need a License?', 'wpzoom-elementor-addons' ); ?></h3>
						<p><?php esc_html_e( 'Purchase WPZOOM Elementor Addons Pro to get access to premium widgets including the Video Slideshow widget.', 'wpzoom-elementor-addons' ); ?></p>
						<a href="https://www.wpzoom.com/plugins/elementor-addons-pro/" target="_blank" class="button button-primary">
							<?php esc_html_e( 'Get License Key', 'wpzoom-elementor-addons' ); ?>
						</a>
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
					<h3 style="margin: 0 0 5px 0;"><?php esc_html_e( 'Unlock Video Slideshow Widget', 'wpzoom-elementor-addons' ); ?></h3>
					<p style="margin: 0;">
						<?php esc_html_e( 'Enter your WPZOOM Elementor Addons Pro license key to unlock the Video Slideshow widget and other premium features.', 'wpzoom-elementor-addons' ); ?>
					</p>
					<p style="margin: 10px 0 0 0;">
						<a href="<?php echo esc_url( admin_url( 'options-general.php?page=wpzoom-addons-license' ) ); ?>" class="button button-primary">
							<?php esc_html_e( 'Enter License Key', 'wpzoom-elementor-addons' ); ?>
						</a>
						<a href="https://www.wpzoom.com/plugins/elementor-addons-pro/" target="_blank" class="button button-secondary" style="margin-left: 10px;">
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