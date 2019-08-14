<?php
 /*
  * Installation script for Multipunkty module database
 */

$installer = $this;
 
$installer->startSetup();
 
//add additional attributes to product and customer by EAV
//Will be used in future releases...
//Mage::getModel('multipunkty/eav_product')->addMultipunktyAttributesToProduct();
Mage::getModel('multipunkty/eav_customer')->addMultipunktyAttributesToCustomer();

$installer->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('orba_multipunkty_transaction')} (
    `id`  int(11) NULL AUTO_INCREMENT ,
    `order_id`  int(11) NULL ,
    `points`  int(11) NULL ,
    `value`  int(11) NULL ,
    `transaction_hash`  varchar(250) NULL ,
    `assign_url`  varchar(250) NULL,
    `status` INT(11) NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
 
$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'multipunkty_userid');
$oAttribute->setData('used_in_forms', array('customer_account_edit'));
$oAttribute->save();
 
$installer->endSetup();