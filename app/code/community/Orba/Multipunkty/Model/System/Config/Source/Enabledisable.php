<?php

class Orba_Multipunkty_Model_System_Config_Source_Enabledisable
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('multipunkty')->__('Enabled')),
            array('value'=>0, 'label'=>Mage::helper('multipunkty')->__('Disabled')),
        );
    }
}