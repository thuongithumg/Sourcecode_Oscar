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
 * Inventorysuccess Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Service_OrderProcess_CancelOrderService
    extends Magestore_Inventorysuccess_Model_Service_OrderProcess_AbstractService
{
    /**
     * @var string
     */
    protected $process = 'cancel_order';      
    
    /**
     * execute the process
     * 
     * @param Mage_Sales_Model_Order_Item $item
     * @return bool
     */
    public function execute($item)
    {
        if(!$this->canProcessItem($item)){
            return;
        }
        
        $this->processCancelItem($item);
        
        $this->markItemProcessed($item);
        
        return true;        
    }
    
    /**
     * Process cancel item
     * 
     * @param Mage_Sales_Model_Order_Item $item
     */
    public function processCancelItem($item)
    {
        $this->queryProcessService->start($this->process);
        
        //$this->_updateCanceledQtyInWarehouse($item); 
        
        $this->_updateAvailableQtyInStocks($item);   
        
        $this->queryProcessService->process($this->process);
    }
    
    /**
     * Update qty_canceled of item in Warehouse
     * 
     * @param Mage_Sales_Model_Order_Item $item
     */
    protected function _updateCanceledQtyInWarehouse($item)
    {
        $orderWarehouseId = $this->getOrderedWarehouse($item->getItemId());
        $qtyChanges = array('qty_canceled' =>  $this->_getCanceledQty($item));
        $query = $this->orderItemService
                        ->prepareChangeItemQty($orderWarehouseId, $item->getItemId(), $qtyChanges);
        $this->queryProcessService->addQuery($query, $this->process);        
    }
    
    /**
     * update available qty of product in Stocks
     * 
     * @param Mage_Sales_Model_Order_Item $item
     */    
    protected function _updateAvailableQtyInStocks($item)
    {
        $globalStockId = $this->stockService->getGlobalStockId();
        $orderedWarehouseId = $this->getOrderedWarehouse($item->getItemId());
        $processWarehouseId = $this->stockService->getStockId();
        $canceledQty = $this->_getCanceledQty($item);
        $qtyChanges = array(
            $globalStockId => array(
                Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY => 0,
            ),
            $orderedWarehouseId => array(
                Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY => 0,
            ),
            $processWarehouseId => array(
                Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY => 0,
            ),            
        );
        $qtyChanges[$globalStockId][Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY] +=  $canceledQty;
        $qtyChanges[$orderedWarehouseId][Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY] +=  $canceledQty;
        $qtyChanges[$processWarehouseId][Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY] -=  $canceledQty;

        $queries = $this->stockRegistryService
                        ->prepareChangeQtys($item->getProductId(), $qtyChanges);
        $this->queryProcessService->addQueries($queries, $this->process);            
    }

    /**
     * 
     * @param Mage_Sales_Model_Order_Item $item
     * @return boolean
     */
    public function canProcessItem($item)
    {
        /* check manage stock or not */
        if(!$this->isManageStock($item)) {
            return false;
        }
        /* check qty-to-cancel of item */
        if(!$this->_getCanceledQty($item)) {
            return false;
        }
        /* check processed item */
        if($this->isProcessedItem($item)) {
            return false;
        }
        /* check canceled item */
        if($this->orderItemService->isCanceledItem($item->getId())) {
            return false;
        }
        
        return true;
    }    
        
    
}