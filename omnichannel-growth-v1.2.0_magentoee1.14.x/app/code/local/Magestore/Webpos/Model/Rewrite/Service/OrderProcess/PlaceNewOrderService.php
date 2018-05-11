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
 * Magestore_Webpos Model
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @author      Magestore Developer
 */
class Magestore_Webpos_Model_Rewrite_Service_OrderProcess_PlaceNewOrderService
    extends Magestore_Inventorysuccess_Model_Service_OrderProcess_PlaceNewOrderService
{

    /**
     *
     * @param Mage_Sales_Model_Order_Item $item
     */
    protected function _addWarehouseOrderItem($item)
    {
        $warehouse = $this->getOrderItemWarehouse($item->getOrder(), $item);
        $item->setWarehouseId($warehouse->getId());

        $this->queryProcessService->addQuery(array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' =>  array('warehouse_id' => $warehouse->getId()),
            'condition' => array('item_id=?' => $item->getId()),
            'table' => Mage::getResourceSingleton('sales/order_item')->getMainTable(),
        ), $this->process);

    }

    /**
     *
     * @param Mage_Sales_Model_Order_Item $item
     */
    protected function _decreaseAvailableQtyInOrderWarehouse($item)
    {
        $orderWarehouse = $this->getOrderItemWarehouse($item->getOrder(), $item);
        $orderedQty = $this->_getOrderedQty($item);

        if($this->stockService->getStockId() == $this->stockService->getGlobalStockId()) {
            /* place order from global stock */
            $updateWarehouseId = $orderWarehouse->getWarehouseId();
        } else {
            /* place order from warehouse stock */
            $updateWarehouseId = $this->stockService->getGlobalStockId();

            /* place order from different one than current warehouse */
            if($orderWarehouse->getWarehouseId() != $this->stockService->getStockId()) {
                /* rollback available qty of item in current warehouse */
                $qtyChanges = array(Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY => $orderedQty);
                $query = $this->stockRegistryService
                    ->prepareChangeProductQty($this->stockService->getStockId(), $item->getProductId(), $qtyChanges);
                $this->queryProcessService->addQuery($query, $this->process);

                /* subtract available qty of item in ordered warehouse */
                $qtyChanges = array(Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY => -$orderedQty);
                $query = $this->stockRegistryService
                    ->prepareChangeProductQty($orderWarehouse->getWarehouseId(), $item->getProductId(), $qtyChanges);
                $this->queryProcessService->addQuery($query, $this->process);
            }
        }
        /* subtract available qty of item in need-update warehouse */
        $qtyChanges = array(Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY => -$orderedQty);
        $query = $this->stockRegistryService->prepareChangeProductQty($updateWarehouseId, $item->getProductId(), $qtyChanges);
        $this->queryProcessService->addQuery($query, $this->process);
    }



    /**
     * Get warehouse which responds to the order
     *
     * @param Mage_Sales_Model_Order $order
     * @param Mage_Sales_Model_Order_Item $item
     * @return Magestore_Inventorysuccess_Model_Warehouse
     */
    public function getOrderItemWarehouse($order, $item = null)
    {
        /* integration with m2e-listings */
        $product_id = 0;
        if($item){
            $product_id = $item->getProductId();
        }
        $orderWarehouse = $this->warehouseService->getPrimaryWarehouse();
        $curStockId = $this->stockService->getStockId();
        if($curStockId == $this->stockService->getGlobalStockId()) {
            $orderWarehouse->setWarehouseId($curStockId);
            $orderWarehouse->setStockId($curStockId);
        }
        /* allow to change the order Warehouse by other extension */
        Mage::dispatchEvent('inventorysuccess_new_order_warehouse', array(
            'order' => $order,
            'item' => $item,
            'warehouse' => $orderWarehouse,
            'product_id' => $product_id,
        ));
        return $orderWarehouse;
    }
}