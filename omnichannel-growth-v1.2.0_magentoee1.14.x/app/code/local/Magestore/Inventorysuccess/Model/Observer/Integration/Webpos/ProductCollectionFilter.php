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
class Magestore_Inventorysuccess_Model_Observer_Integration_Webpos_ProductCollectionFilter
{
    /**
     * 
     * @param type $observer
     */
    public function execute($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        $stockService = Magestore_Coresuccess_Model_Service::stockService();
        $stockId = $stockService->getStockId();
        if($stockId != Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID) {
            $conditions = array(
                'warehouse_product.product_id = e.entity_id',
                'warehouse_product.' . Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID . "=$stockId",
            );
            $collection->getSelect()->join(
                array('warehouse_product' => $collection->getTable('inventorysuccess/warehouse_product')),
                join(' AND ', $conditions),
                array()
            );
        }
    }
}