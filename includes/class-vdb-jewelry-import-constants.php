<?php
class Vdb_Jewelry_Import_Constants {
    
    public $GENERAL_SETTING_URL = VDB_JEWELRY_IMPORT_ADMIN_URL .'admin.php?page=vdb-jewelry-import-dashboard&tab=general';

    public $RING_SETTING_URL = VDB_JEWELRY_IMPORT_ADMIN_URL .'admin.php?page=vdb-jewelry-import-dashboard&tab=ring';

    public $FETCH_PAGE_SIZE = 12;

    public $HOST_API = 'http://apiservices.vdbapp.com/';

    public $RINGS_API_ENDPOINT  = 'http://apiservices.vdbapp.com/v2/jewelry?';

    public $ALPHA_VANTAGE_API_ENDPOINT = 'https://www.alphavantage.co/query?function=CURRENCY_EXCHANGE_RATE&from_currency=USD&';

    public $ONBOARDING_TITLE = 'VDB Jewelry Import Onboarding';

    public $API_KEY_SETTING_TITLE = 'Jewelry API Authentication Settings';

    public $GENERAL_SETTING_TITLE = 'Jewelry Import General Settings';

    public $SEARCH_SETTING_TITLE  = 'Jewelry Import Search Page Settings';
    
    public $CAT_MAPPING_SETTING_TITLE  = 'Jewelry Category Mapping Settings';

    public $DIE_MESSAGE = 'Direct access is not allowed!';
}
?>