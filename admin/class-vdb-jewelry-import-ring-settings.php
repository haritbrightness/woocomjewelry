<?php
// Preventing to direct access
defined('ABSPATH') or die(( new Vdb_Jewelry_Import_Constants )->DIE_MESSAGE);

class Jewelry_Import_Ring_Settings {

    public static function ring_settings_view() {

        $section = ( isset( $_GET['section'] ) ) ? $_GET['section'] : 'general';

        if( $section == 'general' ){
          require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/views/rings/general-settings.php';  
        }else if( $section == 'search' ){
          require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/views/rings/search-page-settings.php';  
        }else if( $section == 'cat_mappings' ){
          require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/views/rings/cat-mappings.php';  
        }
        echo Vdb_Jewelry_Import::infoHtml();
    }
}
?>