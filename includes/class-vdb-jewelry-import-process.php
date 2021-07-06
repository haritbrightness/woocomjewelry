<?php
class Vdb_Jewelry_Import_Process {

    protected $alpha_vantage_key;
    protected $general_setting;
    protected $api;
    protected $token;
    protected $import_type;
    protected $setting;
    protected $logger;
    protected $exchange_rate;
    protected $product_cat;
    protected $product_tag;
    protected $stop_import_process;
    protected $import_type_slug;

    public function __construct( $import_type = '' ) {
        
        $this->stop_import_process  = false;
        $this->general_setting      = (new Vdb_Jewelry_Import)->get_general_settings();
        $this->alpha_vantage_key    = $this->general_setting['apv_api_key'];
        $this->api                  = $this->general_setting['api'];
        $this->token                = $this->general_setting['token'];
        
        $this->exchange_rate        = isset($this->general_setting['exchange_rate']) ? $this->general_setting['exchange_rate'] : 1;
        $this->import_type          = $import_type;
        $this->product_cat          = array();
        $this->product_tag          = "";
        $this->import_type_slug     = strtolower( $this->import_type );

        if( empty( $this->api ) || empty( $this->token )){
            Vdb_Jewelry_Import::logger( __( "API and Token are required.", "vdb-jewelry-import" ),  'checked' );
            $this->stop_import_process = true;
            return true;
        }

        if( $this->import_type == 'jewelry' ){
            $this->setting      = (new Vdb_Jewelry_Import)->jewelry_import_ring_get_general_settings();
        }else{
            Vdb_Jewelry_Import::logger( __( "Arguments | Data missing", "vdb-jewelry-import" ),  $this->logger, $this->logfile );
            $this->stop_import_process = true;
            return true;
        }

        $this->logger   = isset($this->setting['logger']) ? $this->setting['logger'] : false;
        $this->logfile  = $this->import_type;
    }

