<?php

class Orba_Multipunkty_Block_Adminhtml_System_Select_Disabled extends Mage_Adminhtml_Block_System_Config_Form_Field {
    
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {           
        $this->setElement($element);
        
        $element->setReadonly(true, true);
        
        return $element->getElementHtml();
    }
    
}