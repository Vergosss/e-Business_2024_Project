<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://woosmartcod.com/
 * @since             1.0.0
 * @package           Wc_Smart_Cod
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Smart COD
 * Plugin URI:        https://wordpress.org/plugins/wc-smart-cod/
 * Description:       A powerful plugin that extends WooCommerce COD (Cash on Delivery) Gateway, supporting multiple extra fees and a multiple factor gateway restriction.
 * Version:           1.7.2
 * Author:            woosmartcod.com
 * Author URI:        https://woosmartcod.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-smart-cod
 * Domain Path:       /languages
 * WC requires at least: 2.7
 * WC tested up to: 8.8.2
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wc-smart-cod-activator.php
 */
function activate_wc_smart_cod()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-wc-smart-cod-activator.php';
    Wc_Smart_Cod_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wc-smart-cod-deactivator.php
 */
function deactivate_wc_smart_cod()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-wc-smart-cod-deactivator.php';
    Wc_Smart_Cod_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wc_smart_cod');
register_deactivation_hook(__FILE__, 'deactivate_wc_smart_cod');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wc-smart-cod.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wc_smart_cod()
{

    $plugin = new Wc_Smart_Cod();

}

function woocommerce_smart_cod_declare_hpos_compatibility() {
    add_action( 'before_woocommerce_init', function() {
        if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        }
    } );
}

woocommerce_smart_cod_declare_hpos_compatibility();
run_wc_smart_cod();
