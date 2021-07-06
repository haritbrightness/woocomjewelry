<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.vdbapp.com/
 * @since      1.0.0
 *
 * @package    Vdb_Jewelry_Import
 * @subpackage Vdb_Jewelry_Import/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Vdb_Jewelry_Import
 * @subpackage Vdb_Jewelry_Import/includes
 * @author     Virtual Diamond Boutique <Iqbal.Brightnessgroup@gmail.com>
 */
class Vdb_Jewelry_Import_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		(new Vdb_Jewelry_Import_Cron_Manager)->unschedule_event_callback();
	}

}