    /*
    *Entry point to start import process for any import_type like jewelry
    */
    public function import_process(){
        
        if( $this->stop_import_process ){
            return true;
        }

        $this->product_cat = $this->category_mapping();

        update_option( 'vdb_ji_' . $this->import_type_slug . '_import_status', 'import' );
        update_option( 'vdb_ji_' . $this->import_type_slug . '_last_updated', date( 'Y-m-d h:i:s', time() ) );

        /*Step 1 : Generate URL with query parameter*/
        $url                = $this->request_query_builder();
        update_option( 'vdb_ji_' . $this->import_type_slug . '_last_updated', date( 'Y-m-d h:i:s', time() ) );
        
        /*Step 2 : Get jewelry_ids to count the total records*/
        $vdb_ji_jewelry_ids = get_option('vdb_ji_jewelry_ids');
        $jewelry_ids = $vdb_ji_jewelry_ids['jewelry_id'];

        $total_records      = count($jewelry_ids);

        /*If there are no records, mark import process as finished and return*/
        if($total_records == 0){
            Vdb_Jewelry_Import::logger(  __( "Import process has no records. Mark as finished", "vdb-jewelry-import" ),  $this->logger, $this->logfile );
            update_option( 'vdb_ji_' . $this->import_type_slug . '_import_status', 'finish' );
            return true;
        }

        update_option( 'vdb_ji_' . $this->import_type_slug . '_last_updated', date( 'Y-m-d h:i:s', time() ) );

        $page_size          = ( new Vdb_Jewelry_Import_Constants )->FETCH_PAGE_SIZE;
        $pages              = ceil( $total_records / $page_size );

        /*Step 3 : Based on import type, create a csv file name to store the data that will be fetched*/
        switch ( $this->import_type ) {
            
            case 'jewelry': {
                $file_name      = 'storage/jewelry/jewelry_' .date("Y-m-d_H:i:s") . '.csv';
                break;
            }
        }
        
        $file_path          = VDB_JEWELRY_IMPORT_PLUGIN_PATH . $file_name;      

        update_option( 'vdb_ji_' . $this->import_type_slug . '_last_updated', date( 'Y-m-d h:i:s', time() ) );
        update_option( 'vdb_ji_' . $this->import_type_slug . '_last_csv_path', $file_path );

        Vdb_Jewelry_Import::logger(  __( "Total ".$this->import_type.": ", "vdb-jewelry-import" ) . $total_records ."\n\n".__( "CSV file path: ", "vdb-jewelry-import" ) . $file_path."\n\n". __( "Total Pages: ", "vdb-jewelry-import" ) . $pages,  $this->logger, $this->logfile );

        /*
        Step 5 : Loop through number of pages found as per above API call and fetch records, export to csv by calling specific export function based on import type
        */
        for( $i = 1; $i <= $pages; $i++ ){

            $fetch_records = $this->api_call( $url, $i, $page_size, false );

            update_option( 'vdb_ji_' . $this->import_type_slug . '_last_updated', date( 'Y-m-d h:i:s', time() ) );
            
            if( false !== $fetch_records ){
                
                if( $this->import_type == 'jewelry' ){
                    
                    $this->export_to_csv_jewelry( $fetch_records, $file_path, $i );

                }

            }

            update_option( 'vdb_ji_' . $this->import_type_slug . '_last_updated', date( 'Y-m-d h:i:s', time() ) );

            Vdb_Jewelry_Import::logger( __( "CSV exported for page: ", "vdb-jewelry-import" ) . $i,  $this->logger, $this->logfile );

        }
       
        /*Step 6 : Read data from exported csv file and create products*/
        if( file_exists( $file_path ) ){
            
            Vdb_Jewelry_Import::logger( __( 'Reading CSV: ', "vdb-jewelry-import" ) . $file_name . "\n\n".__( 'Creating products', "vdb-jewelry-import" ),  $this->logger, $this->logfile );

            /*We are not usiing this ids for out of stock management*/
            $new_product_ids = $this->read_and_create_products( $file_path );

        }
        /*
        Vdb_Jewelry_Import::logger( __( 'Get IDs affected in current import process', "vdb-jewelry-import" ),  $this->logger, $this->logfile );

        $new_product_ids = $this->get_reset_affected_product_ids_by_meta_key( sanitize_title( $this->product_cat ), 'get' );

        update_option( 'vdb_ji_' . $this->import_type_slug . '_last_updated', date( 'Y-m-d h:i:s', time() ) );
        */    
        
        /*Step 7 : End to end process to  update stock status*/
        /*
        if( 0 != $pages && ! empty( $new_product_ids ) ){

            update_option( 'vdb_ji_' . $this->import_type_slug . '_last_updated', date( 'Y-m-d h:i:s', time() ) );

            Vdb_Jewelry_Import::logger( __( 'Out of Stock Process Started!', "vdb-jewelry-import" ),  $this->logger, $this->logfile );

            $out_of_stock_count = $this->out_of_stock_product_process( $new_product_ids, sanitize_title( $this->product_cat ) );
        
            Vdb_Jewelry_Import::logger( __( "Out of Stock Process Completed. $out_of_stock_count Products set to Out of Stock!", "vdb-jewelry-import" ),  $this->logger, $this->logfile );

            Vdb_Jewelry_Import::logger( __( 'Resetting Meta', "vdb-jewelry-import" ),  $this->logger, $this->logfile );
            $this->get_reset_affected_product_ids_by_meta_key( sanitize_title( $this->product_cat ), 'reset' );
        }
        */
        update_option( 'vdb_ji_' . $this->import_type_slug . '_last_updated', date( 'Y-m-d h:i:s', time() ) );
        update_option( 'vdb_ji_' . $this->import_type_slug . '_import_status', 'finish' );
        Vdb_Jewelry_Import::logger( __( 'Import process completed', "vdb-jewelry-import" ),  $this->logger, $this->logfile );
    }

