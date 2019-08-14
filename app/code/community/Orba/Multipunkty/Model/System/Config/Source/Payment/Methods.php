<?php

class Orba_Multipunkty_Model_System_Config_Source_Payment_Methods extends Mage_Core_Model_Abstract {
    
    public function toOptionArray()
    {
        $options = array(
           array('value' => false, 'label' => Mage::helper('multipunkty')->__('Payment in advance')),
           array('value' => true, 'label' => Mage::helper('multipunkty')->__('Payment in advance & Cash on delivery')),
        );
        
        return $options;
    }
    
}