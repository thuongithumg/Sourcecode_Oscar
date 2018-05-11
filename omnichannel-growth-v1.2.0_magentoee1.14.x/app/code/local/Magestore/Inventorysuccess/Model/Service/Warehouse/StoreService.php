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
class Magestore_Inventorysuccess_Model_Service_Warehouse_StoreService
{
    /**
     * Query process name
     */
    const QUERY_PROCESS = 'warehouse_store';
    
    /**
     * @var Magestore_Coresuccess_Model_Service_QueryProcessorService 
     */
    protected $queryProcessorService;   
    
    /**
     * 
     */
    public function __construct()
    {
        $this->queryProcessorService = Magestore_Coresuccess_Model_Service::queryProcessorService();      
    }

    
    /**
     * 
     * @param int $storeId
     * @return int|null
     */
    public function getWarehouseIdFromStoreId($storeId) 
    {
        $warehouseStore = Mage::getResourceModel('inventorysuccess/warehouse_store_collection')
                        ->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse_Store::STORE_ID, $storeId)
                        ->setPageSize(1)
                        ->setCurPage(1)
                        ->getFirstItem();   
        return $warehouseStore->getWarehouseId();
    }    
    
    /**
     * 
     * @param int $warehouseId
     * @return array
     */
    public function getStoreIdsFromWarehouseId($warehouseId)
    {
        $storeIds = array();
        $warehouseStores = Mage::getResourceModel('inventorysuccess/warehouse_store_collection')
                        ->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse_Store::WAREHOUSE_ID, $warehouseId);
        if($warehouseStores->getSize()) {
            foreach($warehouseStores as $warehouseStore) {
                $storeIds[$warehouseStore->getStoreId()] = $warehouseStore->getStoreId();
            }
        }
        return $storeIds;
    }
    
    /**
     * 
     * @param array $storeIds
     * @param int $warehouseId
     */
    public function addStoresToWarehouse($storeIds, $warehouseId)
    {
        $data = array();
        if(!count($storeIds)) {
            return $this;
        }
        foreach($storeIds as $storeId) {
            $data[] = array(
                'warehouse_id' => $warehouseId,
                'store_id' => $storeId,
            );
        }
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_INSERT,
            'values' => $data,
            'table' => Mage::getResourceModel('inventorysuccess/warehouse_store')->getMainTable()          
        );        
        $this->queryProcessorService->start(self::QUERY_PROCESS);
        $this->queryProcessorService->addQuery($query, self::QUERY_PROCESS);
        $this->queryProcessorService->process(self::QUERY_PROCESS);
        
        return $this;
    }
    
    /**
     * 
     * @param array $storeIds
     * @param int $warehouseId
     */
    public function setStoresToWarehouse($storeIds, $warehouseId)
    {
        /* add $storeIds to warehouse */
        $this->addStoresToWarehouse($storeIds, $warehouseId);
        
        $this->queryProcessorService->start(self::QUERY_PROCESS);       
        
        $storeIds = count($storeIds) ? $storeIds : array(0);
        
        /* remove other storeIds from warehouse */
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_DELETE,
            'condition' => array(
                'warehouse_id = ?' => $warehouseId,
                'store_id NOT IN (?)' => $storeIds,
            ),
            'table' => Mage::getResourceModel('inventorysuccess/warehouse_store')->getMainTable()          
        );       

        $this->queryProcessorService->addQuery($query, self::QUERY_PROCESS);
        
        /* remove other warehouse_ids linked to $storeIds */
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_DELETE,
            'condition' => array(
                'warehouse_id != ?' => $warehouseId,
                'store_id IN (?)' => $storeIds,
            ),
            'table' => Mage::getResourceModel('inventorysuccess/warehouse_store')->getMainTable()          
        );       

        $this->queryProcessorService->addQuery($query, self::QUERY_PROCESS);        
        
        $this->queryProcessorService->process(self::QUERY_PROCESS);
        
        return $this;        
    }
    
    /**
     * make sure that warehouse_id & store_id is unique
     * 
     * @param int $warehouseId
     * @return Magestore_Inventorysuccess_Model_Service_Warehouse_StoreService
     */
    public function makeUniqueWarehouseStoreId($warehouseId)
    {
        $storeIds = $this->getStoreIdsFromWarehouseId($warehouseId);
        $warehouseStores = Mage::getResourceModel('inventorysuccess/warehouse_store_collection')
                                    ->addFieldToFilter('store_id', array('in' => $storeIds))
                                    ->addFieldToFilter('warehouse_id', array('neq' => $warehouseId));
        if($warehouseStores->getSize()) {
            foreach($warehouseStores as $warehouseStore) {
                $warehouseStore->delete();
            }
        }
        
        return $this;
    }    
    
    
    /**
     * transfer store_id data from warehouse table to warehouse_store table
     * 
     */
    public function transferStoreIdFromWarehouseTable()
    {
        $transferData = array();
        $warehouses = Mage::getModel('inventorysuccess/warehouse')
                        ->getCollection()
                        ->addFieldToFilter('store_id', array('gt' => 0));
        if(!$warehouses->getSize())
            return $this;
        foreach($warehouses as $warehouse) {
            $transferData[] = array(
                'warehouse_id' => $warehouse->getId(),
                'store_id' => $warehouse->getStoreId(),
            );
        }

        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_INSERT,
            'values' => $transferData,
            'table' => Mage::getResourceModel('inventorysuccess/warehouse_store')->getMainTable()          
        );        
        $this->queryProcessorService->start(self::QUERY_PROCESS);
        $this->queryProcessorService->addQuery($query, self::QUERY_PROCESS);
        $this->queryProcessorService->process(self::QUERY_PROCESS);    
        
        return $this;
    }    
}