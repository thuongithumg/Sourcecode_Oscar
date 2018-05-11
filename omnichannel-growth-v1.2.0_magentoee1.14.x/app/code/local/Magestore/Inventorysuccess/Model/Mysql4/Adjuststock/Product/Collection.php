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
 * Adjuststock Resource Collection Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_Adjuststock_Product_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Magestore_Inventorysuccess_Model_Mysql4_Adjuststock_Product_Collection constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/adjuststock_product');
    }

    /**
     * @param $adjustStockId
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getProductsByAdjustStockId($adjustStockId){
        $collection = $this->addFieldToFilter('adjuststock_id', $adjustStockId)
//            ->setOrder('product_id', 'DESC')
        ;
        return $collection;
    }

    /**
     * @param $adjustStockId
     * @return mixed
     */
    public function getProductsToAdjust($adjustStockId){
        $adjuststock = Mage::getModel('inventorysuccess/adjuststock')->load($adjustStockId);
        $warehouseId = $adjuststock->getWarehouseId();
        $collection = Mage::getModel('catalog/product')->getCollection()
        ->addAttributeToSelect('name')
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
        if (in_array($adjuststock->getStatus(),
            array(Magestore_Inventorysuccess_Model_Adjuststock::STATUS_COMPLETED,
                Magestore_Inventorysuccess_Model_Adjuststock::STATUS_CANCELED))) {
            $collection->joinField('old_qty', 'inventorysuccess/adjuststock_product', 'old_qty',
                                   'product_id=entity_id', '{{table}}.adjuststock_id=' . $adjustStockId, 'right');
        } else {
            $collection->joinField('old_qty', 'inventorysuccess/warehouse_product', 'total_qty',
                                    'product_id=entity_id', 
                                    '{{table}}.'.Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID.'=' . $warehouseId,
                                    'right'
            );
        }
        $collection->joinField('adjust_qty', 'inventorysuccess/adjuststock_product', 'adjust_qty',
                                    'product_id=entity_id', '{{table}}.adjuststock_id=' . $adjustStockId, 'left');
        $collection->joinField('change_qty', 'inventorysuccess/adjuststock_product', 'change_qty',
                                    'product_id=entity_id', '{{table}}.adjuststock_id=' . $adjustStockId, 'left');
        return $collection;
    }

    /**
     * get products in warehouse
     *
     * @param $adjustStockId
     */
    public function getProductInWarehouse($adjustStockId){
        $adjuststock = Mage::getModel('inventorysuccess/adjuststock')->load($adjustStockId);
        $warehouseId = $adjuststock->getWarehouseId();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('type_id', array('nin' => array('configurable', 'bundle', 'grouped')))
        ;
        $collection->joinField('qty', 'inventorysuccess/warehouse_product', 'total_qty',
                                'product_id=entity_id', 
                                '{{table}}.'.Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID.'=' . $warehouseId, 
                                'left'
        );
    }

    /**
     * get product select for filter
     *
     * @param $adjustStockId
     * @return mixed
     */
    public function getProductSelect($adjustStockId, $storeId)
    {
        $adjuststockProducts = $this->getProductsByAdjustStockId($adjustStockId);
        $productIds = array();
        foreach ($adjuststockProducts as $adjuststockProduct)
            $productIds[] = $adjuststockProduct->getProductId();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addFieldToFilter('entity_id', array('in' => $productIds));
        $collection->joinField('old_qty', 'inventorysuccess/adjuststock_product', 'old_qty', 'product_id=entity_id',
                                '{{table}}.adjuststock_id=' . $adjustStockId, 'left');
        $collection->joinField('adjust_qty', 'inventorysuccess/adjuststock_product', 'adjust_qty',
                                'product_id=entity_id', '{{table}}.adjuststock_id=' . $adjustStockId, 'left');
        $collection->joinField('change_qty', 'inventorysuccess/adjuststock_product', 'change_qty',
                                'product_id=entity_id', '{{table}}.adjuststock_id=' . $adjustStockId, 'left');
        if ($storeId)
            $collection->addStoreFilter($storeId);
        $collection->addOrder('entity_id', 'ASC');

        return $collection;
    }

}