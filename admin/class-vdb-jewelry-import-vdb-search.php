<?php
/**
 * Class to manage product filters 
 */
class Jewelry_Import_VDB_Search {
	
	protected $alpha_vantage_key;
	protected $general_setting;
	protected $api;
	protected $token;
	
	public function __construct(){
		
		$this->general_setting 		= (new Vdb_Jewelry_Import)->get_general_settings();
		$this->alpha_vantage_key   	= $this->general_setting['apv_api_key'];
		$this->api 		 			= $this->general_setting['api'];
		$this->token 				= $this->general_setting['token'];

		add_action( 'wp_ajax_vdb_search', array($this, 'vdb_search_callback') );
		add_action( 'wp_ajax_vdb_schedule_import', array($this, 'vdb_schedule_import_callback') );

	}	

	/*
	* Callback function for search panel parameters . This will created array format and then build query to fetch result from API
	*/
	public function vdb_search_callback() {
   
		global $wpdb;
		
		$query_paramters = array();

		/*parsing search parameters*/
		parse_str($_POST['search_data'], $query_paramters);	

		/*formatting search parameters data for empty value as an empty array*/
		$query_paramters = $this->vdb_update_array_format ($query_paramters);
		
		/*generating query parameters based on search data to display data*/
		$jewelry_import_obj = new Vdb_Jewelry_Import_Process('jewelry');
		$query_string  		= $jewelry_import_obj->query_builder_jewelry($query_paramters);

		//$query_string  = $jewelry_import_obj->query_builder_jewelry($query_paramters, 'by_id');

		$page_size   = $query_paramters['jewelry']['page_size'];
		$page_number = $query_paramters['jewelry']['page_number'];	
		if( isset($_POST['executed_on']) && $_POST['executed_on'] == 'onsearch' ){
			$page_number = 1;
		}

		$ajax_response = array();
		$html_loop = "";
		$ajax_response['html'] = "<div class='no-record-founds'>".__( 'Result Not Found', 'vdb-jewelry-import' )."</div>";
		$ajax_response['total_jewelry_found'] = 0;
		$ajax_response['page_size']           = (int)$page_size;
		$ajax_response['page_number']         = (int)$page_number;
		$ajax_response['total_pages']         = 1;

		$url  = ( new Vdb_Jewelry_Import_Constants )->RINGS_API_ENDPOINT.$query_string;
		Vdb_Jewelry_Import::logger( __( $url, "vdb-jewelry-import" ),  'checked' );

		$ajax_response['url'] = $url;

		//$url  = ( new Vdb_Jewelry_Import_Constants )->RINGS_API_ENDPOINT.'price_total_from=100&price_total_to=300&page_size='.$page_size.'&page_number='.$page_number;

		$api_data = $this->api_call($url);
		$body     = $api_data['response']['body'];

		if( isset($body) && !empty($body) ){

			$jewelry_data = $body['jewelry'];

			$woocommerce_currency_symbol = get_woocommerce_currency_symbol();

			foreach ($jewelry_data as $key => $single_jewelry) {
				
				$single_jewelry = $jewelry_import_obj->convert_to_price($single_jewelry, 'total_sales_price', 'price_per_carat');

				$id 	   = isset( $single_jewelry['id'] ) ? $single_jewelry['id'] : 0;
				$size      = isset( $single_jewelry['size'] ) ? $single_jewelry['size'] : '';
				$shape     = isset( $single_jewelry['shape'] ) ? $single_jewelry['shape'] : '';
				$name      = isset( $single_jewelry['short_title'] ) ? $single_jewelry['short_title'] : '';;
				$image_url = isset( $single_jewelry['image_url'] ) ? $single_jewelry['image_url'] : '';
				$total_sales_price = isset( $single_jewelry['total_sales_price'] ) ? $single_jewelry['total_sales_price'] : 0;

				$single_jewelry_data = json_encode($single_jewelry);
				
				$product_exists = $jewelry_import_obj->check_product_by_sku($id);


				$product_exists_label = "";
				if( $product_exists ){
					$product_exists_label = "<span class='added'>".__( 'Added', 'vdb-jewelry-import' )."</span>";
				}

				$html_loop .="
							<div class='search-result-grid-box'>
				                <div class='search-result-grid-inner'>
				                    <div class='search-result-image status'>
				                        <a href='#' title=''>
				                           <img src='".$image_url."' width='200' alt=''>
				                        </a>
				                    </div>
				                    <div class='search-result-block-text'>
					                    <div class='search-result-block-text-left'>
					                         <span class='product-name'>".$name."</span>
					                         <span class='product-price'>".$woocommerce_currency_symbol.$total_sales_price."</span>
					                         ".$product_exists_label."
					                    </div>
					                    <div class='search-result-block-text-right'>
					                    	 <input class='jewelry-single-cbk' id='jewelry-select-".$id."' type='checkbox' name='jewelry_id[]' value='".$id."'>
					                    </div>
					               </div>
					          </div>
					     </div>";
			}

			if( !empty($html_loop) )
				$ajax_response['html'] = $html_loop;

			$ajax_response['total_jewelry_found'] = $body['total_jewelry_found'];
			$ajax_response['total_pages']         = ceil( $body['total_jewelry_found'] / $page_size );
		}

		echo json_encode($ajax_response);
		exit();
	}

