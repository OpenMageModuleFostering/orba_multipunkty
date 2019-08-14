<?php

class Orba_Multipunkty_Model_System_Config_Backend_Points extends Mage_Core_Model_Config_Data {
    
    public function _afterLoad()
    {
        //take points number from API
        $points = Mage::getModel('multipunkty/api_client')->getPartnerStatus()->pointsAvailable;
        
        if(is_null($points))
        {
            $points = Mage::getStoreConfig('multipunkty/start_settings/points_amount');
            if(is_null($points))
            {
                $points = 0;
            }
        }
        
        Mage::getModel('core/config')->saveConfig('multipunkty/start_settings/points_amount', $points);
        $this->setValue($points);
 
        return parent::_afterLoad();
    }
    
}


