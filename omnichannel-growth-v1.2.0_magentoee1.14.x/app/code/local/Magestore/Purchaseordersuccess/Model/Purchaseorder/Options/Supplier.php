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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Supplier
    extends Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_AbstractOption
{
    public function getSupplierOptions(){
        $collection = Mage::getResourceSingleton('suppliersuccess/supplier_collection')
            ->addFieldToSelect('supplier_code')
            ->addFieldToSelect('supplier_name')
            ->addFieldToSelect('supplier_id');
        $collection = $this->filterCollection($collection);
        $options = array('' => Mage::helper('purchaseordersuccess')->__('Please select a supplier'));
        foreach ($collection as $supplier){
            $options[$supplier->getId()] = $supplier->getSupplierName();
        }
        return $options;
    }

    /**
     * @param $collection
     * @return $collection
     */
    protected function filterCollection($collection){
        return $collection;
    }

    /**
     * Retrieve option array
     *
     * @return array()
     */
    public function getOptionHash()
    {
        return $this->getSupplierOptions();
    }
}