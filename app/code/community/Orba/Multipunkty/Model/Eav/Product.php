<?php

class Orba_Multipunkty_Model_Eav_Product extends Orba_Multipunkty_Model_Eav_Abstract {
    
    public function addMultipunktyAttributesToProduct() {
        $this->createAttribute('multipunkty_is_in_catalog', 'MultiPunkty Is in products catalog', 'select', null, array(
            'backend_type' => 'int',
            'source_model' => 'eav/entity_attribute_source_boolean',
            'sort_order' => 320,
            'is_global' => 1,
            'used_in_product_listing' => 1
        ), false, 'General');
    }
    
}
