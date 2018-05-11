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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Adjuststock Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_LowStockNotification_Source_Rule_Warehouse extends Magestore_Inventorysuccess_Model_LowStockNotification_Source_AbstractSource
{

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->getAvailableWarehouse();
        return $availableOptions;
    }

    /**
     * Prepare rule's warehouse.
     *
     * @return array
     */
    public function getAvailableWarehouse()
    {
        $warehouses = array();
        /** @var Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection $warehouseCollection */
        $warehouseCollection = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        /** @var Magestore_Inventorysuccess_Model_Warehouse $warehouse */
        foreach ($warehouseCollection as $warehouse) {
            $warehouses[$warehouse->getId()] = $warehouse->getWarehouseName();
        }

        return $warehouses;
    }
}
