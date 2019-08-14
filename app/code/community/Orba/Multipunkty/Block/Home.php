<?php

class Orba_Multipunkty_Block_Home extends Mage_Core_Block_Template {
    protected function _construct()
    {
        parent::_construct();
    }
    
    public function isVisible()
    {
        if (Mage::helper('multipunkty')->isActiveHome() AND Mage::helper('multipunkty')->serviceActive())
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