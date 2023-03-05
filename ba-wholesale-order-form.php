<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://barkappeal.com
 * @since             1.0.0
 * @package           Ba_Wholesale_Order_Form
 *
 * @wordpress-plugin
 * Plugin Name:       Wholesale Order Form
 * Plugin URI:        https://barkappeal.com
 * Description:       Creates a wholesale order form
 * Version:           1.0.0
 * Author:            Brady Christopher
 * Author URI:        https://barkappeal.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ba-wholesale-order-form
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BA_WHOLESALE_ORDER_FORM_VERSION', '1.0.0' );

/**
 * Define plugin directory constant
 */
define( 'WHOLESALE_ORDER_FORM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ba-wholesale-order-form-activator.php
 */
function activate_ba_wholesale_order_form() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ba-wholesale-order-form-activator.php';
	Ba_Wholesale_Order_Form_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ba-wholesale-order-form-deactivator.php
 */
function deactivate_ba_wholesale_order_form() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ba-wholesale-order-form-deactivator.php';
	Ba_Wholesale_Order_Form_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ba_wholesale_order_form' );
register_deactivation_hook( __FILE__, 'deactivate_ba_wholesale_order_form' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ba-wholesale-order-form.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ba_wholesale_order_form() {

	$plugin = new Ba_Wholesale_Order_Form();
	$plugin->run();

}
run_ba_wholesale_order_form();
