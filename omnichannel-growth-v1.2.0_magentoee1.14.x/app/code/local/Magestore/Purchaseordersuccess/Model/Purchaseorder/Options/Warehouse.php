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
class Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Warehouse
    extends Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_AbstractOption
{
    public function getWarehouseOptions(){
        $collection = Mage::getResourceModel('inventorysuccess/warehouse_collection')
            ->addFieldToSelect(Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_ID)
            ->addFieldToSelect(Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_CODE)
            ->addFieldToSelect(Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_NAME);
        $resourceId = 'admin/purchaseordersuccess/purchaseorder/transfer';
        $collection = Magestore_Coresuccess_Model_Service::permissionService()->filterPermission(
            $collection,
            $resourceId
        );
        $options = array('' => Mage::helper('purchaseordersuccess')->__('Please select a warehouse'));
        foreach ($collection->getItems() as $warehouse){
            $options[$warehouse->getId()] = $warehouse->getWarehouseName() .' ('. $warehouse->getWarehouseCode() .')';
        }
        return $options;
    }

    /**
     * Retrieve option array
     *
     * @return array()
     */
    public function getOptionHash()
    {
        return $this->getWarehouseOptions();
    }
}