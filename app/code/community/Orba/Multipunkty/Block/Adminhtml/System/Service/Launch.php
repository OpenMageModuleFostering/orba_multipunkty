<?php

class Orba_Multipunkty_Block_Adminhtml_System_Service_Launch extends Mage_Adminhtml_Block_System_Config_Form_Field {
    
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {           
        $this->setElement($element);
        
        if(!Mage::helper('multipunkty')->isStoreRegistered())
        {
            Mage::getModel('core/config')->saveConfig('multipunkty/start_settings/launch_service', "0");
            $element->setReadOnly(true,true);
        }
        
        return $element->getElementHtml();
    }
    
}