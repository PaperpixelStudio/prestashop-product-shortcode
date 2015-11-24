<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Plugin_Name
 *
 * @wordpress-plugin
 * Plugin Name:       Prestashop Product Shortcode
 * Plugin URI:
 * Description:       Display product information with the shortcode [presta-product] in Wordpress.
 * Version:           0.1.0
 * Author:            Paperpixel.net
 * Author URI:        http://paperpixel.net
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       pps
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pps-activator.php
 */
function activate_pps() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pps-activator.php';
	PPS_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pps-deactivator.php
 */
function deactivate_pps() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pps-deactivator.php';
	PPS_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pps' );
register_deactivation_hook( __FILE__, 'deactivate_pps' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pps.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_plugin_name() {

	$plugin = new PPS();
	$plugin->run();

}
run_plugin_name();
