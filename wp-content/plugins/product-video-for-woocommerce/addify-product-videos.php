<?php 
/**
 * Plugin Name:       Product Video for WooCommerce
 * Plugin URI:        https://woocommerce.com/products/product-video-for-woocommerce/ 
 * Description:       Allows merchants to add product videos and improve their sales. (PLEASE TAKE BACKUP BEFORE UPDATING THE PLUGIN).
 * Version:           1.4.2
 * Author:            Addify
 * Developed By:      Addify
 * Author URI:        http://www.addifypro.com
 * Support:           http://www.addifypro.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       addify_videos
 *
 * Woo: 4912085:10f0ccca50ebb14f21419dadcccad509
 *
 * WC requires at least: 3.0.9
 * WC tested up to: 6.*.*
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Check for multisite

if (!is_multisite() && !in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ) {

	function afpv_admin_notice() {

		$afpv_allowed_tags = array(
			'a' => array(
				'class' => array(),
				'href'  => array(),
				'rel'   => array(),
				'title' => array(),
			),
			'b' => array(),

			'div' => array(
				'class' => array(),
				'title' => array(),
				'style' => array(),
			),
			'p' => array(
				'class' => array(),
			),
			'strong' => array(),

		);

		// Deactivate the plugin
		deactivate_plugins(__FILE__);

		$afpv_woo_check = '<div id="message" class="error">
			<p><strong>Product Video for WooCommerce is inactive plugin is inactive.</strong> The <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce plugin</a> must be active for this plugin to work. Please install &amp; activate WooCommerce »</p></div>';
		echo wp_kses( __( $afpv_woo_check, 'addify_wholesale_prices' ), $afpv_allowed_tags);

	}
	add_action('admin_notices', 'afpv_admin_notice');
}



if ( !class_exists('Addify_Product_Videos') ) { 

	class Addify_Product_Videos {

		public function __construct() {

			$this->afreg_global_constents_vars();

			add_action('wp_loaded', array( $this, 'afpv_init' )); 

			add_action( 'init', array($this, 'afpv_custom_post_type' ));

			if (is_admin() ) {
				include_once AFPV_PLUGIN_DIR . 'admin/class-afpv-admin.php';
			} else {
				include_once AFPV_PLUGIN_DIR . 'front/class-afpv-front.php';
			}            
		}

		public function afreg_global_constents_vars() {
			
			if (!defined('AFPV_URL') ) {
				define('AFPV_URL', plugin_dir_url(__FILE__));
			}

			if (!defined('AFPV_BASENAME') ) {
				define('AFPV_BASENAME', plugin_basename(__FILE__));
			}

			if (! defined('AFPV_PLUGIN_DIR') ) {
				define('AFPV_PLUGIN_DIR', plugin_dir_path(__FILE__));
			}
		}

		
		public function afpv_init() {
			if (function_exists('load_plugin_textdomain') ) {
				load_plugin_textdomain('addify_videos', false, dirname(plugin_basename(__FILE__)) . '/languages/');
			}
		}

		public function afpv_custom_post_type() {

			$labels = array(
			'name'                => esc_html__('Product Videos', 'addify_videos'),
			'singular_name'       => esc_html__('Produt Videos', 'addify_videos'),
			'add_new'             => esc_html__('Add New Video', 'addify_videos'),
			'add_new_item'        => esc_html__('Add New Video', 'addify_videos'),
			'edit_item'           => esc_html__('Edit Product Video', 'addify_videos'),
			'new_item'            => esc_html__('New Product Video', 'addify_videos'),
			'view_item'           => esc_html__('View Product Video', 'addify_videos'),
			'search_items'        => esc_html__('Search Product Video', 'addify_videos'),
			'exclude_from_search' => true,
			'not_found'           => esc_html__('No product video found', 'addify_videos'),
			'not_found_in_trash'  => esc_html__('No product video field found in trash', 'addify_videos'),
			'parent_item_colon'   => '',
			'all_items'           => esc_html__('All Product Videos', 'addify_videos'),
			'menu_name'           => esc_html__('Product Videos', 'addify_videos'),
			);
		
			$args = array(
			'labels' => $labels,
			'menu_icon'  => plugin_dir_url( __FILE__ ) . 'images/small_logo_grey.png',
			'public' => false,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => false,
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'has_archive' => true,
			'hierarchical' => false,
			'menu_position' => 30,
			'rewrite' => array('slug' => 'addify_videos', 'with_front'=>false ),
			'supports' => array('title')
			);
		
			register_post_type( 'af_product_videos', $args );

		}
		
	}

	new Addify_Product_Videos();

}
