<?php

/**
 * The admin-specific functionality of the plugin.
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
 * @author     Virtual Diamond Boutique <Iqbal.Brightnessgroup@gmail.com>
 */
class Vdb_Jewelry_Import_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->admin_load_dependencies();
	}

	/**
	 * Load the required dependencies for this plugin.admin area
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function admin_load_dependencies() {

		/**
		 * The class responsible for defining all actions that occur in the admin area onboarding process.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-vdb-jewelry-import-save-settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-vdb-jewelry-import-attributes.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-vdb-jewelry-import-onboarding.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-vdb-jewelry-import-general-settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-vdb-jewelry-import-ring-settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-vdb-jewelry-import-product-filters.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-vdb-jewelry-import-vdb-search.php';

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if (is_admin() && isset($_GET['page']) && 'vdb-jewelry-import-dashboard' == $_GET['page']) {
	        wp_enqueue_style('wp-color-picker');
	        wp_enqueue_style( 'Jewelry-import-load-fa', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css' );
	        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/vdb-jewelry-import-admin.css', array(), $this->version, 'all' );
	        wp_enqueue_style( $this->plugin_name.'-search', plugin_dir_url( __FILE__ ) . 'css/vdb-jewelry-import-search.css', array(), $this->version, 'all' );
	    }

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if (is_admin() && isset($_GET['page']) && 'vdb-jewelry-import-dashboard' == $_GET['page']) {

			if ( ! did_action( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}

            wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/vdb-jewelry-import-admin.js', array( 'jquery' ), $this->version, false );

            if( isset($_GET['tab']) && 'ring' == $_GET['tab'] && isset($_GET['section']) && 'search' == $_GET['section'] ){
            	wp_enqueue_script( $this->plugin_name.'-search', plugin_dir_url( __FILE__ ) . 'js/vdb-jewelry-import-search.js', array( 'jquery' ), $this->version, false );
            }
        }
	}


	/*
	*Redirect to dashboard if onboarding is completed and url is of onboaring
	*/
	public function redirect_to_dashboard(){
        	
    	$onboarding_status  = get_option( 'vdb_jewelry_import_onboarding' );
		$onboarding_step    = get_option( 'vdb_jewelry_import_onboarding_step' );
		$current_tab        = isset($_GET['tab']) ? $_GET['tab'] : '';
		$current_page       = isset($_GET['page']) ? $_GET['page'] : '';

		if( 'onboarding' == $current_tab && 'vdb-jewelry-import-dashboard' == $current_page && ! empty( $onboarding_status ) && 'true'== $onboarding_status ){
		                
            wp_safe_redirect( ( new Vdb_Jewelry_Import_Constants )->GENERAL_SETTING_URL );
            exit;
            
        }

	}


	/*
	*Redirect on plugin activation to onboarding steps
	*/
	public function redirect_to_onboarding(){

		if (get_option('vdb_jewelry_import_do_activation_redirect', false)) {

	        	delete_option('vdb_jewelry_import_do_activation_redirect');
	        	
	        	$onboarding_status  = get_option( 'vdb_jewelry_import_onboarding' );
        		$onboarding_step    = get_option( 'vdb_jewelry_import_onboarding_step' );

	        	$onboarding_url = VDB_JEWELRY_IMPORT_ADMIN_URL . 'admin.php?page=vdb-jewelry-import-dashboard&tab=onboarding&step=' . $onboarding_step;
	         	wp_safe_redirect($onboarding_url);
	         	exit;

	    }
	}

	
	/*
	* Register Jewelry Import menu
	*/
	public function add_admin_menu(){
		add_menu_page(
            esc_html__('VDB Jewelry Import Settings', 'vdb-jewelry-import'), 
            esc_html__('VDB Jewelry Import', 'vdb-jewelry-import'), 
            'manage_options', 
            'vdb-jewelry-import-dashboard', 
            array($this, 'admin_setting_page'), 
            VDB_JEWELRY_IMPORT_PLUGIN_URL . 'admin/images/vdb-admin-menu.png', 
            NULL
        );
	}


	/*
	* Menu setting page callback
	*/
	public function admin_setting_page() {
		
        $current_page       = isset($_GET['page']) ? $_GET['page'] : '';
        $current_tab        = isset($_GET['tab']) ? $_GET['tab'] : 'general';
        $onboarding_status  = get_option( 'vdb_jewelry_import_onboarding' );
        $onboarding_step    = get_option( 'vdb_jewelry_import_onboarding_step' );
        ?>

	        <form method="post" id="jewelry_import_save_settings" action="" enctype="multipart/form-data">
		        <?php
		        
		        if( ! empty( $onboarding_status ) && 'true' != $onboarding_status ){
		            if( ( 'onboarding' != $current_tab &&  'vdb-jewelry-import-dashboard' == $current_page ) || ( !isset( $_GET['step'] ) && 'vdb-jewelry-import-dashboard' == $current_page  ) ){
		                
		                $onboarding_url = VDB_JEWELRY_IMPORT_ADMIN_URL . 'admin.php?page=vdb-jewelry-import-dashboard&tab=onboarding&step=' . $onboarding_step;

		                wp_safe_redirect( $onboarding_url );
		                exit;
		                
		            }else{
		                
		                switch( $_GET['step'] ){
		               
		                    case '2':
		                        Jewelry_Import_Onboarding::onboarding_step_2_view();
		                        break;

		                    case '3':
		                        Jewelry_Import_Onboarding::onboarding_step_3_view();
		                        break;

		                    default :
		                        Jewelry_Import_Onboarding::onboarding_step_1_view();
		                        break;
		                }
		            }

		        }else{
		        ?>
		            <h2 class='nav-tab-wrapper'>
		                <a href="<?php echo ( new Vdb_Jewelry_Import_Constants )->GENERAL_SETTING_URL; ?>" class="nav-tab <?php echo ( 'general' == $current_tab || '' == $current_tab ) ? 'nav-tab-active' : ''; ?>"> <?php _e("Authentication Settings", 'vdb-jewelry-import'); ?></a>

		                <a href="<?php echo ( new Vdb_Jewelry_Import_Constants )->RING_SETTING_URL; ?> " class="nav-tab <?php echo ( 'ring' == $current_tab ) ? 'nav-tab-active' : ''; ?>"> <?php _e("Jewelry Settings", 'vdb-jewelry-import'); ?></a>
		            </h2>
		        <?php
			            
		            if ('vdb-jewelry-import-dashboard' == $current_page && 'ring' == $current_tab) {

		                Jewelry_Import_Ring_Settings::ring_settings_view();

		            }else{

		            	$jewelry_import_general_settings = (new Vdb_Jewelry_Import)->get_general_settings();
		                Jewelry_Import_General_Settings::jewelry_import_general_settings_view($jewelry_import_general_settings);

		            }
		        }
		        ?>
	        </form>
        <?php
	}


	public function jewelry_brand_taxonomy() {

		$labels = array(
			'name'                       => _x( 'Jewelry Brands', 'Taxonomy General Name', 'vdb-jewelry-import' ),
			'singular_name'              => _x( 'Jewelry Brand', 'Taxonomy Singular Name', 'vdb-jewelry-import' ),
			'menu_name'                  => __( 'Jewelry Brand', 'vdb-jewelry-import' ),
			'all_items'                  => __( 'All Jewelry Brands', 'vdb-jewelry-import' ),
			'parent_item'                => __( 'Parent Jewelry Brand', 'vdb-jewelry-import' ),
			'parent_item_colon'          => __( 'Parent Jewelry Brand:', 'vdb-jewelry-import' ),
			'new_item_name'              => __( 'New Jewelry Brand Name', 'vdb-jewelry-import' ),
			'add_new_item'               => __( 'Add New Jewelry Brand', 'vdb-jewelry-import' ),
			'edit_item'                  => __( 'Edit Jewelry Brand', 'vdb-jewelry-import' ),
			'update_item'                => __( 'Update Jewelry Brand', 'vdb-jewelry-import' ),
			'view_item'                  => __( 'View Item', 'vdb-jewelry-import' ),
			'separate_items_with_commas' => __( 'Separate jewelry brands with commas', 'vdb-jewelry-import' ),
			'add_or_remove_items'        => __( 'Add or remove jewelry brands', 'vdb-jewelry-import' ),
			'choose_from_most_used'      => __( 'Choose from the most used manufactures', 'vdb-jewelry-import' ),
			'popular_items'              => __( 'Popular Items', 'vdb-jewelry-import' ),
			'search_items'               => __( 'Search jewelry brands', 'vdb-jewelry-import' ),
			'not_found'                  => __( 'Not Found', 'vdb-jewelry-import' ),
			'no_terms'                   => __( 'No items', 'vdb-jewelry-import' ),
			'items_list'                 => __( 'Items list', 'vdb-jewelry-import' ),
			'items_list_navigation'      => __( 'Items list navigation', 'vdb-jewelry-import' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
		);
		register_taxonomy( 'jewelry_brand', array( 'product' ), $args );

	}

	public function jewelry_metal_types_taxonomy() {

		$labels = array(
			'name'                       => _x( 'Jewelry Metal Types', 'Taxonomy General Name', 'vdb-jewelry-import' ),
			'singular_name'              => _x( 'Jewelry Metal Types', 'Taxonomy Singular Name', 'vdb-jewelry-import' ),
			'menu_name'                  => __( 'Jewelry Metal Types', 'vdb-jewelry-import' ),
			'all_items'                  => __( 'All Jewelry Metal Types', 'vdb-jewelry-import' ),
			'parent_item'                => __( 'Parent Jewelry Metal Types', 'vdb-jewelry-import' ),
			'parent_item_colon'          => __( 'Parent Jewelry Metal Types:', 'vdb-jewelry-import' ),
			'new_item_name'              => __( 'New Jewelry Metal Types Name', 'vdb-jewelry-import' ),
			'add_new_item'               => __( 'Add New Jewelry Metal Types', 'vdb-jewelry-import' ),
			'edit_item'                  => __( 'Edit Jewelry Metal Types', 'vdb-jewelry-import' ),
			'update_item'                => __( 'Update Jewelry Metal Types', 'vdb-jewelry-import' ),
			'view_item'                  => __( 'View Item', 'vdb-jewelry-import' ),
			'separate_items_with_commas' => __( 'Separate jewelry metal types with commas', 'vdb-jewelry-import' ),
			'add_or_remove_items'        => __( 'Add or remove jewelry metal types', 'vdb-jewelry-import' ),
			'choose_from_most_used'      => __( 'Choose from the most used manufactures', 'vdb-jewelry-import' ),
			'popular_items'              => __( 'Popular Items', 'vdb-jewelry-import' ),
			'search_items'               => __( 'Search jewelry metal types', 'vdb-jewelry-import' ),
			'not_found'                  => __( 'Not Found', 'vdb-jewelry-import' ),
			'no_terms'                   => __( 'No items', 'vdb-jewelry-import' ),
			'items_list'                 => __( 'Items list', 'vdb-jewelry-import' ),
			'items_list_navigation'      => __( 'Items list navigation', 'vdb-jewelry-import' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
		);
		register_taxonomy( 'jewelry_metal_types', array( 'product' ), $args );

	}
}
