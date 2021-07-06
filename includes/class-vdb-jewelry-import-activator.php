<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.vdbapp.com/
 * @since      1.0.0
 *
 * @package    Vdb_Jewelry_Import
 * @subpackage Vdb_Jewelry_Import/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Vdb_Jewelry_Import
 * @subpackage Vdb_Jewelry_Import/includes
 * @author     Virtual Diamond Boutique <Iqbal.Brightnessgroup@gmail.com>
 */
class Vdb_Jewelry_Import_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
		if ( !class_exists( 'WooCommerce' ) ) {
           die( 'Plugin not activated: WooCommerce must be installed and active' );
        }

		//if( ! get_option( 'vdb_jewelry_import_onboarding' ) ){
        	update_option( 'vdb_jewelry_import_onboarding', 'false' );
    	//}

        update_option( 'vdb_jewelry_import_onboarding_step', '1' );
        update_option( 'vdb_jewelry_import_php_compatible', 'false' );
        update_option( 'vdb_jewelry_import_dependancy_installed', 'false');
        
        add_option('vdb_jewelry_import_do_activation_redirect', true);

        if (!file_exists(VDB_JEWELRY_IMPORT_PLUGIN_PATH.'storage/jewelry')) {
            mkdir(VDB_JEWELRY_IMPORT_PLUGIN_PATH.'storage/jewelry', 0755, true);
        }
	}
}
