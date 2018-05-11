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
class Magestore_Inventorysuccess_Model_Service_Catalog_ProductSaveService
{

    const PRODUCT_SAVE_KEY = 'inventorysuccess_save_product_to_warehouse';
    const WAREHOUSE_STOCK_FIELD = 'warehouse_stock';
    const SIMPLE_WAREHOUSE_STOCK_FIELD = 'simple_product_warehouse_stock';

    /**
     * @var Magestore_Inventorysuccess_Model_Service_Adjuststock_AdjuststockService
     */
    protected $adjustStockService;

    /**
     * @var Magestore_Inventorysuccess_Model_Service_Stock_StockRegistryService
     */
    protected $stockRegistryService;

    /**
     * @var bool
     */
    protected $updateCatalog = true;

    /**
     * @var Magestore_Inventorysuccess_Model_Service_Stock_StockService
     */
    protected $stockService;

    /**
     *
     */
    public function __construct()
    {
        $this->adjustStockService = Magestore_Coresuccess_Model_Service::adjustStockService();
        $this->stockRegistryService = Magestore_Coresuccess_Model_Service::stockRegistryService();
        $this->stockService = Magestore_Coresuccess_Model_Service::stockService();
    }

    /**
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $postData
     */
    public function handleProductSaveAfter($product, $postData)
    {
        $this->_processUpdateSupplierStock($product,$postData);

        if (!$this->needUpdateStock($product, $postData)) {
            return;
        }
        $warehouseStockData = $this->_getWarehouseStockData($postData);

        $this->_processUpdateWarehouseStock($product, $warehouseStockData);

        $this->_updateStockStatus($product);
        $this->_updateStockItem($product);

    }

    /**
     * @param $product
     * @param $postData
     */
    public function _processUpdateSupplierStock($product,$postData){
        $productId = $product->getData('entity_id');
        if(!$productId){
            return ;
        }
        if(isset($postData['supplier_stock'])){
            foreach($postData['supplier_stock'] as $data){
                if(isset($data['supplier_id'])){
                    $supplierId = isset($data['supplier']) ? $data['supplier'] : $data['supplier_id'];
                    $supplier = Mage::getModel('suppliersuccess/supplier')->load($supplierId);
                    $productData = array();
                    unset($data['supplier']);
                    unset($data['supplier_id']);
                    $productData[$productId] = $data;
                    Magestore_Coresuccess_Model_Service::supplierService()->setProductsToSupplier($supplier, $productData , true);
                }
            }
        }
    }

