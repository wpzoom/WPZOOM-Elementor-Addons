<?php
namespace WPZOOM_Elementor_Addons\Manager;

use \Elementor\Plugin;
use \Elementor\Api;

if( !defined( 'ABSPATH' ) ) {
    exit;
}

class WPZOOM_Manager {

    private static $instance = null;

    public static function instance() {

        if( null == self::$instance ) {
            self::$instance=new self; 
        }
            
        return self::$instance;
    
	}

    public function __construct() {
            
		add_action( 'elementor/init', array( $this, 'library_source' ), 15 );

		if( defined( 'ELEMENTOR_VERSION') && version_compare( ELEMENTOR_VERSION, '2.3.0', '>' ) ) { 
			add_action( 'elementor/ajax/register_actions', array( $this, 'register_ajax' ), 25 );
		}
		
		if( defined ( 'Elementor\Api::LIBRARY_OPTION_KEY' ) ) {
			add_filter( 'option_' . Api::LIBRARY_OPTION_KEY, array( $this, 'add_categories' ) );
		}

    }
    
    public function add_categories ( $data ) {
		$categories = array(
			'WPZOOM Blog',
			'WPZOOM Portfolio',
			'WPZOOM Hero',
			'WPZOOM Contact Form'
		);
        
        if( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION,'2.3.9','>' ) ) {
            $data['types_data']['block']['categories'] = array_merge( $categories,$data['types_data']['block']['categories'] );
        }
        else { 
            $data['categories'] = array_merge( $categories,$data['categories'] );
        }

        return $data;
    }
    
    public function register_ajax( $ajax ) { 

		if( !isset($_REQUEST['actions'])) { 
            return;
        }
        $ajax_actions = json_decode( stripslashes( $_REQUEST['actions'] ), true );
        $template = false;
        foreach( $ajax_actions as $data => $action_data ) { 
            if( !isset( $action_data['get_template_data'] ) ) {
                $template = $action_data;
            }
        }
        if ( !isset( $template['data'] ) || empty( $template['data'] ) ) {
            return;
        }
        if ( empty( $template['data']['template_id']) ) {
            return;
        }
		if( false === strpos( $template['data']['template_id'], 'wpzoom_eladdons_' ) ) { 
			return;
		}

		$ajax->register_ajax_action( 'get_template_data', array( $this,'get_template' ) );

	}
    
    public function get_template( $args )  {

		$template_source = Plugin::instance()->templates_manager->get_source( 'wpzoom_addons_templates' );
        $template = $template_source->get_data( $args );
        return $template;

	}

    public function library_source() { 
        
		require_once( WPZOOM_EL_ADDONS_PATH . 'includes/wpzoom-template-library.php' );
		Plugin::instance()->templates_manager->register_source( 'Elementor\TemplateLibrary\WPZOOM_Library_Source' );

	}
}