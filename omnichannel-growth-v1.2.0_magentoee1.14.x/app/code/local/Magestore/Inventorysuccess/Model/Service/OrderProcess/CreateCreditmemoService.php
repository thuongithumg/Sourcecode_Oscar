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
class Magestore_Inventorysuccess_Model_Service_OrderProcess_CreateCreditmemoService
    extends Magestore_Inventorysuccess_Model_Service_OrderProcess_AbstractService
{
    /**
     * @var string
     */
    protected $process = 'create_creditmemo';

    /**
     * @var array
     */
    protected $doReturnWarehouses = array();

    /**
     * execute the process
     *
     * @param Mage_Sales_Model_Order_Creditmemo_Item $item
     * @return bool
     */
    public function execute($item)
    {
        if(!$this->canProcessItem($item)){
            return;
        }

        $this->processRefundItem($item);

        $this->markItemProcessed($item);

        return true;
    }

    /**
     * Process to refund item
     *
     * @param Mage_Sales_Model_Order_Creditmemo_Item $item
     */
    public function processRefundItem($item)
    {
        if ($item->getBackToStock() || $this->catalogInventoryHelper->isAutoReturnEnabled()) {
            /* return item to warehouse */
            $this->_returnToWarehouse($item);
        } else {
            /* do not return item to warehouse */
            $this->_subtractItemInWarehouse($item);
        }
    }

    /**
     * Process return item to Warehouse
     *
     * @param Mage_Sales_Model_Order_Creditmemo_Item $item
     */
    protected function _returnToWarehouse($item)
    {
        if(!$this->_getRefundQty($item)) {
            return;
        }
        $this->queryProcessService->start($this->process);

        /* Add a new creditmemoItem record to return Warehouse */
        $this->_addWarehouseCreditmemoItem($item);

        /* Receive shipped item to returned Warehouse */
        $this->_receiveItemToReturnWarehouse($item);

        /* update available_qty in stocks after returned */
        $this->_updateAvailableQtyAfterReturn($item);

        $this->queryProcessService->process($this->process);
    }

    /**
     * Subtract item from warehouse if do not return item
     *
     * @param Mage_Sales_Model_Order_Creditmemo_Item $item
     */
    protected function _subtractItemInWarehouse($item)
    {
        if(!$this->_getRefundQty($item)) {
            return;
        }

        $this->queryProcessService->start($this->process);

        /* Add a new creditmemoItem record to return Warehouse */
        $this->_addWarehouseCreditmemoItem($item);

        /* Decrease qty in stocks */
        $this->_adjustStockInOrderWarehouse($item);

        /* update available_qty after refund without return */
        $this->_updateAvailableQtyAfterRefund($item);

        $this->queryProcessService->process($this->process);
    }

    /**
     * Create new adjutsStock to decrease total_qty in ordered Warehouse
     *
     * @param Mage_Sales_Model_Order_Creditmemo_Item $item
     */
    protected function _adjustStockInOrderWarehouse($item)
    {
        $simpleItem = $this->_getSimpleItem($item->getOrderItem());
        $orderWarehouseId = $this->getOrderedWarehouse($simpleItem->getItemId());
        /* prepare adjuststock data */
        $creditmemoData =  Mage::app()->getRequest()->getParam('creditmemo');
        $reason = isset($creditmemoData['items'][$item->getOrderItemId()]['reason']) ? $creditmemoData['items'][$item->getOrderItemId()]['reason'] : '';
        $warehouseStock = $this->stockRegistryService->getStock($orderWarehouseId, $simpleItem->getProductId());
        $adjustData = array();
        $adjustQty = max(0, $warehouseStock->getTotalQty() - $this->_getNotShipQtyInRefund($item));
        $adjustData['products'] = array(
            $simpleItem->getProductId() => array(
                'adjust_qty' => $adjustQty,
                'product_name' => $simpleItem->getProduct()->getName(),
                'product_sku' => $simpleItem->getProduct()->getSku(),
            )
        );
        $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_ID] = $orderWarehouseId;
        $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::REASON] = Mage::helper('inventorysuccess')->__('Do not return items to stock in Creditmemo #%s', $item->getCreditmemo()->getIncrementId())
            . '. '. $reason;

        /* change stock adjustment data by other extension */
        $adjustDataObject = new Varien_Object($adjustData);
        Mage::dispatchEvent('inventorysuccess_create_creditmemo_adjuststock_data', array(
            'adjuststock_data' => $adjustDataObject,
            'order' => $item->getOrderItem()->getOrder(),
        ));
        $adjustData = $adjustDataObject->getData();

        /* create new stock adjustment (also update global stock), then complete it */
        $adjustStock = Mage::getModel('inventorysuccess/adjuststock');
        $adjustStockService = Magestore_Coresuccess_Model_Service::adjustStockService();
        $adjustStockService->createAdjustment($adjustStock, $adjustData);
        $adjustStockService->complete($adjustStock, true);
    }

    /**
     * Add a new creditmemoItem record to return Warehouse
     *
     * @param Mage_Sales_Model_Order_Creditmemo_Item $item
     */
    protected function _addWarehouseCreditmemoItem($item)
    {
        $simpleItem = $this->_getSimpleItem($item->getOrderItem());
        $returnWarehouse = $this->getDoReturnWarehouse($item);
        
        $this->queryProcessService->addQuery(array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' =>  array('warehouse_id' => $returnWarehouse->getId()), 
            'condition' => array('entity_id=?' => $item->getId()),
            'table' => Mage::getResourceSingleton('sales/order_creditmemo_item')->getMainTable(),
        ), $this->process);         
        
        /*
        $warehouseCreditmemoItemModel = Mage::getModel('inventorysuccess/warehouse_creditmemo_item');
        $warehouseCreditmemoItemData = array(
            'warehouse_id' => $returnWarehouse->getId(),
            'creditmemo_id' => $item->getCreditmemo()->getId(),
            'item_id' => $item->getId(),
            'order_id' => $item->getOrderItem()->getOrderId(),
            'order_item_id' => $item->getOrderItemId(),
            'product_id' => $simpleItem->getProductId(),
            'qty_refunded' => $this->_getRefundQty($item),
            'subtotal' => $item->getBaseRowTotal(),
            'created_at' => $item->getCreditmemo()->getCreatedAt(),
            'updated_at' => $item->getCreditmemo()->getUpdatedAt(),
        );
        $this->queryProcessService->addQuery(array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_INSERT,
            'values' =>  array($warehouseCreditmemoItemData),
            'table' => $warehouseCreditmemoItemModel->getResource()->getMainTable(),
        ), $this->process);
        */
    }

    /**
     * update available_qty in stocks after returned
     *
     * @param Mage_Sales_Model_Order_Creditmemo_Item $item
     */
    protected function _updateAvailableQtyAfterReturn($item)
    {
        $notShipQty = $this->_getNotShipQtyInRefund($item);
        $refundQty = $this->_getRefundQty($item);
        $globalStockId = $this->stockService->getGlobalStockId();
        $orderedWarehouseId = $this->getOrderedWarehouse($item->getOrderItemId());
        $refundWarehouseId = $this->stockService->getStockId();
        $qtyChanges = array(
            $globalStockId => array(
                Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY => 0,
            ),
            $orderedWarehouseId => array(
                Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY => 0,
            ),
            $refundWarehouseId => array(
                Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY => 0,
            ),
        );

        $qtyChanges[$globalStockId][Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY] += $notShipQty;
        $qtyChanges[$orderedWarehouseId][Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY] += $notShipQty;
        $qtyChanges[$refundWarehouseId][Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY] -= $refundQty;

        $queries = $this->stockRegistryService
            ->prepareChangeQtys($item->getProductId(), $qtyChanges);
        $this->queryProcessService->addQueries($queries, $this->process);
    }

    /**
     * update available_qty after refund without return
     *
     * @param Mage_Sales_Model_Order_Creditmemo_Item $item
     */
    protected function _updateAvailableQtyAfterRefund($item)
    {
        $notShipQty = $this->_getNotShipQtyInRefund($item);
        $globalStockId = $this->stockService->getGlobalStockId();
        $orderedWarehouseId = $this->getOrderedWarehouse($item->getOrderItemId());
        $qtyChanges = array(
            $globalStockId => array(
                Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY => 0,
            ),
            $orderedWarehouseId => array(
                Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY => 0,
            )
        );

        $qtyChanges[$globalStockId][Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY] += $notShipQty;
        $qtyChanges[$orderedWarehouseId][Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY] += $notShipQty;

        $queries = $this->stockRegistryService
            ->prepareChangeQtys($item->getProductId(), $qtyChanges);
        $this->queryProcessService->addQueries($queries, $this->process);
    }

    /**
     * Receive item to returned Warehouse
     *
     * @param Mage_Sales_Model_Order_Creditmemo_Item $item
     */
    protected function _receiveItemToReturnWarehouse($item)
    {
        $simpleItem = $this->_getSimpleItem($item->getOrderItem());
        $returnWarehouse = $this->getDoReturnWarehouse($item);
        $products = array($simpleItem->getProductId() => $this->_getShippedQtyInRefund($item));
        $creditmemo = $item->getCreditmemo();
        /* receive item to warehouse, do not update global stock */
        $this->stockChangeService->receive(
            $returnWarehouse->getId(),
            $products,
            Magestore_Inventorysuccess_Model_Service_StockMovement_Activity_SalesRefundService::STOCK_MOVEMENT_ACTION_CODE,
            $creditmemo->getId(),
            true
        );
    }

    /**
     * Get Warehouse to return item to
     *
     * @param Mage_Sales_Model_Order_Creditmemo_Item $item
     */
    public function getDoReturnWarehouse($item)
    {
        if(!isset($this->doReturnWarehouses[$item->getId()])) {
            $orderItemId = $item->getOrderItemId();
            /* get return warehouse_id from post data */
            $creditmemoData =  Mage::app()->getRequest()->getParam('creditmemo');
            $paramOrderItemId = $orderItemId;
            if($parentItem = $item->getOrderItem()->getParentItem()) {
                if($parentItem->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                    $paramOrderItemId = $parentItem->getItemId();
                }
            }

            $returnWarehouseId = null;
            if(isset($creditmemoData['items'][$paramOrderItemId]['warehouse'])) {
                $returnWarehouseId = $creditmemoData['items'][$paramOrderItemId]['warehouse'];
            }
            /* get ordered warehouse Id */
            if(!$returnWarehouseId) {
                $returnWarehouseId = $this->getOrderedWarehouse($orderItemId);
            }
            /* get primary warehouse_id */
            if(!$returnWarehouseId) {
                $returnWarehouse = $this->warehouseService->getPrimaryWarehouse();
            } else {
                $returnWarehouse = Mage::getModel('inventorysuccess/warehouse')->load($returnWarehouseId);
            }
            /* allow to change the Warehouse by other extension */
            Mage::dispatchEvent('inventorysuccess_create_creditmemo_warehouse', array(
                'warehouse' => $returnWarehouse,
                'item' => $item,
            ));
            $this->doReturnWarehouses[$item->getId()] = $returnWarehouse;
        }
        return $this->doReturnWarehouses[$item->getId()];
    }

    /**
     * get refund qty of item
     *
     * @param Mage_Sales_Model_Order_Creditmemo_Item $item
     * @return float
     */
    protected function _getRefundQty($item)
    {
        $qty = $item->getQty();
        $parentItemId = $item->getOrderItem()->getParentItemId();
        $parentItem = $parentItemId ? $item->getCreditmemo()->getItemByOrderId($parentItemId) : false;
        $qty = $parentItem ? $parentItem->getQty() * $qty : $qty;
        return $qty;
    }

    /**
     * Get not ship qty in refunded item
     *
     * @param Mage_Sales_Model_Order_Creditmemo_Item $item
     * @return float
     */
    protected function _getNotShipQtyInRefund($item)
    {
        $qtyToShip = $this->_getQtyToShipBeforeRefund($item);
        $refundQty = $this->_getRefundQty($item);
        return min($qtyToShip, $refundQty);
    }

    /**
     * Get shipped qty in refunded item
     *
     * @param Mage_Sales_Model_Order_Creditmemo_Item $item
     * @return float
     */
    protected function _getShippedQtyInRefund($item)
    {
        $qtyToShip = $this->_getQtyToShipBeforeRefund($item);
        $refundQty = $this->_getRefundQty($item);
        return max(0, $refundQty - $qtyToShip);
    }

    /**
     * Get qty_to_ship before refunding item
     *
     * @param Mage_Sales_Model_Order_Creditmemo_Item $item
     * @return float
     */
    protected function _getQtyToShipBeforeRefund($item)
    {
        $orderItem = $item->getOrderItem();
        /* get shipped-qty */
        $qtyShipped = $orderItem->getQtyShipped();
        if($parentItem = $orderItem->getParentItem()) {
            if($parentItem->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                $qtyShipped = $parentItem->getQtyShipped();
            }
        }

        /* calculate qty-to-ship before refunded */
        $qty = $orderItem->getQtyOrdered()
            - $qtyShipped
            - $orderItem->getQtyCanceled()
            - ($orderItem->getQtyRefunded() - $this->_getRefundQty($item));
        return max($qty, 0);
    }

    /**
     *
     * @param Mage_Sales_Model_Order_Creditmemo_Item $item
     * @return boolean
     */
    public function canProcessItem($item)
    {
        /* check processed item */
        if($this->isProcessedItem($item)) {
            return false;
        }

        if(!Mage::helper('cataloginventory')->isQty($item->getOrderItem()->getProductType())){
            return false;
        }

        /* check manage stock or not */
        if(!$this->isManageStock($item->getOrderItem())) {
            return false;
        }

        /* check added item */
        if($this->creditmemoItemService->getWarehouseIdByCreditmemoItemId($item->getId())) {
            return false;
        }

        return true;
    }

}