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
class Magestore_Inventorysuccess_Model_Service_OrderProcess_AbstractService
{

    /**
     * @var string
     */
    protected $process = 'order_process';

    /**
     * @var array
     */
    protected $orderWarehouseIds = array();

    /**
     * @var array
     */
    protected $shipWarehouseIds = array();

    /**
     * @var array
     */
    protected $returnWarehouseIds = array();

    /**
     * @var Magestore_Inventorysuccess_Model_Service_Warehouse_Sales_OrderItemService
     */
    protected $orderItemService;

    /**
     * @var Magestore_Inventorysuccess_Model_Service_Warehouse_Sales_ShipmentItemService
     */
    protected $shipmentItemService;

    /**
     * @var Magestore_Inventorysuccess_Model_Service_Warehouse_Sales_CreditmemoItemService
     */
    protected $creditmemoItemService;

    /**
     * @var Magestore_Coresuccess_Model_Service_QueryProcessorService
     */
    protected $queryProcessService;

    /**
     * @var Magestore_Inventorysuccess_Model_Service_Warehouse_WarehouseService
     */
    protected $warehouseService;

    /**
     * @var Magestore_Inventorysuccess_Model_Service_Stock_StockRegistryService
     */
    protected $stockRegistryService;

    /**
     * @var Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService
     */
    protected $stockChangeService;

    /**
     * @var Mage_CatalogInventory_Helper_Data
     */
    protected $catalogInventoryHelper;

    /**
     * @var Magestore_Inventorysuccess_Model_Service_Stock_StockService
     */
    protected $stockService;

    /**
     *
     */
    public function __construct()
    {
        $this->queryProcessService = Magestore_Coresuccess_Model_Service::queryProcessorService();
        $this->orderItemService = Magestore_Coresuccess_Model_Service::orderItemService();
        $this->shipmentItemService = Magestore_Coresuccess_Model_Service::shipmentItemService();
        $this->creditmemoItemService = Magestore_Coresuccess_Model_Service::creditmemoItemService();
        $this->warehouseService = Magestore_Coresuccess_Model_Service::warehouseService();
        $this->stockRegistryService = Magestore_Coresuccess_Model_Service::stockRegistryService();
        $this->stockChangeService = Magestore_Coresuccess_Model_Service::stockChangeService();
        $this->catalogInventoryHelper = Mage::helper('cataloginventory');
        $this->stockService = Magestore_Coresuccess_Model_Service::stockService();
    }

    /**
     * Get ordered warehouse id from orderItemId
     *
     * @param int $orderItemId
     * @return int
     */
    public function getOrderedWarehouse($orderItemId)
    {
        if (!isset($this->orderWarehouseIds[$orderItemId])) {
            $this->orderWarehouseIds[$orderItemId] = $this->orderItemService->getWarehouseIdByItemId($orderItemId);
        }
        return $this->orderWarehouseIds[$orderItemId];
    }

    /**
     * Get shipped warehouse id from orderItemId
     * @param int $orderItemId
     * @return int
     */
    public function getShippedWarehouse($orderItemId)
    {
        if (!isset($this->shipWarehouseIds[$orderItemId])) {
            $this->shipWarehouseIds[$orderItemId] = $this->shipmentItemService->getWarehouseIdByItemId($orderItemId);
        }
        return $this->shipWarehouseIds[$orderItemId];
    }

    /**
     * Get returned warehouse id from orderItemId
     * @param int $orderItemId
     * @return int
     */
    public function getReturnedWarehouse($orderItemId)
    {
        if (!isset($this->returnWarehouseIds[$orderItemId])) {
            $this->returnWarehouseIds[$orderItemId] = $this->creditmemoItemService->getWarehouseIdByItemId($orderItemId);
        }
        return $this->returnWarehouseIds[$orderItemId];
    }

    /**
     * Mark item processed
     *
     * @param Mage_Core_Model_Abstract $item
     */
    public function markItemProcessed($item)
    {
        $key = $this->process . 'item' . $item->getId();
        if (!Mage::registry($key)) {
            Mage::register($key, true);
        }
    }

    /**
     * Check item processed or not
     *
     * @param Mage_Core_Model_Abstract $item
     * @return boolean
     */
    public function isProcessedItem($item)
    {
        $key = $this->process . 'item' . $item->getId();
        if (Mage::registry($key)) {
            return true;
        }
        return false;
    }

    /**
     * Manage stock of product in this item or not
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return bool
     */
    public function isManageStock($item)
    {
        /* do not manage qty of this product type */
//        if (!Mage::helper('cataloginventory')->isQty($item->getProductType())) {
//            return false;
//        }

        /** @var Magestore_Coresuccess_Model_Rewrite_CataloginventoryStockItem $stockItem */
        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($item->getProductId());

        /* do not manage stock of this product */
        if (!$stockItem->getManageStock()) {
            return false;
        }

        return true;
    }

    /**
     * Get simple item from order item
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return Mage_Sales_Model_Order_Item
     */
    protected function _getSimpleItem($item)
    {
        $simpleItem = $item;
        if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            foreach ($item->getChildrenItems() as $childItem) {
                $simpleItem = $childItem;
                break;
            }
        }

        return $simpleItem;
    }


    /**
     * Get shipped qty
     *
     * @param Mage_Sales_Model_Order_Shipment_Item $item
     * @return float
     */
    protected function _getShippedQty($item)
    {
        return $item->getQty();
    }

    /**
     * Get orderred qty of item
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return int|float
     */
    protected function _getOrderedQty($item)
    {
        $qtyOrdered = 0;
        /*
        if($parentItem = $item->getParentItem()) {
           if($parentItem->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
               $qtyOrdered = $parentItem->getQtyOrdered();
           }
        }
         */
        $qtyOrdered = $qtyOrdered ? $qtyOrdered : $item->getQtyOrdered();
        return $qtyOrdered;
    }

    /**
     * Get canceled qty of item
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return int|float
     */
    protected function _getCanceledQty($item)
    {
        if (!isset($this->canceledQtys[$item->getItemId()])) {
            $qtyCanceled = 0;

            /* get qty-to-cancel of configurable product */
            if ($parentItem = $item->getParentItem()) {
                if ($parentItem->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                    $this->canceledQtys[$item->getItemId()] = $parentItem->getQtyCanceled();
                    return $this->canceledQtys[$item->getItemId()];
                }
            }

            $children = $item->getChildrenItems();
            $qtyToCancel = $item->getQtyToCancel();
            if ($item->getId() && $item->getProductId() && empty($children)) {
                $qtyCanceled = $qtyToCancel;
            }
            $this->canceledQtys[$item->getItemId()] = $qtyCanceled;
        }

        return $this->canceledQtys[$item->getItemId()];
    }

}