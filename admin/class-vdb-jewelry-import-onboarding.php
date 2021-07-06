<?php
/**
 * The admin-specific functionality of the plugin. Represents On-Boarding View
 *
 * @link       https://www.vdbapp.com/
 * @since      1.0.0
 *
 * @package    Vdb_Jewelry_Import
 * @subpackage Vdb_Jewelry_Import/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Vdb_Jewelry_Import
 * @subpackage Vdb_Jewelry_Import/admin
 * @author     Virtual Diamond Boutique <info@vdbapp.com>
 */
// Preventing to direct access
defined('ABSPATH') or die( ( new Vdb_Jewelry_Import_Constants )->DIE_MESSAGE );

class Jewelry_Import_Onboarding {

    public static function onboarding_step_1_view() {

        if (isset($_POST['jewelry_import_step_1'])) {

            Vdb_Jewelry_Import_Save_Settings::save_onboarding_step_1_callback();

        }

        $html = "<h1>" . __( ( new Vdb_Jewelry_Import_Constants )->ONBOARDING_TITLE, 'vdb-jewelry-import' ) . "</h1><div class='onboarding-step-wrapper'>
            <input type='hidden' name='jewelry_import_step_1' value='done' />
                <div class='onboarding-step-left onboarding-step-1-top'>
                     <div class='onboarding-step-bg'>
                        <div class='onboarding-first-top-left'>
                          <h2>" . __( "Features and Benefits", 'vdb-jewelry-import' ) . "</h2>
                          <p>" . __( "Welcome to Virtual Diamond Boutique’s (VDB’s) marketplace plugin. Once you complete the setup, you can:", 'vdb-jewelry-import' ) . "</p>
                          <ul>
                            <li>" . __( "Display your VDB marketplace inventory on your website", 'vdb-jewelry-import' ) . "</li>
                            <li>" . __( "Pick and choose your favorite suppliers, and show their inventory on your website", 'vdb-jewelry-import' ) . "</li>
                            <li>" . __( "Setup markups and different currencies", 'vdb-jewelry-import' ) . "</li>
                            <li>" . __( "Customize the VDB integration with your brand and website colors", 'vdb-jewelry-import' ) . "</li>
                          </ul>
                        </div>
                        <div class='onboarding-first-top-right'>
                          <h2>" . __( "Instructions", 'vdb-jewelry-import' ) . "</h2>
                          <p>" . __( "To start using this plugin, you need to complete the following:", 'vdb-jewelry-import' ) . "</p>
                          <ol type='1'>
                            <li>" . __( "Enroll in a VDB API subscription plan", 'vdb-jewelry-import' ) . "</li>
                            <li>" . __( "Make sure you have the API key and token provided by VDB to connect this plugin to the VDB marketplace", 'vdb-jewelry-import' ) . "</li>
                          </ol>
                          <p>" . __( "If you need help with any of the above steps, please reach out to custom", 'vdb-jewelry-import' ) . " <a href='mailto:customapp.support@vdbapp.com'>customapp.support@vdbapp.com</a></p>
                        </div>
                     </div>
                </div>
                <div class='onboarding-step-right onboarding-step-1-bottom'>
                     <div class='onboarding-step-bg'>
                          <div class='onboarding-image-block-wrapper'>
                               <div class='onboarding-image-block'>
                                    <img src='" . VDB_JEWELRY_IMPORT_PLUGIN_URL . "admin/images/onboarding-1.jpg' alt=''>
                                    <p>" . __( "Improve your product page with advanced media, and convert your catalog into a powerful jewelry search engine with 600+ refined options" ) . "</p>
                               </div>
                               <div class='onboarding-image-block'>
                                    <img src='" . VDB_JEWELRY_IMPORT_PLUGIN_URL . "admin/images/onboarding-2.jpg' alt=''>
                                    <p>" . __( "Designers, manufactures, wholsalers and retailers do more business together, and avoid any inventory risks using shared inventory strategy", 'vdb-jewelry-import' ) . "</p>
                               </div>
                               <div class='onboarding-image-block'>
                                    <img src='" . VDB_JEWELRY_IMPORT_PLUGIN_URL . "admin/images/onboarding-3.jpg' alt=''>
                                    <p>" . __( "Automate and insure high-value shipments and order updates. Save money with the most competitive rates offered by UPS/Parcel Pro", 'vdb-jewelry-import' ) . "</p>
                               </div>
                          </div>
                     </div>
                </div>
                <p class='onboarding-step-button'>
                    " . wp_nonce_field('jewelry_import_step_1_nonce', 'jewelry_import_step_1_nonce_field') . "
                    <button name='jewelry_import_step_1' type='submit' value='" . __('Check Compatibility', 'vdb-jewelry-import') . "'>" . __('Check Compatibility', 'vdb-jewelry-import') . "</button>
                </p>
           </div>";

        echo $html;
    }

