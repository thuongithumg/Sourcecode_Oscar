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
class Magestore_Inventorysuccess_Model_Service_OrderProcess_ShipmentFormService
{
    const CREATE_SHIPMENT_ACL_RESOURCE = 'admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/create_shipment';
    
    /**
     * @var Magestore_Inventorysuccess_Model_Service_Warehouse_Sales_OrderItemService 
     */
    protected $orderItemService;
    
    /**
     * @var Magestore_Inventorysuccess_Model_Service_Warehouse_WarehouseService
     */
    protected $warehouseService;
    
    /**
     * @var Magestore_Inventorysuccess_Model_Service_Stock_StockRegistryService
     */
    protected $stockRegistryService;
    
    /**
     * 
     */
    public function __construct()
    {
        $this->orderItemService = Magestore_Coresuccess_Model_Service::orderItemService();
        $this->warehouseService = Magestore_Coresuccess_Model_Service::warehouseService();
        $this->stockRegistryService = Magestore_Coresuccess_Model_Service::stockRegistryService();
    }
    /**
     * 
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function getAvailableWarehouses($order)
    {
        /* prepare list of items to ship */
        $needToShipItems = $this->_prepareNeedToShipItems($order);
        
        /* get orderred Warehouses from items */
        $orderWarehouses = $this->_loadOrderWarehouses($needToShipItems);

        $warehouseList = $this->warehouseService->getWarehouses(
            null, Magestore_Inventorysuccess_Model_Service_OrderProcess_OrderProcessService::CREATE_SHIPMENT_WAREHOUSE_PERMISSION
        );

        /* get products of items in all warehouses */
        $whProducts = $this->stockRegistryService
            ->getStocksFromEnableWarehouses(array_keys($needToShipItems), array_keys($warehouseList));

        /*Get stock items are not manage stock*/
        $warehouseProductIds = $whProducts->getColumnValues('product_id');
        $notInWarehouseProductIds = array_diff(array_keys($needToShipItems), $warehouseProductIds);

        if(!empty($notInWarehouseProductIds)) {
            $notManageStocksItem = $this->getNotManageStockItems($notInWarehouseProductIds);
            if ($notManageStocksItem->count() > 0) {
                foreach ($notManageStocksItem as $item) {
                    $this->addNotManageStockItemToShip($whProducts, $item, array_keys($warehouseList));
                }
            }
        }
        
        /* load information of warehouses */
        $warehouseIds = array();
        foreach ($whProducts as $whProduct) {
            $warehouseIds[$whProduct->getWarehouseId()] = $whProduct->getWarehouseId();
        }
        /*$warehouseList = $this->warehouseService->getWarehouses(
            $warehouseIds, Magestore_Inventorysuccess_Model_Service_OrderProcess_OrderProcessService::CREATE_SHIPMENT_WAREHOUSE_PERMISSION
        );*/
        
        /* prepare list of available warehouses */
        $warehouses = $this->_prepareAvailableWarehouses($needToShipItems, $whProducts, $orderWarehouses, $warehouseList);

        /* scan need-to-ship items before returning */
        $warehouses = $this->_scanShipItemsInWarehouseList($warehouses, $needToShipItems);

