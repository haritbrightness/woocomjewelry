<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.vdbapp.com/
 * @since      1.0.0
 *
 * @package    Vdb_Jewelry_Import
 * @subpackage Vdb_Jewelry_Import/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Vdb_Jewelry_Import
 * @subpackage Vdb_Jewelry_Import/includes
 * @author     Virtual Diamond Boutique <Iqbal.Brightnessgroup@gmail.com>
 */
class Vdb_Jewelry_Import {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Vdb_Jewelry_Import_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'VDB_JEWELRY_IMPORT_VERSION' ) ) {
			$this->version = VDB_JEWELRY_IMPORT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'vdb-jewelry-import';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Vdb_Jewelry_Import_Loader. Orchestrates the hooks of the plugin.
	 * - Vdb_Jewelry_Import_i18n. Defines internationalization functionality.
	 * - Vdb_Jewelry_Import_Admin. Defines all hooks for the admin area.
	 * - Vdb_Jewelry_Import_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once VDB_JEWELRY_IMPORT_PLUGIN_PATH . 'includes/class-vdb-jewelry-import-constants.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		
		require_once VDB_JEWELRY_IMPORT_PLUGIN_PATH . 'includes/class-vdb-jewelry-import-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once VDB_JEWELRY_IMPORT_PLUGIN_PATH . 'includes/class-vdb-jewelry-import-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once VDB_JEWELRY_IMPORT_PLUGIN_PATH . 'admin/class-vdb-jewelry-import-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once VDB_JEWELRY_IMPORT_PLUGIN_PATH . 'public/class-vdb-jewelry-import-public.php';
		
		require_once VDB_JEWELRY_IMPORT_PLUGIN_PATH . 'includes/class-vdb-jewelry-import-process.php';
		
		require_once VDB_JEWELRY_IMPORT_PLUGIN_PATH . 'includes/class-vdb-jewelry-import-cron-manager.php';

		$this->loader = new Vdb_Jewelry_Import_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Vdb_Jewelry_Import_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Vdb_Jewelry_Import_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Vdb_Jewelry_Import_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_admin, 'jewelry_brand_taxonomy', 0 );
		$this->loader->add_action( 'init', $plugin_admin, 'jewelry_metal_types_taxonomy', 0 );
		
		$this->loader->add_action( 'admin_init', $plugin_admin, 'redirect_to_onboarding', 11 );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'redirect_to_dashboard', 11 );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Vdb_Jewelry_Import_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'woocommerce_thankyou', $plugin_public, 'jewelry_auto_buy_request' );
		$this->loader->add_filter( 'woocommerce_add_to_cart_validation', $plugin_public, 'check_jewelry_before_add_to_cart', 10, 5 );
		$this->loader->add_action( 'woocommerce_before_cart', $plugin_public, 'check_jewelry_in_cart' );



	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Vdb_Jewelry_Import_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}


	/*
	* Jewelry Import Get General Settings
	*/
	public function get_general_settings(){
	    return get_option('jewelry_import_general_settings');
	}

	/*
	* Jewelry Import Update General Settings
	*/
	public function update_general_settings($args = array()){
	    return update_option('jewelry_import_general_settings', $args);
	}

	/*
	* Ring Builder Get Rings Settings
	*/

	public function jewelry_import_ring_get_general_settings(){
	    return get_option('jewelry_import_rings_general_settings');
	}

	public function jewelry_import_ring_get_search_settings(){
	    return get_option('jewelry_import_rings_search_settings');
	}

	public function jewelry_import_get_cat_mappings_settings(){
	    return get_option('jewelry_import_cat_mappings_settings');
	}

	public function get_minimum_requirements(){
	    $requirements = [
	        'memory_limit'          => '512',//'2048M',
	        'max_execution_time'    => '120',//'18000',
	        'wp_spawning'           => 'Enabled',
	        'ram_size'              => '4',
	        'PHP_Version'           => '7',
	        'WP_Version'            => '5',
	    ];

	    return $requirements;
	}

	/**
     *
     * Log data and process status
     *
     * @param String | JSON OBJ | Int | Float $message
     */
    public static function logger( $message, $write_log = false, $logfile = 'logger' ){

    	if(self::jewelry_import_ring_get_general_settings()['logger'] != 'checked')
    		return false;
    	

        if( $write_log == "checked" ){
            if (is_array($message)) {
                $message = json_encode($message);
            }

            $file = fopen(VDB_JEWELRY_IMPORT_PLUGIN_PATH . 'storage/'.$logfile.'.log', "a+");

            fwrite($file, "\n" . date('Y-m-d h:i:s A') . " :: " . $message);
            fclose($file);
        }
    }

	public static function infoHtml(){
		$html =
	    '<div class="jewelry-import-info-div">
	        <div class="info-left">
	            <a href="https://www.vdbapp.com" target="_blank">
	                <img src="' . VDB_JEWELRY_IMPORT_PLUGIN_URL . 'admin/images/vdb-logo.svg" >
	            </a>
	        </div>

	        <div class="info-right">
	            <h2><b>VDB Jewelry Import Extension</b></h2>
	            <p>
	                <b>Installed Version: v' . VDB_JEWELRY_IMPORT_VERSION . '</b><br>
	                Website: <a target="_blank" href="https://www.facebook.com/vdiamondb/">Facebook</a>,
	                <a target="_blank" href="https://www.linkedin.com/company/virtual-diamond-boutique">Linked In</a> and
	                <a target="_blank" href="https://twitter.com/vdbapp">Twitter</a>.<br>
	                Do you need Extension Support? Please create support ticket from <a href="https://www.vdbapp.com/contact-us/" target="_blank">here</a>
	                or <br> Please contact us on <a href="mailto:info@vdbapp.com">info@vdbapp.com</a> for quick reply.
	            </p>
	        </div>

	    </div>';

	    return $html;
	}

	public static function get_exchange_rate( $apikey = "" ){

		if(! empty($apikey) ){
			
			$to_currency = get_option('woocommerce_currency');

			$api_url =  ( new Vdb_Jewelry_Import_Constants )->ALPHA_VANTAGE_API_ENDPOINT . '&to_currency=' . $to_currency . '&apikey=' . $apikey;

            // Price Range Filter
            if ( !empty($api_url )) {
                $api_data = file_get_contents($api_url);
                $exchange_price  = (float) json_decode( $api_data, true )['Realtime Currency Exchange Rate']['5. Exchange Rate'];

                return $exchange_price;
            }
		}
		
        return '1';    
	}
}