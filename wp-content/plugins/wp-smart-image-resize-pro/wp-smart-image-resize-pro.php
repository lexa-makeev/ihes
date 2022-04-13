<?php


/**
 *
 * @link              https://sirplugin.com
 * @since             1.0.0
 * @package           WP_Smart_Image_Resize
 *
 * @wordpress-plugin
 * Plugin Name: Smart Image Resize PRO
 * Plugin URI: https://sirplugin.com
 * Description: Сделайте изображения продуктов WooCommerce одинакового размера и однородными без обрезки.
 * Version: 1.5.4
 * Author: Nabil Lemsieh
 * Author URI: https://sirplugin.com
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.html
 * Text Domain: wp-smart-image-resize
 * Domain Path: /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 5.4
 */



define( 'WP_SIR_VERSION', '1.5.4' );
define( 'WP_SIR_NAME', 'wp-smart-image-resize' );
define( 'WP_SIR_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_SIR_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_SIR_BASENAME', plugin_basename( __FILE__ ) );

update_option ('license_key_wp-smart-image-resize-pro','7C888772-0CB24F59-826E738E-1164BFE9');

try {
    require_once WP_SIR_DIR . 'lib/wp-package-updater/class-wp-package-updater.php';

    $__u = new \WP_Package_Updater_SIR(
        'https://updates.nabillemsieh.com',
        wp_normalize_path( __FILE__ ),
        wp_normalize_path( WP_SIR_DIR ),
        'wp-smart-image-resize-pro'
    );

    if( $__u->__pb() ){ return; }

    add_action('wp_sir_manage_license', [ $__u, 'show_license_form' ] );

} catch ( \Exception $e ) {
}


// Load plugin loader class.
require_once WP_SIR_DIR . 'src/Plugin.php';

// Activate plugin callback.
function wp_sir_activate()
{
    add_option( 'wp_sir_plugin_version', WP_SIR_VERSION );
}

register_activation_hook( __FILE__, 'wp_sir_activate' );

// Run the plugin.
add_action( 'plugins_loaded', [\WP_Smart_Image_Resize\Plugin::get_instance(), 'run']);
