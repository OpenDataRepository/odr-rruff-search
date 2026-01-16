<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://opendatarepository.org
 * @since             1.0.0
 * @package           Odr_Rruff_Search
 *
 * @wordpress-plugin
 * Plugin Name:       RRUFF Search
 * Plugin URI:        https://opendatarepository.org
 * Description:       A plugin to search ODR Databases that use the RRUFF Structure.
 * Version:           1.0.0
 * Author:            Nathan Stone
 * Author URI:        https://opendatarepository.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       odr-rruff-search
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
define( 'ODR_RRUFF_SEARCH_VERSION', '1.3.3' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-odr-rruff-search-activator.php
 */
function activate_odr_rruff_search() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-odr-rruff-search-activator.php';
	Odr_Rruff_Search_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-odr-rruff-search-deactivator.php
 */
function deactivate_odr_rruff_search() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-odr-rruff-search-deactivator.php';
	Odr_Rruff_Search_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_odr_rruff_search' );
register_deactivation_hook( __FILE__, 'deactivate_odr_rruff_search' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-odr-rruff-search.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_odr_rruff_search() {

	$plugin = new Odr_Rruff_Search();
	$plugin->run();

}
run_odr_rruff_search();
