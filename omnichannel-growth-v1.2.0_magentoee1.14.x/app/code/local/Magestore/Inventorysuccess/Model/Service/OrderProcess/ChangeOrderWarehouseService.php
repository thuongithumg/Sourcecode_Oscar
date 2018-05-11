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
 * Class Magestore_Inventorysuccess_Model_Service_OrderProcess_ChangeOrderWarehouseService
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Service_OrderProcess_ChangeOrderWarehouseService
    extends Magestore_Inventorysuccess_Model_Service_OrderProcess_AbstractService
{
    /**
     * @var string
     */
    protected $process = 'change_order_warehouse';

    /**
     * @var array
     */
    protected $orderWarehouses = array();

    /**
     * execute the process
     *
     * @param Mage_Sales_Model_Order $order
     * @param Magestore_Inventorysuccess_Model_Warehouse $warehouse
     * @return bool
     */
    public function execute($order, $warehouse)
    {
        if (!$this->canProcessOrder($order, $warehouse)) {
            return false;
        }
        $this->queryProcessService->start($this->process);

        $this->assignOrderToWarehouse($order, $warehouse);

        $this->changeOrderItemsWarehouse($order, $warehouse);

        $this->queryProcessService->process($this->process);

        $this->markOrderProcessed($order, $warehouse);

        return true;
    }

    /**
     *
     * @param Mage_Sales_Model_Order $order
     * @param Magestore_Inventorysuccess_Model_Warehouse $warehouse
     */
    public function assignOrderToWarehouse($order, $warehouse)
    {
        $this->queryProcessService->addQuery(array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => array('warehouse_id' => $warehouse->getId()),
            'condition' => array('entity_id=?' => $order->getId()),
            'table' => $order->getResource()->getMainTable(),
        ), $this->process);


        $this->queryProcessService->addQuery(array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => array('warehouse_id' => $warehouse->getId()),
            'condition' => array('entity_id=?' => $order->getId()),
            'table' => Mage::getSingleton('core/resource')->getTableName('sales_flat_order_grid'),
        ), $this->process);
    }

    /**
     * Change order items to new warehouse
     *
     * @param Mage_Sales_Model_Order $order
     * @param Magestore_Inventorysuccess_Model_Warehouse $warehouse
     */
    public function changeOrderItemsWarehouse($order, $warehouse)
    {
        /** @var Mage_Sales_Model_Order_Item $orderItem */
        foreach ($order->getAllItems() as $orderItem) {
            if ($this->canProcessItem($orderItem)) {
                if ($orderItem->getWarehouseId()) {
                    $this->_decreaseQtyToShipInOrderWarehouse($orderItem, $orderItem->getWarehouseId());
                }
                $this->assignOrderItemToWarehouse($orderItem, $warehouse);
            }
        }
    }

    /**
     * Assign order item to Warehouse
     *
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @param Magestore_Inventorysuccess_Model_Warehouse $warehouse
     */
    public function assignOrderItemToWarehouse($orderItem, $warehouse)
    {
        $this->_increaseQtyToShipInOrderWarehouse($orderItem, $warehouse);
        $this->_addWarehouseOrderItem($orderItem, $warehouse);
    }

    /**
     * Decrease qty to ship in ordered warehouse
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @param int $warehouseId
     */
    protected function _decreaseQtyToShipInOrderWarehouse($orderItem, $warehouseId)
    {
        $qtyChanges = array(
            Magestore_Inventorysuccess_Model_Warehouse_Product::QTY_TO_SHIP => -$this->_getQtyToShip($orderItem)
        );
        $query = $this->stockRegistryService
            ->prepareChangeProductQty($warehouseId, $orderItem->getProductId(), $qtyChanges);
        $this->queryProcessService->addQuery($query, $this->process);
    }

    /**
     * Increase qty to ship in changing warehouse
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @param Magestore_Inventorysuccess_Model_Warehouse $warehouse
     */
    protected function _increaseQtyToShipInOrderWarehouse($orderItem, $warehouse)
    {
        $qtyChanges = array(
            Magestore_Inventorysuccess_Model_Warehouse_Product::QTY_TO_SHIP => $this->_getQtyToShip($orderItem)
        );
        $query = $this->stockRegistryService
            ->prepareChangeProductQty($warehouse->getWarehouseId(), $orderItem->getProductId(), $qtyChanges);
        $this->queryProcessService->addQuery($query, $this->process);
    }

    /**
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @param Magestore_Inventorysuccess_Model_Warehouse $warehouse
     */
    protected function _addWarehouseOrderItem($item, $warehouse)
    {
        $this->queryProcessService->addQuery(array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => array('warehouse_id' => $warehouse->getId()),
            'condition' => array('item_id=?' => $item->getId()),
            'table' => $item->getResource()->getMainTable(),
        ), $this->process);
    }

    /**
     *
     * @param Mage_Sales_Model_Order_Item $item
     */
    protected function _decreaseAvailableQtyInOrderWarehouse($item)
    {
        $orderWarehouse = $this->getOrderWarehouse($item->getOrder());
        if ($this->stockService->getStockId() == $this->stockService->getGlobalStockId()) {
            /* place order from global stock */
            $updateWarehouseId = $orderWarehouse->getWarehouseId();
        } else {
            /* place order from warehouse stock  */
            $updateWarehouseId = $this->stockService->getGlobalStockId();
        }

        $qtyChanges = array(Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY => -$this->_getOrderedQty($item));
        $query = $this->stockRegistryService->prepareChangeProductQty($updateWarehouseId, $item->getProductId(), $qtyChanges);
        $this->queryProcessService->addQuery($query, $this->process);
    }

    /**
     * Check order can be changed warehouse
     *
     * @param Mage_Sales_Model_Order $order
     * @param Magestore_Inventorysuccess_Model_Warehouse $warehouse
     * @return bool
     */
    public function canProcessOrder($order, $warehouse)
    {
        if ($order->getWarehouseId() == $warehouse->getWarehouseId()) {
            return false;
        }
        $key = $this->process . 'order' . $order->getId();
        if (Mage::registry($key)) {
            return false;
        }
        return true;
    }

    /**
     * Mark order as proccessed
     *
     * @param Mage_Sales_Model_Order $order
     * @param Magestore_Inventorysuccess_Model_Warehouse $warehouse
     */
    public function markOrderProcessed($order, $warehouse)
    {
        $order->setWarehouseId($warehouse->getWarehouseId());
        $key = $this->process . 'order' . $order->getId();
        if (!Mage::registry($key)) {
            Mage::register($key, true);
        }
    }

    /**
     *
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @return boolean
     */
    public function canProcessItem($orderItem)
    {
        /* check manage stock or not */
        if (!$this->isManageStock($orderItem)) {
            return false;
        }
        return true;
    }

    /**
     * Get Qty to Ship of Item
     *
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @return float
     */
    protected function _getQtyToShip($orderItem)
    {
        if ($this->_isUsedParentItem($orderItem)) {
            return $orderItem->getParentItem()->getQtyToShip();
        }
        return $orderItem->getQtyToShip();
    }

    /**
     * Check used parent item to ship
     *
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @return boolean
     */
    protected function _isUsedParentItem($orderItem)
    {
        if ($orderItem->getParentItemId()) {
            if ($orderItem->getParentItem()->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                return true;
            }
            if ($orderItem->isShipSeparately()) {
                return false;
            }
            return true;
        }
        return false;
    }
}