    public static function onboarding_step_2_view() {

        /**
         * Go Forward on Compatibility check pass
         */
        if (isset($_POST['jewelry_import_step_2'])) {
            Vdb_Jewelry_Import_Save_Settings::save_onboarding_step_2_callback();
        }

        $compatibility_check_pass = $disable = $html = '';

        $jewelry_import_minimum_requirements = (new Vdb_Jewelry_Import)->get_minimum_requirements();
        $jewelry_import_env_values           = self::get_current_environment_values();
        $jewelry_import_active_plugins       = self::check_active_plugins();

        $jewelry_import_mem_limit_prefix = preg_replace('/[0-9]+/', '', $jewelry_import_env_values['memory_limit']);

        if ('G' == $jewelry_import_mem_limit_prefix) {
            $jewelry_import_env_values['memory_limit'] = (float)$jewelry_import_env_values['memory_limit'] * 1024 . " M";
        }

        /**
         * Check if necessary plugins are active or not.
         */
        if( 'active' != $jewelry_import_active_plugins['woocommerce'] || 
            'active' != $jewelry_import_active_plugins['contact_form_7'] ||
            'active' != $jewelry_import_active_plugins['featured_image_by_url'] ){
            
            $compatibility_check_pass   = 'disabled=disabled';
            $disable                    = 'disable';
            
        }

        /**
         * Check if Minimum Requirements are fulfilled or not
         */
        if (((float)$jewelry_import_env_values['ram_size'] < (float)$jewelry_import_minimum_requirements['ram_size']) || ((float)$jewelry_import_env_values['memory_limit'] < (float)$jewelry_import_minimum_requirements['memory_limit'] || - 1 == $jewelry_import_env_values['memory_limit']) || ((float)$jewelry_import_env_values['max_execution_time'] < (float)$jewelry_import_minimum_requirements['max_execution_time']) || ((float)$jewelry_import_env_values['PHP_Version'] < (float)$jewelry_import_minimum_requirements['PHP_Version']) || ((float)$jewelry_import_env_values['WP_Version'] < (float)$jewelry_import_minimum_requirements['WP_Version'])) {

            $compatibility_check_pass = 'disabled=disabled';
            $disable = 'disable';

        }

        if ('disable' != $disable) {
            $html = "<input type='hidden' value='step_2_done' name='jewelry_import_step_2'>";
        }

        //$disable = $compatibility_check_pass = '';
        $html .= "<h1>" . __( ( new Vdb_Jewelry_Import_Constants )->ONBOARDING_TITLE, 'vdb-jewelry-import' )  . "</h1><div class='onboarding-step-wrapper'>                
           <div class='onboarding-step-left'>
                <div class='onboarding-step-bg'>
                     <div class='compatibility-checker-box'>
                          <h3>" . __("PHP Compatibility check", 'vdb-jewelry-import') . "</h3>
                          <table border='0' class='compatibility-checker-table'>
                            <tr>
                              <th></th>
                              <th>" . __("Current Value", 'vdb-jewelry-import') . "</th>
                              <th>" . __("Minimum Expected Value", 'vdb-jewelry-import') . "</th>
                            </tr>
                            <tr>
                                <th><i class='fas fa-" . (((float)$jewelry_import_env_values['ram_size'] >= (float)$jewelry_import_minimum_requirements['ram_size']) ? 'check' : 'times') . "'></i> " . __("RAM Size", 'vdb-jewelry-import') . "</th>
                                <td>" . round($jewelry_import_env_values['ram_size'] / 1048576, 2) . " G</td>
                                <td>" . $jewelry_import_minimum_requirements['ram_size'] . " G</td>
                            </tr>
                            <tr>
                                <th><i class='fas fa-" . (((float)$jewelry_import_env_values['memory_limit'] >= (float)$jewelry_import_minimum_requirements['memory_limit']) ? 'check' : 'times') . "'></i> " . __("Memory Limit", 'vdb-jewelry-import') . "</th>
                                <td>" . $jewelry_import_env_values['memory_limit'] . "</td>
                                <td>" . $jewelry_import_minimum_requirements['memory_limit'] . " </td>
                            </tr>
                            <tr>
                                <th><i class='fas fa-" . (((float)$jewelry_import_env_values['max_execution_time'] >= (float)$jewelry_import_minimum_requirements['max_execution_time']) || (-1 == $jewelry_import_env_values['memory_limit']) ? 'check' : 'times') . "'></i> " . __("Max Execution Time", 'vdb-jewelry-import') . "</th>
                                <td>" . $jewelry_import_env_values['max_execution_time'] . "</td>
                                <td>" . $jewelry_import_minimum_requirements['max_execution_time'] . " </td>
                            </tr>
                            <tr>
                                <th><i class='fas fa-" . (((float)$jewelry_import_env_values['PHP_Version'] >= (float)$jewelry_import_minimum_requirements['PHP_Version']) ? 'check' : 'times') . "'></i> " . __("PHP Version ", 'vdb-jewelry-import') . "</th>
                                <td>" . $jewelry_import_env_values['PHP_Version'] . "</td>
                                <td>" . $jewelry_import_minimum_requirements['PHP_Version'] . " </td>
                            </tr>
                            <tr>
                                <th><i class='fas fa-" . (((float)$jewelry_import_env_values['WP_Version'] >= (float)$jewelry_import_minimum_requirements['WP_Version']) ? 'check' : 'times') . "'></i> " . __("WordPress Version ", 'vdb-jewelry-import') . "</th>
                                <td>" . $jewelry_import_env_values['WP_Version'] . "</td>
                                <td>" . $jewelry_import_minimum_requirements['WP_Version'] . " </td>
                            </tr>
                            <tr>
                                <th><i class='fas fa-" . ($jewelry_import_env_values['wp_spawning'] == $jewelry_import_minimum_requirements['wp_spawning'] ? 'check' : 'times') . "'></i> " . __("Cron Spawning", 'vdb-jewelry-import') . "</th>
                                <td>" . $jewelry_import_env_values['wp_spawning'] . "</td>
                                <td>" . $jewelry_import_minimum_requirements['wp_spawning'] . " </td>
                            </tr>
                          </table>
                     </div>
                     <div class='compatibility-checker-box'>
                          <h3>" . __("Plugin Checker", 'vdb-jewelry-import') . "</h3>
                          <table border='0' class='compatibility-checker-table'>
                            <tr>
                              <th></th>
                              <th>" . __("Status", 'vdb-jewelry-import') . "</th>
                            </tr>
                            <tr>
                                <th><i class='fas fa-" . ($jewelry_import_active_plugins['woocommerce'] == 'active' ? 'check' : 'times') . "'></i> <a href='https://wordpress.org/plugins/woocommerce/' target='_blank'>" . __("WooCommerce", 'vdb-jewelry-import') . "</a></th>
                                <td>" . ucfirst($jewelry_import_active_plugins['woocommerce']) . " </td>
                            </tr>
                            <tr>
                                <th><i class='fas fa-" . ($jewelry_import_active_plugins['contact_form_7'] == 'active' ? 'check' : 'times') . "'></i> <a href='https://wordpress.org/plugins/contact-form-7/' target='_blank'>" . __("Contact Form 7", 'vdb-jewelry-import') . "</a></th>
                                <td>" . ucfirst($jewelry_import_active_plugins['contact_form_7']) . " </td>
                            </tr>
                            <tr>
                                <th><i class='fas fa-" . ( $jewelry_import_active_plugins['featured_image_by_url'] == 'active' ? 'check' : 'times' ) . "'></i> <a href='https://wordpress.org/plugins/featured-image-by-url/' target='_blank'>" . __( "Featured Image By URL", "vdb-gemstone" ) . "</a></th>
                                <td>". ucfirst( $jewelry_import_active_plugins['featured_image_by_url'] ) ." </td>
                            </tr>
                            <tr>
                                <th><i class='fas fa-" . ($jewelry_import_active_plugins['yith_woocommerce_wishlist'] == 'active' ? 'check' : 'times') . "'></i> <a href='https://wordpress.org/plugins/yith-woocommerce-wishlist/' target='_blank'>" . __("YITH WooCommerce Wishlist", 'vdb-jewelry-import') . "</a></th>
                                <td>" . ucfirst($jewelry_import_active_plugins['yith_woocommerce_wishlist']) . " </td>
                            </tr>
                          </table>
                     </div>
                </div>
           </div>

           <div class='onboarding-step-right'>
                <div class='onboarding-step-bg'>
                     <h2>" . __( "Why is compatibility check needed?", 'vdb-jewelry-import' ) . "</h2>
                     <h4>" . __( "To provide you a hassle-free experience, we provide a compatibility check to confirm if:", 'vdb-jewelry-import' ) . "</h4>
                     <ul>
                          <li>" . __( "The plugin is compatible with your version of WordPress.", 'vdb-jewelry-import' ) . "</li>
                          <li>" . __( "Your server environment is ready for the plugin to be installed.", 'vdb-jewelry-import' ) . "</li>
                          <li>" . __( "If any upgrades are needed for your server to run the plugin smoothly.", 'vdb-jewelry-import' ) . "</li>
                          <li>" . __( "If you have all the required WP plugins installed.", 'vdb-jewelry-import' ) . "</li>
                     </ul>
                </div>
           </div>
           

           <hr>

           <div class='onboarding-step-wrapper'>
           <div class='onboarding-step-full'>
                <div class='onboarding-step-full-heading'>
                     <p><b>" . __( "Verify Ownership", 'vdb-jewelry-import' ) . "</b></p>
                     <p>" . __( "Please verify by entering API Key and Access Token provided to you by Virtual Diamond Boutique.<br/>Don't have credentials? <a href='https://www.vdbapp.com/' target='_blank'>Get Keys</a>", 'vdb-jewelry-import' ) . "</p>
                </div>
                
                <div class='onboarding-field-wrapper'>
                     <div class='onboarding-field'>
                          <div class='onboarding-field-left'>
                              <label for='rb_api'>Jewelry Import API KEY:</label>
                          </div>
                          <div class='onboarding-field-right'>
                              <input type='text' name='jewelry_import_general[api]' id='rb_api' required='required'>
                              <span>" . __("Jewelry Import API Key provided by VDB needs to enter here.", 'vdb-jewelry-import') . "</span>
                          </div>
                     </div>
                     <div class='onboarding-field'>
                          <div class='onboarding-field-left'>
                              <label for='rb_token'>Jewelry Import Access Token:</label>
                          </div>
                          <div class='onboarding-field-right'>
                              <input type='text' name='jewelry_import_general[token]' id='rb_token' required='required'>
                              <span>" . __("Jewelry Import Access Token given by VDB needs to enter here.", 'vdb-jewelry-import') . "</span>
                          </div>
                     </div>
                     <div class='onboarding-field apv-api-key'>
                          <div class='onboarding-field-left'>
                              <label for='rb_apv_api'>Alpha Vantage API Key:</label>
                          </div>
                          <div class='onboarding-field-right'>
                              <input type='text' name='jewelry_import_general[apv_api_key]' id='rb_apv_api' required='required'>
                              
                              <span>" . __("We use Alpha Vantage Services to update prices. Get a free Alpha Vantage ", 'vdb-jewelry-import') . "<a href='#'>API key</a>.</span>
                          </div>
                     </div>
                </div>
           </div>
           <div class='onboarding-step-button'>
                    " . wp_nonce_field('jewelry_import_step_2_nonce', 'jewelry_import_step_2_nonce_field') . "
                    <button name='jewelry_import_step_2 $disable' type='submit' $compatibility_check_pass value='" . __('Authenticate', 'vdb-jewelry-import') . "'>" . __('Authenticate & Get Started', 'vdb-jewelry-import') . "</button>
                </div>
      </div>";

        echo $html;
    }