    /*
    * Re-import backup CRON for Ring builder 
    */
    public function reimport_process(){

        if( $this->stop_import_process ){
            return true;
        }
        
        $this->product_cat = $this->category_mapping();

        /*Logic to check whether to proceed with re-import cron or not start here*/
        $current_timestamp = date( 'Y-m-d h:i:s', time() );
        $last_updated_on   = get_option( 'vdb_ji_' . $this->import_type_slug . '_last_updated' );
        $time_difference   = (int)round(abs( strtotime( $current_timestamp ) - strtotime( $last_updated_on ) ) / 60,2);
        $import_status     = get_option( 'vdb_ji_' . $this->import_type_slug . '_import_status' );

        if( $time_difference < 15 || 'finish' == $import_status ){
            /*Reimport cron will not proceed*/
            Vdb_Jewelry_Import::logger( __( "Reimport process unable to proceed", "vdb-jewelry-import" ) . "\n\n" . __( "Time Difference: ", "vdb-jewelry-import" ) . $time_difference . "\n\n" . __( "Import Status: ", "vdb-jewelry-import" ) . $import_status,  $this->logger, $this->logfile );
            return true;
        }
        /*Logic to check whether to proceed with re-import cron or not end here*/


        $vdb_ji_last_sku = get_option( 'vdb_ji_' . $this->import_type_slug. '_last_sku' );
        update_option( 'vdb_ji_' . $this->import_type_slug . '_import_status', 'import' );
        update_option( 'vdb_ji_' . $this->import_type_slug . '_last_updated', date( 'Y-m-d h:i:s', time() ) );
        

        /*Step 2 : Get last CSV path from WP options*/
        $file_path          = get_option( 'vdb_ji_' . $this->import_type_slug . '_last_csv_path' );
        Vdb_Jewelry_Import::logger( __( "Last CSV file path: ", "vdb-jewelry-import" ) . $file_path,  $this->logger, $this->logfile );
       
        /*Step 3 : Read data from exported csv file and create products*/
        if( file_exists( $file_path ) ){
        
            Vdb_Jewelry_Import::logger( __( 'Reading CSV (Reimport): ', "vdb-jewelry-import" ) . $file_path . "\n\n".__( 'Creating products (Reimport)', "vdb-jewelry-import" ),  $this->logger, $this->logfile );

            update_option( 'vdb_ji_' . $this->import_type_slug . '_last_updated', date( 'Y-m-d h:i:s', time() ) );

            /*We are not usiing this ids for out of stock management*/
            $new_product_ids = $this->read_and_create_products( $file_path, $vdb_ji_last_sku, true );
        }
        
        /*
        Vdb_Jewelry_Import::logger( __( 'Get IDs affected in current re-import process', "vdb-jewelry-import" ),  $this->logger, $this->logfile );
        $new_product_ids = $this->get_reset_affected_product_ids_by_meta_key( sanitize_title( $this->product_cat ), 'get' );

        update_option( 'vdb_ji_' . $this->import_type_slug . '_import_status', 'finish' );
        */

        /*Step 4 : End to end process to update stock status*/
        /*
        if( ! empty( $new_product_ids ) ){
            
            update_option( 'vdb_ji_' . $this->import_type_slug . '_last_updated', date( 'Y-m-d h:i:s', time() ) );
                
            Vdb_Jewelry_Import::logger( __( 'Out of Stock Process Started!', "vdb-jewelry-import" ),  $this->logger, $this->logfile );

            update_option( 'vdb_ji_' . $this->import_type_slug . '_last_updated', date( 'Y-m-d h:i:s', time() ) );

            $out_of_stock_count = $this->out_of_stock_product_process( $new_product_ids, sanitize_title( $this->product_cat ) );
            
            Vdb_Jewelry_Import::logger( __( "Out of Stock Process Completed. $out_of_stock_count Products set to Out of Stock!", "vdb-jewelry-import" ),  $this->logger, $this->logfile );

            Vdb_Jewelry_Import::logger( __( 'Resetting meta on re-import', "vdb-jewelry-import" ),  $this->logger, $this->logfile );
            $this->get_reset_affected_product_ids_by_meta_key( sanitize_title( $this->product_cat ), 'reset' );
        }
        */
        update_option( 'vdb_ji_' . $this->import_type_slug . '_last_updated', date( 'Y-m-d h:i:s', time() ) );

        Vdb_Jewelry_Import::logger( __( 'Re Import process completed', "vdb-jewelry-import" ),  $this->logger, $this->logfile );

    }

