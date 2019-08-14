<?php

/*
  * Installation script for Multipunkty module database
 * 
 *  Change: Add database table to store nonces for API queries
 *  
 * @author Michał Zabielski <michal.zabielski@orba.pl>
 */
 
$installer = $this;
 
$installer->startSetup();

//datetime format compatible with API: yyyyMMddTHHmmss
$installer->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('orba_multipunkty_nonce')} (
    `id`  int(11) NOT NULL AUTO_INCREMENT ,
    `secret_phrase`  varchar(32) NOT NULL ,
    `query_time`  char(15) NOT NULL ,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();