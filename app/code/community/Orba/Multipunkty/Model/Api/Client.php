<?php

/*
 * MultiPunkty.pl API Client, compatible with API version 5
 * 
 * @author Michał Zabielski <michal.zabielski@orba.pl>
 * @created         15-07-2013
 * @lastModified    26-11-2013
 */
class Orba_Multipunkty_Model_Api_Client extends Orba_Multipunkty_Model_Api_Abstract {

    /*
     * Checking if debug mode is active
     */
    protected function isDebugMode()
    {
        return Mage::helper('multipunkty')->isDebugMode();
    }
    
    /*
     * Signing queries to MultiPunkty.pl API server
     * Time format: yyyyMMddTHHmmSS UTC
     * 
     * @doc Section 4.1, 4.2 from API Specification
     */
    protected function signQuery()
    {
        //API requirement
        date_default_timezone_set('UTC');
        
        $privatekey = Mage::helper('multipunkty')->getPrivateKey();
        if(is_null($privatekey) || $privatekey === '')
        {
            $privatekey = Mage::getStoreConfig('multipunkty/basic_config/platform_private_key');
        }
        $appId = Mage::helper('multipunkty')->getApiKey();
        if(is_null($appId) || $appId === '')
        {
            $appId = Mage::getStoreConfig('multipunkty/basic_config/platform_api_key');
        }
        $time = date("Ymd").'T'.date("His");
        $nonce = substr( md5(time()+rand(0,time())), 0, self::API_NONCE_MAX_LEN );
        
        //save nonce in database
        /* $nonceData = Mage::getModel('multipunkty/nonce');
        $nonceData->setSecretPhrase($nonce);
        $nonceData->setQueryTime($time);
        $nonceData->save(); */
        
        $codingString = $appId.'&'.$time.'&'.$nonce;

        return base64_encode($codingString.'&'.base64_encode(hash_hmac('sha256', $codingString.'&'.$privatekey, $privatekey, true)));
    }

    /*
     * Sending query to MultiPunkty.pl
     * $sufix - const API_QUERY available in configuration (Model/Api/Abstract.php)
     * 
     * @doc Section 4.2 from API specification
     */
    protected function sendQuery($sufix, $query = NULL, $method = "POST", array $options = array())
    {
        if(strtoupper($method) == "GET")
        {
            $connMethod = 0;
        }else{
            $connMethod = 1;
        }
            
        $query_body = json_encode($query);
        
        $url = self::API_URL.$sufix;

        $sign = $this->signQuery();

        //$f = fopen('request.txt', 'w');
        $defaults = array(
             CURLOPT_HTTPHEADER => array('Authorization: MultiPunktyApp '.$sign),
             CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)',
             CURLOPT_AUTOREFERER => true,
             CURLOPT_FOLLOWLOCATION => true,
             CURLOPT_POST => $connMethod,
             CURLOPT_HEADER => 0,
             CURLOPT_URL => $url,
             CURLOPT_VERBOSE => 1,
             //CURLOPT_STDERR => $f,
             CURLOPT_RETURNTRANSFER => 1,
             CURLOPT_TIMEOUT => 300,
             CURLOPT_COOKIEFILE => 'cookie2.txt',
             CURLOPT_COOKIEJAR => 'cookie2.txt',
             CURLOPT_SSL_VERIFYPEER => false
        );
        
        // If method POST, then JSON body is sending
        if($connMethod)
        {
            $defaults[CURLOPT_POSTFIELDS] = $query_body;
        }

        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        if( ! $result_raw = curl_exec($ch))
        {
             trigger_error(curl_error($ch));
        }
        //fclose($f);
        curl_close($ch);
        
        $result = json_decode($result_raw);
        
