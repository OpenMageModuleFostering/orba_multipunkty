<?php

class Orba_Multipunkty_Model_Api_Abstract extends Mage_Core_Model_Abstract {

    const API_URL = 'http://backend.multipunkty.pl/';
    const API_NONCE_MAX_LEN = 32;
    const API_APP_ID_LEN = 32;
    const API_PARTNER_TOKEN_LEN = 128;
    const API_PARTNER_ID_LEN = 32;
    const API_TIME_LEN = 15;
    const API_TIME_MAX_TOLERANTION_SEC = 1800;
    const API_QUERY_REGISTER_TRANSACTION = 'api/1/transactions/register';
    const API_QUERY_GET_STATUS = 'api/1/status';
    const API_QUERY_GET_PARTNER_STATUS = 'api/1/partners/{partnerId}/status';
    const API_QUERY_ASSIGN_TRANSACTION_TO_USER = 'api/1/transactions/assign';
    const API_QUERY_CHANGE_TRANSACTION_STATUS = 'api/1/transactions/status';
    const API_TRANSACTION_STATUS_ACCEPT = 'Accept';
    const API_TRANSACTION_STATUS_REJECT = 'Reject';
    
}
