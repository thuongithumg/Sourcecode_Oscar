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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseorder Invoice Resource Collection Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Invoice_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchaseordersuccess/purchaseorder_invoice');
        $this->_setIdFieldName('purchase_order_invoice_id');
    }

    protected function _initSelect()
    {
        $this->getSelect()->from(array('main_table' => $this->getMainTable()))
            ->columns(array(
                'total_paid' => new \Zend_Db_Expr('main_table.grand_total_incl_tax - main_table.total_due')
            ));
        return $this;
    }

    /**
     * Add field filter to collection
     *
     * @see self::_getConditionSql for $condition
     *
     * @param   string|array $field
     * @param   null|string|array $condition
     *
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if($field == 'total_paid')
            $field = new \Zend_Db_Expr('main_table.grand_total_incl_tax - main_table.total_due');
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  Varien_Data_Collection_Db
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if($field == 'total_paid')
            $field = new \Zend_Db_Expr('main_table.grand_total_incl_tax - main_table.total_due');
        return parent::setOrder($field, $direction);
    }

    /**
     * self::setOrder() alias
     *
     * @param string $field
     * @param string $direction
     * @return Varien_Data_Collection_Db
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if($field == 'total_paid')
            $field = new \Zend_Db_Expr('main_table.grand_total_incl_tax - main_table.total_due');
        return parent::addOrder($field, $direction);
    }
}