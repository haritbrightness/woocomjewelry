<?php
/**
 * Class to manage product filters 
 */
class Jewelry_Import_Product_Filters {
	
	function __construct(){
		add_filter( 'woocommerce_product_filters', array($this, 'filter_by_jewelry_brand_dashboard_products') );
		add_filter( 'woocommerce_product_filters', array($this, 'filter_by_jewelry_metal_types_dashboard_products') );
		add_filter( 'woocommerce_product_filters', array($this, 'filter_by_price_dashboard_products') );
		add_action( 'pre_get_posts', array($this, 'apply_jewelry_import_product_filters') );
	}	


	public function filter_by_jewelry_brand_dashboard_products( $output ) {
   
		global $wp_query;

		$output .= wc_product_dropdown_categories( array(
		'show_option_none' => 'Filter by Brand',
		'taxonomy' => 'jewelry_brand',
		'name' => 'jewelry_brand',
		'selected' => isset( $wp_query->query_vars['jewelry_brand'] ) ? $wp_query->query_vars['jewelry_brand'] : '',
		) );

		
		return $output;
	}

	public function filter_by_jewelry_metal_types_dashboard_products( $output ) {
   
		global $wp_query;

		$output .= wc_product_dropdown_categories( array(
		'show_option_none' => 'Filter by Metal Types',
		'taxonomy' => 'jewelry_metal_types',
		'name' => 'jewelry_metal_types',
		'selected' => isset( $wp_query->query_vars['jewelry_metal_types'] ) ? $wp_query->query_vars['jewelry_metal_types'] : '',
		) );

		return $output;
	}

	public function filter_by_price_dashboard_products( $output ) {
   
		global $wp_query;
		
		$price_from = $price_to = "";
		if( isset( $_GET['price_from'] ) ) { $price_from = $_GET['price_from'];}
		if( isset( $_GET['price_to'] ) ) { $price_to = $_GET['price_to'];}

		$output .= "<input type='text' name='price_from' style='max-width:90px' value='".$price_from."' placeholder='Price from'>";
		$output .= "<input type='text' name='price_to' style='max-width:90px' value='".$price_to."' placeholder='Price to'>";

		return $output;
	}

	public function apply_jewelry_import_product_filters( $query ){

		global $pagenow;
		
		$price_from = isset($_GET['price_from']) ? $_GET['price_from'] : "" ;
		$price_to 	= isset($_GET['price_to']) ? $_GET['price_to'] : "" ;

		// Ensure it is an edit.php admin page, the filter exists and has a value, and that it's the products page
		if ( $query->is_admin && $pagenow == 'edit.php' && ( $price_from != '' || $price_to != '' ) && $_GET['post_type'] == 'product' ) {

		  	if( $price_from != '' && $price_to != '' ){

		  		$price_array = array(
						      		'key'     => '_regular_price',
						      		'value'   => array( $price_from, $price_to ),
						      		'compare' => 'BETWEEN',
		            				'type' 	  => 'NUMERIC'
						    	);

		  	}else if( $price_from != '' ){
		  		
		  		$price_array = array(
						      		'key'     => '_regular_price',
						      		'value'   => $price_from,
						      		'compare' => '>=',
		            				'type' 	  => 'NUMERIC'
						    	);	

		  	}else if( $price_to != '' ){

		  		$price_array = array(
						      		'key'     => '_regular_price',
						      		'value'   => $price_to,
						      		'compare' => '<=',
		            				'type' 	  => 'NUMERIC'
						    	);

		  	}

		  	$meta_key_query = array( $price_array );

		  	$query->set( 'meta_query', $meta_key_query );

		}
	}
}

new Jewelry_Import_Product_Filters();
?>