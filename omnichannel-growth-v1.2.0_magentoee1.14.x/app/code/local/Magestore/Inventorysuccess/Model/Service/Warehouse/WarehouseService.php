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
class Magestore_Inventorysuccess_Model_Service_Warehouse_WarehouseService
{
    const DEFAULT_WAREHOUSE_ID = 2;
    
    /**
     * remove the warehouse which has the same ID with default Stock (1)
     * 
     * @return Magestore_Inventorysuccess_Model_Service_Warehouse_WarehouseService
     */
    public function removeFirstWarehouseId()
    {
        $warehouse = $this->getPrimaryWarehouse();
        if(!$warehouse->getId()) {
            $warehouse->setWarehouseName('Primary Warehouse');
            $warehouse->setWarehouseCode('primary');
            $warehouse->setIsPrimary(Magestore_Inventorysuccess_Model_Warehouse::PRIMARY_YES);
            $warehouse->save();
            $warehouse->delete();
        }        
        if($warehouse->getId() == Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID) {
            $warehouse->delete();
        }
        return $this;
    }
    
    /**
     * create primary warehouse
     * 
     * @return Magestore_Inventorysuccess_Model_Warehouse
     */
    public function createPrimaryWarehouse()
    {
        $warehouse = $this->getPrimaryWarehouse();
        if(!$warehouse->getId()) {
            $warehouse->setWarehouseName('Primary Warehouse');
            $warehouse->setWarehouseCode('primary');
            $warehouse->setIsPrimary(Magestore_Inventorysuccess_Model_Warehouse::PRIMARY_YES);
            $warehouse->save();
        }
        /* check if primay warehouse has the same ID with default Stock */
        if($warehouse->getWarehouseId() == Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID) {
            $warehouse->delete();
            $warehouse = $this->createPrimaryWarehouse();
        }
        return $warehouse;
    }
    
    /**
     * get primary warehouse
     * 
     * @return Magestore_Inventorysuccess_Model_Warehouse
     */
    public function getPrimaryWarehouse()
    {
        $warehouse = Mage::getModel('inventorysuccess/warehouse')
            ->getCollection()
            ->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse::IS_PRIMARY, Magestore_Inventorysuccess_Model_Warehouse::PRIMARY_YES)
            ->setPageSize(1)
            ->setCurPage(1)
            ->getFirstItem();    
        return $warehouse;
    }


    public function addProductToWarehouse($warehouseId, $productIds = array()){
        $adjustProductData = $this->prepareAdjustProductData($productIds);
        if(!empty($adjustProductData)){
            $adjustData = $this->_prepareAdjustmentData($adjustProductData, $warehouseId);
            Magestore_Coresuccess_Model_Service::warehouseStockService()->createAdjustment($adjustData, false);
        }
        return $this;
    }

    /**
     * Prepare adjust product data from none warehouse product ids
     * 
     * @param array $productIds
     * @return array
     */
    public function prepareAdjustProductData($productIds = array()){
        $collection = Mage::getResourceModel('inventorysuccess/warehouse_nonwarehouseproduct_collection');
        $collection->addProductsToFilter($productIds);
        $adjustProductData = array();
        foreach ($collection as $product){
            $adjustProductData[$product->getEntityId()] = array(
                'adjust_qty' => $product->getQty()?$product->getQty():0,
                'old_qty' => 0,
                'product_sku' => $product->getSku(),
                'product_name' => $product->getName()
            );
        }
        return $adjustProductData;
    }

    /**
     * Prepare stock adjustment data
     *
     * @param array $adjustData
     * @param int $warehouseId
     * @return array
     */
    protected function _prepareAdjustmentData($adjustProductData = array(), $warehouseId)
    {
        $adjustData = array('products' => $adjustProductData);
        $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_ID] = $warehouseId;
        $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_NAME] = null;
        $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_CODE] = null;
        $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::REASON] =
            Mage::helper('inventorysuccess')->__('Add Non-Warehouse Product');
        return $adjustData;
    }
    
    /**
     * 
     * @param array $warehouseIds
     * @param string $aclResource
     * @return array
     */
    public function getWarehouses($warehouseIds = array(), $aclResource = '')
    {
        $warehouses = array();
        $collection = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        if(count($warehouseIds)) {
            $collection->addFieldToFilter('warehouse_id', array('in' => $warehouseIds));
        }
        if($aclResource) {
            $collection = Magestore_Coresuccess_Model_Service::permissionService()->filterPermission(
                $collection, $aclResource
            );            
        }

        if($collection->getSize()) {
            foreach($collection as $warehouse) {
                $warehouses[$warehouse->getId()] = $warehouse->getData();
            }
        }        
        return $warehouses;
    }
    
    /**
     * 
     * @return Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection
     */
    public function getEnableWarehouses()
    {
        $collection = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        //$collection->addFieldToFilter('status', Magestore_Inventorysuccess_Model_Warehouse::ENABLE);
        return $collection;
    }
    
    /**
     * 
     * @param int $storeId
     * @return Magestore_Inventorysuccess_Model_Warehouse
     */
    public function getWarehouseFromStoreId($storeId) 
    {
        $warehouse = Mage::getModel('inventorysuccess/warehouse');
        $warehouseId = Magestore_Coresuccess_Model_Service::warehouseStoreService()
                ->getWarehouseIdFromStoreId($storeId);
        if($warehouseId) {
            $warehouse->load($warehouseId);
        }
        return $warehouse;
    }
    
    /**
     * make sure that warehouse_id & store_id is unique
     * 
     * @param Magestore_Inventorysuccess_Model_Warehouse $warehouse
     * @return Magestore_Inventorysuccess_Model_Service_Warehouse_WarehouseService
     */
    public function makeUniqueWarehouseStoreId($warehouse)
    {
        Magestore_Coresuccess_Model_Service::warehouseStoreService()
                ->makeUniqueWarehouseStoreId($warehouse->getId());
        
        return $this;
    }
    
}