<?php
/*
 * Basic helper class
 * 
 * @author
 * 
 * @created
 * @lastModified 22-01-2014
 * @lastModifiedBy Michał Zabielski <michal.zabielski@orba.pl>
 */
class Orba_Multipunkty_Helper_Data extends Mage_Core_Helper_Abstract {
    //list of important URL's
    const URL_BUY_CREDITS = 'http://partner.multipunkty.pl/stan-konta-doladuj';
    const URL_ABOUT_MP_SITE = 'czym-sa-multipunkty';
    const URL_HOME_PAGE = 'http://multipunkty.pl';
    const URL_REGISTER_ACCOUNT_PAGE = 'http://multipunkty.pl/rejestracja';
    const MAIL_SENDER_EMAIL = 'cafenews@cafenews.pl'; //deprecated
    const MAIL_SENDER_NAME = 'MultiPunkty.pl'; //deprecated
    
    public function isActive() {
        return Mage::getStoreConfig('multipunkty/start_settings/launch_service');
    }
    
    public function serviceActive()
    {
        return Mage::getStoreConfig('multipunkty/start_settings/service_status') && $this->getPointsAmount() != 0;
    }
    
    public function isActiveProduct() {
        return Mage::getStoreConfig('multipunkty/presenting_information/show_near_product');
    }
    
    public function isActiveCart() {
        return Mage::getStoreConfig('multipunkty/presenting_information/show_near_shopping_cart');
    }
    
    public function isActiveHome() {
        return Mage::getStoreConfig('multipunkty/presenting_information/show_on_homepage');
    }
    
    public function isActiveCheckout() {
        return Mage::getStoreConfig('multipunkty/presenting_information/show_on_summary');
    }
    
    public function isStoreRegistered()
    {
        $isApiKey = (is_null(Mage::getStoreConfig('multipunkty/basic_config/api_key')) || Mage::getStoreConfig('multipunkty/basic_config/api_key') === '') ? false : true;
        $isPrivKey = (is_null(Mage::getStoreConfig('multipunkty/basic_config/private_key')) || Mage::getStoreConfig('multipunkty/basic_config/private_key') === '') ? false : true;
        $isPartnerId = (is_null(Mage::getStoreConfig('multipunkty/basic_config/partner_id'))|| Mage::getStoreConfig('multipunkty/basic_config/partner_id') === '') ? false : true;
        
        return $isApiKey && $isPrivKey && $isPartnerId;
    }
    
    public function isCashOnDeliveryAccepted()
    {
        return Mage::getStoreConfig('multipunkty/rewarding_settings/apply_cash_on_delivery');
    }
    
    public function getPurchaseReward() {
        return Mage::getStoreConfig('multipunkty/rewarding_settings/reward_purchase');
    }
    
    public function getBuyCreditsUrl()
    {
        return self::URL_BUY_CREDITS;
    }
    
    public function getAboutMPSiteUrl()
    {
        return self::URL_HOME_PAGE . '/' . self::URL_ABOUT_MP_SITE;
    }
    
    public function getHomePageUrl()
    {
        return self::URL_HOME_PAGE;
    }
    
    public function getRegisterAccountUrl()
    {
        return self::URL_REGISTER_ACCOUNT_PAGE;
    }
    
    public function getApiKey()
    {
        return Mage::getStoreConfig('multipunkty/basic_config/api_key');
    }
    
    public function getPartnerId()
    {
        return Mage::getStoreConfig('multipunkty/basic_config/partner_id');
    }
    
    public function getPrivateKey()
    {
        return Mage::getStoreConfig('multipunkty/basic_config/private_key');
    }
    
    public function getTimeToActivateTransaction()
    {
        return Mage::getStoreConfig('multipunkty/rewarding_settings/days_after_trans_act');
    }
    
    public function getTransactionStatus($status)
    {
        switch($status)
        {
            case 'Accept':
                return 'Accept';
                
            case 'Reject':
            default:
                return 'Reject';
        }
    }
    
    public function getPointsAmount()
    {
        return Mage::getStoreConfig('multipunkty/start_settings/points_amount');
    }
    
    public function isDebugMode()
    {
        return Mage::getStoreConfig('multipunkty/basic_config/debug_mode');
    }
    
    public function getMailSenderEmail()
    {
        return self::MAIL_SENDER_EMAIL;
    }
    
    public function getMailSenderName()
    {
        return self::MAIL_SENDER_NAME;
    }
}