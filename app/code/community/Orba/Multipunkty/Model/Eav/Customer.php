<?php

class Orba_Multipunkty_Model_Eav_Customer extends Orba_Multipunkty_Model_Eav_Abstract {
    
    public function addMultipunktyAttributesToCustomer() {
        $entity_type = 'customer';
        
        $this->createAttribute('multipunkty_userid', 'User ID in MultiPunkty.pl service', 'text', null, array(
            'backend_type' => 'varchar',
            'sort_order' => 200,
            'is_global' => 1,
            'is_required' => 1,
        ), false, 'Account Information', $entity_type);
        
        $this->createAttribute('multipunkty_active', 'Is user active in MultiPunkty service', 'select', null, array(
            'backend_type' => 'int',
            'source_model' => 'eav/entity_attribute_source_boolean',
            'sort_order' => 210,
            'is_global' => 1
        ), false, 'Account Information', $entity_type);
        
        $this->createAttribute('multipunkty_userlogin', 'User login in MultiPunkty.pl service', 'text', null, array(
            'backend_type' => 'varchar',
            'sort_order' => 220,
            'is_global' => 1,
            'is_required' => 1,
        ), false, 'Account Information', $entity_type);
    }
    
}