        return $result;
    }

    /*
     * Checking API status in MultiPunkty.pl
     * Tip: Good method for API testing
     *
     * @doc Section 5.1 from API specification
     * 
     * @return boolean { isAuthorized }
     */
    public function getApiStatus()
    {
        $result = $this->sendQuery(self::API_QUERY_GET_STATUS, null, "GET");

        if($result->statusCode == 200)
        {
            $status = $result->data->isAuthorized;
        }else {
            $status = $result->error;
        }

        if($this->isDebugMode())
        {
            Mage::log('getApiStatus() -> '.$status, null, 'multipunkty.log');
        }

        return $status;
    }

    /*
     * Checking partner status in MultiPunkty.pl (is active in service?)
     * Tip: Good method for API testing
     * 
     * @doc Section 5.2 from API specification
     *
     * @return Object { isEnabled, paymentModel, pointsAvailable }
     */
    public function getPartnerStatus()
    {
        $partnerId = Mage::helper('multipunkty')->getPartnerId();
        Mage::log($partnerId,null,'mp_points.log');
        $result = $this->sendQuery('api/1/partners/'.$partnerId.'/status', null, "GET");
        Mage::log($result,null,'mp_points.log');
        if($result->statusCode == 200)
        {
            $status = $result->data;
        }else {
            $status = $result->error;
        }
        
        
        if($this->isDebugMode())
        {
            Mage::log('getPartnerStatus() -> '.$status, null, 'multipunkty.log');
        }

        return $status;
    }

    /*
     * Register transaction in MultiPunkty service
     *
     * @doc Section 5.3 for API specification
     * 
     * @return Object { transactionId, assignUrl }
     */
    public function registerTransaction($partnerID, $transactionFK, $points, $value = 0, $title = '', $autoStatusTime = null, $autoStatus = null)
    {   
        if(is_null($autoStatusTime))
        {
            $daysToactivate = Mage::helper('multipunkty')->getTimeToActivateTransaction();
            //must be in minutes
            $autoStatusTime = $daysToactivate * 24 * 60;
        }
        
        if(is_null($autoStatus))
        {
            $autoStatus = self::API_TRANSACTION_STATUS_ACCEPT;
        }
        
        $query_body = array("partnerId" => $partnerID, 
            "transactionFK" => $transactionFK, 
            "points" => $points, 
            "value" => $value, 
            "title" => $title,
            "autoStatusTime" => $autoStatusTime,
            "autoStatus" => $autoStatus);
        $result = $this->sendQuery(self::API_QUERY_REGISTER_TRANSACTION, $query_body);
        
        if($result->statusCode == 200)
        {
            $data = $result->data;
        }else {
            $data =  $result->error;
        }
        
        if($this->isDebugMode())
        {
            Mage::log('registerTransaction() -> '.$result->statusCode.' '.$data, null, 'multipunkty.log');
        }
        
        return $data;
    }

    /*
     * Assigning transaction to approprate user in MultiPunkty service
     * 
     * @doc Section 5.4 from API specification
     * 
     * @return boolean { success }
     */
    public function assignTransactionToUser($transactionId, $mpid, $status = '', $description = '')
    {
        $query_body = array("mpid" => $mpid, "status" => $status, "description" => $description);
        $result = $this->sendQuery('api/1/transactions/'.$transactionId.'/assign', $query_body);
        
        if($this->isDebugMode())
        {
            Mage::log('assignTransactionToUser() -> '.$result->statusCode, null, 'multipunkty.log');
        }
        
        return ($result->statusCode == 200) ? true : false;
    }

    /*
     * Change transaction status (eg. from accepted to rejected)
     * 
     * @doc Section 5.5 from API specification
     * 
     * @return boolean { success }
     */
    public function changeTransactionStatus($transactionId, $status, $description = '')
    {
        $query_body = array("status" => $status, "description" => $description);
        $result = $this->sendQuery('api/1/transactions/'.$transactionId.'/status', $query_body);
        
        if($this->isDebugMode())
        {
            Mage::log('changeTransactionStatus() -> '.$result->statusCode, null, 'multipunkty.log');
        }

        return ($result->statusCode == 200) ? true : false;
    }
}
