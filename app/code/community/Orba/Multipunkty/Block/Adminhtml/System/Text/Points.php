<?php

class Orba_Multipunkty_Block_Adminhtml_System_Text_Points extends Mage_Adminhtml_Block_System_Config_Form_Field {
    
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $helper = Mage::helper('multipunkty');
        $txtMore = $helper->__('You get 1000 MP$ at the beginning, which you can reward your customers for the purchase');
        $url = $helper->getBuyCreditsUrl();
        $element->setReadOnly(true, true);
        $element->setTitle($txtMore);
        $this->setElement($element);
        
        $html = $element->getElementHtml();
        $html .= '<p class="note"><span><a href="'.$url.'" target="_blank" title="';
        $html .= $txtMore;
        $html .= '">'.$helper->__('Buy credits').'</a></span></p>';
        
        return $html;
    }
    
}