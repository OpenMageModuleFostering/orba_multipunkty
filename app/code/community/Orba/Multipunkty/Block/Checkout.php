<?php

class Orba_Multipunkty_Block_Checkout extends Mage_Core_Block_Template {
    protected function _construct()
    {
        parent::_construct();
    }
    
    public function isVisible()
    {
        $helper = Mage::helper('multipunkty');
        
        if ($helper->isActiveCheckout() AND $helper->serviceActive() AND $helper->isCashOnDeliveryAccepted())
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