<?php 
    $attributes         = new Jewelry_Import_Attributes();
    $jewelry_styles     = $attributes->jewelry_styles;
    $jewelry_sub_types  = $attributes->jewelry_sub_types;
    $metals             = $attributes->metals;
    $brands             = $attributes->brands;
    $import_status      = get_option('vdb_ji_jewelry_import_status');
    $import_button_class = '';
    if($import_status == 'import'){
        $import_button_class = 'disabled';
    }
    
$html = "<ul class='subsubsub'>";
    $html .= "<li><a href='".basename( $_SERVER['REQUEST_URI'] )."&section=general' class=''>".__( 'General Settings', 'vdb-jewelry-import' )."</a></li>";
    $html .= "<li><a href='".basename( $_SERVER['REQUEST_URI'] )."&section=search' class='current'>".__( 'Search Page Settings', 'vdb-jewelry-import' )."</a></li>";
    $html .= "<li><a href='".basename( $_SERVER['REQUEST_URI'] )."&section=cat_mappings' class=''>".__( 'Category Mappings', 'vdb-jewelry-import' )."</a></li>";
$html .= "</ul>";

$html .= "<div class='general_settings'>";
    $html .= "<div class='wrap'>";
        $html .= "<h1>".__( ( new Vdb_Jewelry_Import_Constants )->SEARCH_SETTING_TITLE, 'vdb-jewelry-import' )."</h1>";
    $html .= "</div>";
    $html .= "<span class='import_info'></span>";
    $html .= "<div class='main-wrap'>";
    $html .= "<div class='search-containt-card card-box'>";
        $html .= "<div class='top-d-strip'>";
            $html .= "<div class='btns-group'>";
                $html .= "<input type='checkbox' class='jewelry_select_cbk' id='checkAll'/><label for='checkAll'>".__( 'Select All', 'vdb-jewelry-import' )."</label>";
                $html .= "<a href='#' class='btn btn-perple {$import_button_class}' id='jewelry_schedule_import'>".__( 'Schedule Import', 'vdb-jewelry-import' )."</a>";
            $html .= "</div>";
            $html .= "<a id='vdb_js_clear_search' class='btn btn-perple '>" . __( 'Clear Search', 'vdb-jewelry-import' ) . "</a>";
            $html .= "<div class='total-result-txt'><span id='total_jewelry_found_wrapper'>0</span> ".__( 'Results', 'vdb-jewelry-import' )."</div>";
        $html .= "</div>";
      
        $html .= "<div class='search-result-grid-wrapper' id='search_result_wrapper'></div>";
        $html .= "<div class='jewelry_loader'>";
            $html .= "<div class='loader-circle'></div>";
        $html .= "</div>";
    $html .= "</div>";

    $html .= "<div class='search-side-card card-box'>";
        $html .= "<h2 class='title-txt'>Search Options</h2>";
        $html .= "<div class='wrap form-table-wrapper'>";
            $html .= "<div class='container types'>";
                $html .= "<div class='header'> <div class='icon-img'><i class='icon-wedding-ring'></i> </div>".__( 'Type', 'vdb-jewelry-import' )."<span class='icon-plus'></span>";

                $html .= "</div>";
                $html .= "<div class='content'>";
                    $html .= "<ul class='list-box'>";
                            foreach ($jewelry_styles as $key => $value) {
                                $html .= "<li class='inner-box'>";
                                    $html .= "<div class='jewelry-type' data-types = '{$key}'>";
                                        $html .= "<span class='btn-icon {$key}'></span>";
                                        $html .= "<input type='checkbox' name='jewelry[jewelry_styles][{$key}]' class='jewelry_inputs' value='{$key}' id='{$value}'>";
                                        $html .= "<label for='{$value}'>{$value}</label>";
                                    $html .= "</div>";
                                $html .= "</li>";
                            }
                            
                    $html .= "</ul>";
                $html .= "</div>";
            $html .= "</div>";


            $html .= "<div id='inject_sub_types' class='sub-menu-list'>";
                
                    foreach ($jewelry_sub_types as $type => $sub_types) {
                
                    $html .= "<div class='container sub_types {$type}' style='display: none;'>";
                        $html .= "<div class='header'> <div class='icon-img'><i class='btn-icon {$type}'></i> </div> {$jewelry_styles[$type]}<span class='icon-plus'></span>";

                        $html .= "</div>";
                        $html .= "<div class='content'>";
                            $html .= "<ul>";
                                
                                    foreach ($sub_types as $key => $value) {
                                        if( is_array($value) &&  $key =='watch_size' ){
                                            
                                            $html .= "<li class='list-style {$key}'>";
                                            foreach ($value as $watch_size_key => $watch_size_value) {
                                            $html .= "<div class='{$type}-types'>";
                                                $html .= "<div class='sub-icon'>";
                                                    $html .= "<span class='{$type}-{$watch_size_key}'>";
                                                        $html .= "<span class='path1'></span>";
                                                    $html .= "</span>";
                                                $html .= "</div>";
                                                $html .= "<div class='sub-action-btn'>";
                                                    $html .= "<div class='custom-checkbox radio-circle'>";
                                                        $html .= "<input type='radio' name='jewelry[jewelry_styles][{$key}][]' value='{$watch_size_key}' class='jewelry_inputs' id='{$type}-{$watch_size_key}'>";
                                                        $html .= "<label for='{$type}-{$watch_size_key}'>{$watch_size_value}</label>";
                                                    $html .= "</div>";
                                                $html .= "</div>";
                                            $html .= "</div>";
                                            }
                                            $html .= "</li>";
                                            
                                        }else{
                                        
                                          $html .= "<li class='list-style'>";
                                            $html .= "<div class='{$type}-types'>";
                                                $html .= "<div class='sub-icon'>";
                                                    $html .= "<span class='{$type}-{$key}'>";
                                                        $html .= "<span class='path1'></span>";
                                                    $html .= "</span>";
                                                $html .= "</div>";
                                                $html .= "<div class='sub-action-btn'>";
                                                    $html .= "<div class='custom-checkbox checkbox-circle'>";
                                                        $html .= "<input type='checkbox' name='jewelry[jewelry_styles][{$type}][]' value='{$value}' class='jewelry_inputs' id='{$type}-{$value}'>";
                                                        $html .= "<label for='{$type}-{$value}'>{$value}</label>";
                                                    $html .= "</div>";
                                                $html .= "</div>";
                                            $html .= "</div>";
                                          $html .= "</li>";
                                        }
                                    }
                            $html .= "</ul>";
                        $html .= "</div>";
                    $html .= "</div>";
                
                    }
                
            $html .= "</div>";
            

            $html .= "<div class='container metals'>";
                $html .= "<div class='header'><div class='icon-img'><i class='icon-gold-ingots'></i></div>".__( 'Metal', 'vdb-jewelry-import' )."<span class='icon-plus'></span></div>";
                $html .= "<div class='content'>";
                    $html .= "<ul class='box-list-2'>";
                        
                            $array_index = 0; 
                            foreach ($metals as $key => $value) {
                                if( is_array($value) ){ 
                                    foreach ($value as $_inner_key => $inner_value) {
                                    
                                    $html .= "<li class='box-list-2-li'>";
                                        $html .= "<div class='metal-types'>";
                                            $html .= "<div class='sub-action-btn'>";
                                                $html .= "<div class='ring-import-shape-box-inner'>";
                                                     $html .= "<span class='metal-shape ".strtolower( str_replace(' ', '-', $key))."'>{$inner_value} K</span>";
                                                $html .= "</div>";
                                                $html .= "<div class='custom-checkbox checkbox-circle'>";
                                                    $html .= "<input type='checkbox' name='jewelry[metals][{$array_index}][{$key}][]' class='jewelry_inputs' value='{$inner_value}' id='{$inner_value} K {$key}'>";
                                                    $html .= "<label for={$inner_value} K {$key}'>{$key}</label>";
                                                $html .= "</div>";
                                            $html .= "</div>";
                                        $html .= "</div>";
                                    $html .= "</li>";

                                    }
                                }else{
                                    
                                    $html .= "<li class='box-list-2-li'>";
                                        $html .= "<div class='metal-types'>";
                                            $html .= "<div class='sub-action-btn'>";
                                                $html .= "<div class='ring-import-shape-box-inner'>";
                                                     $html .= "<span class='metal-shape ".strtolower( str_replace(' ', '-', $value))."'></span>";
                                                $html .= "</div>";
                                                $html .= "<div class='custom-checkbox checkbox-circle'>";
                                                    $html .= "<input type='checkbox' name='jewelry[metals][{$array_index}][{$value}]' class='jewelry_inputs' value='{$value}' id='{$value}'>";
                                                    $html .= "<label for='{$value}'>{$value}</label>";
                                                $html .= "</div>";
                                            $html .= "</div>";
                                        $html .= "</div>";
                                    $html .= "</li>";

                                } 
                                        
                                $array_index++;  
                            }
                        
                    $html .= "</ul>";
                $html .= "</div>";
            $html .= "</div>";
            $html .= "<div class='sub-menu-list'>";               
                $html .= "<div class='container brands sub_types brand-list-types'>";
                    $html .= "<div class='header'><div class='icon-img'><i class='icon-price-tag'></i></div> ".__( 'Brand', 'vdb-jewelry-import' )."<span class='icon-plus'></span></div>";
                        $html .= "<div class='content'>";
                            $html .= "<ul class='brand-list'>";
                                    foreach ($brands as $key => $value) {
                                		$html .= "<li class='list-style'>";
                                    		$html .= "<div class='brand-names'>"; 
                                        		$html .= "<input type='checkbox' name='{$key}' class='jewelry_inputs' value='{$key}' id='{$key}'>";
                                        		$html .= "<label for='{$key}'>{$value}</label>";
                                        	$html .= "</div>";
                                		$html .= "</li>";
                                    }
                            $html .= "</ul>";
                    $html .= "</div>";
                $html .= "</div>";
            $html .= "</div>";

            $html .= "<div class='container prices'>";
                $html .= "<div class='header'><div class='icon-img'><i class='icon-wallet'></i></div> ".__( 'Price', 'vdb-jewelry-import' )."<span class='icon-plus'></span></div>";
                $html .= "<div class='content'>";
                    $html .= "<div class='budget-range-container'>";
                        $html .= "<div class='slider-min-max'>";
                            $html .= "<span>Min</span><span>Max</span>";
                        $html .= "</div>";
                        $html .= "<div class='slider-values'>";
                            $html .= "<div class='budget-min'><span class='currency'>" . get_woocommerce_currency_symbol() . "</span>";
                            $html .= "<input type='number' name='jewelry[price_total_from]' placeholder='0.00' id='price_total_from' class='price jewelry_inputs' min='0' oninput='this.value = 
 !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null'>";
                            $html .= "</div>";
                            $html .= "<span>to</span>";
                            $html .= "<div class='budget-max'><span class='currency'>" . get_woocommerce_currency_symbol() . "</span>";
                            $html .= "<input type='number' name='jewelry[price_total_to]' placeholder='99999.99' id='price_total_to' class='price jewelry_inputs' min='0' oninput='this.value = 
 !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null'>";
                            $html .= "</div>";
                        $html .= "</div>";
                        $html .= "<div class='price-error' style='display:none;'>Min must be less than Max!</div>";
                        $html .= "<div class='price-to-error' style='display:none;'>Max must be greater than Min!</div>";
                    $html .= "</div>";
                $html .= "</div>";
            $html .= "</div>";

            $html .= "<input type='hidden' name='jewelry[page_number]' id='jewelry_page_number' value='1' class='jewelry_inputs'>";
            $html .= "<input type='hidden' name='jewelry[page_size]' id='jewelry_page_size' value='".( new Vdb_Jewelry_Import_Constants )->FETCH_PAGE_SIZE."' class='jewelry_inputs'>";
            $html .= "<input type='hidden' name='jewelry_total_pages' id='jewelry_total_pages' value='1'>";
            $html .= "<input type='hidden' name='jewelry[price_mode]' value='1' class='jewelry_inputs'>";
            $html .= "<input type='hidden' name='jewelry[is_center_size_selected]' value='true' class='jewelry_inputs'>";
            $html .= "<input type='hidden' name='jewelry[vdb_setting]' value='true' class='jewelry_inputs'>";
            $html .= "<input type='hidden' name='jewelry_load_more' id='jewelry_load_more' value='false' class='jewelry_inputs'>";
            $html .= "<input type='hidden' name='jewelry_plugin_url' id='jewelry_plugin_url' value='" . VDB_JEWELRY_IMPORT_PLUGIN_URL . "' class='jewelry_inputs'>";
            
        $html .= "</div>";
    $html .= "</div>";
    $html .= "</div>";
    $html .= "<div class='clear'></div>";
$html .= "</div>";

$html .= "</style><script type='text/javascript'>
var jewelry_plugin_url = jQuery( '#jewelry_plugin_url' ).val();
// Normal Window Load
jQuery( 'img' ).attr( 'onerror','this.onerror=null;this.src=' + jewelry_plugin_url + 'admin/images/error-thumbnail.png\';' );

// On AJAX Call
jQuery(document).ajaxComplete(function() {
    jQuery( 'img' ).attr( 'onerror','this.onerror=null;this.src=' + jewelry_plugin_url + 'admin/images/error-thumbnail.png\';' );
});
</script>";

echo $html;