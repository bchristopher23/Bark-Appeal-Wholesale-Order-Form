<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://barkappeal.com
 * @since      1.0.0
 *
 * @package    Ba_Wholesale_Order_Form
 * @subpackage Ba_Wholesale_Order_Form/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ba_Wholesale_Order_Form
 * @subpackage Ba_Wholesale_Order_Form/includes
 * @author     Brady Christopher <brady@mail.com>
 */
class Ba_Wholesale_Order_Form {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ba_Wholesale_Order_Form_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'BA_WHOLESALE_ORDER_FORM_VERSION' ) ) {
			$this->version = BA_WHOLESALE_ORDER_FORM_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ba-wholesale-order-form';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ba_Wholesale_Order_Form_Loader. Orchestrates the hooks of the plugin.
	 * - Ba_Wholesale_Order_Form_i18n. Defines internationalization functionality.
	 * - Ba_Wholesale_Order_Form_Admin. Defines all hooks for the admin area.
	 * - Ba_Wholesale_Order_Form_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ba-wholesale-order-form-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ba-wholesale-order-form-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ba-wholesale-order-form-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ba-wholesale-order-form-public.php';

		/**
		 * Custom Template Loader
		 */
		if( ! class_exists( 'Gamajo_Template_Loader' ) ) {
			require plugin_dir_path( dirname( __FILE__ ) ). 'includes/libraries/class-gamajo-template-loader.php';
		}

		require plugin_dir_path( dirname( __FILE__ ) ) . 'includes/libraries/class-custom-template-loader.php';

		$this->loader = new Ba_Wholesale_Order_Form_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ba_Wholesale_Order_Form_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ba_Wholesale_Order_Form_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ba_Wholesale_Order_Form_Admin( $this->get_plugin_name(), $this->get_version() );

		// Enqueue Scripts
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Save/Update our plugin options
		$this->loader->add_action( 'admin_action_ba_save_settings', $plugin_admin, 'ba_save_settings' );
		$this->loader->add_action( 'admin_action_ba_save_product_order', $plugin_admin, 'ba_save_product_order' );

		// Add menu item
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'ba_admin_menu' );
	
		// Get producs by category AJAX
		$this->loader->add_action('wp_ajax_get_products_by_category', $plugin_admin, 'ba_get_products_by_category');
		$this->loader->add_action('wp_ajax_nopriv_get_products_by_category', $plugin_admin, 'ba_get_products_by_category');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Ba_Wholesale_Order_Form_Public( $this->get_plugin_name(), $this->get_version() );

		// Enqueue Scripts
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Insert Modal
		$this->loader->add_action( 'wp_body_open', $plugin_public, 'ba_insert_modal' );

		// Add to cart AJAX
		$this->loader->add_action('wp_ajax_add_to_cart', $plugin_public, 'ba_add_to_cart');
		$this->loader->add_action('wp_ajax_nopriv_add_to_cart', $plugin_public, 'ba_add_to_cart');

		// Get products by category AJAX
		$this->loader->add_action('wp_ajax_get_products_html_by_category', $plugin_public, 'ba_get_products_by_category');
		$this->loader->add_action('wp_ajax_nopriv_get_products_html_by_category', $plugin_public, 'ba_get_products_by_category');

		/**
		 * Register shortcode via loader
		 *
		 * Use: [short-code-name args]
		 *
		 * @link https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/issues/262
		 */
		$this->loader->add_shortcode( "ba_wholesale_order_form", $plugin_public, "ba_render_order_form", $priority = 10, $accepted_args = 2 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ba_Wholesale_Order_Form_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
