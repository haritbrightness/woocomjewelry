<?php
// Preventing to direct access
defined( 'ABSPATH' ) or die( ( new Vdb_Jewelry_Import_Constants )->DIE_MESSAGE );

class Vdb_Jewelry_Import_Save_Settings {

    public static function save_jewelry_import_general_settings_callback() {

        if (!is_admin() || !isset($_POST['save_jewelry_import_general_settings']) || !wp_verify_nonce($_REQUEST['jewelry_import_general_settings_nonce_field'], 'jewelry_import_general_settings_nonce')) {
            return;
        }

        $post_data = stripslashes_deep($_POST);

        $jewelry_import_general_settings = $post_data['jewelry_import_general'];

        update_option('jewelry_import_general_settings', $jewelry_import_general_settings);

    }

    /**
     * Callback function to save the category mapping
    **/
    public function save_jewelry_import_cat_mappings_callback(){
        if (!is_admin() || !isset($_POST['save_jewelry_import_cat_mappings']) || !wp_verify_nonce($_REQUEST['jewelry_import_cat_mappings_nonce_field'], 'jewelry_import_cat_mappings_nonce')) {
            return;
        }

        $post_data = stripslashes_deep($_POST);

        $jewelry_import_cat_mappings_settings = $post_data['jewelry_cat_mapping'];

        update_option('jewelry_import_cat_mappings_settings', $jewelry_import_cat_mappings_settings);
    }

    /**
     * Callback function to save General Settings for Rings
    **/
    public static function save_jewelry_import_rings_general_settings_callback() {

        if (!is_admin() || !isset($_POST['save_jewelry_import_rings_general_settings']) || !wp_verify_nonce($_REQUEST['jewelry_import_rings_general_settings_nonce_field'], 'jewelry_import_rings_general_settings_nonce')) {
            return;
        }
        
        $post_data = stripslashes_deep($_POST);

        $jewelry_import_rings_general_settings = $post_data['jewelry_import_rings_general_settings'];

        update_option('jewelry_import_rings_general_settings', $jewelry_import_rings_general_settings);

        (new Vdb_Jewelry_Import_Cron_Manager)->unschedule_event_callback(true);
    }

    /**
     * Callback function to save Search Settings for Natural Diamonds
    **/
    public static function save_jewelry_import_rings_search_settings_callback() {

        if (!is_admin() || !isset($_POST['save_jewelry_import_rings_search_settings']) || !wp_verify_nonce($_REQUEST['jewelry_import_rings_search_settings_nonce_field'], 'jewelry_import_rings_search_settings_nonce')) {
            return;
        }
        
        $post_data = stripslashes_deep($_POST);

        $jewelry_import_rings_search_settings = $post_data['jewelry_import_rings_search_settings'];

        update_option('jewelry_import_rings_search_settings', $jewelry_import_rings_search_settings);

    }

    /**
     * Callback function to save Details Settings for Natural Diamonds
    **/
    public static function save_jewelry_import_rings_details_settings_callback() {

        if (!is_admin() || !isset($_POST['save_jewelry_import_rings_details_settings']) || !wp_verify_nonce($_REQUEST['jewelry_import_rings_details_settings_nonce_field'], 'jewelry_import_rings_details_settings_nonce')) {
            return;
        }
        
        $post_data = stripslashes_deep($_POST);

        $jewelry_import_rings_details_settings = $post_data['jewelry_import_rings_details_settings'];

        update_option('jewelry_import_rings_details_settings', $jewelry_import_rings_details_settings);

    }

   
    public static function show_admin_notice($setting) {

        echo "<div class='notice notice-success is-dismissible'>
            <p>$setting" . __(' Settings Saved Successfully!', 'vdb-jewelry-import') . "</p>
        </div>";
    }

    public static function save_onboarding_step_1_callback() {

        update_option('vdb_jewelry_import_onboarding_step', '2');
        wp_safe_redirect(VDB_JEWELRY_IMPORT_ADMIN_URL . 'admin.php?page=vdb-jewelry-import-dashboard&tab=onboarding&step=2');

    }

    public static function save_onboarding_step_2_callback() {

        //update_option('vdb_jewelry_import_onboarding_step', '3');
        update_option('vdb_jewelry_import_php_compatible', 'true');
        update_option('vdb_jewelry_import_dependancy_installed', 'true');


        $attributes = new Jewelry_Import_Attributes();
        $post_data = stripslashes_deep($_POST);
        $jewelry_import_general_settings = $post_data['jewelry_import_general'];

        $default_jewelry_import_general_settings = [
            'api'                               => $jewelry_import_general_settings['api'], 
            'token'                             => $jewelry_import_general_settings['token'],
            'apv_api_key'                       => $jewelry_import_general_settings['apv_api_key'],
            'module'                            => 'checked', 
            'page_size'                         => '12', 
            'certificate_popup'                 => 'checked',
            'exchange_rate'                     => Vdb_Jewelry_Import::get_exchange_rate($jewelry_import_general_settings['apv_api_key']),
        ];

        $default_jewelry_import_modules_general_settings = [
            'module'        => 'checked',
            'without_image' => 'checked',
            'logger'        => 'checked',
        ];

        /**
         * Default Rings Settings Starts Here
        **/

        // Create Core Options of Search Page
        $default_jewelry_import_rings_search_setting = self::create_options($attributes->rings_search_setings);

        // Create Shapes Options of Search Page
        $default_jewelry_import_rings_search_setting = array_merge_recursive($default_jewelry_import_rings_search_setting, self::create_nested_options('JewelryStyle', $attributes->rings_ring_style_array));

        // Create Colors Options of Search Page
        $default_jewelry_import_rings_search_setting = array_merge_recursive($default_jewelry_import_rings_search_setting, self::create_nested_options('MetalType', $attributes->rings_metal_type_array));

        /**
         * Default Rings Settings Ends Here
        **/

       
        update_option( 'jewelry_import_general_settings', $default_jewelry_import_general_settings );

        update_option( 'jewelry_import_rings_general_settings', $default_jewelry_import_modules_general_settings );

        update_option( 'jewelry_import_rings_search_settings', $default_jewelry_import_rings_search_setting );

        update_option( 'jewelry_import_rings_details_settings', self::create_options($attributes->rings_details_settings) );

        update_option( 'vdb_jewelry_import_onboarding', 'true' );

        wp_safe_redirect(VDB_JEWELRY_IMPORT_ADMIN_URL . 'admin.php?page=vdb-jewelry-import-dashboard');

    }

    private static function create_options($section) {
        $counter = 0;
        $settings_array = [];

        // Define Sort Order of Each Option
        foreach ($section as $k => $setting) {
            $counter++;
            $settings_array[$setting]['sort_order'] = $counter;
        }

        // Define Status of Each Option
        foreach ($section as $k => $setting) {
            $settings_array[$setting]['Status'] = 'checked';
        }

        return $settings_array;
    }

    private static function create_nested_options($section, $option) {
        $counter = 0;
        $settings_array = [];

        // Define Sort Order of Each Option
        foreach ($option as $k => $setting) {
            $counter++;
            $settings_array[$section][$setting]['sort_order'] = $counter;
        }

        // Define Status of Each Option
        foreach ($option as $k => $setting) {
            $settings_array[$section][$setting]['Status'] = 'checked';
        }

        return $settings_array;
    }
}