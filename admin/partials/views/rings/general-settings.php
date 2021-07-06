<?php
if (isset($_POST['jewelry_import_rings_general_settings'])) {
    Vdb_Jewelry_Import_Save_Settings::save_jewelry_import_rings_general_settings_callback();
    Vdb_Jewelry_Import_Save_Settings::show_admin_notice(__('General', 'vdb-jewelry-import'));
}

$jewelry_import_rings_general_settings = (new Vdb_Jewelry_Import)->jewelry_import_ring_get_general_settings();

$html = "<ul class='subsubsub'>";
    $html .= "<li><a href='".basename( $_SERVER['REQUEST_URI'] )."&section=general' class='current'>".__( 'General Settings', 'vdb-jewelry-import' )."</a></li>";
    $html .= "<li><a href='".basename( $_SERVER['REQUEST_URI'] )."&section=search' class=''>".__( 'Search Page Settings', 'vdb-jewelry-import' )."</a></li>";
    $html .= "<li><a href='".basename( $_SERVER['REQUEST_URI'] )."&section=cat_mappings' class=''>".__( 'Category Mappings', 'vdb-jewelry-import' )."</a></li>";
$html .= "</ul>
    <div class='general_settings'>
	<div class='wrap'>
		<h1>" . __( ( new Vdb_Jewelry_Import_Constants )->GENERAL_SETTING_TITLE, "vdb-jewelry-import" ). "</h1>
	</div>
        <div class='wrap form-bg'>
	    <div class='accordion-wrapper open'>
	    	<h2 class='toggle'>" . __( 'Basic Plugin Settings', 'vdb-jewelry-import' ) . "</h2>
	        <div class='accordion-content' style='display: block;'>
	            <table class='form-table'>
	                <tbody>
	                    <tr>
	                        <th scope='row'><label for='disallow_without_image'>" . __( 'Import Jewelry with images only', 'vdb-jewelry-import' ) . "</label></th>
	                        <td>
                                 <label class='switch'>
                                  <input type='checkbox' name='jewelry_import_rings_general_settings[without_image]' " . ( isset($jewelry_import_rings_general_settings['without_image']) ? $jewelry_import_rings_general_settings['without_image'] : "" ) . " value='checked'>
                                  <span class='slider round'></span>
                                 </label>                                    
	                            <p class='description'>" . __( 'Enable the switch to import products with images.', 'vdb-jewelry-import' ) . "</p>
	                        </td>
	                    </tr>
	                    <tr>
	                        <th scope='row'><label for='custom_log_option'>" . __( 'Enable Logger?', 'vdb-jewelry-import' ) . "</label></th>
	                        <td>
	                            <label class='switch'>
                                  <input type='checkbox' name='jewelry_import_rings_general_settings[logger]' " . ( isset($jewelry_import_rings_general_settings['logger']) ? $jewelry_import_rings_general_settings['logger'] : "" ) . " value='checked' >
                                  <span class='slider round'></span>
                                 </label>  
	                            <p class='description'>" . __( 'Disable if you do not want to generate custom logs for VDB App.', 'vdb-jewelry-import' ) . "</p>
	                        </td>
	                    </tr>
	                    <tr>
	                        <th scope='row'><label for='custom_cron_option'>" . __( 'Set Cron Time', 'vdb-jewelry-import' ) . "</label></th>
	                        <td>
	                            <input type='time' step='1' name='jewelry_import_rings_general_settings[cron_time]' value='" . ( isset($jewelry_import_rings_general_settings['cron_time']) ? $jewelry_import_rings_general_settings['cron_time'] : "16:00:00" ) ."' >
                                <p class='description'>" . __( 'Set time of the day you want to do the sync as per UTC+0 Timezone. Default is <b>16:00:00</b>', 'vdb-jewelry-import' ) . "</p>
	                        </td>
	                    </tr>
	                </tbody>
	            </table>
	        </div>
	    </div>
	    
	    <p class='submit'>
            " . wp_nonce_field('jewelry_import_rings_general_settings_nonce', 'jewelry_import_rings_general_settings_nonce_field') . "
            <button name='save_jewelry_import_rings_general_settings' class='button-primary' type='submit' value='" . __( 'Save Changes', 'vdb-jewelry-import' ) . "'>" . __( 'Save Changes', 'vdb-jewelry-import' ) . "</button>
        </p>
        </div>
</div>
            <div class='clear'></div>";

echo $html;
?>