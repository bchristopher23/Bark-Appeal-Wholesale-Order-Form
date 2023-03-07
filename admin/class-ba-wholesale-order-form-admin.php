<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://barkappeal.com
 * @since      1.0.0
 *
 * @package    Ba_Wholesale_Order_Form
 * @subpackage Ba_Wholesale_Order_Form/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ba_Wholesale_Order_Form
 * @subpackage Ba_Wholesale_Order_Form/admin
 * @author     Brady Christopher <brady@mail.com>
 */
class Ba_Wholesale_Order_Form_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ba-wholesale-order-form-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( 'sortable', plugin_dir_url( __FILE__ ) . 'js/sortable.min.js', array(), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ba-wholesale-order-form-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'ajax_params', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	}

	/**
	 * Create Admin Menu Page
	 * 
	 * @since 	1.0.0
	 */
	public function ba_admin_menu() {
		add_menu_page( 'Wholesale Order Form Settings', 'Wholesale Order Form', 'manage_options', 'ba-wholesale-order-form-admin-page.php', array($this, 'ba_render_admin_page' ), 'dashicons-cart', 56 );
		add_submenu_page(
			'ba-wholesale-order-form-admin-page.php',
			'Products Order', //page title
			'Products Order', //menu title
			'manage_options', //capability,
			'ba-wholesale-order-form-products-admin-page.php',//menu slug
			array($this, 'ba_render_admin_product_page' ) //callback function
		);
	}

	/**
	 * Render Admin Settings Page
	 * 
	 * @since 	1.0.0
	 */
	public function ba_render_admin_page() {

		$templates = new Custom_Template_Loader;

		$templates->get_template_part( 'admin/content', 'settings-page' );

	}

	public function ba_save_settings() {

		$ordered_cats = isset( $_POST['ordered_cats'] ) ? $_POST['ordered_cats'] : '';
		update_option( 'ba_categories', $ordered_cats );

		wp_redirect( admin_url( 'admin.php' ) . '?page=ba-wholesale-order-form-admin-page.php&success=yes');
		exit;

	}

	public function ba_render_admin_product_page() {

		$templates = new Custom_Template_Loader;

		$templates->get_template_part( 'admin/content', 'products-order-page' );

	}

	public function ba_save_product_order() {

		$ordered_products = isset( $_POST['product_ids'] ) ? $_POST['product_ids'] : '';
		$ordered_products = json_decode( str_replace('\\', '', $ordered_products), true);

		$count = 1;

		foreach( $ordered_products as $product ) {

			update_post_meta( intval($product), 'ba_order', $count );
			$count++;

		}


		wp_redirect( admin_url( 'admin.php' ) . '?page=ba-wholesale-order-form-products-admin-page.php&success=yes');
		exit;

	}

	function ba_get_products_by_category() {

		$data = isset( $_POST['data'] ) ? json_decode( str_replace('\\', '', $_POST['data'] ), true ) : '';
		$category_id = intval( $data['category_id'] );

		// Category products
		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'tax_query'             => array(
				array(
					'taxonomy' => 'product_cat',
					'field' => 'term_id',
					'terms' => $category_id,
					'operator' => 'IN'
				),
			),
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key'=>'ba_order',
					'compare' => 'EXISTS'         
				),
				array(
					'key'=>'ba_order',
					'compare' => 'NOT EXISTS'         
				)
			),
			'order'          => 'ASC',
			'orderby'        => 'meta_value_num',
		);
		$products = get_posts( $args );

        wp_send_json( json_encode($products) );

	}

}
