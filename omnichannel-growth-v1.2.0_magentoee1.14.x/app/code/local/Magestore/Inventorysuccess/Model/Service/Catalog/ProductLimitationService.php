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
class Magestore_Inventorysuccess_Model_Service_Catalog_ProductLimitationService
{
    const FILTER_BY_STOCK_ID_FLAG = 'filter_by_stock_id_flag';
    
    /**
     * @var Magestore_Inventorysuccess_Model_Service_Stock_StockService 
     */
    protected $stockService;
    
    /**
     * @var Magestore_Inventorysuccess_Model_Service_Warehouse_WarehouseService
     */
    protected $warehouseService;    
    
    /**
     * 
     */
    public function __construct()
    {
        $this->stockService = Magestore_Coresuccess_Model_Service::stockService();
        $this->warehouseService = Magestore_Coresuccess_Model_Service::warehouseService();
    }
    
    /**
     * Filter product collection by current stock_id
     * 
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return Magestore_Inventorysuccess_Model_Service_Catalog_ProductLimitationService
     */
    public function filterProductByCurrentStock($collection)
    {
        if($collection->getFlag(self::FILTER_BY_STOCK_ID_FLAG)) {
            return $this;
        }
        /*
        if(!$this->stockService->isLinkWarehouseToStore()) {
            return $this;
        }
        */
        /* get current stock Id */
        $stockId = $this->stockService->getStockId();
        if($stockId == Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID) {
            return $this;
        }
        
        $conditions = array(
            'warehouse_product_filter.product_id = e.entity_id',
            'warehouse_product_filter.' . Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID . "=$stockId",
        );
        $collection->getSelect()->join(
            array('warehouse_product_filter' => $collection->getTable('inventorysuccess/warehouse_product')),
            join(' AND ', $conditions),
            array()
        ); 
        $collection->setFlag(self::FILTER_BY_STOCK_ID_FLAG, true);
        
        return $this;
    }
}