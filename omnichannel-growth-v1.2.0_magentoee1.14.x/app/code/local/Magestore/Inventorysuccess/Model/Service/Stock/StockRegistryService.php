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
class Magestore_Inventorysuccess_Model_Service_Stock_StockRegistryService
{
    /**
     * Query process name
     */
    const QUERY_PROCESS = 'stock_registry';

    /**
     * @var Magestore_Coresuccess_Model_Service_QueryProcessorService
     */
    protected $queryProcessorService;

    /**
     * @var Magestore_Inventorysuccess_Model_Service_Warehouse_WarehouseService
     */
    protected $warehouseService;

    /**
     *
     */
    public function __construct()
    {
        $this->queryProcessorService = Magestore_Coresuccess_Model_Service::queryProcessorService();
        $this->warehouseService = Magestore_Coresuccess_Model_Service::warehouseService();
    }

    /**
     * get stocks from warehouse
     *
     * @param int $warehouseId
     * @param array $productIds
     * @param bool $global
     * @return Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Product_Collection
     */
    public function getStocks($warehouseId, $productIds = array(), $global = false)
    {
        $stocks = Mage::getResourceModel('inventorysuccess/warehouse_product_collection');
        if ($global) {
            $stocks->selectAllStocks();
        }
        $stocks->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID, $warehouseId);
        if (count($productIds)) {
            $stocks->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse_Product::PRODUCT_ID, array('in' => $productIds));
        }
        return $stocks;
    }

    /**
     * get stocks from warehouse
     *
     * @param int $warehouseId
     * @param array $productIds
     * @return Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Product_Collection
     */
    public function getProductList($warehouseId, $productIds = array())
    {
        $collection = $this->getStock()->getProductList();
        return $collection;
    }

    /**
     * get stock in warehouse
     *
     * @param int $warehouseId
     * @param int $productId
     * @param boll $global
     * @return Magestore_Inventorysuccess_Model_Warehouse_Product
     */
    public function getStock($warehouseId, $productId, $global = false)
    {
        return $this->getStocks($warehouseId, array($productId), $global)
            ->setPageSize(1)
            ->setCurPage(1)
            ->getFirstItem();
    }

    /**
     * get stock data by product Id
     *
     * @param int $productId
     * @return array
     */
    public function getStocksByProduct($productId)
    {
        $collection = Mage::getResourceModel('inventorysuccess/warehouse_product_collection')
            ->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse_Product::PRODUCT_ID, $productId);
        $stocks = $collection->toArray();
        return $stocks['items'];
    }

    /**
     * Update shelf locations of products in Warehouse
     *
     * @param int $warehouseId
     * @param array $locations ([$productId => $location])
     */
    public function updateLocation($warehouseId, $locations)
    {
        /* start queries processing */
        $this->queryProcessorService->start(self::QUERY_PROCESS);

        /* prepare to update shelf location in Warehouse, then add queries to Processor */
        $queryData = $this->getResource()->prepareUpdateLocation($warehouseId, $locations);
        $this->queryProcessorService->addQuery($queryData, self::QUERY_PROCESS);

        /* process queries in Processor */
        $this->queryProcessorService->process(self::QUERY_PROCESS);
    }

    /**
     * Prepare query to change total_qty, qty_to_ship of product in warehouse
     * Do not update global stock
     * Do not commit query
     *
     * @param int $warehouseId
     * @param int $productId
     * @param array $changeQtys
     * @return array
     */
    public function prepareChangeProductQty($warehouseId, $productId, $changeQtys)
    {
        $stock = $this->getStock($warehouseId, $productId, true);
        if (!$stock->getId()) {
            return $this->getResource()->prepareAddProductToWarehouse($warehouseId, $productId, $changeQtys);
        }
        return $this->getResource()->prepareChangeProductQty($warehouseId, $productId, $changeQtys);
    }

    /**
     * prepare to change qtys of product in multiple warehouses
     *
     * @param int $productId
     * @param array $changeQtys
     * @return array
     */
    public function prepareChangeQtys($productId, $changeQtys)
    {
        $queries = array();
        $changeQtys = $this->prepareChangeQtysData($changeQtys);
        if (count($changeQtys)) {
            foreach ($changeQtys as $warehouseId => $changeQtyData) {
                $stock = $this->getStock($warehouseId, $productId, true);
                if (!$stock->getId()) {
                    $queries[] = $this->getResource()->prepareAddProductToWarehouse($warehouseId, $productId, $changeQtyData);
                } else {
                    $queries[] = $this->getResource()->prepareChangeProductQty($warehouseId, $productId, $changeQtyData);
                }
            }
        }
        return $queries;
    }

    /**
     *
     * @param array $changeQtys
     * @return array
     */
    protected function prepareChangeQtysData($changeQtys)
    {
        if (count($changeQtys)) {
            foreach ($changeQtys as $warehouseId => $changeQtyData) {
                if (!count($changeQtyData)) {
                    unset($changeQtys[$warehouseId]);
                    continue;
                }
                foreach ($changeQtyData as $field => $value) {
                    if ($value == 0) {
                        unset($changeQtyData[$field]);
                    }
                }
            }
        }
        return $changeQtys;
    }

    /**
     * get Stocks from enable Warehouses
     *
     * @param array $productIds
     * @param array $warehouseIds
     * @return Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Product_Collection
     */
    public function getStocksFromEnableWarehouses($productIds = array(), $warehouseIds = array())
    {
//        $enableWarehouseIds = $this->warehouseService->getEnableWarehouses()->getAllIds();
        $warehouseProductCollection = Mage::getResourceModel('inventorysuccess/warehouse_product_collection');
//            ->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID, array('in' => $enableWarehouseIds));
        if (count($productIds)) {
            $warehouseProductCollection->addFieldToFilter('product_id', array('in' => $productIds));
        }
        if (count($warehouseIds)) {
            $warehouseProductCollection->addFieldToFilter(
                Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID, array('in' => $warehouseIds)
            );
        }
        return $warehouseProductCollection;
    }

    /**
     *
     * @param int $productId
     * @param array $stockItemData
     * @param array $warehouses
     * @param array $ignoreWarehouses
     */
    public function cloneStockItemData($productId, $stockItemData, $warehouses = array(), $ignoreWarehouses = array())
    {
        /* start queries processing */
        $this->queryProcessorService->start(self::QUERY_PROCESS);

        /* prepare to update shelf location in Warehouse, then add queries to Processor */
        $queryData = $this->getResource()->prepareCloneStockItemData($productId, $stockItemData, $warehouses, $ignoreWarehouses);
        $this->queryProcessorService->addQuery($queryData, self::QUERY_PROCESS);

        /* process queries in Processor */
        $this->queryProcessorService->process(self::QUERY_PROCESS);
    }

    /**
     *
     * @param int $productId
     * @param array $stockStatusData
     * @param array $warehouses
     * @param array $ignoreWarehouses
     */
    public function cloneStockStatus($productId, $stockStatusData, $warehouses = array(), $ignoreWarehouses = array())
    {
        /* start queries processing */
        $this->queryProcessorService->start(self::QUERY_PROCESS);

        /* prepare to update shelf location in Warehouse, then add queries to Processor */
        $queryData = $this->getResource()->prepareCloneStockStatus($productId, $stockStatusData, $warehouses, $ignoreWarehouses);

        $this->queryProcessorService->addQuery($queryData, self::QUERY_PROCESS);

        /* process queries in Processor */
        $this->queryProcessorService->process(self::QUERY_PROCESS);
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Mysql4_Stock_StockRegistry
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('inventorysuccess/stock_stockRegistry');
    }
}