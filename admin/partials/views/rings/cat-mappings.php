<?php
$attributes = new Jewelry_Import_Attributes();
$jewelry_styles     = $attributes->jewelry_styles;
$jewelry_sub_types  = $attributes->jewelry_sub_types;

if (isset($_POST['save_jewelry_import_cat_mappings'])) {
    Vdb_Jewelry_Import_Save_Settings::save_jewelry_import_cat_mappings_callback();
    Vdb_Jewelry_Import_Save_Settings::show_admin_notice(__('Category Mappings', 'vdb-jewelry-import'));
}

$cat_mappings_data = (new Vdb_Jewelry_Import)->jewelry_import_get_cat_mappings_settings();

$orderby = 'name';
$order = 'asc';
$hide_empty = false ;
$cat_args = array(
    'orderby'    => $orderby,
    'order'      => $order,
    'hide_empty' => $hide_empty,
);
 
$product_categories = get_terms( 'product_cat', $cat_args );

$html = "<ul class='subsubsub'>";
    $html .= "<li><a href='".basename( $_SERVER['REQUEST_URI'] )."&section=general' class=''>".__( 'General Settings', 'vdb-jewelry-import' )."</a></li>";
    $html .= "<li><a href='".basename( $_SERVER['REQUEST_URI'] )."&section=search' class=''>".__( 'Search Page Settings', 'vdb-jewelry-import' )."</a></li>";
    $html .= "<li><a href='".basename( $_SERVER['REQUEST_URI'] )."&section=cat_mappings' class='current'>".__( 'Category Mappings', 'vdb-jewelry-import' )."</a></li>";
$html .= "</ul>";

$html .= "<div class='general_settings'>";
    $html .= "<div class='wrap'>";
        $html .= "<h1>". __( ( new Vdb_Jewelry_Import_Constants )->CAT_MAPPING_SETTING_TITLE, "vdb-jewelry-import" )."
        </h1>";
    $html .= "</div>";

    $html .= "<div class='wrap form-table-wrapper'>";
        $html .= "<div class='form-table-half'>";
        
        foreach ($jewelry_styles as $type_key => $type_value) {

            $html .= "<div class='accordion-wrapper'>";
                    $html .= "<h2 class='toggle'>".$type_value." Mapping</h2>";
                    $html .= "<div class='accordion-content'>";
                        $html .= "<table class='form-table'>";
                            $html .= "<thead>";
                                $html .= "<tr>";
                                    $html .= "<th>".__( 'Types & Sub Types', 'vdb-jewelry-import' )."</th>";
                                    $html .= "<th>".__( 'Product Category', 'vdb-jewelry-import' )."</th>";
                                $html .= "</tr>";
                                $html .= "</thead>";
                                $html .= "<tbody>";
                                    $html .= "<tr>";
                                        $html .= "<td><label>".$type_value."</label></td>";
                                        $html .= "<td>";
                                            $html .= "<select name='jewelry_cat_mapping[{$type_key}][{$type_key}]'>";

                                                $html .= "<option value=''>".__( 'None', 'vdb-jewelry-import' )."</option>";
                                                if( !empty($product_categories) ){
                                                    foreach ($product_categories as $key => $category) {
                                            
                                                    $html .= "<option value='".$category->slug."' ".selected( $cat_mappings_data[$type_key][$type_key], $category->slug, false ).">".__( $category->name, 'vdb-jewelry-import' )."</option>";
                                                    }
                                                }
                                            $html .= "</select>";
                                        $html .= "</td>";
                                    $html .= "</tr>";
                                    
                                        if( is_array($jewelry_sub_types[$type_key]) && !empty($jewelry_sub_types[$type_key]) ){
                                
                                            foreach ($jewelry_sub_types[$type_key] as $sub_type_key => $sub_type_value) {
                                           
                                                if( ! is_array( $sub_type_value ) ){
                                                    $html .= "<tr>";
                                                        $html .= "<td><label>".$sub_type_value."</label></td>";
                                                        $html .= "<td>";
                                                            $html .= "<select name='jewelry_cat_mapping[{$type_key}][{$sub_type_key}]'>";
                                                                $html .= "<option value=''>".__( 'None', 'vdb-jewelry-import' )."</option>";
                                                                if( !empty($product_categories) ){
                                                                    foreach ($product_categories as $key => $category) {
                                                                    
                                                                    $html .= "<option value='".$category->slug."' ".selected( $cat_mappings_data[$type_key][$sub_type_key], $category->slug, false ).">".__( $category->name, 'vdb-jewelry-import' )."</option>";
                                                                    }
                                                                }
                                                
                                                            $html .= "</select>";
                                                        $html .= "</td>";
                                                    $html .= "</tr>";
                                                }
                                            }
                                        }
                                    
                                $html .= "</tbody>";
                        $html .= "</table>";
                    $html .= "</div>";
                $html .= "</div>";
                $html .= "<hr>";
        }
            
        $html .= "</div>";
        $html .= "<p class='submit'>";
        $html .= wp_nonce_field('jewelry_import_cat_mappings_nonce', 'jewelry_import_cat_mappings_nonce_field');
        $html .= "<button name='save_jewelry_import_cat_mappings' class='button-primary' type='submit' value='save'>". __( 'Save Changes', 'vdb-jewelry-import' )."</button>";
        $html .= "</p>";
        
    $html .= "</div>";

    

    echo $html;