<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Vdb_Jewelry_Import_Cron_Manager class responsable to manage vdb crons
 */
class Vdb_Jewelry_Import_Cron_Manager {

    public function __construct() {

        $this->init_hooks();

    }

    public function init_hooks() {
       
        add_action ( 'init', array( $this, 'schedule_event_callback' ) );

        add_action ( 'vdb_cron_jewelry_import', array( $this, 'vdb_jewelry_import_callback' ) );
        add_action ( 'vdb_cron_jewelry_reimport', array( $this, 'vdb_jewelry_reimport_callback' ) );
        add_action ( 'vdb_cron_jewelry_exchange_rate', array( $this, 'vdb_cron_jewelry_exchange_rate_callback' ) );

    }

    public function schedule_event_callback(){

        $jewelry_setting    = (new Vdb_Jewelry_Import)->jewelry_import_ring_get_general_settings();
        $cron_time  = isset( $jewelry_setting['cron_time'] ) ? $jewelry_setting['cron_time'] : "16:00:00";
        if(empty($cron_time)){
            $cron_time = "16:00:00";
        }

        if (!wp_next_scheduled('vdb_cron_jewelry_import')) {
            
            wp_schedule_event(strtotime($cron_time), 'daily', 'vdb_cron_jewelry_import');

        }

        if (!wp_next_scheduled('vdb_cron_jewelry_exchange_rate')) {
            
            wp_schedule_event(strtotime($cron_time), 'daily', 'vdb_cron_jewelry_exchange_rate');

        }

        if (!wp_next_scheduled('vdb_cron_jewelry_reimport')) {
            
            wp_schedule_event(strtotime($cron_time), 'hourly', 'vdb_cron_jewelry_reimport');

        }
    }

    public function unschedule_event_callback( $schedule = false ){
        wp_clear_scheduled_hook('vdb_cron_jewelry_import');
        wp_clear_scheduled_hook('vdb_cron_jewelry_reimport');
        wp_clear_scheduled_hook('vdb_cron_jewelry_exchange_rate');

        if($schedule){
            $this->schedule_event_callback();
        }
    }

    
    /**
    * Jewelry Import CRON Callback
    */
    public function vdb_jewelry_import_callback(){
        
        $message = __( "Jewelry Import CRON is Started!", "vdb-jewelry-import" );
        Vdb_Jewelry_Import::logger( __( $message, "vdb-jewelry-import" ),  'checked' );

        /*Jewelry Import Process*/
        $data = new Vdb_Jewelry_Import_Process( 'jewelry' );
        $data->import_process();

    }

  
    /**
    * Jewelry Re-Import cron Callback
    */
    public function vdb_jewelry_reimport_callback(){

        $message = __( "Jewelry Re Import CRON is Started!", "vdb-jewelry-import" );
        Vdb_Jewelry_Import::logger( __( $message, "vdb-jewelry-import" ),  'checked' );

        /*Jewelry Re Import Process*/
        $data = new Vdb_Jewelry_Import_Process( 'jewelry' );
        $data->reimport_process();
    }


    /**
    * Jewelry exchange rate option update
    */
    public function vdb_cron_jewelry_exchange_rate_callback(){

        $general_setting    = (new Vdb_Jewelry_Import)->get_general_settings();
        $exchange_rate      = Vdb_Jewelry_Import::get_exchange_rate($general_setting['apv_api_key']);

        $general_setting['exchange_rate'] = $exchange_rate;
        (new Vdb_Jewelry_Import)->update_general_settings($general_setting);

        $message = __( "Jewelry exchange rate API executed", "vdb-jewelry-import" );
        Vdb_Jewelry_Import::logger( __( $message, "vdb-jewelry-import" ),  'checked' );
    }

} // end of class Vdb_Jewelry_Import_Cron_Manager

$Vdb_Jewelry_Import_Cron_Manager = new Vdb_Jewelry_Import_Cron_Manager();