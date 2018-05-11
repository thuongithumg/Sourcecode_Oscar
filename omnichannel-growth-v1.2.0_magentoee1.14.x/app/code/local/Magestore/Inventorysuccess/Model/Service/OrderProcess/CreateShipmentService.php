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
class Magestore_Inventorysuccess_Model_Service_OrderProcess_CreateShipmentService
    extends Magestore_Inventorysuccess_Model_Service_OrderProcess_AbstractService
{
    /**
     * @var string
     */
    protected $process = 'create_shipment';    
    
    /**
     * @var array 
     */
    protected $shipWarehouses = array();
    
    /**
     * @array
     */
    protected $simpleOrderItems = array();
    
    /**
     * execute the process
     * 
     * @param Mage_Sales_Model_Order_Shipment_Item $item
     * @return bool
     */        
    public function execute($item)
    {   
        if(!$this->canProcessItem($item)){
            return;
        }

        $this->processShipItem($item);
        
        $this->markItemProcessed($item);
        
        return true;        
    }
    
    /**
     * Process to ship item from Warehouse
     * 
     * @param Mage_Sales_Model_Order_Shipment_Item $item
     */
    public function processShipItem($item)
    {
        $this->queryProcessService->start($this->process);
        
        /* add shipped item to the Warehouse */
        $this->_addWarehouseShipmentItem($item);     
        
        /* increase available_qty in ordered Warehouse by shipped qty*/
        $this->_increaseAvailableQtyInOrderWarehouse($item);

        $this->queryProcessService->process($this->process);

        /* issue ship item from Warehouse */
        $this->_issueItemFromWarehouse($item);
    }

   /**
    * Add shipment item to Warehouse
    * 
    * @param Mage_Sales_Model_Order_Shipment_Item $item
    */
    protected function _addWarehouseShipmentItem($item)
    {
        $simpleItem = $this->_getSimpleOrderItem($item);
        $shipWarehouse = $this->getShipmentWarehouse($item);
        if (!$shipWarehouse) {
            return $this;
        }

        $this->queryProcessService->addQuery(array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' =>  array('warehouse_id' => $shipWarehouse->getId()), 
            'condition' => array('entity_id=?' => $item->getId()),
            'table' => Mage::getResourceSingleton('sales/order_shipment_item')->getMainTable(),
        ), $this->process);          
        
        /*
        $warehouseShipModel = Mage::getModel('inventorysuccess/warehouse_shipment_item');
        $warehouseShipData = array(
            'warehouse_id' => $shipWarehouse->getId(),
            'shipment_id' => $item->getParentId(),
            'item_id' => $item->getId(),            
            'order_id' => $item->getOrderItem()->getOrderId(),
            'order_item_id' => $item->getOrderItemId(),
            'product_id' => $simpleItem->getProductId(),
            'qty_shipped' => $this->_getShippedQty($item),
            'subtotal' => $item->getPrice(),
            'created_at' => $item->getShipment()->getCreatedAt(),
            'updated_at' => $item->getShipment()->getUpdatedAt(),            
        );
        $this->queryProcessService->addQuery(array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_INSERT,
            'values' =>  array($warehouseShipData), 
            'table' => $warehouseShipModel->getResource()->getMainTable(),
        ), $this->process);
        */    
    }
    
    /**
     * Get simple item from ship item
     * 
     * @param Mage_Sales_Model_Order_Shipment_Item $shipItem
     * @return Mage_Sales_Model_Order_Item
     */
    protected function _getSimpleOrderItem($shipItem)
    {
        if(!isset($this->simpleOrderItems[$shipItem->getId()])) {
            $simpleItem = $shipItem->getOrderItem();
            $orderItem = $shipItem->getOrderItem();
            if ($orderItem->getProduct()->isComposite()) {
                if($orderItem->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                    foreach($orderItem->getChildrenItems() as $childItem) {
                        $simpleItem = $childItem;
                        break;
                    }
                }
            }   
     
            $this->simpleOrderItems[$shipItem->getId()] = $simpleItem;
        }
        return $this->simpleOrderItems[$shipItem->getId()];
    }
    
    /**
     * increase available_qty of product in ordered warehouse
     * 
     * @param Mage_Sales_Model_Order_Shipment_Item $item
     */
    protected function _increaseAvailableQtyInOrderWarehouse($item)
    {
        $orderItem = $this->_getSimpleOrderItem($item);
        $orderWarehouseId = $this->getOrderedWarehouse($orderItem->getItemId());
        $shipWarehouse = $this->getShipmentWarehouse($item);
        $qtyChanges = array(Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY =>  $this->_getShippedQty($item));
        /* increase available_qty in ordered warehouse  */
        $query = $this->stockRegistryService
                        ->prepareChangeProductQty($orderWarehouseId, $orderItem->getProductId(), $qtyChanges);
        $this->queryProcessService->addQuery($query, $this->process);
        /* increase available_qty in global stock */
        $query = $this->stockRegistryService
                        ->prepareChangeProductQty($this->stockService->getGlobalStockId(), $orderItem->getProductId(), $qtyChanges);        
        $this->queryProcessService->addQuery($query, $this->process);
    }
    
    /**
     * issue item from ship warehouse
     * 
     * @param Mage_Sales_Model_Order_Shipment_Item $item
     */      
    protected function _issueItemFromWarehouse($item)     
    {
        $orderItem = $this->_getSimpleOrderItem($item);
        if (!$this->getShipmentWarehouse($item)) {
            return $this;
        }
        $shipWarehouseId = $this->getShipmentWarehouse($item)->getId();
        $products = array($orderItem->getProductId() => $this->_getShippedQty($item));
        /* issue item for shipment from Warehouse, do not update global stock */
        $this->stockChangeService->issue(
                $shipWarehouseId, 
                $products, 
                Magestore_Inventorysuccess_Model_Service_StockMovement_Activity_SalesShipmentService::STOCK_MOVEMENT_ACTION_CODE, 
                $item->getShipment()->getId(), 
                true
        );
    }
    
    /**
     * 
     * @param Mage_Sales_Model_Order_Shipment_Item $item
     * @return boolean
     */
    public function canProcessItem($item)
    {
        /* check processed item */
        if($this->isProcessedItem($item)) {
            return false;
        }
        
        /* check manage stock or not */
//        if(!$this->isManageStock($item->getOrderItem())) {
//            return false;
//        }        
        
        /* ignore child of configurable product */
        $orderItem = $item->getOrderItem();
        if($orderItem->getParentItem()
                && $orderItem->getParentItem()->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            return false;
        }
        
        /* check added item */
        if($this->shipmentItemService->getWarehouseIdByShipmentItemId($item->getId())) {
            return false;
        }
        
        return true;
    }      
    
    /**
     * Get warehouse to ship item
     * 
     * @param Mage_Sales_Model_Order_Shipment_Item $item
     * @return null|Magestore_Inventorysuccess_Model_Warehouse
     */
    public function getShipmentWarehouse($item)
    {
        if(!isset($this->shipWarehouses[$item->getId()])) {
            /* get posted warehouse_id */
            $postData = Mage::app()->getRequest()->getPost('shipment');
            $shipWarehouseId = isset($postData['warehouse']) ? $postData['warehouse'] : null;
            /* get ordered warehouse_id */
            $orderItem = $this->_getSimpleOrderItem($item);
            $shipWarehouseId = $shipWarehouseId 
                                ? $shipWarehouseId 
                                : $this->getOrderedWarehouse($orderItem->getItemId());
            /* get primary warehouse_id */                    
            if(!$shipWarehouseId) {
                $shipWarehouse = $this->warehouseService->getPrimaryWarehouse();
            } else {
                $shipWarehouse = Mage::getModel('inventorysuccess/warehouse')->load($shipWarehouseId);
            }
            $skipWarehouse = false;
            /* allow to change the Warehouse by other extension */
            Mage::dispatchEvent('inventorysuccess_create_shipment_warehouse', array(
                                            'warehouse' => $shipWarehouse,
                                            'item' => $item,
                                            'order_id' => $item->getShipment()->getOrderId(),
                                            'skip_warehouse' => &$skipWarehouse
                    ));
            if ($skipWarehouse) {
                return null;
            }
            $this->shipWarehouses[$item->getId()] = $shipWarehouse;
        }
        return $this->shipWarehouses[$item->getId()];                
    }    
}