    /*
    *Category mapping logical implementation
    */
    public function category_mapping(){
        $jewelry_search_parameters = get_option('vdb_ji_jewelry_search_parameters');
        
        $cat_mappings_data = (new Vdb_Jewelry_Import)->jewelry_import_get_cat_mappings_settings();
        
        $product_cat_terms = array();

        foreach ($jewelry_search_parameters['jewelry'] as $key => $value) {

            if($key == 'jewelry_styles' && is_array($value)){
                foreach ($value as $jewelry_key => $jewelry_value) {
                    $product_cat_terms[] = $cat_mappings_data[$jewelry_key][$jewelry_key];
                    if(is_array($jewelry_value) && !empty($jewelry_value)){
                        foreach ($jewelry_value as $single_key => $single_value) {
                            $single_value = strtolower( str_replace(' ', '-', $single_value) );
                            $product_cat_terms[] = $cat_mappings_data[$jewelry_key][$single_value];
                        }
                    }
                }
            }
        }
        $product_cat_terms = array_filter($product_cat_terms);
        
        return $product_cat_terms;
    }

    /*
    *Check whether product exist or not using the sku field
    */
    public function check_product_by_sku( $sku ){

        global $wpdb;

        $product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", (float)( trim( $sku ) ) ) );

        if ( ! empty( $product_id ) ) {
            return $product_id;
        }

        return false;

    }

    /*
    *Callback function to update price as per conversion price recevied via api (which will be in options in 'exchange_rate' key )
    *For different import type there may be possibilities that the price field will be on different index.
    *For easy to use, we just need to pass the array with the index of two price parameters
    */
    public function convert_to_price( $data, $index_one = 5, $index_two = 6 ){

        $conversion_price = !empty( $this->exchange_rate  ) ? (float) $this->exchange_rate  : 1;

        if( ! empty( $conversion_price ) && 'USD' != get_option( 'woocommerce_currency' ) ){

            $data[$index_one]   = $data[$index_one] * $conversion_price; // Price Per Caret
            $data[$index_two]   = $data[$index_two] * $conversion_price; // Total Price
        }
        return $data;
    }


    /*
    * Callback function to check whether inserting product has price or price less than 26
    */
    private function jewelry_is_excluded( $data ){

        # Exclude diamonds having no price or less than 26 $ price
        return ( is_null( $data->total_sales_price ) || $data->total_sales_price < 26 ) ? true : false;

    }


    /*
    * Set out of stock status to the products which are not fetched while import process for the specific import type
    * If there are 20 products of 'Diamonds' category in wP and in import process 10 products are fetched then other then 10 products rest all other products status will be set to 'outofstock'
    */

    private static function out_of_stock_product_process( $new_product_ids = array(), $product_cat = "", $product_tag = "" ){
        $out_of_stock_array = [];
        $count_out_of_stock = 0;
        
        $args = array (
            'posts_per_page'    => -1,
            'post_type'         => 'product',
            'fields'            => 'ids',
            'tax_query'         => array(
                                        array(
                                            'taxonomy'  => 'product_cat',
                                            'field'     => 'slug',
                                            'terms'     => $product_cat //pass your cat term name here
                                        )
                                    )
        );

        if( !empty( $product_tag )){
            $args['tax_query'][] = array(
                                            'taxonomy'  => 'product_tag',
                                            'field'     => 'slug',
                                            'terms'     => $product_tag //pass your tag term name here
                                        );
        }

        $current_ids = new WP_Query( $args );
        
      
        foreach ( $current_ids->posts as $post_id ) {

            if( ! in_array( $post_id, $new_product_ids ) ){

                array_push( $out_of_stock_array, $post_id );

            }

        }

        foreach ( $out_of_stock_array as $old_product_id ) {

            // 1. Updating the stock quantity
            update_post_meta( $old_product_id, '_stock', 0 );

            // 2. Updating the stock status
            update_post_meta( $old_product_id, '_stock_status', wc_clean( 'outofstock' ) );

            // 3. Updating post term relationship
            wp_set_post_terms( $old_product_id, 'outofstock', 'product_visibility', true );

            // And finally (optionally if needed)
            wc_delete_product_transients( $old_product_id ); // Clear/refresh the variation cache

            $count_out_of_stock++;

        }

        return $count_out_of_stock;

    }

    /*
    * General query builder function to manage all other sub query builder functions based on import_type
    * query_builder_jewelry()
    */
    public function request_query_builder(){
        
        if( $this->import_type == 'jewelry' ){
            
            /*get query build based on jewelry ids store in options*/
            return ( new Vdb_Jewelry_Import_Constants )->RINGS_API_ENDPOINT . $this->query_builder_jewelry(array(), 'by_id');
            //return ( new Vdb_Jewelry_Import_Constants )->RINGS_API_ENDPOINT . $this->query_builder_jewelry();
        }
    }


