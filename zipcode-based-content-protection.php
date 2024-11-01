<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.presstigers.com/
 * @since             1.0.0
 * @package           Zipcode_BCP
 *
 * @wordpress-plugin
 * Plugin Name:       ZIP Code Based Content Protection
 * Plugin URI:        https://wordpress.org/plugins/zipcode-based-content-protection
 * Description:       Limit content visibility to your selected zipcodes.
 * Version:           1.0.0
 * Author:            PressTigers
 * Author URI:        http://pressTigers.com
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       zipcode-bcp
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
define( 'ZIPCODE_BCP', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-zipcode-bcp-activator.php
 */
function activate_zipcode_bcp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-zipcode-bcp-activator.php';
	Zipcode_BCP_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-zipcode-bcp-deactivator.php
 */
function deactivate_zipcode_bcp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-zipcode-bcp-deactivator.php';
	Zipcode_BCP_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_zipcode_bcp' );
register_deactivation_hook( __FILE__, 'deactivate_zipcode_bcp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-zipcode-bcp.php';

function zbcp_plugin_add_settings_link( $links ) {
	$settings_link = '<a href="admin.php?page=zbcp-settings">' . __( 'Settings', 'zipcode-bcp' ) . '</a>';
	array_push( $links, $settings_link );
	return $links;
}

$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'zbcp_plugin_add_settings_link' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_zipcode_bcp() {
	$plugin = new Zipcode_BCP();
	$plugin->run();
}
run_zipcode_bcp();
