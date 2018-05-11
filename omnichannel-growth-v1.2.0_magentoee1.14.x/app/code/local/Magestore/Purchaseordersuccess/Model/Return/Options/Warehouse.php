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
class Magestore_Purchaseordersuccess_Model_Return_Options_Warehouse
    extends Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_AbstractOption
{
    public function getSupplierOptions(){
        $collection = Mage::getResourceSingleton('inventorysuccess/warehouse_collection')
            ->addFieldToSelect('warehouse_code')
            ->addFieldToSelect('warehouse_name')
            ->addFieldToSelect('warehouse_id');
        $collection = $this->filterCollection($collection);
        $options = array('' => Mage::helper('purchaseordersuccess')->__('Please select a warehouse'));
        foreach ($collection as $warehouse){
            $options[$warehouse->getId()] = $warehouse->getData('warehouse_name');
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