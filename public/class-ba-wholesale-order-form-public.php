<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://barkappeal.com
 * @since      1.0.0
 *
 * @package    Ba_Wholesale_Order_Form
 * @subpackage Ba_Wholesale_Order_Form/public
 */

class Ba_Wholesale_Order_Form_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ba_Wholesale_Order_Form_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ba_Wholesale_Order_Form_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ba-wholesale-order-form-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ba_Wholesale_Order_Form_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ba_Wholesale_Order_Form_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ba-wholesale-order-form-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'ajax_params', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	}


	/**
	 * Output content for the order form shortcode
	 *
	 * @since    1.0.0
	 */
	function ba_render_order_form( $atts ) {

		$templates = new Custom_Template_Loader;

		// Turn on output buffering, because it is a shortcode, content file should not echo anything out.
		// In other cases, output buffering may not required.
		ob_start();

		// Load template from PLUGIN_NAME_BASE_DIR/templates/content-header.php
		$templates->get_template_part( 'content', 'order-form' );

		// Return content ftom the file
		return ob_get_clean();

	}

	function ba_insert_modal() {

		$templates = new Custom_Template_Loader;

		$templates->get_template_part( 'content', 'product-modal' );

	}

	function ba_add_to_cart() {

		$data = isset( $_POST['data'] ) ? json_decode( str_replace('\\', '', $_POST['data'] ), true ) : '';
		$product_id = intval( $data['product_id'] );
		$variations = $data['variations'];

		foreach( $variations as $variation ) {

			if (isset( $variation['id']) ) {

				// Variable Product
				WC()->cart->add_to_cart( $product_id, intval( $variation['quantity'] ), intval( $variation['id'] ) ); 

			} else {

				// Simple Product
				WC()->cart->add_to_cart( $product_id, intval( $variation['quantity'] ) ); 

			}

		}

        wp_send_json( json_encode(array('success' => true, 'count' => WC()->cart->get_cart_contents_count())) );

	}

}