    /**
     * Adjust stock of Product in Warehouse(s)
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $warehouseStockData
     */
    protected function _processUpdateWarehouseStock($product, $warehouseStockData)
    {

        $data = $product->getData();
        if (isset($data['stock_data'])) {
            if (isset($data['stock_data']['inventory_use_force_edit'])) {
                $this->updateCatalog = false;
            }
        }

        $updateWarehouseIds = array();

        foreach ($warehouseStockData as $stockItemData) {
            if (!$stockItemData || !count($stockItemData)) {
                continue;
            }
            $adjustData = $this->_prepareAdjustmentData($product, $stockItemData);
            $shelfLocationData = $this->_prepareShelfLocationData($product, $stockItemData);
            $warehouseId = $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_ID];
            /* continue if the warehouse has been processed */
            if (in_array($warehouseId, $updateWarehouseIds)) {
                continue;
            }
            $updateWarehouseIds[] = $warehouseId;

            /* prepare force_edit */
            $check_force_edit = $this->_prepareForceEdit($stockItemData);
            if ($check_force_edit) {
                /* create stock force_edit */
                $this->updateCatalog = false;
                $this->_createForceEdit($product->getId(), $stockItemData);
            } else {
                /* create stock adjustment */
                $this->_createAdjustment($adjustData);
            }

            /* update shelf location of in warehouse */
            $this->_massUpdateShelfLocation($shelfLocationData);
        }
    }


    /* Add bY Kai */
    public function _createForceEdit($pId, $stockItemData)
    {

        /* adjust stocks in warehouse & global */
        $updateCatalog = $this->updateCatalog;
        $stockChangeService = Mage::getModel('inventorysuccess/service_stock_stockChangeService');
        $stockChangeService->forceEdit($pId, $stockItemData, 'force_edit', $updateCatalog);
    }

    /**
     * @param $stockItemData
     * @return bool
     */
    public function _prepareForceEdit($stockItemData)
    {
        if (isset($stockItemData['force_edit'])) {
            if ($stockItemData['force_edit'] == 1) {
                return true;
            }
            return false;
        }
        return false;
    }


    /**
     * Create stock adjustment, $adjustData['products' => [], 'warehouse_id' => $warehouseId,... ]
     *
     * @param array $adjustData
     * @return Magestore_Inventorysuccess_Model_AdjustStock
     */
    protected function _createAdjustment($adjustData)
    {
        $adjustStock = Mage::getModel('inventorysuccess/adjustStock');;

        /* create stock adjustment, require products, require qty changed */
        $this->adjustStockService->createAdjustment($adjustStock, $adjustData, true, true);

        /* created adjuststock or not */
        if ($adjustStock->getId()) {
            /* complete stock adjustment */
            $this->adjustStockService->complete($adjustStock, $this->updateCatalog);
        }
        return $adjustStock;
    }

    /**
     * Prepare stock adjustment data
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $data
     * @return array
     */
    protected function _prepareAdjustmentData($product, $data)
    {
        $adjustData = array();
        $totalQty = isset($data['total_qty']) ? $data['total_qty'] : 0;
        $adjustData['products'] = array(
            $product->getId() => array(
                'adjust_qty' => $totalQty,
                'product_name' => $product->getName(),
                'product_sku' => $product->getSku(),
            ));

        $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_ID] = isset($data['warehouse']) ?
            $data['warehouse'] :
            (isset($data['warehouse_id']) ? $data['warehouse_id'] : null);
        $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_CODE] = isset($data['warehouse_code']) ?
            $data['warehouse_code'] : null;
        $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_NAME] = isset($data['warehouse_name']) ?
            $data['warehouse_name'] : null;
        $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::REASON] = Mage::helper('inventorysuccess')->__('Direct Adjust from Product edit');

        return $adjustData;
    }

    /**
     * Prepare shelf location data
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $data
     * @return array
     */
    protected function _prepareShelfLocationData($product, $data)
    {
        $locations = array();
        $shelfLocation = isset($data['shelf_location']) ? $data['shelf_location'] : null;
        $warehouseId = isset($data['warehouse']) ? $data['warehouse'] :
            (isset($data['warehouse_id']) ? $data['warehouse_id'] : null);
        if (!$shelfLocation || !$warehouseId) {
            return $locations;
        }
        $locations[$warehouseId] = array($product->getId() => $shelfLocation);
        return $locations;
    }

    /**
     * Mass update shelf location of products in warehouses
     * $shelfLocationData[$warehouseId => [$productId => $shelfLocation]]
     *
     * @param array $shelfLocationData
     */
    protected function _massUpdateShelfLocation($shelfLocationData)
    {
        if (!count($shelfLocationData)) {
            return;
        }
        /** @var array $locations [$productId => $shelfLocation] */
        foreach ($shelfLocationData as $warehouseId => $locations) {
            if (!count($locations)) {
                continue;
            }
            $this->stockRegistryService->updateLocation($warehouseId, $locations);
        }
    }

    /**
     *
     * @param array $postData
     * @return array
     */
    protected function _getSimpleWarehouseStockData($postData)
    {
        if (!isset($postData[self::SIMPLE_WAREHOUSE_STOCK_FIELD])) {
            return array();
        }
        return $postData[self::SIMPLE_WAREHOUSE_STOCK_FIELD];
    }

    /**
     *
     * @param array $postData
     * @return array
     */
    protected function _getWarehouseStockData($postData)
    {
        if (!isset($postData[self::WAREHOUSE_STOCK_FIELD])
            && !isset($postData[self::SIMPLE_WAREHOUSE_STOCK_FIELD])
        ) {
            return array();
        }
        $warehouseStockData = isset($postData[self::WAREHOUSE_STOCK_FIELD]) ?
            $postData[self::WAREHOUSE_STOCK_FIELD] :
            $postData[self::SIMPLE_WAREHOUSE_STOCK_FIELD];
        if (!count($warehouseStockData)) {
            return array();
        }
        /* prepare warehouse stock data */
        $preparedStockData = array();
        foreach ($warehouseStockData as $stockItemData) {
            if (!$stockItemData || !count($stockItemData)) {
                continue;
            }
            $warehouseId = isset($stockItemData['warehouse']) ?
                $stockItemData['warehouse'] :
                (isset($stockItemData['warehouse_id']) ? $stockItemData['warehouse_id'] : null);
            if (!$warehouseId) {
                continue;
            }
            $stockItemData['warehouse_id'] = $warehouseId;
            if (!isset($preparedStockData[$warehouseId])) {
                $preparedStockData[$warehouseId] = $stockItemData;
            } else {
                /* Add by Kai */
                $preparedStockData[$warehouseId]['force_edit'] = isset($stockItemData['force_edit']) ? $stockItemData['force_edit'] : 0;
                /* End by Kai */
                $preparedStockData[$warehouseId]['total_qty'] += isset($stockItemData['total_qty']) ? $stockItemData['total_qty'] : 0;
                $preparedStockData[$warehouseId]['shelf_location'] .= (isset($stockItemData['shelf_location']) && $stockItemData['shelf_location']) ?
                    ', ' . $stockItemData['shelf_location'] : '';
            }
        }
        return $preparedStockData;
    }

    /**
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $postData
     * @return bool
     */
    public function needUpdateStock($product, $postData)
    {
        if ($product->isComposite()) {
            //return false;
        }

        if (Mage::registry(self::PRODUCT_SAVE_KEY)) {
            return false;
        }

        if (!count($this->_getWarehouseStockData($postData))
            && !count($this->_getSimpleWarehouseStockData($postData))
        ) {
            return false;
        }

        return true;
    }

    /**
     *
     * @param Mage_Catalog_Model_Product $product
     *
     */
    protected function _updateStockStatus($product)
    {

    }

    /**
     *
     * @param Mage_Catalog_Model_Product $product
     */
    protected function _updateStockItem($product)
    {
        $item = $product->getStockItem();
        if (!$item) {
            return;
        }
        if ($item->getStockId() != $this->stockService->getGlobalStockId()) {
            return;
        }
        $stockItemData = $this->_prepareSaveDataStockItem($item);
        $this->stockRegistryService->cloneStockItemData($product->getId(), $stockItemData, array(), array($this->stockService->getGlobalStockId()));
    }

    /**
     *
     *
     * @param Mage_Cataloginventory_Model_Stock_Item $item
     * @return array
     */
    protected function _prepareSaveDataStockItem($item)
    {
        $stockItemData = array();
        $updateFields = array(
            'min_qty',
            'use_config_min_qty',
            'is_qty_decimal',
            'is_decimal_divided',
            'backorders',
            'use_config_backorders',
            'min_sale_qty',
            'use_config_min_sale_qty',
            'max_sale_qty',
            'use_config_max_sale_qty',
            'use_config_notify_stock_qty',
            'manage_stock',
            'use_config_manage_stock',
            'use_config_qty_increments',
            'use_config_enable_qty_inc',
            'enable_qty_increments',
        );
        foreach ($updateFields as $field) {
            $stockItemData[$field] = $item->getData($field);
        }
        return $stockItemData;
    }
}