<?php

class Orba_Multipunkty_Block_Adminhtml_System_Service_Status extends Mage_Adminhtml_Block_System_Config_Form_Field {
    
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $_helper = Mage::helper('multipunkty');
        $descrHtml = '';
        
        $this->setElement($element);
        
        $element->setReadOnly(true,true);
        
        if(!($_helper->isActive() && $_helper->isStoreRegistered() && Mage::getStoreConfig('multipunkty/start_settings/points_amount') != 0)){
            if(!$_helper->isStoreRegistered())
            {
                $msg = $_helper->__('No account - create one or complete registration process');
            }else if(Mage::getStoreConfig('multipunkty/start_settings/points_amount') == 0) {
                $msg = $_helper->__('No Multipoints - buy credits');
            }else {
                
                $apiClient = Mage::getModel('multipunkty/api_client');
                $data = $apiClient->getPartnerStatus();
                
                if($data->isEnabled)
                {
                    $msg = '';
                }else{
                    $msg = $_helper->__('After activating your account using activation link sent by email, please launch service in module and save changes');
                }
                
            }
            
            if(!empty($msg))
            {
                $descrHtml = '<p class="note"><span style="color: #ea7601;">'.$msg.'</span></p>';
            }
        }
        
        $html = $element->getElementHtml();
        $html .= $descrHtml;
        
        return $html;
    }
    
}
