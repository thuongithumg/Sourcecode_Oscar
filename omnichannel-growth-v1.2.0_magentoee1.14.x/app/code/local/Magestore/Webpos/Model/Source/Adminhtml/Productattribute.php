<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

class Magestore_Webpos_Model_Source_Adminhtml_Productattribute extends Varien_Object {
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = null;
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addVisibleFilter()
            ->addFieldToFilter('frontend_input', array('nin'=> array('select','media_image','multiselect','gallery','weee','boolean','date')));

        if ($attributes != null && $attributes->count() > 0):
            $result[] = array('value' => 'entity_id' ,'label' => 'ID');
            foreach ($attributes as $item):
                $result[] = array('value' => $item->getAttributeCode(), 'label' => Mage::helper('webpos')->__($item->getFrontendLabel()));
            endforeach;
        endif;
        return $result;

//        $options = [];
//        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
//            ->addFieldToFilter('frontend_input', ['in' => ['text', 'textarea']]);
//        if (!empty($attributes)) {
//            foreach ($attributes as $attribute) {
//                $options[] = array('value' => $attribute->getAttributeCode(), 'label' => Mage::helper('webpos')->__($attribute->getFrontendLabel()));
//            }
//        }
//        return $options;
    }

}