    public static function get_current_environment_values() {

        require ABSPATH . 'wp-includes/version.php';

        $free           = shell_exec('free');
        $free           = (string)trim($free);
        $free_arr       = explode("\n", $free);
        $mem            = explode(" ", $free_arr[1]);
        $mem            = array_filter($mem, function ($value) {
                            return ($value !== null && $value !== false && $value !== '');
                        }); // removes nulls from array
        $mem            = array_merge($mem); // puts arrays back to [0],[1],[2] after filter removes nulls
        $memtotal       = $mem[1];
        $wp_spawning    = 'Enabled'; //Default value

        /**
         * Check if WP CRON Spawning is enable or not.
         */
        if ( defined('DISABLE_WP_CRON') ) {
            switch (DISABLE_WP_CRON) {

                case 0:
                    $wp_spawning = 'Enabled';
                break;

                default:
                    $wp_spawning = 'Disabled';
                break;
            }
        }
        
        /**
         * Check for WP CRON Spawning ends here.
         */

        /**
         * Return whole Array of Current Environment Values.
         */
        return [
            'memory_limit'        => ini_get('memory_limit'), 
            'max_execution_time'  => ini_get('max_execution_time'), 
            'ram_size'            => $memtotal, 
            'wp_spawning'         => $wp_spawning, 
            'WP_Version'          => $wp_version, 
            'PHP_Version'         => phpversion()
          ];
    }

