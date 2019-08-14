<?php

class Orba_Multipunkty_Block_Adminhtml_System_Button_Register extends Mage_Adminhtml_Block_System_Config_Form_Field {
    
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {           
        $this->setElement($element);
        
        $helper = Mage::helper('multipunkty');
        
        if($helper->isStoreRegistered())
        {
            $btnTxt = 'Account Registered';
            $commentHtml = '';
            $element->setReadOnly(true,true);
            
            $onClickUrl = false;
        }else{
           $btnTxt = 'Register Account';           
           
           $onClickUrl = 'regStore()';
           
           $commentHtml = '<p class="note"><span>'.$helper->__('Click here to activate your account in MultiPunkty.pl service').'</span></p>';
        }
        
        //Build button
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel($helper->__($btnTxt))
                    ->setOnClick($onClickUrl);
        
        $html = $button->toHtml();
        
        /*
         * Create JS to handle register button
         */
        $jsHtml = '<script type="text/javascript">
                function regStore()
                {
                    new Ajax.Request("'.$this->getUrl('multipunkty/index/fetchdata').'", {
                        onSuccess: function(response) {
                          var data = response.responseText.evalJSON();
                          $MP.api.connectPartner(data.appId, data.partnerFK, data.secret, data.eventUrl, data.returnUrl, {
                              name: data.storeName,
                              categories: "[ " +data.categories+" ]",
                              url: data.storeUrl,
                              email: data.email,
                              phone: data.phone,
                              firstname: data.firstName
                          });
                        }
                    });
                }
                </script>';
        
        return $html . $commentHtml . $jsHtml;
    }
    
}