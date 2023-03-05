<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://barkappeal.com
 * @since      1.0.0
 *
 * @package    Ba_Wholesale_Order_Form
 * @subpackage Ba_Wholesale_Order_Form/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Ba_Wholesale_Order_Form
 * @subpackage Ba_Wholesale_Order_Form/includes
 * @author     Brady Christopher <brady@mail.com>
 */
class Ba_Wholesale_Order_Form_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ba-wholesale-order-form',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
