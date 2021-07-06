<?php
defined('ABSPATH') OR die( ( new Vdb_Jewelry_Import_Constants )->DIE_MESSAGE );

class Jewelry_Import_General_Settings {

    public static function jewelry_import_general_settings_view($jewelry_import_general_settings) {

        if (isset($_POST['jewelry_import_general'])) {
            Vdb_Jewelry_Import_Save_Settings::save_jewelry_import_general_settings_callback();
            $jewelry_import_general_settings = (new Vdb_Jewelry_Import)->get_general_settings();

            Vdb_Jewelry_Import_Save_Settings::show_admin_notice( __( 'API', 'vdb-jewelry-import' ) );
        }

        $html = "<div class='general_settings'>
	    	<div class='wrap'>
	    		<h1>" . __( ( new Vdb_Jewelry_Import_Constants )->API_KEY_SETTING_TITLE, 'vdb-jewelry-import' ) . "</h1>
    		</div>
		        <div class='wrap form-bg'>
				<div class='accordion-wrapper open'>
			    	<h2 class='toggle'>" . __( 'Plugin Authentication Settings', 'vdb-jewelry-import' ) . "</h2>
			        <div class='accordion-content' style='display: block;'>
			            <table class='form-table'>
			                <tbody>
			                    <tr>
			                        <th scope='row'><label for='api'>" . __( 'Jewelry Import API Key', 'vdb-jewelry-import' ) . "</label></th>
			                        <td>
			                            <input name='jewelry_import_general[api]' type='text' value='" . ( isset($jewelry_import_general_settings['api']) ? $jewelry_import_general_settings['api'] : "" ) . "' id='api' class='regular-text' required='required'>
			                            <p class='description'>" . __( 'API Key to Access VDB Jewelry API.', 'vdb-jewelry-import' ) . "</p>
			                        </td>
			                    </tr>
			                    <tr>
			                        <th scope='row'><label for='token'>" . __( 'Jewelry Import Access Token', 'vdb-jewelry-import' ) . "</label></th>
			                        <td>
			                            <input name='jewelry_import_general[token]' type='text' value='" . ( isset($jewelry_import_general_settings['token']) ? $jewelry_import_general_settings['token'] : "" ) . "' id='token' class='regular-text'  required='required'>
			                            <p class='description'>" . __( 'Token to access VDB Jewelry API.', 'vdb-jewelry-import' ) . "</p>
			                        </td>
			                    </tr>
			                    <tr>
			                        <th scope='row'><label for='apv_api_key'>" . __( 'Alpha Vantage API Key', 'vdb-jewelry-import' ) . "</label></th>
			                        <td>
			                            <input name='jewelry_import_general[apv_api_key]' type='text' value='" . ( isset($jewelry_import_general_settings['apv_api_key']) ? $jewelry_import_general_settings['apv_api_key'] : "" ) . "' id='apv_api_key' class='regular-text' required='required'>
			                            <p class='description'>" . __( 'We are using Alpha Vantage services for price conversion as per your store preferences. You just need to signup in order to get the FREE API KEY.<br/>You can get your free ', 'vdb-jewelry-import' ) . "<a href='https://www.alphavantage.co/support/#api-key' target='_blank'> Alpha Vantage API Key</a>.</p>
			                        </td>
			                    </tr>
			                </tbody>
			            </table>
			        </div>
			    </div>
			   			  
			     <p class='submit'>
                    " . wp_nonce_field('jewelry_import_general_settings_nonce', 'jewelry_import_general_settings_nonce_field') . "
                    <button name='save_jewelry_import_general_settings' class='button-primary' type='submit' value='" . __( 'Save Changes', 'vdb-jewelry-import' ) . "'>" . __( 'Save Changes', 'vdb-jewelry-import' ) . "</button>
                </p>
	            </div>
	    </div>
                    <div class='clear'></div>";

        $html .= Vdb_Jewelry_Import::infoHtml();

        echo $html;
    }
}