    /*
    * Callback function to built the query paramters for jewelry based on search setting
    */
    public function query_builder_jewelry($query_paramters = array(), $type = 'by_search_param' ){

        $query_string = "";
        /*This logic will be useful when import process is ongoing*/
        if($type == 'by_id'){
            $jewelry_ids = get_option('vdb_ji_jewelry_ids');
            if(is_array($jewelry_ids['jewelry_id']) && !empty($jewelry_ids['jewelry_id'])){
                foreach ($jewelry_ids['jewelry_id'] as $key => $id) {
                    $query_string .= "jewelry_ids[]={$id}&";
                }
            }
            return $query_string;
        }

        /*This logic will be useful when search process is onging*/
        $search_settings        = ( new Vdb_Jewelry_Import() )->jewelry_import_ring_get_search_settings();
        $disallow_without_image = isset($this->setting['without_image']) ? $this->setting['without_image'] : '';

        if ($disallow_without_image == "checked") {
            $query_string .= "with_images=true&";
        }
        
        /*Generate Query parameters based on posted data*/
            foreach ($query_paramters['jewelry'] as $key => $value) {

                if($key == 'jewelry_styles' && is_array($value)){
                    
                    foreach ($value as $jewelry_key => $jewelry_value) {
                        if(is_array($jewelry_value) && !empty($jewelry_value)){
                            foreach ($jewelry_value as $single_key => $single_value) {
                                $query_string .= "jewelry_styles[{$jewelry_key}][]={$single_value}&";
                            }
                        }else{
                            $query_string .= "jewelry_styles[{$jewelry_key}][]=&";
                        }
                    }

                }else if( $key == 'metals' && is_array($value) ){

                    foreach ($value as $metals_index_key => $metals_value) {
                        if(is_array($metals_value) && !empty($metals_value)){
                            foreach ($metals_value as $metal_key => $metal_value) {
                                if(is_array($metal_value) && !empty($metal_value)){
                                    foreach ($metal_value as $single_key => $single_value) {
                                        $query_string .= "metals[][{$metal_key}][]={$single_value}&";
                                    }
                                }else{
                                    $query_string .= "metals[][{$metal_key}][]=&";
                                }
                            }
                        }
                    }

                }else if( $key =='brands' && is_array($value) ){
                    foreach ($value as $brand_key => $brand_value) {
                        $query_string .= "brands[]={$brand_value}&";
                    }
                }else{
                    $query_string .= "{$key}={$value}&";
                }
            }
        /*Generate Query parameters based on posted data end*/
        return $query_string;
    }
        
    /*
    * Final api call to collect data by passing url with query parameters for jewelry
    * Response will be provided by checking import_type
    */
    private function api_call( $url, $page_number = 1, $page_size = 0, $get_count = true, $retry = false ){

        $api_key            = $this->api;
        $token              = $this->token;

        if( !$retry ){
            $url = $url . '&page_number='.$page_number.'&page_size=' . $page_size;
        }

        if( $get_count ){
            Vdb_Jewelry_Import::logger( __( "Generated API URL to fetch records: ", "vdb-jewelry-import" ) . $url,  $this->logger, $this->logfile );
        }else{
            Vdb_Jewelry_Import::logger( __( "Generated API URL to fetch total count: ", "vdb-jewelry-import" ) . $url,  $this->logger, $this->logfile );
        }

        $header = [
            'Accept'        => 'application/json',
            'Authorization' => "Token token=$token, api_key=$api_key",
        ];

        $response = wp_remote_get($url, [
            'headers' => $header,
            'body'    => array(),
            'cookies' => array(),
        ]);

        $result = wp_remote_retrieve_body($response);

        $code   = wp_remote_retrieve_response_code($response);
        
        if ( !empty( $result ) && $code == 200 ) {
            $json = json_decode($result);
            
            if( $this->import_type == 'jewelry' ){
                /*Return data for setting cum ring Import process*/
                if( $get_count ){
                    return $json->response->body->total_jewelry_found;
                }
                return $json->response->body->jewelry;
            }
        }else{
            Vdb_Jewelry_Import::logger( __( "Server responded with NULL reposnse! Retrying: ", "vdb-jewelry-import" ) . $url,  $this->logger, $this->logfile );
            return $this->api_call($url, $page_number, $page_size, $get_count, true);
        }
        
        return FALSE;
    }

