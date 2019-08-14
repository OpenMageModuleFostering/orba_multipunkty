<?php

class Orba_Multipunkty_Model_Mysql4_Transaction_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

	public function _construct()
	{
		parent::_construct();
		$this->init('multipunkty/transaction');
	}
	
}