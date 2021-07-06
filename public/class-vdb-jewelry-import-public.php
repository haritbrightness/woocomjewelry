<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.vdbapp.com/
 * @since      1.0.0
 *
 * @package    Vdb_Jewelry_Import
 * @subpackage Vdb_Jewelry_Import/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Vdb_Jewelry_Import
 * @subpackage Vdb_Jewelry_Import/public
 * @author     Virtual Diamond Boutique <Iqbal.Brightnessgroup@gmail.com>
 */
class Vdb_Jewelry_Import_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function api_check_jewelry_in_cart( $stock_num = "" ){
		$settings    = (new Vdb_Jewelry_Import)->get_general_settings();
        $api         = $settings[ 'api' ];
        $token       = $settings[ 'token' ];
      
        $response = wp_remote_get( ( new Vdb_Jewelry_Import_Constants )->RINGS_API_ENDPOINT.'stock_num=' . $stock_num, array(
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => "Token token=$token, api_key=$api"
            ),
            'body'    => array(),
            'cookies' => array(),
            'timeout' => 1000
        ) );

        $responseBody = wp_remote_retrieve_body( $response );
        $response = json_decode( $responseBody );
        $records  = $response->response->body->total_jewelry_found;
        return $records;
	}

	/*
	* This function will execute when cart page will load to check VDB Jewelry product are in stock or not by calling api
	*/
	public function check_jewelry_in_cart(){
	    
	    $message   = "";

	    foreach ( WC()->cart->get_cart() as $cart_item ) {

			$product_id  = $cart_item[ 'product_id' ];
			$stock_num   = get_post_meta( $product_id, '_jewelry_stock_number', true );
			if($stock_num){
            
	            $product     = wc_get_product( $product_id );
				
				$records = $this->api_check_jewelry_in_cart($stock_num);
	            				
	            if ( $records == 0 ) {
	                $name    = $product->get_name();
	                $message = $name . __( " Jewelry is no longer available.", "vdb-jewelry-import" );
	                update_post_meta( $product_id, '_stock_status', wc_clean( 'outofstock' ) );
	                remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
		            ?>
		           <script>
		                jQuery(document).ready(function(){
		                    alert("<?php  echo $message; ?>");
		                });
		            </script>
		            <?php
	            }
	        }
        }
	}

	/*
	* This function is used to check validation of Jewelry product before add to cart
	*/
	public function check_jewelry_before_add_to_cart( $passed, $product_id, $quantity, $variation_id = '', $variations= '' ){

		$message   = "";
		$stock_num   = get_post_meta( $product_id, '_jewelry_stock_number', true );
	    if($stock_num){
        	$records = $this->api_check_jewelry_in_cart($stock_num);
            				
            if ( $records == 0 ) {
                $message = apply_filters('jewelry_single_product)unavailable_message', __( " Jewelry is no longer available.", "vdb-jewelry-import" ));
                update_post_meta( $product_id, '_stock_status', wc_clean( 'outofstock' ) );
                wc_add_notice( __( $message, 'vdb-jewelry-import' ), 'error' );
	            return false;
            }

            return true;
        }

        return true;
	}


	/*
	* This function will execute when user place an order. `woocommerce_thankyou` hook will get executed
	*/
	public function jewelry_auto_buy_request( $order_id ){
		
		if ( !$order_id ) { return; }

	    $order = wc_get_order( $order_id );

        foreach ( $order->get_items() as $item_id => $item ) {

            $product          = $item->get_product();
            $product_id       = $product->get_id();
            $stock_item_id    = $product->get_sku();
            $price       	  = $product->get_price();
            $stock_num   	  = get_post_meta( $product_id, '_jewelry_stock_number', true );
            if ( $stock_num ) {

                $settings    = (new Vdb_Jewelry_Import)->get_general_settings();
               	$api         = $settings[ 'api' ];
               	$token       = $settings[ 'token' ];

               	$body = array(
                        "stock_item_request" => array(
                                "stock_item_id"             => $stock_item_id,
                                "request_type"              => "buy_jewelry",
                                "payment_type"              => "COD",
                                "price_mode"                => 1,
                                "offer_price_per_carat"     => 1,
                                "offer_total_sales_price"   => $price,
                                "comments" 					=> "Jewelry Import WooCommerce request for purchase"
                            )
                    );
        		
        		$headers = array(
                    'Content-Type'  => 'application/json',
                	'Accept'        => 'application/json',
                	'Authorization' => "Token token=$token, api_key=$api"
                );
        
        		$args = array(
                        'headers'       => $headers,
                        'timeout'       => 120,
                        'httpversion'   => '1.1',
                        'sslverify'     => false,
                        'body'          => json_encode($body)
                    );

        		$response       = wp_remote_post( ( new Vdb_Jewelry_Import_Constants )->HOST_API."v2/stock_item_requests", $args );
        		$responseBody   = wp_remote_retrieve_body( $response );
        		$responseBody   = json_decode( $responseBody, true );
            }
        }
	}
}