    /*
    * Callback function to stored fetched data into csv for ring (jewelry)
    */
    private function export_to_csv_jewelry( $records, $file_path, $page_number ){
        $csv_data = '';
        
        try{
            $header = array(
                'Id', // SKU
                'Name',
                'Available',
                'Gem Type',
                'Vendor Id',
                'Certificate Number',
                'Price Per Carat',
                'Total Sales Price',
                'Stock Number',
                'Size',
                'Color',
                'Meas. Depth',
                'Meas. Length',
                'Meas. Width',
                'Shape',
                'Certificate URL',
                'Video URL',
                'Image URL',
                'Short Name',
                'City',
                'State',
                'Country',
                'Is Pair?',
                'Brand',
                'Metals',
                'Gallery'

            );

            if( 1 == $page_number ){
                $csv_data  .= implode(',', $header) . "\n";
            }

            foreach ($records as $single_record) {

                if ( !$this->jewelry_is_excluded( $single_record ) ) {

                    $name      = ( isset( $single_record->size ) ? $single_record->size : '' ) . " Ct. " . ( isset( $single_record->shape ) ? $single_record->shape : '' ) . " Shape Ring";

                    $shortname = ( isset( $single_record->size ) ? $single_record->size : '' );

                    $single_records_array = array(
                        "id"                => ( isset( $single_record->id ) ) ? $single_record->id : "",
                        "name"              => $name,
                        "available"         => ( isset( $single_record->available ) ) ? $single_record->available : "",
                        "gem_type"          => ( isset( $single_record->gem_type ) ) ? $single_record->gem_type : "",
                        "vendor_id"         => ( isset( $single_record->vendor_id ) ) ? $single_record->vendor_id : "",
                        "cert_num"          => ( isset( $single_record->cert_num ) ) ? $single_record->cert_num : "",
                        "price_per_carat"   => ( isset( $single_record->price_per_carat ) ) ? $single_record->price_per_carat : "",
                        "total_sales_price" => ( isset( $single_record->total_sales_price ) ) ? $single_record->total_sales_price : "",
                        "stock_num"         => ( isset( $single_record->stock_num ) ) ? $single_record->stock_num : "",


                        "size"              => ( isset( $single_record->size ) ) ? $single_record->size : "",
                        "color"             => ( isset( $single_record->color ) ) ? $single_record->color : "",
                        
                        "meas_depth"        => ( isset( $single_record->meas_depth ) ) ? $single_record->meas_depth : "",
                        "meas_length"       => ( isset( $single_record->meas_length ) ) ? $single_record->meas_length : "",
                       
                        "meas_width"        => ( isset( $single_record->meas_width ) ) ? $single_record->meas_width : "",
                        "shape"             => ( isset( $single_record->shape ) ) ? $single_record->shape : "",
                       

                        // URL's may have comma, that's why adding quote
                        "cert_url"          => '"' . ( isset( $single_record->cert_url ) ) ? $single_record->cert_url : '' . '"',
                        "video_url"         => '"' . ( isset( $single_record->video_url ) ) ? $single_record->video_url : '' . '"',
                        "image_url"         => '"' . ( isset( $single_record->image_url ) ) ? $single_record->image_url : '' . '"',

                        "short_name"        => ( isset( $shortname ) ) ? $shortname : "",
                        "city"              => ( isset( $single_record->city ) ) ? $single_record->city : "",
                        "state"             => ( isset( $single_record->state ) ) ? $single_record->state : "",
                        "country"           => ( isset( $single_record->country ) ) ? $single_record->country : "",
                    
                        "is_pair"           => ( isset( $single_record->pair ) ) ? $single_record->pair : "",
                        "brand"             => ( isset( $single_record->brand ) ) ? $single_record->brand : "",
                        "metals"            => ( isset( $single_record->metals ) ) ? $single_record->metals : "",
                        "gallery"           => ( isset( $single_record->image_urls ) ) ? implode('||', array_column($single_record->image_urls, 'image_url')): "",
                    );

                    $csv_data .= implode(',', $single_records_array) . "\n";
                }
            }


            $file = fopen( $file_path, "a" );

            fwrite($file, $csv_data);
            fclose($file);
            update_option( 'vdb_ji_' . $this->import_type_slug . '_last_updated', date( 'Y-m-d h:i:s', time() ) );
        } catch (Exception $e) {
            Vdb_Jewelry_Import::logger( 'Caught exception: ', $e->getMessage(),  $this->logger, $this->logfile );
        }
    }