    public static function check_active_plugins() {

        $woocommerce = $contact_form_7 = $fetured_image_by_url = $yith_woocommerce_wishlist = 'Not Installed';
        
        $woocommerce_plugin_path                = WP_PLUGIN_DIR . '/woocommerce/woocommerce.php';
        $contact_form_7_plugin_path             = WP_PLUGIN_DIR . '/contact-form-7/wp-contact-form-7.php';
        $featured_image_by_url_plugin_path      = WP_PLUGIN_DIR . '/featured-image-by-url/featured-image-by-url.php';
        $yith_woocommerce_wishlist_plugin_path  = WP_PLUGIN_DIR . '/yith-woocommerce-wishlist/init.php';
        
        if( file_exists( $woocommerce_plugin_path ) ){
            $woocommerce = 'inactive';
        }
        
        if( file_exists( $contact_form_7_plugin_path ) ){
            $contact_form_7 = 'inactive';
        }
        
        if( file_exists( $featured_image_by_url_plugin_path) ){
            $fetured_image_by_url = 'inactive';
        }

        if( file_exists( $yith_woocommerce_wishlist_plugin_path) ){
            $yith_woocommerce_wishlist = 'inactive';
        }
        
        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $woocommerce = 'active';
        }
        
        if ( in_array( 'contact-form-7/wp-contact-form-7.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $contact_form_7 = 'active';
        }
        
        if ( in_array( 'featured-image-by-url/featured-image-by-url.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $fetured_image_by_url = 'active';
        }

        if ( in_array( 'yith-woocommerce-wishlist/init.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $yith_woocommerce_wishlist = 'active';
        }

        return [
            'woocommerce'               => $woocommerce,
            'contact_form_7'            => $contact_form_7,
            'featured_image_by_url'     => $fetured_image_by_url,
            'yith_woocommerce_wishlist' => $yith_woocommerce_wishlist,
        ];
    }
}