	/*
	*Api call to fetch records using query parameters 
	*/
	public function api_call( $url = array(), $retry = false ){

		$api_key            = $this->api;
        $token              = $this->token;

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
            $response_data = json_decode($result, true);
        	return $response_data;
	    }else{
            return $this->api_call($data, true);
        }
        
        return FALSE;
	}

	/*
	*Callback function to schedule an import using 'Schedule Import' button
	*/
	public function vdb_schedule_import_callback(){

		$jewelry_ids = array();
		
		$ajax_response['spawn_cron'] = 'false';

		parse_str($_POST['search_data'], $query_paramters);
		$query_paramters = $this->vdb_update_array_format ($query_paramters);
		update_option('vdb_ji_jewelry_search_parameters', $query_paramters);

		parse_str($_POST['jewelry_ids'], $jewelry_ids);

		$jewelry_id = $jewelry_ids['jewelry_id'];

		/*Sort by ascending order and then update the option*/
		usort($jewelry_id, function($a,$b){
							if ($a==$b) return 0;
  								return ($a<$b)?-1:1;
							});
		$jewelry_ids['jewelry_id'] = $jewelry_id;

		update_option('vdb_ji_jewelry_ids', $jewelry_ids);

		wp_schedule_single_event( time(), 'vdb_cron_jewelry_import' );
		if(spawn_cron()){
			update_option('vdb_ji_jewelry_import_status', 'import');
			$ajax_response['spawn_cron'] = 'true';
		}

		echo json_encode($ajax_response);
		exit();
	}


	/*
	* Formatting submitted data into empty array for the keys having not array format	
	*/
	public function vdb_update_array_format( $query_paramters = array() ){
		foreach ($query_paramters as $key => $value) {
			if( isset($value['jewelry_styles']) ){
				foreach ($value['jewelry_styles'] as $jewelry_styles_key => $jewelry_styles_value) {
					if(!is_array($jewelry_styles_value) && !empty($jewelry_styles_value)){
						$query_paramters['jewelry']['jewelry_styles'][$jewelry_styles_key] = array();
					}
				}
			}

			if( isset($value['metals']) ){
				foreach ($value['metals'] as $metals_key => $metals_value) {
					
					foreach ($metals_value as $inner_metals_key => $inner_metals_value) {
						if( !is_array($inner_metals_value) && !empty($inner_metals_value) ){
							$query_paramters['jewelry']['metals'][$metals_key][$inner_metals_key] = array();		
						}
					}
				}
			}
		}

		return $query_paramters;
	}
}

new Jewelry_Import_VDB_Search();
?>