<?php

class Orba_Multipunkty_Model_System_Config_Source_Points_Ttl extends Mage_Core_Model_Abstract {
    
    public function toOptionArray()
    {
        $helper = Mage::helper('multipunkty');
        
        $options = array(
           array('value' => 14, 'label' => '14 ' . $helper->__("days")),
           array('value' => 30, 'label' => '30 ' . $helper->__("days")),
           array('value' => 60, 'label' => '60 ' . $helper->__("days")),
        );
        
        return $options;
    }
    
}