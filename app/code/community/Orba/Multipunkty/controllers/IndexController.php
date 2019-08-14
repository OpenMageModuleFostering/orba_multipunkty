<?php
/*
 * Controller to handle Multipunkty API v6 and test it
 * 
 * @author Michał Zabielski <michal.zabielski@orba.pl>
 * 
 * @created 25-11-2013
 * @lastModified 3-12-2013
 */

class Orba_Multipunkty_IndexController extends Mage_Core_Controller_Front_Action {
    
    /*
     * Get HTTP headers to get Authorization header
     */
    protected function _getAllHeaders() 
    { 
        $headers = ''; 
       foreach ($_SERVER as $name => $value) 
       { 
           if (substr($name, 0, 5) == 'HTTP_') 
           { 
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
           } 
       } 
       return $headers; 
    }
    
    /*
     * For test purposes
     */
    public function indexAction()
    {
        $params = Mage::app()->getRequest()->getParams();
        echo Mage::helper('core/url')->getCurrentUrl();
        print_r($params);
    }
    
    /*
     * Action that handles registration in Multipunkty.pl service
     * 
     * @doc Section 6.1 from API specification
     */
    public function connectedAction()
    {
        $headers = $this->_getAllHeaders();
        $auth = $headers['Authorization'];
        
        if(empty($auth))
        {
            echo '{ statusCode: 403, error: "Error: Authorization failed" }';
        }else{
            $postData = file_get_contents('php://input');
            $data = json_decode($postData);
            
            if(is_null($data))
            {
                echo '{ statusCode: 403, error: "Error: Query validation failed" }';
            }else{
                $server = Mage::getModel('multipunkty/api_server');
                $result = $server->getRegisteredData($auth, $data);
                
                echo $result;
            }
        }
    }
    
    /*
     * Action that handles parter status change
     * 
     * @doc Section 6.2 from API specification
     */
    public function statusAction()
    {
        $headers = $this->_getAllHeaders();
        $auth = $headers['Authorization'];
        
        if(is_null($auth))
        {
            echo '{ statusCode: 403, error: "Error: Authorization failed" }';
        }else{
            $postData = file_get_contents('php://input');
            $data = json_decode($postData);
            
            if(is_null($data))
            {
                echo '{ statusCode: 403, error: "Error: Query validation failed" }';
            }else{
                $server = Mage::getModel('multipunkty/api_server');
                $result = $server->getPartnerStatus($auth, $data);
                
                echo $result;
            }
        }
    }
    
    /*
     * Method to fetch data for register button
     */
    public function fetchdataAction()
    {
        $appId = Mage::getStoreConfig('multipunkty/basic_config/platform_api_key');
        $secret = (string)rand(0, 99999);
        $partnerFK = Mage::getStoreConfig('multipunkty/basic_config/platform_partner_id');

        $nonce = Mage::getModel('multipunkty/nonce');
        $nonce->setSecretPhrase($secret);
        $nonce->setQueryTime(date("Ymd").'T'.date("His"));
        $nonce->save();

        $storeUrl = htmlentities(Mage::helper('core/url')->getHomeUrl());
        //$returnUrl = Mage::helper('core/url')->getCurrentUrl();
        $returnUrl = $this->getRequest()->getServer('HTTP_REFERER');
        $eventUrl = str_replace('/index.php', '', $storeUrl) . 'multipunkty';
        $storeName = Mage::app()->getStore()->getFrontendName();
        $categories = 'Sklep';
        $email = Mage::getStoreConfig('trans_email/ident_general/email');
        $phone = Mage::getStoreConfig('general/store_information/phone');
        $firstName = Mage::getStoreConfig('trans_email/ident_general/name');
        
        $data = array(
            "appId" => $appId,
            "secret" => $secret,
            "partnerFK" => $partnerFK,
            "storeUrl" => $storeUrl,
            "returnUrl" => $returnUrl,
            "eventUrl" => $eventUrl,
            "storeName" => $storeName,
            "categories" => $categories,
            "email" => $email,
            "phone" => $phone,
            "firstName" => $firstName,
        );
        
        echo json_encode($data);
    }
    
    public function testapiAction()
    {
        $result = Mage::getModel('multipunkty/api_client')->getPartnerStatus();
        
        var_dump($result);
    }
    
}