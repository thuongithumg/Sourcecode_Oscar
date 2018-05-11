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
 * Catalog Observer
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magestore_Inventorysuccess_Model_Dataflow_Observer
{
    protected $warehouseIds = array();
    protected $warehouses = array();
    protected $warehouseAdjustStock = array();
    protected $warehouseAdjustIds = array();
    protected $warehouseProductLocations = array();
    protected $warehouseLocationIds = array();

    /**
     * create adjust stock and update product shelf location to warehouse
     */
    public function updateDataWarehouseProduct()
    {
        $saveData = Mage::getModel('adminhtml/session')->getData('save_data_flow', array());
        if (count($saveData)) {
            if (isset($saveData['warehouse_ids'])) {
                $this->warehouseIds = $saveData['warehouse_ids'];
            }
            if (isset($saveData['warehouse_adjust_stock'])) {
                $this->warehouseAdjustStock = $saveData['warehouse_adjust_stock'];
            }
            if (isset($saveData['warehouse_adjust_ids'])) {
                $this->warehouseAdjustIds = $saveData['warehouse_adjust_ids'];
            }
            if (isset($saveData['warehouse_product_locations'])) {
                $this->warehouseProductLocations = $saveData['warehouse_product_locations'];
            }
            if (isset($saveData['warehouse_location_ids'])) {
                $this->warehouseLocationIds = $saveData['warehouse_location_ids'];
            }
            if (isset($saveData['warehouses'])) {
                $this->warehouses = $saveData['warehouses'];
            }

            /** create adjust stock */
            $this->createAdjustStock();

            /** update location for product in warehouse */
            $this->updateLocationProductWarehouse();
        }
        Mage::getModel('adminhtml/session')->unsetData('save_data_flow');
    }

    /**
     * create adjust stock
     */
    public function createAdjustStock()
    {
        if (!empty($this->warehouseAdjustStock)) {
            foreach ($this->warehouseAdjustIds as $warehouseId) {
                $productToAdjusts = $this->warehouseAdjustStock[$warehouseId];
                $adjustData['products'] = $productToAdjusts['products'];

                $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_ID] = $warehouseId;
                $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_CODE] = isset($this->warehouses[$warehouseId]['warehouse_code']) ?
                    $this->warehouses[$warehouseId]['warehouse_code'] :
                    null;
                $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_NAME] = isset($this->warehouses[$warehouseId]['warehouse_name']) ?
                    $this->warehouses[$warehouseId]['warehouse_name'] :
                    null;
                $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::REASON] = Mage::helper('inventorysuccess')->__('Import Products');
                if (!empty($productToAdjusts)) {
                    /** @var Magestore_Inventorysuccess_Model_Adjuststock $adjustStock */
                    $adjustStock = Mage::getModel('inventorysuccess/adjuststock');

                    $adjustStockService = Magestore_Coresuccess_Model_Service::adjustStockService();
                    /* create stock adjustment, require products */
                    $adjustStockService->createAdjustment($adjustStock, $adjustData);

                    /* created adjuststock or not */
                    if($adjustStock->getId()) {
                        /* complete stock adjustment */
                        $adjustStockService->complete($adjustStock, false);
                    }
                }
            }
        }
    }

    /**
     * update product shelf location to warehouse
     */
    public function updateLocationProductWarehouse()
    {
        if (!empty($this->warehouseProductLocations)) {
            foreach ($this->warehouseLocationIds as $warehouseId) {
                if (!empty($this->warehouseProductLocations[$warehouseId])) {
                    $locations = $this->warehouseProductLocations[$warehouseId];
                    $stockRegistryService = Magestore_Coresuccess_Model_Service::stockRegistryService();
                    $stockRegistryService->updateLocation($warehouseId, $locations);
                }
            }
        }
    }
}
