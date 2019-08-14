<?php

class Orba_Multipunkty_Model_System_Config_Backend_Service_Launch extends Mage_Core_Model_Config_Data {
    
    protected function _afterLoad()
    {
        if(!Mage::helper('multipunkty')->isStoreRegistered())
        {
            Mage::getModel('core/config')->saveConfig('multipunkty/start_settings/launch_service', "0");
            $this->setValue(0);
        }
        
        parent::_afterLoad();
    }
    
}
