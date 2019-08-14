<?php

class Orba_Multipunkty_Model_Config extends Mage_Core_Model_Abstract {
    
    /* 
     * Stale do obslugi API 
     */
    const API_URL = 'http://backend.multipunkty.pl/';
    const API_NONCE_MAX_LEN = 32;
    const API_APP_ID_LEN = 32;
    const API_PARTNER_TOKEN_LEN = 128;
    const API_PARTNER_ID_LEN = 32;
    const API_TIME_LEN = 15;
    //adresy do zapytan API
    const API_QUERY_REGISTER_TRANSACTION = 'api/1/transactions/register';
    const API_QUERY_GET_PARTNER_STATUS = 'api/1/partners/status';
    const API_QUERY_ASSIGN_TRANSACTION_TO_USER = 'api/1/transactions/assign';
    const API_QUERY_CHANGE_TRANSACTION_STATUS = 'api/1/transactions/status';
    //list of important URL's
    const URL_BUY_CREDITS = 'http://backend.multipunkty.pl/';
    
    public function _construct() {
        $this->_init('multipunkty/config');
    }
    
}

