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
 * Adjuststock Resource Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_LowStockNotification_Rule extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('inventorysuccess/lowStockNotification_rule', 'rule_id');
    }

    /**
     * get available rules
     * @return array
     */
    public function getAvailableRules()
    {
        /** @var Zend_Date $dateTime */
        $dateTime = new Zend_Date();
        $format = 'Y-m-d H:i:s';
        $date = $dateTime->getTimestamp();
        $now = date($format, $date);
        
        $status = Magestore_Inventorysuccess_Model_LowStockNotification_Rule::STATUS_ACTIVE;
        $typeLowStockThresholdByProductQty = Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_LOWSTOCK_THRESHOLD_PRODUCT_QTY;
        $typeLowStockThresholdBySaleDays = Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_LOWSTOCK_THRESHOLD_SALE_DAY;
        $select = $this->_getConnection('read')->select()->from(array('main_table' => $this->getMainTable()));
        $orWhereConditions = array(
            "(main_table.from_date <= '{$now}' and main_table.to_date >= '{$now}')",
            "(main_table.from_date <= '{$now}' and main_table.to_date is null)",
            "(main_table.from_date is null and main_table.to_date >= '{$now}')",
            "(main_table.from_date is null and main_table.to_date is null)"
        );
        $orWhereConditions1 = array(
            "(main_table.lowstock_threshold_type = '{$typeLowStockThresholdByProductQty}' and main_table.lowstock_threshold_qty > 0)",
            "(main_table.lowstock_threshold_type = '{$typeLowStockThresholdBySaleDays}' and main_table.lowstock_threshold > 0 and main_table.sales_period > 0)"
        );
        $andWhereConditions = array(
            "main_table.status = '{$status}'",
            "main_table.next_time_action <= '{$now}'"
        );
        $orWhereCondition = implode(' OR ', $orWhereConditions);
        $orWhereCondition1 = implode(' OR ', $orWhereConditions1);
        $andWhereCondition = implode(' AND ', $andWhereConditions);
        $select->where('(' . $orWhereCondition . ') AND (' . $orWhereCondition1 . ') AND ' . $andWhereCondition);
        $result = $this->_getConnection('read')->fetchAll($select);

        return $result;
    }
}