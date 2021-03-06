<?php

/*
Plugin Name: Peaceful Dragon Customer Data
Description: A brief description of the Plugin.
Version: 1.0.1
Author: joelworsham
Author URI: http://realbigmarketing.com
*/

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Define plugin constants
define( 'PD_CUSTOMERDATA_VERSION', '1.0.1' );
define( 'PD_CUSTOMERDATA_DIR', plugin_dir_path( __FILE__ ) );
define( 'PD_CUSTOMERDATA_URL', plugins_url( '', __FILE__ ) );

/**
 * Class PD_CustomerData
 *
 * Initiates the plugin.
 *
 * @since   0.1.0
 *
 * @package PD_CustomerData
 */
class PD_CustomerData {

	public $primary_page;

	private function __clone() { }

	private function __wakeup() { }

	/**
	 * Returns the *Singleton* instance of this class.
	 *
	 * @since     0.1.0
	 *
	 * @staticvar Singleton $instance The *Singleton* instances of this class.
	 *
	 * @return PD_CustomerData The *Singleton* instance.
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

		require_once __DIR__ . '/core/class-pd-customerdata-page.php';
		$this->primary_page = new PD_CustomerData_Page();
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

require_once __DIR__ . '/core/pd-customerdata-functions.php';
PD_CD();