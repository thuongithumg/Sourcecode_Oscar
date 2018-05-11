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

/**
 * Rule resource model class
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Magestore_Giftvoucher_Model_Mysql4_Rule extends Mage_CatalogRule_Model_Mysql4_Rule
{

    /**
     * Get active rule data based on few filters
     *
     * @param int|string $date
     * @param int $websiteId
     * @param int $customerGroupId
     * @param int $productId
     * @return array
     */
    public function getRulesFromProduct($date, $websiteId, $customerGroupId, $productId)
    {
        $adapter = $this->_getReadAdapter();
        $dateQuoted = $adapter->quote($this->formatDate($date, false));
        $joinCondsQuoted[] = 'main_table.rule_id = rp.rule_id';
        $joinCondsQuoted[] = $adapter->quoteInto('rp.website_id = ?', $websiteId);
        $joinCondsQuoted[] = $adapter->quoteInto('rp.customer_group_id = ?', $customerGroupId);
        $joinCondsQuoted[] = $adapter->quoteInto('rp.product_id = ?', $productId);
        $fromDate = $this->getIfNullSql('main_table.from_date', $dateQuoted);
        $toDate = $this->getIfNullSql('main_table.to_date', $dateQuoted);
        $select = $adapter->select()
            ->from(array('main_table' => $this->getTable('catalogrule/rule')))
            ->joinInner(
                array('rp' => $this->getTable('catalogrule/rule_product')), implode(' AND ', $joinCondsQuoted), array())
            ->where(new Zend_Db_Expr("{$dateQuoted} BETWEEN {$fromDate} AND {$toDate}"))
            ->where('main_table.is_active = ?', 1)
            ->order('main_table.sort_order');
        return $adapter->fetchAll($select);
    }

    /**
     * Check null Sql query
     *
     * @param int|string $expression
     * @param mixed $value
     * @return Zend_Db_Expr
     */
    public function getIfNullSql($expression, $value = 0)
    {
        if ($expression instanceof Zend_Db_Expr || $expression instanceof Zend_Db_Select) {
            $expression = sprintf("IFNULL((%s), %s)", $expression, $value);
        } else {
            $expression = sprintf("IFNULL(%s, %s)", $expression, $value);
        }
        return new Zend_Db_Expr($expression);
    }

}
