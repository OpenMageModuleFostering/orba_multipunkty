<?php

class Orba_Multipunkty_Block_Checkout_Transaction extends Mage_Core_Block_Template {
    protected function _construct()
    {
        parent::_construct();
    }
	
	public function isVisible()
    {
        if (Mage::helper('multipunkty')->isActive())
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function getPurchaseReward()
    {
        return Mage::helper('multipunkty')->getPurchaseReward();
    }
}