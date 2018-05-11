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
 * Stocktaking Resource Collection Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_Stocktaking_Product_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Magestore_Inventorysuccess_Model_Mysql4_Stocktaking_Product_Collection constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/stocktaking_product');
    }

    /**
     * get stocktaking products
     *
     * @return void
     */
    public function getStocktakingProducts($stocktakingId){
        $collection = $this->addFieldToFilter('stocktaking_id', $stocktakingId)
            ->setOrder('product_id', 'DESC');
        return $collection;
    }

    /**
     * get stocktaking different products
     *
     * @return void
     */
    public function getStocktakingDifferentProducts($stocktakingId){
        $collection = $this->addFieldToFilter('stocktaking_id', $stocktakingId);
        $collection->getSelect()->columns(array(
            'different_qty' => 'ABS(main_table.old_qty - main_table.stocktaking_qty)'))
            ->where('ABS(main_table.old_qty - main_table.stocktaking_qty) != 0');
        return $collection;
    }

    /**
     * @param $stocktakingId
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getProductsByStocktakingId($stocktakingId){
        $collection = $this->addFieldToFilter('stocktaking_id', $stocktakingId)
            ->setOrder('product_id', 'DESC');
        return $collection;
    }

    /**
     * @param $stocktakingId
     * @return mixed
     */
    public function getProductsToStocktake($stocktakingId){
        $stocktaking = Mage::getModel('inventorysuccess/stocktaking')->load($stocktakingId);
        $warehouseId = $stocktaking->getWarehouseId();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter(
                'type_id',
                array('nin' => array('configurable', 'bundle', 'grouped', 'downloadable', 'virtual'))
            );
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id='.Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID,
                'left');
        }
        if (in_array($stocktaking->getStatus(),
            array(Magestore_Inventorysuccess_Model_Stocktaking::STATUS_COMPLETED,
                Magestore_Inventorysuccess_Model_Stocktaking::STATUS_VERIFIED,
                Magestore_Inventorysuccess_Model_Stocktaking::STATUS_CANCELED
            ))) {
            $collection->joinField('old_qty', 'inventorysuccess/stocktaking_product', 'old_qty',
                'product_id=entity_id', '{{table}}.stocktaking_id=' . $stocktakingId, 'right');
        } else {
            $collection->joinField('old_qty', 'inventorysuccess/warehouse_product', 'total_qty',
                'product_id=entity_id', '{{table}}.'.Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID.'=' . $warehouseId, 'right');
        }
        $collection->joinField('stocktaking_qty', 'inventorysuccess/stocktaking_product', 'stocktaking_qty',
            'product_id=entity_id', '{{table}}.stocktaking_id=' . $stocktakingId, 'left');
        $collection->joinField('stocktaking_reason', 'inventorysuccess/stocktaking_product', 'stocktaking_reason',
            'product_id=entity_id', '{{table}}.stocktaking_id=' . $stocktakingId, 'left');

        return $collection;
    }

    /**
     * get different product list
     *
     * @return string
     */
    public function getDifferentProducts($stocktakingId) {
        $collection = $this->getProductsToStocktake($stocktakingId);
        $collection->getSelect()
            ->where('ABS(at_old_qty.old_qty - at_stocktaking_qty.stocktaking_qty) != 0')
        ;
        return $collection;
    }
}