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
class Magestore_Inventorysuccess_Model_Service_Warehouse_WarehouseStockService
{
    /**
     * @var Magestore_Inventorysuccess_Model_Service_Adjuststock_AdjustStockService
     */
    protected $adjustStockService;
    /**
     * @var Magestore_Inventorysuccess_Model_Service_Stock_StockRegistryService
     */
    protected $stockRegistryService;

    /**
     * Magestore_Inventorysuccess_Model_Service_Warehouse_WarehouseStockService constructor.
     */
    public function __construct()
    {
        $this->adjustStockService = Magestore_Coresuccess_Model_Service::adjustStockService();
        $this->stockRegistryService = Magestore_Coresuccess_Model_Service::stockRegistryService();
    }

    /**
     * Return warehouse product collection with product information and total qtys
     * 
     * @return Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Product_Collection
     */
    public function getAllStocksWithProductInformation(){
        return Mage::getResourceModel('inventorysuccess/warehouse_product_collection')
            ->joinProductCollection()
            ->calculateQtys();
    }

    /**
     * Get stock collection with warehouse id and product ids
     * 
     * @param null $warehouseId
     * @param $productIds
     * @return Magestore_Inventorysuccess_Model_Mysql4_Transferstock_Product_Collection
     */
    public function getStocks($warehouseId = null, $productIds = null)
    {
        $collection = $this->getAllStocksWithProductInformation();
        if ($warehouseId)
            $collection->addWarehouseToFilter($warehouseId);
        if (is_array($productIds)) {
            $collection->addProductIdsToFilter($productIds);
        } else if (!empty($productIds)){
            $collection->addProductIdsToFilter(array($productIds));
        }
        return $collection;
    }

    /**
     * update stock in grid stock on hand
     *
     * @param int $warehouseId
     * @param array $stockData
     * @return $this
     */
    public function updateStockInGrid($warehouseId = null, $warehouseStockData = array())
    {
        if (!$warehouseId)
            return $this;
        $prepareData = $this->_prepareUpdateStockInGrid($warehouseStockData);
        if (isset($prepareData['adjust_data']['products']) && count($prepareData['adjust_data']['products']) > 0) {
            $prepareData['adjust_data'] = $this->_prepareAdjustmentData($prepareData['adjust_data'], $warehouseId);
            $this->createAdjustment($prepareData['adjust_data']);
        }
        if (count($prepareData['locations']) > 0)
            $this->stockRegistryService->updateLocation($warehouseId, $prepareData['locations']);
        return $this;
    }

    /**
     * @param array $adjustData
     * @param array $locations
     * @param array $warehouseStockData
     */
    public function _prepareUpdateStockInGrid($warehouseStockData = array())
    {
        $adjustData = $locations = array();
        foreach ($warehouseStockData as $productId => $stockItemData) {
            if (isset($stockItemData['sum_total_qty']) && isset($stockItemData['sum_total_qty_old']) &&
                $stockItemData['sum_total_qty'] != $stockItemData['sum_total_qty_old']
            ) {
                $product = Mage::getModel('catalog/product')->load($productId);
                $adjustData['products'][$productId] = array(
                    'adjust_qty' => $stockItemData['sum_total_qty'],
                    'old_qty' => $stockItemData['sum_total_qty_old'],
                    'product_sku' => $product->getSku(),
                    'product_name' => $product->getName()
                );
            }
            if (isset($stockItemData['shelf_location']) && isset($stockItemData['shelf_location_old']) &&
                $stockItemData['shelf_location'] != $stockItemData['shelf_location_old']
            ) {
                $locations[$productId] = $stockItemData['shelf_location'];
            }
        }
        return array('adjust_data' => $adjustData, 'locations' => $locations);
    }

    /**
     * Prepare stock adjustment data
     *
     * @param array $adjustData
     * @param int $warehouseId
     * @return array
     */
    protected function _prepareAdjustmentData($adjustData, $warehouseId)
    {
        $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_ID] = $warehouseId;
        $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_NAME] = null;
        $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_CODE] = null;
        $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::REASON] =
            Mage::helper('inventorysuccess')->__('Update Stock In Grid');
        return $adjustData;
    }

    /**
     * Create stock adjustment, $adjustData['products' => array(), 'warehouse_id' => $warehouseId,... ]
     *
     * @param array $adjustData
     * @return Magestore_Inventorysuccess_Model_AdjustStock
     */
    public function createAdjustment($adjustData, $updateCatalog = true)
    {
        $adjustStock = Mage::getModel('inventorysuccess/adjustStock');;

        /* create stock adjustment, require products, require qty changed */
        $this->adjustStockService->createAdjustment($adjustStock, $adjustData);

        /* created adjuststock or not */
        if ($adjustStock->getId()) {
            /* complete stock adjustment */
            $this->adjustStockService->complete($adjustStock, $updateCatalog);
        }
        return $adjustStock;
    }

    /**
     * Remove Product with warehouse id and product ids
     * 
     * @param $warehouseId
     * @param array $productIds
     * @return array
     */
    public function removeProducts($warehouseId, $productIds = array())
    {
        $success = array();
        $collection = $this->getStocks($warehouseId, $productIds)
            ->getCanDeleteProducts();
        foreach ($collection as $item) {
            $productId = $item->getProductId();
            if (in_array($productId, $productIds)) {
                $item->delete();
                $success[] = $productId;
            }
        }
        return array('success' => $success, 'error' => array_diff($success, $productIds));
    }

}