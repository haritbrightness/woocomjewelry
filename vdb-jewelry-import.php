<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.vdbapp.com/
 * @since             1.0.0
 * @package           Vdb_Jewelry_Import
 *
 * @wordpress-plugin
 * Plugin Name:       VDB Jewelry Import
 * Plugin URI:        https://www.vdbapp.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Virtual Diamond Boutique
 * Author URI:        https://www.vdbapp.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       vdb-jewelry-import
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
define( 'VDB_JEWELRY_IMPORT_VERSION', '1.0.0' );

if (! defined('VDB_JEWELRY_IMPORT_ADMIN_URL') ) {
    define('VDB_JEWELRY_IMPORT_ADMIN_URL', get_admin_url());
}

if (! defined('VDB_JEWELRY_IMPORT_PLUGIN_FILE') ) {
    define('VDB_JEWELRY_IMPORT_PLUGIN_FILE', __FILE__);
}

if (! defined('VDB_JEWELRY_IMPORT_PLUGIN_PATH') ) {
    define('VDB_JEWELRY_IMPORT_PLUGIN_PATH', plugin_dir_path(VDB_JEWELRY_IMPORT_PLUGIN_FILE));
}

if (! defined('VDB_JEWELRY_IMPORT_PLUGIN_URL') ) {
    define('VDB_JEWELRY_IMPORT_PLUGIN_URL', plugin_dir_url(VDB_JEWELRY_IMPORT_PLUGIN_FILE));
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-vdb-jewelry-import-activator.php
 */
function activate_vdb_jewelry_import() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vdb-jewelry-import-activator.php';
	Vdb_Jewelry_Import_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-vdb-jewelry-import-deactivator.php
 */
function deactivate_vdb_jewelry_import() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vdb-jewelry-import-deactivator.php';
	Vdb_Jewelry_Import_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_vdb_jewelry_import' );
register_deactivation_hook( __FILE__, 'deactivate_vdb_jewelry_import' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-vdb-jewelry-import.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_vdb_jewelry_import() {

    $plugin = new Vdb_Jewelry_Import();
	$plugin->run();

}
run_vdb_jewelry_import();