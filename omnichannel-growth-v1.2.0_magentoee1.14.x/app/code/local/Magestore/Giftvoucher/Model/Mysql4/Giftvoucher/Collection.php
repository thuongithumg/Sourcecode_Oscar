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
 * Giftvoucher resource collection
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Magestore_Giftvoucher_Model_Mysql4_Giftvoucher_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected $_isGroupSql = false;

    public function _construct()
    {
        parent::_construct();
        $this->_init('giftvoucher/giftvoucher');
    }

    /**
     * @return $this
     */
    public function joinHistory()
    {
        if ($this->hasFlag('join_history') && $this->getFlag('join_history')) {
            return $this;
        }
        $this->setFlag('join_history', true);
        $this->_isGroupSql = true;
        // $this->getSelect()->joinLeft(
        $this->getSelect()->group('main_table.giftvoucher_id')->joinLeft(
            array('history' => $this->getTable('giftvoucher/history')),
            'main_table.giftvoucher_id = history.giftvoucher_id', array(
                'history_amount' => 'amount',
                'history_currency' => 'currency',
                'created_at',
                'extra_content',
                'order_increment_id'
            )
        )->where('history.amount > ?', 0 or 'history.action = ?', Magestore_Giftvoucher_Model_Actions::ACTIONS_CREATE);
        return $this;
    }

    /**
     * @return $this
     */
    public function getAvailable()
    {
        $this->addFieldToFilter('main_table.status', array(
            'neq' => Magestore_Giftvoucher_Model_Status::STATUS_DELETED
        ));
        return $this;
    }

    /**
     * @param $itemId
     * @return $this
     */
    public function addItemFilter($itemId)
    {
        if ($this->hasFlag('add_item_filer') && $this->getFlag('add_item_filer')) {
            return $this;
        }
        $this->setFlag('add_item_filer', true);

        $this->getSelect()->joinLeft(
                array('history' => $this->getTable('giftvoucher/history')), 
                'main_table.giftvoucher_id = history.giftvoucher_id', array('order_item_id')
            )->where('history.order_item_id = ?', $itemId)
            ->where('history.action = ?', Magestore_Giftvoucher_Model_Actions::ACTIONS_CREATE);

        return $this;
    }

    /**
     * @param $dayBefore
     * @return $this
     */
    public function addExpireAfterDaysFilter($dayBefore)
    {
        $date = Mage::getModel('core/date')->gmtDate();
        $zendDate = new Zend_Date($date);
        $dayAfter = $zendDate->addDay($dayBefore)->toString('YYYY-MM-dd');
        $this->getSelect()->where('date(expired_at) = ?', $dayAfter);
        return $this;
    }

    /**
     * @return Varien_Db_Select
     * @throws Zend_Db_Select_Exception
     */
    public function getSelectCountSql()
    {
        if ($this->_isGroupSql) {
            $this->_renderFilters();
            $countSelect = clone $this->getSelect();
            $countSelect->reset(Zend_Db_Select::ORDER);
            $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
            $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
            $countSelect->reset(Zend_Db_Select::COLUMNS);
            if (count($this->getSelect()->getPart(Zend_Db_Select::GROUP)) > 0) {
                $countSelect->reset(Zend_Db_Select::GROUP);
                $countSelect->distinct(true);
                $group = $this->getSelect()->getPart(Zend_Db_Select::GROUP);
                $countSelect->columns("COUNT(DISTINCT " . implode(", ", $group) . ")");
            } else {
                $countSelect->columns('COUNT(*)');
            }
            return $countSelect;
        }
        return parent::getSelectCountSql();
    }

}
