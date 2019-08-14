<?php

/*
  * Installation script for Multipunkty module database
 *  
 *  Change: Added platform key set
 * 
 * @author Michał Zabielski <michal.zabielski@orba.pl>
 */
 
$installer = $this;
 
$installer->startSetup();

//add key set to module system config
Mage::getModel('core/config')->saveConfig('multipunkty/basic_config/platform_api_key', "ab9687d7d975420298325df02c610760");
Mage::getModel('core/config')->saveConfig('multipunkty/basic_config/platform_private_key', "ed18e87476eef6fb7f55c56cafa35ac95cef03d8a319fa7aad5af6b895463614");
Mage::getModel('core/config')->saveConfig('multipunkty/basic_config/platform_partner_id', "160");
//adding variable to detect debug mode
Mage::getModel('core/config')->saveConfig('multipunkty/basic_config/debug_mode', "0");

$installer->endSetup();