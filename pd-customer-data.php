<?php

/*
Plugin Name: Peaceful Dragon Customer Data
Description: A brief description of the Plugin.
Version: 0.1.0
Author: joelworsham
Author URI: http://realbigmarketing.com
*/

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Define plugin constants
define( 'PD_CUSTOMEREXPORT_VERSION', '0.1.0' );
define( 'PD_CUSTOMEREXPORT_DIR', plugin_dir_path( __FILE__ ) );
define( 'PD_CUSTOMEREXPORT_URL', plugins_url( '', __FILE__ ) );

/**
 * Class PD_CustomerExport
 *
 * Initiates the plugin.
 *
 * @since   0.1.0
 *
 * @package PD_CustomerExport
 */
class PD_CustomerExport {

	private function __clone() { }

	private function __wakeup() { }

	/**
	 * Returns the *Singleton* instance of this class.
	 *
	 * @since     0.1.0
	 *
	 * @staticvar Singleton $instance The *Singleton* instances of this class.
	 *
	 * @return PD_CustomerExport The *Singleton* instance.
	 */
	public static function getInstance() {

		static $instance = null;

		if ( null === $instance ) {
			$instance = new static();
		}

		return $instance;
	}

	/**
	 * Initializes the plugin.
	 *
	 * @since 0.1.0
	 */
	protected function __construct() {

		$this->add_base_actions();
		$this->require_necessities();
	}

	/**
	 * Requires necessary base files.
	 *
	 * @since 0.1.0
	 */
	public function require_necessities() {
	}

	/**
	 * Adds global, base functionality actions.
	 *
	 * @since 0.1.0
	 */
	private function add_base_actions() {

		add_action( 'init', array( $this, '_register_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, '_enqueue_assets' ) );
	}

	/**
	 * Registers the plugin's assets.
	 *
	 * @since 0.1.0
	 */
	function _register_assets() {
	}

	function _enqueue_assets() {
	}
}