    /*
    *General Read csv and create products function for all Diamonds, Rings and Gemstones
    */
    private function read_and_create_products( $file_path, $last_imported_sku = 0, $reimport = false ){
        
        $headers                    = true;
        $row_count                  = 0;
        $handle                     = fopen($file_path, "r");
        $new_product_ids            = [];
        $reimport_sku_reached       = false;
        
        if (empty($handle) === false) {
            while ( ( $data = fgetcsv( $handle, 1000, "," ) ) !== false ) {

                if($headers) { $headers = false; continue; }

                if( $reimport ){
                    if( $data[0] != $last_imported_sku && ! $reimport_sku_reached ){
                        continue;
                    }else{
                        $reimport_sku_reached = true;
                    }
                }

                $row_count++;

                if( $this->import_type == 'jewelry' ){
                
                    $inserted_post_id = $this->create_woo_products_jewelry( $data, $row_count );
                    array_push($new_product_ids, $inserted_post_id);

                }
            }

            fclose($handle);
        }

        return $new_product_ids;
    }


    /*
    *callback function to insert product from csv for gemstones
    *This functions is called from read_and_create_products()
    */
    private function create_woo_products_jewelry( $data, $count ){

        $usd_per_carat_price    = $data[6];
        $usd_total_sales_price  = $data[7];

        $data = $this->convert_to_price( $data, 6, 7 );
        
        $single_product_data = array(
            'sku'               => $data[0],
            'full_name'         => $data[1],
            'available'         => $data[2],
            'type'              => $data[3],
            'vendor_id'         => $data[4],
            'cert_num'          => $data[5],
            'price_per_carat'   => $data[6],
            'total_sales_price' => $data[7],
            'stock_number'      => $data[8],
            'size'              => $data[9],
            'color'             => $data[10],
            'meas_depth'        => $data[11],
            'meas_length'       => $data[12],
            'meas_width'        => $data[13],
            'shape'             => $data[14],
            'cert_url'          => $data[15],
            'video_url'         => $data[16],
            'image'             => $data[17],
            'short_name'        => $data[18],
            'city'              => $data[19],
            'state'             => $data[20],
            'country'           => $data[21],
            'is_pair'           => $data[22],
            'brand'             => $data[23],
            'metals'            => $data[24],
            'gallery'           => $data[25],
        );

        
        // $sku_array = array('20539794', '20539804');
        // if( in_array($single_product_data['sku'], $sku_array)){
        //     return 0;
        // }

        /*Gallery images based on response data*/
        $gallery_images = array();

        if( !empty($single_product_data['gallery']) ){
            $gallery = explode('||', $single_product_data['gallery']);
            foreach ($gallery as $key => $url) {
                $gallery_images[]['url'] = $url;
            }
        }

        /*check product based on sku*/
        $product_id = $this->check_product_by_sku( $single_product_data['sku'] );
        
        $is_new = true;
        
        if( ! empty( $product_id ) || false != $product_id ){
            $is_new = false;
        }
                
        $product_metas = [
            '_sku'              => $single_product_data['sku'],
            '_price'            => $single_product_data['total_sales_price'],
            '_regular_price'    => $single_product_data['total_sales_price'],
            '_price_usd'        => $usd_total_sales_price,
            '_carat_price_usd'  => $usd_per_carat_price,
            '_knawatfibu_url'   => $single_product_data['image'],
            '_knawatfibu_wcgallary'  => serialize($gallery_images),
            '_jewelry_stock_number'  => $single_product_data['stock_number'],
            '_jewelry_data'     => serialize( $single_product_data ),
            '_stock_status'     => 'instock',
            '_is_import_finished' => 'no',
        ];


        if( $is_new && $single_product_data['available']== 1 ){

            $product_id = wp_insert_post( array(
                'post_title'    => $single_product_data['stock_number'] . " " . $single_product_data['full_name'],
                'post_status'   => 'publish',
                'post_type'     => "product",
            ) );
            
            wp_set_object_terms( $product_id, 'simple', 'product_type' );
            wp_set_object_terms( $product_id, $single_product_data['brand'], 'jewelry_brand');
            wp_set_object_terms( $product_id, $single_product_data['metals'], 'jewelry_metal_types');
            
            $this->insert_post_meta( $product_id, $product_metas );

            Vdb_Jewelry_Import::logger( __( "New Jewelry Product Added Successfully: [ SKU:", "vdb-jewelry-import" ) . $single_product_data['sku'] . " ] ( " . $count . " )",  $this->logger, $this->logfile );
                
        }else{
            
            $this->update_post_meta( $product_id, $product_metas );

            Vdb_Jewelry_Import::logger( __( "Jewelry Product Updated Successfully: [ SKU:", "vdb-jewelry-import" ) . $single_product_data['sku'] . " ] ( " . $count . " )",  $this->logger, $this->logfile );
        }
        
        wp_set_object_terms( $product_id, $this->product_cat, 'product_cat');

        update_option( 'vdb_ji_' . $this->import_type_slug . '_last_sku', $single_product_data['sku'] );

        return $product_id;
       
    }

