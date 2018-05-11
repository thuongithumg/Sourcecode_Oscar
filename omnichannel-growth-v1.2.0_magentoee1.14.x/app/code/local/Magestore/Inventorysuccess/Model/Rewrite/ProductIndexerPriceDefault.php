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

class Magestore_Inventorysuccess_Model_Rewrite_ProductIndexerPriceDefault
    extends Mage_Catalog_Model_Resource_Product_Indexer_Price_Default
{


    /**
     * Prepare products default final price in temporary index table
     *
     * @param int|array $entityIds  the entity ids limitation
     * @return Mage_Catalog_Model_Resource_Product_Indexer_Price_Default
     */
    protected function _prepareFinalPriceData($entityIds = null)
    {
        $this->_prepareDefaultFinalPriceTable();

        $write  = $this->_getWriteAdapter();
        $select = $write->select()
            ->from(array('e' => $this->getTable('catalog/product')), array('entity_id'))
            ->join(
                array('cg' => $this->getTable('customer/customer_group')),
                '',
                array('customer_group_id'))
            ->join(
                array('cw' => $this->getTable('core/website')),
                '',
                array('website_id'))
            ->join(
                array('cwd' => $this->_getWebsiteDateTable()),
                'cw.website_id = cwd.website_id',
                array())
            ->join(
                array('csg' => $this->getTable('core/store_group')),
                'csg.website_id = cw.website_id AND cw.default_group_id = csg.group_id',
                array())
            ->join(
                array('cs' => $this->getTable('core/store')),
                'csg.default_store_id = cs.store_id AND cs.store_id != 0',
                array())
            ->join(
                array('pw' => $this->getTable('catalog/product_website')),
                'pw.product_id = e.entity_id AND pw.website_id = cw.website_id',
                array())
            ->joinLeft(
                array('tp' => $this->_getTierPriceIndexTable()),
                'tp.entity_id = e.entity_id AND tp.website_id = cw.website_id'
                . ' AND tp.customer_group_id = cg.customer_group_id',
                array())
            ->joinLeft(
                array('gp' => $this->_getGroupPriceIndexTable()),
                'gp.entity_id = e.entity_id AND gp.website_id = cw.website_id'
                . ' AND gp.customer_group_id = cg.customer_group_id',
                array())
            ->where('e.type_id = ?', $this->getTypeId());

        // add enable products limitation
        $statusCond = $write->quoteInto('=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', 'e.entity_id', 'cs.store_id', $statusCond, true);
        if (Mage::helper('core')->isModuleEnabled('Mage_Tax')) {
            $taxClassId = $this->_addAttributeToSelect($select, 'tax_class_id', 'e.entity_id', 'cs.store_id');
        } else {
            $taxClassId = new Zend_Db_Expr('0');
        }
        $select->columns(array('tax_class_id' => $taxClassId));

        $price          = $this->_addAttributeToSelect($select, 'price', 'e.entity_id', 'cs.store_id');
        $specialPrice   = $this->_addAttributeToSelect($select, 'special_price', 'e.entity_id', 'cs.store_id');
        $specialFrom    = $this->_addAttributeToSelect($select, 'special_from_date', 'e.entity_id', 'cs.store_id');
        $specialTo      = $this->_addAttributeToSelect($select, 'special_to_date', 'e.entity_id', 'cs.store_id');
        $currentDate    = $write->getDatePartSql('cwd.website_date');
        $groupPrice     = $write->getCheckSql('gp.price IS NULL', "{$price}", 'gp.price');

        $specialFromDate    = $write->getDatePartSql($specialFrom);
        $specialToDate      = $write->getDatePartSql($specialTo);

        $specialFromUse     = $write->getCheckSql("{$specialFromDate} <= {$currentDate}", '1', '0');
        $specialToUse       = $write->getCheckSql("{$specialToDate} >= {$currentDate}", '1', '0');
        $specialFromHas     = $write->getCheckSql("{$specialFrom} IS NULL", '1', "{$specialFromUse}");
        $specialToHas       = $write->getCheckSql("{$specialTo} IS NULL", '1', "{$specialToUse}");
        $finalPrice         = $write->getCheckSql("{$specialFromHas} > 0 AND {$specialToHas} > 0"
            . " AND {$specialPrice} < {$price}", $specialPrice, $price);
        $finalPrice         = $write->getCheckSql("{$groupPrice} < {$finalPrice}", $groupPrice, $finalPrice);

        $select->columns(array(
            'orig_price'       => $price,
            'price'            => $finalPrice,
            'min_price'        => $finalPrice,
            'max_price'        => $finalPrice,
            'tier_price'       => new Zend_Db_Expr('tp.min_price'),
            'base_tier'        => new Zend_Db_Expr('tp.min_price'),
            'group_price'      => new Zend_Db_Expr('gp.price'),
            'base_group_price' => new Zend_Db_Expr('gp.price'),
        ));

        if (!is_null($entityIds)) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }
        

        $query = $select->insertFromSelect($this->_getDefaultFinalPriceTable(), array(), false);
        $write->query($query);

        /**
         * Add possibility modify prices from external events
         */
        $select = $write->select()
            ->join(array('wd' => $this->_getWebsiteDateTable()),
                'i.website_id = wd.website_id',
                array());
        Mage::dispatchEvent('prepare_catalog_product_price_index_table', array(
            'index_table'       => array('i' => $this->_getDefaultFinalPriceTable()),
            'select'            => $select,
            'entity_id'         => 'i.entity_id',
            'customer_group_id' => 'i.customer_group_id',
            'website_id'        => 'i.website_id',
            'website_date'      => 'wd.website_date',
            'update_fields'     => array('price', 'min_price', 'max_price')
        ));

        return $this;
    }
}
