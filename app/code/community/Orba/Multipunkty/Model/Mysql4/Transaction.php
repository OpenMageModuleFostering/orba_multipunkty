<?php

class Orba_Multipunkty_Model_Mysql4_Transaction extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('multipunkty/transaction', 'id');
	}
}