    private function update_post_meta( $post_id, $metadata = array() )
    {
        global $wpdb;

        $table = $wpdb->prefix . "postmeta";

        foreach( $metadata as $meta_key => $meta_value ){
            $where = array(
                'post_id'   => $post_id,
                'meta_key'  => $meta_key,
            );
            $data = array(
                'meta_value' => $meta_value,
            );

            $wpdb->update( $table, $data, $where );
            $wpdb->flush();
        }
    }

    private function insert_post_meta( $post_id, $metadata = array() ){
        global $wpdb;

        $loop_counter = 1;
        $count = count( $metadata );
        $value = "";

        foreach ( $metadata as $meta_key => $meta_value ) {
            if ( $loop_counter < $count ) {
                $value .= "( $post_id, '$meta_key', '$meta_value' ), ";
                $loop_counter++;
            } else {
                $value .= "( $post_id, '$meta_key', '$meta_value' );";
            }
        }

        $query = "INSERT INTO $wpdb->postmeta ( `post_id`, `meta_key`, `meta_value` ) VALUES $value";

        $wpdb->get_results( $query );
        $wpdb->flush();
    }

    private function get_reset_affected_product_ids_by_meta_key( $term_taxonomy = "", $action = "get" ){
        
        global $wpdb;
        
        $term_taxonomy_id = get_term_by( 'slug', sanitize_title($term_taxonomy), 'product_cat' )->term_id;
        
        if( !empty($term_taxonomy_id ) && $action == "get" ){
        
            $query = "SELECT $wpdb->posts.ID FROM $wpdb->posts LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_key = '_is_import_finished' ) LEFT JOIN $wpdb->postmeta AS mt1 ON ( $wpdb->posts.ID = mt1.post_id ) WHERE 1=1 AND ( $wpdb->term_relationships.term_taxonomy_id IN ($term_taxonomy_id) ) AND ( $wpdb->postmeta.post_id IS NULL OR ( mt1.meta_key = '_is_import_finished' AND mt1.meta_value = 'no' ) ) AND $wpdb->posts.post_type = 'product' AND ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private') GROUP BY $wpdb->posts.ID ORDER BY $wpdb->posts.post_date DESC";
         
            $result = $wpdb->get_col( $query );
            
            return $result;

        }else if( $action == "reset" ){

            $table = $wpdb->prefix . "postmeta";

            $where = array(
                'meta_key'  => '_is_import_finished',
            );
            $data = array(
                'meta_value' => 'yes',
            );

            $wpdb->update( $table, $data, $where );
        }
        return array();
        
    }
}

add_action('init', 'ji_debug_init');
function ji_debug_init(){
    if(isset($_GET['ji_debug'])){

        if($_GET['ji_debug'] == 'jewelry'){
            
            $data = new Vdb_Jewelry_Import_Process( 'jewelry' );
            $data->import_process();

        }else if($_GET['ji_debug'] == 'jewelry_r'){
            
            $data = new Vdb_Jewelry_Import_Process( 'jewelry' );
            $data->reimport_process();

        }else{
            
            echo "Please pass ji_debug argument as jewelry";
            exit();
            
        }
    }   
}