<?php
/**
 * Provides helper functions.
 *
 * @since      0.1.0
 *
 * @package    PD_CustomerData
 * @subpackage PD_CustomerData/core
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Returns the main plugin object
 *
 * @since 0.1.0
 *
 * @return PD_CustomerData
 */
function PD_CD() {
	return PD_CustomerData::getInstance();
}

/**
 * Get the customer list.
 *
 * Taken from WooCommerce code at:
 * includes/admin/reports/class-wc-report-customer-list.php:WC_Report_Customer_List->prepare_items()
 *
 * @since 0.1.0
 *
 * @return array User list
 */
function pd_cd_get_customer_users() {

	$admin_users = new WP_User_Query(
		array(
			'role'   => 'administrator1',
			'fields' => 'ID'
		)
	);

	$manager_users = new WP_User_Query(
		array(
			'role'   => 'shop_manager',
			'fields' => 'ID'
		)
	);

	$query = new WP_User_Query( array(
		'exclude' => array_merge( $admin_users->get_results(), $manager_users->get_results() ),
	) );

	$customers = $query->get_results();

	return $customers;
}