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
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

if (Mage::helper('core')->isModuleEnabled('Innoexts_StorePricing') && class_exists('Innoexts_StorePricing_Model_Mysql4_Catalog_Product_Indexer_Price_Default')) {
    abstract class Magestore_Giftvoucher_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default extends Innoexts_StorePricing_Model_Mysql4_Catalog_Product_Indexer_Price_Default
    {

    }
} elseif (Mage::helper('core')->isModuleEnabled('Innoexts_CurrencyPricing') && class_exists('Innoexts_CurrencyPricing_Model_Mysql4_Bundle_Indexer_Price')) {
    abstract class Magestore_Giftvoucher_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default extends Innoexts_CurrencyPricing_Model_Mysql4_Bundle_Indexer_Price
    {

    }
} else {
    abstract class Magestore_Giftvoucher_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
    {

    }
}

/**
 * Giftvoucher Product Price Indexer Resource Model
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Magestore_Giftvoucher_Model_Mysql4_Indexer_Price
    extends Magestore_Giftvoucher_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
{

    /**
     * Prepare products default final price in temporary index table
     *
     * @param int|array $entityIds the entity ids limitation
     * @return Magestore_Giftvoucher_Model_Mysql4_Indexer_Price
     */
    protected function _prepareFinalPriceData($entityIds = null)
    {
        $this->_prepareDefaultFinalPriceTable();

        $write = $this->_getWriteAdapter();
        $select = $write->select()
            ->from(array('e' => $this->getTable('catalog/product')), array('entity_id'))
            ->join(
                array('cg' => $this->getTable('customer/customer_group')), '', array('customer_group_id'))
            ->join(
                array('cw' => $this->getTable('core/website')), '', array('website_id'))
            ->join(
                array('cwd' => $this->_getWebsiteDateTable()), 'cw.website_id = cwd.website_id', array())
            ->join(
                array('csg' => $this->getTable('core/store_group')),
                'csg.website_id = cw.website_id AND cw.default_group_id = csg.group_id', array())
            ->join(
                array('cs' => $this->getTable('core/store')),
                'csg.default_store_id = cs.store_id AND cs.store_id != 0', array())
            ->join(
                array('pw' => $this->getTable('catalog/product_website')),
                'pw.product_id = e.entity_id AND pw.website_id = cw.website_id', array())
            ->joinLeft(
                array('tp' => $this->_getTierPriceIndexTable()),
                'tp.entity_id = e.entity_id AND tp.website_id = cw.website_id'
                . ' AND tp.customer_group_id = cg.customer_group_id', array())
            ->where('e.type_id=?', $this->getTypeId());

        // add enable products limitation
        $statusCond = $write->quoteInto('=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', 'e.entity_id', 'cs.store_id', $statusCond, true);
        if (Mage::helper('core')->isModuleEnabled('Mage_Tax')) {
            $taxClassId = $this->_addAttributeToSelect($select, 'tax_class_id', 'e.entity_id', 'cs.store_id');
        } else {
            $taxClassId = new Zend_Db_Expr('0');
        }
        $select->columns(array('tax_class_id' => $taxClassId));

        $giftAmount = $this->_addAttributeToSelect($select, 'gift_price', 'e.entity_id', 'cs.store_id');
        $defaultAmount = floatval(Mage::getStoreConfig('giftvoucher/interface/amount'));
        $amount = "CAST({$giftAmount->__toString()} AS UNSIGNED)";
        $price = new Zend_Db_Expr("IF($amount > 0, $amount, ABS($defaultAmount))");

        if (version_compare(Mage::getVersion(), '1.7.0.0', '>=')) {
            // Group Price - From Magento 1.7.0.0, Enterprise 1.12.0.0
            $select->joinLeft(
                array('gp' => $this->_getGroupPriceIndexTable()),
                'gp.entity_id = e.entity_id AND gp.website_id = cw.website_id'
                . ' AND gp.customer_group_id = cg.customer_group_id', array());
            $select->columns(array(
                'orig_price' => $price,
                'price' => $price,
                'min_price' => $price,
                'max_price' => $price,
                'tier_price' => new Zend_Db_Expr('tp.min_price'),
                'base_tier' => new Zend_Db_Expr('tp.min_price'),
                'group_price' => new Zend_Db_Expr('gp.price'),
                'base_group_price' => new Zend_Db_Expr('gp.price'),
            ));
        } else {
            $select->columns(array(
                'orig_price' => $price,
                'price' => $price,
                'min_price' => $price,
                'max_price' => $price,
                'tier_price' => new Zend_Db_Expr('tp.min_price'),
                'base_tier' => new Zend_Db_Expr('tp.min_price'),
            ));
        }

        if (!is_null($entityIds)) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }

        /**
         * Add additional external limitation
         */
        Mage::dispatchEvent('prepare_catalog_product_index_select', array(
            'select' => $select,
            'entity_field' => new Zend_Db_Expr('e.entity_id'),
            'website_field' => new Zend_Db_Expr('cw.website_id'),
            'store_field' => new Zend_Db_Expr('cs.store_id')
        ));

        if (version_compare(Mage::getVersion(), '1.6.0.0', '>=')) { // From Magento 1.6.0.0, Enterprise 1.11.0.0
            $query = $select->insertFromSelect($this->_getDefaultFinalPriceTable(), array(), false);
        } else {
            $query = $select->insertFromSelect($this->_getDefaultFinalPriceTable());
        }
        $write->query($query);

        /**
         * Add possibility modify prices from external events
         */
        $select = $write->select()
            ->join(array('wd' => $this->_getWebsiteDateTable()), 'i.website_id = wd.website_id', array());
        Mage::dispatchEvent('prepare_catalog_product_price_index_table', array(
            'index_table' => array('i' => $this->_getDefaultFinalPriceTable()),
            'select' => $select,
            'entity_id' => 'i.entity_id',
            'customer_group_id' => 'i.customer_group_id',
            'website_id' => 'i.website_id',
            'website_date' => version_compare(Mage::getVersion(), '1.6.0.0', '>=') ?
                'wd.website_date' : 'wd.date', // From Magento 1.6.0.0, Enterprise 1.11.0.0
            'update_fields' => array('price', 'min_price', 'max_price')
        ));

        return $this;
    }

}
