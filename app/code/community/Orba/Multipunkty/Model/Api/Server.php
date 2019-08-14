<?php

/*
 * MultiPunkty.pl API Server, compatible with API version 5
 * 
 * @author Michał Zabielski <michal.zabielski@orba.pl>
 * @created         25-11-2013
 * @lastModified    26-11-2013
 */
class Orba_Multipunkty_Model_Api_Server extends Orba_Multipunkty_Model_Api_Abstract {
    
    /*
     * Validates if time of response fits in tolerantion time of response,
     * which equals to 1800 seconds (30 minutes)
     * 
     * @doc Section 4.1 from API specification
     * 
     * @return boolean { fitToTolerantionLevel }
     */
    private function validateTimeTolerance($qTime)
    {
        $time = time();
        $qTimestamp = strtotime($qTime);
        $timeDiff = abs($qTimestamp-$time);

        if($timeDiff > self::API_TIME_MAX_TOLERANTION_SEC)
        {
            return 0;
        }else{
            return 1;
        }
    }
    
    /*
     * Validates AppID of received response
     * 
     * @doc Section 4.1 from API specification
     * 
     * @return boolean { accept }
     */
    private function validateAppId($qAppId)
    {
        $appId = Mage::helper('multipunkty')->getApiKey();
        if(is_null($appId) || $appId === '')
        {
            $appId = Mage::getStoreConfig('multipunkty/basic_config/platform_api_key');
        }
        
        //for test purposes only
        //$appId = 'fb62897b90ad4d1d8af1adeb5a0a7b66';
        
        if(strcmp($appId, $qAppId))
        {
            return 0;                
        }else{
            return 1;
        }
    }
    
    /*
     * Validates private key of received response
     * 
     * @doc Section 4.1 from API specification
     * 
     * @return boolean { accept }
     */
    private function validatePrivateKey($qAppId, $qTime, $qNonce, $qHash)
    {
        $hashDecode = base64_decode($qHash);
        
        $privatekey = Mage::helper('multipunkty')->getPrivateKey();
        if(is_null($privatekey) || $privatekey === '')
        {
            $privatekey = Mage::getStoreConfig('multipunkty/basic_config/platform_private_key');
        }
        
        //For test purposes only
        //$privatekey = 'f1e1e13b0de2ea9e1ce072a0a83a49832c5a59ad33c7b6c07f7b94aa0f90322f';
        
        $hashNew = hash_hmac('sha256', $qAppId.'&'.$qTime.'&'.$qNonce.'&'.$privatekey, $privatekey, true);
        
        if(strcmp($hashNew, $hashDecode))
        {
            return 0;                
        }else{
            return 1;
        }
    }

    /*
     * Validating queries received from MultiPunkty.pl
     * 
     * @doc Section 4.1, 4.3 and 6 from API specification
     * 
     * @return boolean { isValid }
     */
    public function validateQuery($query)
    {   
        if(strpos($query,'MultiPunktyApp') !== false)
        {
            $query = str_replace('MultiPunktyApp ', '', $query);
            $isValid = false;
            $result = base64_decode($query);
            $queryData = explode('&', $result);
            
            $valAppId = $this->validateAppId($queryData[0]);
            $valTime = $this->validateTimeTolerance($queryData[1]);
            $valPK = $this->validatePrivateKey($queryData[0], $queryData[1], $queryData[2], $queryData[3]);
            
            //verifyData
            if($valAppId && $valTime && $valPK)
            {
                $isValid = true;
            }

            return $isValid;
        }else{
            return false;
        }
    }
    
    /*
     * Receive data about registered account - function to complete registration process
     * 
     * @doc Section 6.1 from API specification
     * 
     * @return boolean { success }
     */
    public function getRegisteredData($auth, $data)
    {   
        if($this->validateQuery($auth))
        {
            $nonce = Mage::getModel('multipunkty/nonce')->load($data->secret, 'secret_phrase');
            
            if($nonce->getId())
            {
                Mage::getModel('core/config')->saveConfig('multipunkty/basic_config/api_key', $data->appId);
                Mage::getModel('core/config')->saveConfig('multipunkty/basic_config/private_key', $data->key);
                Mage::getModel('core/config')->saveConfig('multipunkty/basic_config/partner_id', $data->partnerId);
                
                //delete used secret phrase
                $nonce->delete();
                
                return '{ statusCode: 200 }';
            }else{
                return '{ statusCode: 403, error: "Forbidden: Your access data are invalid" }';
            }
        }else{
            return '{ statusCode: 403, error: "Error: Query validation failed" }';
        }
    }
    
    /*
     * Receive data about changing partner status
     * 
     * @doc Section 6.2 from API specification 
     */
    public function getPartnerStatus($auth, $data)
    {
        if($this->validateQuery($auth))
        {
            if(Mage::getStoreConfig('multipunkty/basic_config/partner_id') == $data->partnerId)
            {
                Mage::getModel('core/config')->saveConfig('multipunkty/start_settings/service_status', $data->isEnabled);
                Mage::getModel('core/config')->saveConfig('multipunkty/start_settings/points_amount', $data->pointsAvailable);
                
                return '{ statusCode: 200 }';
            }else{
                return '{ statusCode: 403, error: "Error: PartnerID is incorrect" }';
            }
        }else{
            return '{ statusCode: 403, error: "Error: Query validation failed" }';
        }
    }
}
