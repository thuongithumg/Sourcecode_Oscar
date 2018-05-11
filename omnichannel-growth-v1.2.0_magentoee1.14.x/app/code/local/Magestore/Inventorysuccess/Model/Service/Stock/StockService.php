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
class Magestore_Inventorysuccess_Model_Service_Stock_StockService
{

    const LINK_WAREHOUSE_STORE_CONFIG = 'inventorysuccess/stock_control/link_warehouse_store_view';

    /**
     *
     * @return int
     */
    public function getGlobalStockId()
    {
        return Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID;
    }

    /**
     *
     * @return int
     */
    public function getStockId()
    {
        if (Mage::getSingleton('admin/session')->getUser()) {
            /* always return DEFAULT_STOCK_ID in backend */
            return Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID;
        }

        $stock = new Varien_Object(array('stock_id' => $this->getStockIdFromCurrentStore()));

        /* allow to switch stock_id from other extensions */
        Mage::dispatchEvent('inventorysuccess_get_stock_id', array('stock' => $stock));

        return $stock->getStockId();
    }

    /**
     *
     * @param Mage_Sales_Model_Order $order
     * @return int
     */
    public function getStockIdFromSalesOrder($order)
    {
        $stock = new Varien_Object(array('stock_id' => Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID));

        /* allow to switch stock_id from other extensions */
        Mage::dispatchEvent('inventorysuccess_get_stock_id_from_sales_order', array(
            'stock' => $stock,
            'order' => $order
        ));

        return $stock->getStockId();
    }

    /**
     *
     * @param int $storeId
     * @return int
     */
    public function getStockIdFromCurrentStore($storeId = null)
    {
        if (!$this->isLinkWarehouseToStore()) {
            return Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID;
        }
        $storeId = $storeId ? $storeId : Mage::app()->getStore()->getId();
        $warehouse = Magestore_Coresuccess_Model_Service::warehouseService()->getWarehouseFromStoreId($storeId);

        return $warehouse->getWarehouseId() ? $warehouse->getWarehouseId() : Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID;
    }

    /**
     *
     * @return bool
     */
    public function isLinkWarehouseToStore()
    {
        return Mage::getStoreConfig(self::LINK_WAREHOUSE_STORE_CONFIG);
    }

}