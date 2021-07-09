<?php
namespace Elementor\TemplateLibrary;

//Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) 
	exit;

/**
 * WPZOOM Templates Library
 *
 * Uses built-in Elementor functions to create a new source for template library.
 *
 * @since 1.0.0
 */
if ( did_action( 'elementor/loaded' ) ) {
	class WPZOOM_Library_Source extends Source_Base {

		public function __construct() {
			
			parent::__construct();
			add_action( 'wp_ajax_get_content_from_elementor_export_file', array( $this, 'get_finalized_data' ) );
		
		}

		public function get_id() {}
		public function get_title() {}
		public function register_data(){ }
		public function get_items( $args = [] ){}
		public function get_item( $template_id ){}
		public function get_data( array $args ){}
		public function delete_template( $template_id ){}
		public function save_item( $template_data ){}
		public function update_item( $new_data ){}
		public function export_template( $template_id ){}

		public function get_finalized_data() {

			$url = sprintf( 'https://api.wpzoom.com/templates/%s', sanitize_text_field( $_POST['filename'] ) );
			$response = wp_remote_get( $url, array( 'timeout' => 60 ) );
			if( !is_wp_error( $response ) ) {
				$data = json_decode( wp_remote_retrieve_body( $response ), true );
			}
			else {
				$local_file = sprintf( WPZOOM_EL_ADDONS_PATH . '/includes/data/json/%s', sanitize_text_field( $_POST['filename'] ) ) ;
				if( self::get_filesystem()->exists( $local_file ) ) {
					$data = self::get_filesystem()->get_contents( $local_file );
					$data = json_decode( $data, true );
				}
			}
			$content = $data['content'];
			$content = $this->process_export_import_content( $content, 'on_import' );
			$content = $this->replace_elements_ids( $content );
			
			echo json_encode( $content );
			wp_die();

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
	}
	new WPZOOM_Library_Source();
}