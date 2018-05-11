<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 *   @category    Magestore
 *   @package     Magestore_Reportsuccess
 *   @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Reportsuccess Resource Collection Model
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Model_Mysql4_Salesreport_Order_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $isGrid = false;

    /**
     * @var array
     */
    protected $MAPPING_FIELD = array(
        'order_id' => 'main_table.order_id',
        'status' => 'main_table.status',
        'order_potential_sold_qty' => 'SUM(main_table.potential_sold_qty)',
        'order_realized_sold_qty' => 'SUM(main_table.realized_sold_qty)',
        'order_cogs' => 'SUM(main_table.cogs)',
        'order_profit' => 'SUM(main_table.profit)',
        'order_tax' => 'SUM(main_table.tax)',
        'order_discount' => 'SUM(main_table.realized_discount + main_table.potential_discount)',
        'order_total_sale' => 'SUM(main_table.total_sale)',
    );

    /**
     * construct
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('reportsuccess/salesreport');
    }

    /**
     * Init collection select
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(array('main_table' => $this->getMainTable()));
        return $this;
    }
//
//    /**
//     * @return mixed
//     */
//    public function getSelectCountSql()
//    {
//        $this->_renderFilters();
//        $countSelect = clone $this->getSelect();
//        $countSelect->reset(Zend_Db_Select::ORDER);
//        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
//        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
//        $countSelect->reset(Zend_Db_Select::COLUMNS);
//
//        // Count doesn't work with group by columns keep the group by
//        if (count($this->getSelect()->getPart(Zend_Db_Select::GROUP)) > 0) {
//            $countSelect->reset(Zend_Db_Select::GROUP);
//            $countSelect->distinct(true);
//            $group = $this->getSelect()->getPart(Zend_Db_Select::GROUP);
//            $countSelect->columns("COUNT(DISTINCT " . implode(", ", $group) . ")");
//        } else {
//            $countSelect->columns('COUNT(*)');
//        }
//        return $countSelect;
//    }


//    /**
//     * Add field filter to collection
//     *
//     * @param   string|array $field
//     * @param   null|string|array $condition
//     * @return  Mage_Eav_Model_Entity_Collection_Abstract
//     */
//    public function addFieldToFilter($field, $condition = null)
//    {
//        foreach ($this->MAPPING_FIELD as $alias => $realField) {
//            if ($alias == $field) {
//                $field = new Zend_Db_Expr($this->MAPPING_FIELD[$alias]);
//            }
//        }
//        return parent::addFieldToFilter($field, $condition);
//    }

    /**
     * Get collection size
     *
     * @return int
     */
    public function getSize()
    {
        if (is_null($this->_totalRecords)) {
            $sql = $this->getSelect();
            $this->_totalRecords = count($this->getConnection()->fetchAll($sql, $this->_bindParams));
        }
        return intval($this->_totalRecords);
    }

    public function setIsGrid($value)
    {
        $this->isGrid = $value;
        return $this;
    }

    public function getIsGrid()
    {
        return $this->isGrid;
    }
}