        return $this->_sortWarehouses($warehouses);
    }
    
    /**
     * get Stocks of not manage stock items
     *
     * @param array $productIds
     * @return Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Product_Collection
     */
    protected function getNotManageStockItems($productIds)
    {
        $warehouseProductCollection = Mage::getResourceModel('inventorysuccess/warehouse_product_collection')
            ->selectAllStocks()
            ->addFieldToFilter(
                Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID, 
                Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID
            )
            ->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse_Product::PRODUCT_ID, array('in' => $productIds));
        return $warehouseProductCollection;
    }

    /**
     * Add item not manage stock into warehouse product to ship
     *
     * @param Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Product_Collection $whProducts
     * @param Magestore_Inventorysuccess_Model_Warehouse_Product $item
     * @param array $warehouseIds
     */
    protected function addNotManageStockItemToShip($whProducts, $item, $warehouseIds)
    {
        if ($item->getManageStock())
            return $whProducts;
        foreach ($warehouseIds as $warehouseId) {
            $stockItem = Mage::getModel('inventorysuccess/warehouse_product');
            $stockItem->addData($item->getData());
            $stockItem->setId('99999999' . $item->getProductId() . $warehouseId);
            $stockItem->setWarehouseId($warehouseId);
            $stockItem->setQty('9999999');
            $stockItem->setTotalQty('9999999');
            $whProducts->addItem($stockItem);
        }
        return $whProducts;
    }
    
    
    /**
     * prepare list of available warehouses
     * 
     * @param array $needToShipItems
     * @param Magestore_InventorySuccess_Model_Mysql4_Warehouse_Product_Collection $whProducts
     * @param array $orderWarehouses
     * @param array $warehouseList
     * @return array
     */
    protected function _prepareAvailableWarehouses($needToShipItems, $whProducts, $orderWarehouses, $warehouseList)
    {
        $warehouses = array();       
        $whProductList = array();
        foreach($whProducts as $whProduct) {
            if (!$whProduct->getManageStock()) {
                $whProduct->setQty('9999999');
                $whProduct->setTotalQty('9999999');
            }
            $whProductList[$whProduct->getWarehouseId()][$whProduct->getProductId()] = $whProduct;
        }

        foreach ($warehouseList as $warehouseId => $warehouseInfo) {
            if(!in_array($warehouseId, array_keys($whProductList))) {
                /* ignore warehouse which doesn't contain any need-to-ship items*/
                continue;
            }
            foreach($needToShipItems as $productId => $items) {
                foreach($items as $item) {
                    $whProduct = isset($whProductList[$warehouseId][$productId]) ? $whProductList[$warehouseId][$productId] : null;
                    $qtyInWarehouse = 0;
                    if($whProduct) {
                        /* get orderred warehouseId */
                        $orderWarehouseId = isset($orderWarehouses[$item->getItemId()]) ? $orderWarehouses[$item->getItemId()] : null;

                        if($warehouseId == $orderWarehouseId) {
                            /* create shipment from orderred Warehouse */
                            $qtyInWarehouse = floatval($whProduct->getTotalQty());
                        } else {
                            /* create shipment from other warehouse */
                            $qtyInWarehouse = floatval($whProduct->getTotalQty() - $whProduct->getQtyToShip());
                        }
                    }
                    /* calculate qty-in-warehouse in the case of bundle item */
                    $qtyInWarehouse = $this->_getParentQtyInWarehouse($item, $qtyInWarehouse);

                    $qtyToShip = $this->_getQtyToShip($item);       
                    $itemId = $this->_getItemIdToShip($item);        

                    $lackQty = max($qtyToShip - $qtyInWarehouse, 0);

                    if(!isset($warehouses[$warehouseId]['items'][$itemId])) {
                        $warehouses[$warehouseId]['items'][$itemId] = array(
                                    'qty_in_warehouse' => $qtyInWarehouse,
                                    'lack_qty' => $lackQty,
                        );
                        /* insert warehouse data to array */
                        if(isset($warehouses[$warehouseId]['lack_qty'])) {
                            $warehouses[$warehouseId]['lack_qty'] += $lackQty;
                        } else {
                            $warehouses[$warehouseId]['lack_qty'] = $lackQty;
                        }
                    } else {
                        /* insert warehouse data to array */
                        $warehouses[$warehouseId]['lack_qty'] += $lackQty - $warehouses[$warehouseId]['items'][$itemId]['lack_qty'];   
                        
                        $warehouses[$warehouseId]['items'][$itemId] = array(
                                    'qty_in_warehouse' => min($qtyInWarehouse, $warehouses[$warehouseId]['items'][$itemId]['qty_in_warehouse']),
                                    'lack_qty' => max($lackQty, $warehouses[$warehouseId]['items'][$itemId]['lack_qty']),
                        );                  
                    }
                    $warehouses[$warehouseId]['info'] = $warehouseInfo;
                }
            }
        }
        return $warehouses;
    }    
    
    /**
     * prepare list of items to ship 
     * 
     * @param Mage_Sales_Model_Order $order
     */
    protected function _prepareNeedToShipItems($order)
    {
        /* prepare list of items to ship */
        $needToShipItems = array();
        foreach($order->getAllItems() as $item) {
            if($item->getProduct()->isComposite())
                continue;
            /* ignore virtual product */
            if($item->getIsVirtual()){
                continue;
            }       
            $needToShip = true;
            if($item->getQtyToShip() == 0) {
                if($item->getParentItemId()) {
                    if(!$item->getParentItem()->getQtyToShip()) {
                        $needToShip = false;
                    }
                } else {
                    $needToShip = false;
                }
            }
            if(!$needToShip) {
                continue;
            }
            if(!isset($needToShipItems[$item->getProductId()])) {
                $needToShipItems[$item->getProductId()] = array($item);
            } else {
                $needToShipItems[$item->getProductId()][] = $item;
            }
        }     
        return $needToShipItems;
    }
    
    /**
     * 
     * @param array $needToShipItems
     * @return array
     */
    protected function _loadOrderWarehouses($needToShipItems)
    {
        $orderItemIds = array();
        foreach($needToShipItems as $items) {
            foreach($items as $item)
                $orderItemIds[] = $item->getItemId();
        }
        
        return $this->orderItemService->getWarehousesByItemIds($orderItemIds);   
    }
    
    /**
     * 
     * @param Mage_Sales_Model_Order_Item $item
     * @return boolean
     */
    protected function _isUsedParentItem($item)
    {
        if($item->getParentItemId()) {
            if($item->getParentItem()->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                return true;
            }
            if($item->isShipSeparately()) {
                return false;
            }
            return true;
        }
        return false;
    }
    
    /**
     * scan need-to-ship items before returning
     * 
     * @param array $warehouses
     * @param array $needToShipItems
     * @return array
     */
    protected function _scanShipItemsInWarehouseList($warehouses, $needToShipItems)
    {
        foreach($warehouses as $warehouseId => &$warehouseData) {
            foreach($needToShipItems as $items) {
                foreach($items as $item) {
                    $qtyToShip = $this->_getQtyToShip($item);
                    $itemId = $this->_getItemIdToShip($item);                
                    if(!isset($warehouseData['items'][$itemId])) {
                        $warehouseData['items'][$itemId] = array(
                            'qty_in_warehouse' => 0,
                            'lack_qty' => $qtyToShip,
                        );
                        $warehouses[$warehouseId]['lack_qty'] += $qtyToShip;
                    }
                }
            }
        }
        
        return $warehouses;
    }
    
    /**
     * Sort warehouses by lack_qty ASC
     * 
     * @param array $warehouses
     * @return array
     */
    protected function _sortWarehouses($warehouses)
    {
        $sortedWarehouses = array();
        usort($warehouses, array($this, "sortShipmentWarehouses"));    
        foreach($warehouses as $warehouse){
            $warehouseId = $warehouse['info']['warehouse_id'];
            $sortedWarehouses[$warehouseId] = $warehouse;
        }
        return $sortedWarehouses;
    }
    
    /**
     * Compare lack_qty of warehouses
     * 
     * @param array $warehouseA
     * @param array $warehouseB
     * @return int
     */
    public function sortShipmentWarehouses($warehouseA, $warehouseB)
    {
        if($warehouseA['lack_qty'] == $warehouseB['lack_qty'])
            return 0;
        if($warehouseA['lack_qty'] < $warehouseB['lack_qty'])
            return -1;
        return 1;
    }
    
    /**
     * Get Qty to Ship of Item
     * 
     * @param Mage_Sales_Model_Order_Item $item
     * @return float
     */
    protected function _getQtyToShip($item)
    {
        if($this->_isUsedParentItem($item)) {
            return $item->getParentItem()->getQtyToShip();
        }
        return $item->getQtyToShip();
    }
    
    /**
     * Get ItemId of need-to-ship item
     * 
     * @param Mage_Sales_Model_Order_Item $item
     * @return int
     */
    protected function _getItemIdToShip($item)
    {
        if($this->_isUsedParentItem($item)) {
            return $item->getParentItemId();
        }
        return $item->getItemId();        
    }       
    
    /**
     * 
     * @param Mage_Sales_Model_Order_Item $item
     * @param float $qtyInWarehouse
     */
    protected function _getParentQtyInWarehouse($item, $qtyInWarehouse)
    {
        if(!$this->_isUsedParentItem($item)) {
            return $qtyInWarehouse;
        }
        $parentQtyInWarehouse = $qtyInWarehouse;
        if($item->getParentItem()->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            $parentQtyInWarehouse = intval($qtyInWarehouse / $item->getQtyOrdered() * $item->getParentItem()->getQtyOrdered());
        }

        return $parentQtyInWarehouse;
    }
}