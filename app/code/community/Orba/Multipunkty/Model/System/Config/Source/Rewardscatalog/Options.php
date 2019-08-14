<?php

class Orba_Multipunkty_Model_System_Config_Source_Rewardscatalog_Options extends Mage_Core_Model_Abstract {
    
    public function toOptionArray()
    {
        $options = array(
           array('value' => 'none', 'label' => Mage::helper('multipunkty')->__('None')),
           array('value' => 'all', 'label' => Mage::helper('multipunkty')->__('All')),
           array('value' => 'category', 'label' => Mage::helper('multipunkty')->__('From Category')),
        );
        
        return $options;
    }
    
}