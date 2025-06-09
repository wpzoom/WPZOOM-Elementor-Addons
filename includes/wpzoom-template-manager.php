<?php

//Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) 
	exit;

if ( !class_exists( 'WPZOOM_Elementor_Library_Manager' ) ) {

	class WPZOOM_Elementor_Library_Manager {


		/**
		 * Creates a single Instance of self
		 *
		 * @var Static data - Define menu main menu name
		 * @since 1.0.0
		 */
		private static $_instance = null;


		/**
		 * Settings plugin details
		 *
		 * @var Static data - Define all important magic strings
		 * @since 1.0.0
		 */
		static $library_source = null;

		/**
		 * Define All Actions
		 *
		 * @var Static data - Define all actions
		 * @since 1.0.0
		 */
		static $element_pro_actions = null;


		/**
		 * Creates and returns the main object for this plugin
		 *
		 *
		 * @since  1.0.0
		 * @return WPZOOM_Elementor_Library_Manager
		 */
		static public function init() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;

		}

		/**
		 * Main Constructor that sets up all static data associated with this plugin.
		 *
		 *
		 * @since  1.0.0
		 *
		 */
		private function __construct() {

			//Setup static library_source
			self::$library_source = 'https://api.wpzoom.com/elementor/templates/';

			add_action( 'wp_ajax_get_wpzoom_templates_library_view', array( $this, 'get_wpzoom_templates_library_view' ) );
			add_action( 'wp_ajax_get_wpzoom_preview', array( $this, 'ajax_get_wpzoom_preview' ) );
			add_action( 'wp_ajax_get_filter_options', array( $this, 'get_template_filter_options_values' ) );
			
			// Clear template cache when license status changes
			add_action( 'wpzoom_license_status_changed', array( $this, 'clear_template_cache' ) );

			/* Set initial version to the and call update on first use */
			if( get_option( 'wpz_current_version' ) == false ) {
				update_option( 'wpz_current_version', '0.0.0' );
			}

		}

		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wpzoom-elementor-addons' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wpzoom-elementor-addons' ), '1.0.0' );
		}

		/**
		 * Get templates from the json library
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function get_wpzoom_templates_library_view() {
			
			$template_list = array();
			$thumb_url = '';
			echo '<script> var WPZ_Index = []; </script>';
			
			// Check license and premium theme status
			$license_manager = \WPZOOM_Elementor_Addons\License_Manager::instance();
			$has_premium_access = $license_manager->is_license_active() || class_exists( 'WPZOOM' );
			$license_status = $license_manager->get_license_status();

			// Define which themes are free for everyone
			$free_themes = array( 'Foodica', 'Inspiro Lite' );

			//Get libray json from source
			$response = wp_remote_get( self::$library_source, array( 'timeout' => 60 ) );

			//if( !is_wp_error( $response ) ) {
			//	$info_data = wp_remote_retrieve_body( $response );
			//	$template_list = json_decode( $info_data, true );
			//}
			//else {
				$local_file = WPZOOM_EL_ADDONS_PATH . '/includes/data/json/info.json';
				if( self::init()->get_filesystem()->exists( $local_file ) ) {
					$data = self::init()->get_filesystem()->get_contents( $local_file );
					$template_list = json_decode( $data, true );
				}
				$thumb_url = 'https://wpzoom.s3.us-east-1.amazonaws.com/elementor/templates/assets/thumbs/';
			//}

			echo '<div class="wpzoom-main-tiled-view">';
			if( count( $template_list ) != 0 ) {
				
				for( $i = 0; $i < count( $template_list ); $i++ ) {
					$slug = strtolower( str_replace( ' ', '-', $template_list[$i]['id'] ) );
					$theme = $template_list[$i]['theme'];
					$is_theme_free = in_array( $theme, $free_themes );
					$is_restricted = !$has_premium_access && !$is_theme_free;
					
					// Get appropriate button data based on license status
					$button_data = $this->get_license_button_data( $license_status );

					if( isset( $template_list[$i]['separator'] ) ) {
						$separator_title = $template_list[$i]['separator'];
						if ( !$is_theme_free ) {
							$separator_title .= ' <span class="wpzoom-pro-badge" style="color: #fff; font-weight: 600; font-size: 12px;">PRO</span>';
						}
						echo '<h2 class="wpzoom-templates-library-template-category" data-theme="'. esc_attr( strtolower( str_replace( ' ', '-', $template_list[$i]['theme'] ) ) ) .'">' . wp_kses_post( $separator_title ) . '</h2>';
					}
					?>
					<div 
						class="wpzoom-templates-library-template wpzoom-item <?php echo $is_restricted ? 'wpzoom-template-pro-only' : ''; ?>"
						data-theme="<?php echo esc_attr( strtolower( str_replace( ' ', '-', $template_list[$i]['theme'] ) ) ) ?>" 
						data-category="<?php echo esc_attr( strtolower( str_replace( ' ', '-', $template_list[$i]['category'] ) ) ) ?>"
						>
						<div class="wpzoom-template-title">
							<?php echo esc_html( $template_list[$i]['name'] ); ?>

						</div>
						<div 
							class="wpzoom-template-thumb wpzoom-index-<?php echo esc_attr( $i ); ?> <?php echo $is_restricted ? 'wpzoom-template-thumb-locked' : ''; ?>"
							data-index="<?php echo esc_attr( $i ); ?>" 
							data-template="<?php echo esc_attr( wp_json_encode( $template_list[$i] ) ); ?>"
							style="background-image:url(<?php echo esc_url( $thumb_url . $template_list[$i]['thumbnail'] ); ?>-thumb.png);"
						>
							<?php if ( $is_restricted ) : ?>
								<div class="wpzoom-template-overlay">
									<div class="wpzoom-template-lock-icon">🔒</div>
									<div class="wpzoom-template-pro-text"><?php esc_html_e( 'PRO Only', 'wpzoom-elementor-addons' ); ?></div>
								</div>
							<?php endif; ?>
						</div>
						<div class="wpzoom-action-bar">
							<div class="wpzoom-grow"> </div>
							<?php if ( $is_restricted ) : ?>
								<a href="<?php echo esc_url( $button_data['url'] ); ?>" target="_blank" class="wpzoom-btn-template-upgrade wpzoom-btn-license-<?php echo esc_attr( $license_status ); ?>" title="<?php echo esc_attr( $button_data['text'] ); ?>">
									<?php echo esc_html( $button_data['text'] ); ?>
								</a>
							<?php else : ?>
								<div class="wpzoom-btn-template-insert" data-version="WPZ__version-<?php echo esc_attr( $i ); ?>" data-template-name="<?php echo esc_attr( $slug ); ?>"><?php esc_html_e( 'Insert Template', 'wpzoom-elementor-addons' ); ?></div>
							<?php endif; ?>
						</div>
					</div>
				<?php
				}  /* Thumbnail Loop */
			} else {
				echo '<div class="wpzoom-no-results"> <i class="fa fa-frown-o"></i> ' . esc_html__( 'No Templates Found!', 'wpzoom-elementor-addons' ) . ' </div>';
			}
			
			echo '</div>';	
			
			wp_die();
		
		}

		/**
		 * Get templates themes
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function get_template_filter_options_values( $data ) {

			$themesList = $templates = array();

			//Get libray json from source
			$response = wp_remote_get( self::$library_source, array( 'timeout' => 60 ) );

			if( !is_wp_error( $response ) ) {
				$data = wp_remote_retrieve_body( $response );
				$templates = json_decode( $data, true );
			}
			else {
				$localJson = WPZOOM_EL_ADDONS_PATH . '/includes/data/json/info.json';
				if( self::init()->get_filesystem()->exists( $localJson ) ) {
					$data = self::init()->get_filesystem()->get_contents( $localJson );
					$templates = json_decode( $data, true );
				}
			}
			if( count( $templates ) != 0 ) {
				foreach( $templates as $key => $template ) {
					$themesList[] = strtolower( str_replace(' ', '-', $template['theme'] ) );
				}
			}
			$themesList = array_unique( $themesList );

			echo json_encode( $themesList );
			
			wp_die();

		}


		/**
		 * Get  ajax preview template
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function ajax_get_wpzoom_preview() {
			$this->get_preview_template( $_POST['data'] );
			wp_die();
		}

		/**
		 * Print the preview window and make callable through ajax
		 *
		 * @return void
		 */
		private function get_preview_template( $data ) {

			if ( wp_http_validate_url( $data['thumbnail'] ) ) {
				$thumb_url = $data['thumbnail'];
			}
			else {
				$thumb_url = 'https://wpzoom.s3.us-east-1.amazonaws.com/elementor/templates/assets/thumbs/' . $data['thumbnail'];
			}

			// Check if this template is restricted
			$license_manager = \WPZOOM_Elementor_Addons\License_Manager::instance();
			$has_premium_access = $license_manager->is_license_active() || class_exists( 'WPZOOM' );
			$license_status = $license_manager->get_license_status();
			$free_themes = array( 'Foodica', 'Inspiro Lite' );
			$theme = $data['theme'];
			$is_theme_free = in_array( $theme, $free_themes );
			$is_restricted = !$has_premium_access && !$is_theme_free;
			
			// Get appropriate button data and messages based on license status
			$button_data = $this->get_license_button_data( $license_status );
			$preview_message = $this->get_preview_message( $license_status );

			?>
			<div id="wpzoom-elementor-template-library-preview">
				<?php if ( $is_restricted ) : ?>
					<div class="wpzoom-preview-pro-notice" style="background: #222; color: white; padding: 15px; margin-bottom: 20px; border-radius: 8px; text-align: center;">
						<div style="font-size: 18px; font-weight: 500; margin-bottom: 5px;"><?php esc_html_e( 'Premium Template Preview', 'wpzoom-elementor-addons' ); ?></div>
						<p style="margin: 0; opacity: 0.9; font-size: 14px;">
							<?php echo esc_html( $preview_message ); ?>
						</p>
						<a href="<?php echo esc_url( $button_data['url'] ); ?>" target="_blank" style="display: inline-block; background: rgba(255,255,255,0.2); color: white; padding: 8px 16px; text-decoration: none; border-radius: 20px; font-size: 13px; margin-top: 10px; transition: background 0.3s ease;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'" title="<?php echo esc_attr( $button_data['text'] ); ?>">
							<?php echo esc_html( $button_data['text'] ); ?>
						</a>
					</div>
				<?php endif; ?>
				<img src="<?php echo esc_url( $thumb_url ); ?>-full.png" alt="<?php echo esc_attr( $data['name']); ?>" />
			</div>
			<?php
		}

		/**
		 * Get an instance of WP_Filesystem_Direct.
		 *
		 * @since 1.0.0
		 * @return object A WP_Filesystem_Direct instance.
		 */
		public static function get_filesystem() {
		
			global $wp_filesystem;
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();

			return $wp_filesystem;
		}

		/**
		 * Get appropriate button text and URL based on license status
		 *
		 * @param string $license_status Current license status
		 * @return array Button text and URL
		 */
		private function get_license_button_data( $license_status ) {
			switch ( $license_status ) {
				case 'expired':
					return array(
						'text' => esc_html__( 'Renew License', 'wpzoom-elementor-addons' ),
						'url' => 'https://www.wpzoom.com/account/licenses/'
					);
					
				case 'inactive':
				case 'site_inactive':
					return array(
						'text' => esc_html__( 'Activate License', 'wpzoom-elementor-addons' ),
						'url' => admin_url( 'options-general.php?page=wpzoom-addons-license' )
					);
					
				case 'invalid':
					return array(
						'text' => esc_html__( 'Enter Valid License', 'wpzoom-elementor-addons' ),
						'url' => admin_url( 'options-general.php?page=wpzoom-addons-license' )
					);
					
				case 'disabled':
					return array(
						'text' => esc_html__( 'Contact Support', 'wpzoom-elementor-addons' ),
						'url' => 'https://www.wpzoom.com/support/'
					);
					
				default:
					return array(
						'text' => esc_html__( 'Unlock with Pro', 'wpzoom-elementor-addons' ),
						'url' => 'https://www.wpzoom.com/plugins/elementor-addons-pro/'
					);
			}
		}

		/**
		 * Get appropriate preview message based on license status
		 *
		 * @param string $license_status Current license status
		 * @return string Preview message
		 */
		private function get_preview_message( $license_status ) {
			switch ( $license_status ) {
				case 'expired':
					return esc_html__( 'Your license has expired. Renew your license to unlock this and all premium templates.', 'wpzoom-elementor-addons' );
					
				case 'inactive':
				case 'site_inactive':
					return esc_html__( 'Your license is inactive. Activate your license to unlock this and all premium templates.', 'wpzoom-elementor-addons' );
					
				case 'invalid':
					return esc_html__( 'Your license key is invalid. Enter a valid license key to unlock this and all premium templates.', 'wpzoom-elementor-addons' );
					
				case 'disabled':
					return esc_html__( 'Your license has been disabled. Contact support to restore access to premium templates.', 'wpzoom-elementor-addons' );
					
				default:
					return esc_html__( 'This template requires WPZOOM Elementor Addons Pro license. Get your license key to unlock this and all premium templates.', 'wpzoom-elementor-addons' );
			}
		}

		/**
		 * Clear template cache when license status changes
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function clear_template_cache() {
			// This will force the template library to reload with updated restrictions
			// The cache is cleared by removing the JavaScript WPZCached variable on next load
			// We could also delete transients here if we implement server-side caching
		}
	}

	// Initialize the Elementor library
	WPZOOM_Elementor_Library_Manager::init();

	require __DIR__ . '/wpzoom-template-library.php';

} // Make sure class doesn't already exist