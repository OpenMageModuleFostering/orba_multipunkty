<?php

class Orba_Multipunkty_Model_System_Config_Backend_Service_Status extends Mage_Core_Model_Config_Data {
    
    protected function checkActivation()
    {
        $_helper = Mage::helper('multipunkty');
        
        if($_helper->isActive() && $_helper->isStoreRegistered() && Mage::getStoreConfig('multipunkty/start_settings/points_amount') != 0)
        {
            Mage::getModel('core/config')->saveConfig('multipunkty/start_settings/service_status', "1");
            $this->setValue(1);
        }else{
            Mage::getModel('core/config')->saveConfig('multipunkty/start_settings/service_status', "0");
            $this->setValue(0);
        }
    }


    protected function _afterLoad()
    {
        $this->checkActivation();
        parent::_afterLoad();
    }
    
    /* protected function _beforeSave()
    {
        $this->checkActivation();
        parent::_beforeSave();
    } */
    
}

