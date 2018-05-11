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
class Magestore_Inventorysuccess_Model_Mysql4_Sales_Order_Item_Collection
    extends Mage_Sales_Model_Resource_Order_Item_Collection
{
    protected function _initSelect()
    {
        parent::_initSelect();
        /*
        $this->getSelect()->joinRight(
            array('warehouse_order' => Mage::getSingleton('core/resource')->getTableName('os_warehouse_order_item')),
            'main_table.item_id = warehouse_order.item_id',
            '*'
        );
        */
        return $this;
    }

    public function getSalesReport($warehouseId = null, $day = null){
        if($warehouseId)
            $this->getSelect()->where('warehouse_id = ?', $warehouseId);
        if($day){
            //$firstDate = Mage::getSingleton('core/date')->gmtDate('Y-m-d 00:00:00', strtotime('-'.$day.' days'));
            $firstDate =  strtotime('-'.$day.' days', Mage::getSingleton('core/date')->gmtTimestamp());
            $firstDate = date('Y-m-d 00:00:00', $firstDate);
            $this->addFieldToFilter('created_at', array('gteq' => $firstDate));
        }
        $this->getSelect()->columns(array('date_without_hour' => 'date(created_at)'));
        return $this;
    }

    public function getTotalOrderItem(){
        $this->getSelect()->columns(array(
            'item_qty_by_day' => 'SUM(qty_ordered)',
            'order_by_day' => 'COUNT(item_id)',
            'revenue_by_day' => 'SUM(base_row_total_incl_tax)',
        ));
        $this->getSelect()->group(array('date(created_at)'));
        return $this;
    }
}