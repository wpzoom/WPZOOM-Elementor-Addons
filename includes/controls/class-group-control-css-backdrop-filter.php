<?php
/**
 * Backdrop Filter Group Control.
 *
 * @package WPZOOM_Elementor_Addons
 * @since   1.2.0
 */

namespace WPZOOMElementorWidgets;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Base;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Backdrop Filter Group Control.
 *
 * @since 1.2.0
 */
class Group_Control_Css_Backdrop_Filter extends Group_Control_Base {
	/**
	 * Prepare fields.
	 *
	 * Process css_filter control fields before adding them to `add_control()`.
	 *
	 * @since 1.2.0
	 * @access protected
	 * @var    array
	 * @param  array $fields CSS filter control fields.
	 * @return array Processed fields.
	 */
	protected static $fields;

	/**
	 * Get CSS filter control type.
	 *
	 * Retrieve the control type, in this case `css-backdrop-filter`.
	 *
	 * @since 1.2.0
	 * @access public
	 * @static
	 * @return string Control type.
	 */
	public static function get_type() {
		return 'css-backdrop-filter';
	}

	/**
	 * Init fields.
	 *
	 * Initialize CSS filter control fields.
	 *
	 * @since 1.2.0
	 * @access protected
	 * @return array Control fields.
	 */
	protected function init_fields() {
		$controls = array();

		$controls['blur'] = array(
			'label'     => _x( 'Blur', 'Backdrop Filter Control', 'wpzoom-elementor-addons' ),
			'type'      => Controls_Manager::SLIDER,
			'required'  => 'true',
			'range'     => array(
				'px' => array(
					'min'  => 0,
					'max'  => 10,
					'step' => 0.1,
				),
			),
			'default'   => array(
				'size' => 0,
			),
			'selectors' => array(
				'{{SELECTOR}}' => 'backdrop-filter: brightness( {{brightness.SIZE}}% ) contrast( {{contrast.SIZE}}% ) saturate( {{saturate.SIZE}}% ) blur( {{blur.SIZE}}px ) hue-rotate( {{hue.SIZE}}deg )',
			),
		);

		$controls['brightness'] = array(
			'label'       => _x( 'Brightness', 'Backdrop Filter Control', 'wpzoom-elementor-addons' ),
			'type'        => Controls_Manager::SLIDER,
			'render_type' => 'ui',
			'required'    => 'true',
			'default'     => array(
				'size' => 100,
			),
			'range'       => array(
				'px' => array(
					'min' => 0,
					'max' => 200,
				),
			),
			'separator'   => 'none',
		);

		$controls['contrast'] = array(
			'label'       => _x( 'Contrast', 'Backdrop Filter Control', 'wpzoom-elementor-addons' ),
			'type'        => Controls_Manager::SLIDER,
			'render_type' => 'ui',
			'required'    => 'true',
			'default'     => array(
				'size' => 100,
			),
			'range'       => array(
				'px' => array(
					'min' => 0,
					'max' => 200,
				),
			),
			'separator'   => 'none',
		);

		$controls['saturate'] = array(
			'label'       => _x( 'Saturation', 'Backdrop Filter Control', 'wpzoom-elementor-addons' ),
			'type'        => Controls_Manager::SLIDER,
			'render_type' => 'ui',
			'required'    => 'true',
			'default'     => array(
				'size' => 100,
			),
			'range'       => array(
				'px' => array(
					'min' => 0,
					'max' => 200,
				),
			),
			'separator'   => 'none',
		);

		$controls['hue'] = array(
			'label'       => _x( 'Hue', 'Backdrop Filter Control', 'wpzoom-elementor-addons' ),
			'type'        => Controls_Manager::SLIDER,
			'render_type' => 'ui',
			'required'    => 'true',
			'default'     => array(
				'size' => 0,
			),
			'range'       => array(
				'px' => array(
					'min' => 0,
					'max' => 360,
				),
			),
			'separator'   => 'none',
		);

		return $controls;
	}

	/**
	 * Get default options.
	 *
	 * Retrieve the default options of the CSS filter control. Used to return the
	 * default options while initializing the CSS filter control.
	 *
	 * @since 1.2.0
	 * @access protected
	 * @return array Default CSS filter control options.
	 */
	protected function get_default_options() {
		return array(
			'popover' => array(
				'starter_name'  => 'css_backdrop_filter',
				'starter_title' => _x( 'CSS Backdrop Filters', 'Backdrop Filter Control', 'wpzoom-elementor-addons' ),
				'settings'      => array(
					'render_type' => 'ui',
				),
			),
		);
	}
}
