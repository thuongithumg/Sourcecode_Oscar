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
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_Installation_ConvertData extends Magestore_Coresuccess_Model_Mysql4_Base
{
    /**
     * 
     */
    protected $warehouseMap = array();
    
    /**
     * 
     * @param int $start
     * @param int $size
     * @return array
     */
    public function prepareConvertWarehouses($start = 0, $size = 0)
    {
        $connection = $this->_getConnection('read');
        /* load all warehouses in Magestore_Inventoryplus */
        $select = $connection->select()
                            ->from($this->getTable('inventoryplus/warehouse'), '*');        

        $total = $this->getTotalItems($select);
        if($size) {
            $select->limit($size, $start);
        }
        
        $query = $connection->query($select);
        $warehouses = array();
        while ($row = $query->fetch()) {
            $warehouses[] = $this->_mapWarehouseData($row);
        }     
        
        if(!count($warehouses)) {
            return array();
        }
        
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_INSERT,
            'values' => $warehouses,
            'table' => $this->getTable('inventorysuccess/warehouse')
        );        
        
        return array('query' => $query, 'total' => $total);
    }
    
    /**
     * 
     * @param int $start
     * @param int $size
     * @return array
     */
    public function prepareConvertWarehouseStocks($start = 0, $size = 0)
    {
        $connection = $this->_getConnection('read');
        $select = $connection->select()
                            ->from($this->getTable('inventoryplus/warehouse_product'), '*');  
        $total = $this->getTotalItems($select);
        if($size) {
            $select->limit($size, $start);
        }
        
        $query = $connection->query($select);
        $stocks = array();
        while ($row = $query->fetch()) {
            $stocks[] = $this->_mapWarehouseStock($row);
        }       

        if(!count($stocks)) {
            return array();
        }
        
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_INSERT,
            'values' => $stocks,
            'table' => $this->getTable('inventorysuccess/warehouse_product')
        );        
        
        return array('query' => $query, 'total' => $total);        
    }    
    
    /**
     * 
     * @param array $warehouseData
     * @return array
     */
    protected function _mapWarehouseData($warehouseData)
    {
        $convertedData = array();
        $mapFields = array(
            //Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_ID => 'warehouse_id',
            Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_NAME => 'warehouse_name',
            Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_CODE => 'warehouse_name',
            Magestore_Inventorysuccess_Model_Warehouse::CONTACT_EMAIL => 'manager_email',
            Magestore_Inventorysuccess_Model_Warehouse::TELEPHONE => 'telephone',
            Magestore_Inventorysuccess_Model_Warehouse::STREET => 'street',
            Magestore_Inventorysuccess_Model_Warehouse::CITY => 'city',
            Magestore_Inventorysuccess_Model_Warehouse::COUNTRY_ID => 'country_id',
            Magestore_Inventorysuccess_Model_Warehouse::REGION => 'state',
            Magestore_Inventorysuccess_Model_Warehouse::REGION_ID => 'state_id',
            Magestore_Inventorysuccess_Model_Warehouse::POSTCODE => 'postcode',
            Magestore_Inventorysuccess_Model_Warehouse::IS_PRIMARY => 'is_root',
            Magestore_Inventorysuccess_Model_Warehouse::CREATED_AT => 'created_at',
            Magestore_Inventorysuccess_Model_Warehouse::UPDATED_AT => 'updated_at',
        );

        foreach($mapFields as $newField => $oldField) {
            $convertedData[$newField] = isset($warehouseData[$oldField]) ? $warehouseData[$oldField] : null;
            if($newField == Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_CODE) {
                $convertedData[$newField] = Magestore_Coresuccess_Model_Service::installationConvertDataService()
                                                     ->generateWarehouseCode($warehouseData['warehouse_id']);
            }
        }

        return $convertedData;
    }
    
    /**
     * 
     * @param array $stockData
     * @return array
     */
    protected function _mapWarehouseStock($stockData)
    {
        $convertedData = array();
        $mapFields = array(
            Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID => 'warehouse_id',
            Magestore_Inventorysuccess_Model_Warehouse_Product::PRODUCT_ID => 'product_id',
            Magestore_Inventorysuccess_Model_Warehouse_Product::TOTAL_QTY=> 'total_qty',
            Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY => 'total_qty',
            Magestore_Inventorysuccess_Model_Warehouse_Product::SHELF_LOCATION => 'product_location',
        );
        
        foreach($mapFields as $newField => $oldField) {
            $convertedData[$newField] = isset($stockData[$oldField]) ? $stockData[$oldField] : null;
        }        
        
        $convertedData[Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID] = $this->_getConvertedWarehouseId($stockData['warehouse_id']);
        $convertedData['stock_status_changed_auto'] = 1;
        $convertedData['is_in_stock'] = Mage_CatalogInventory_Model_Stock::STOCK_IN_STOCK;      
        
        return $convertedData;
    }
    
    /**
     * 
     * @param int $warehouseId
     * @return int
     */
    protected function _getConvertedWarehouseId($warehouseId)
    {
        if(!isset($this->warehouseMap[$warehouseId])) {
            $warehouseCode = Magestore_Coresuccess_Model_Service::installationConvertDataService()
                                                     ->generateWarehouseCode($warehouseId);
            $warehouse = Mage::getResourceModel('inventorysuccess/warehouse_collection')
                            ->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_CODE, $warehouseCode)
                            ->setPageSize(1)->setCurPage(1)
                            ->getFirstItem();
            $this->warehouseMap[$warehouseId] = $warehouse->getId();
        }
        return $this->warehouseMap[$warehouseId];
    }
    
    /**
     * 
     * @return array
     */
    public function prepareGenerateStocksFromWarehouses()
    {
        $warehouses = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        if(!$warehouses->getSize()) {
            return array();
        }
        $stocks = array();
        foreach($warehouses as $warehouse) {
            $stocks[] = array(
                'stock_id' => $warehouse->getId(),
                'stock_name' => $warehouse->getWarehouseName(),
            );
        }
        
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_INSERT,
            'values' => $stocks,
            'table' => $this->getTable('cataloginventory/stock')
        );        
                
        return $query;
    }
    
    /**
     * Get total items in select
     * 
     * @param Zend_Db_Select $select
     * @return int
     */
    public function getTotalItems($select)
    {
        $countSelect = clone $select;
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->columns(array('total' => 'COUNT(*)'));
        $connection = $this->_getConnection('read');
        $query = $connection->query($countSelect);
        $row = $query->fetch();
        return isset($row['total']) ? intval($row['total']) : 0;
    }    
}