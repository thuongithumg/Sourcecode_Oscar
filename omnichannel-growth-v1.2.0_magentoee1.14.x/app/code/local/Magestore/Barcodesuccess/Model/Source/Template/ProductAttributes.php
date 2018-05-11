<?php

/**
 * Class Magestore_Barcodesuccess_Model_Source_Template_Measurement
 */
class Magestore_Barcodesuccess_Model_Source_Template_ProductAttributes extends
    Varien_Object
{
    /**
     * Get options
     *
     * @return array
     */
    public static function toOptionArray()
    {
        $attributes = Mage::getSingleton('catalogsearch/advanced')->getAttributes();
        $options = array();
        $options[] = array('value' => 'sku' ,'label' => 'Sku');
        foreach($attributes as $attribute){
            if( ($attribute->getAttributeCode() ==='sku') ||
                ($attribute->getAttributeCode() ==='description') ||
                    ($attribute->getAttributeCode() ==='short_description'))
                continue;
            $options[] = array('value' => $attribute->getAttributeCode(), 'label' => $attribute->getFrontendLabel());
        }
        return $options;
    }
}

