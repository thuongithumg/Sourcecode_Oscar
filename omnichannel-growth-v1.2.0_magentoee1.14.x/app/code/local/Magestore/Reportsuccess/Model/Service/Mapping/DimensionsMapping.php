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
 * ReportSuccess Resource Model
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */

class Magestore_Reportsuccess_Model_Service_Mapping_DimensionsMapping
{

    public function dimensionsMapping($collection,$type){

        if($type == Magestore_Reportsuccess_Helper_Data::salesreportGridJsObjectdimentions){
            return $this->mappingSalesCollection($collection,$type);
        }
    }

    public function mappingSalesCollection($collection,$type){
        $check_group = 0;
        $removecolumn = Mage::getModel('reportsuccess/editcolumns')->getCollection()->addFieldToFilter('grid',$type)
            ->getFirstItem();
        if($removecolumn->getId()){
            $columns = $removecolumn->getValue();
            $columns = explode(',',$columns);
            foreach($columns as $value){
                $column = explode(':',$value);
                if($column[1] == 1){
                    $check_group = 1;
                    if($column[0] == 'customer'){

                    }else{
                        $collection->getSelect()->group($column[0]);
                    }
                }
            }
        }
        if($check_group == 1){
            $collection->getSelect()->columns(array(
                'order_id' => ('GROUP_CONCAT(DISTINCT main_table.order_id SEPARATOR ",")'),
                'order_ids_view' => ('GROUP_CONCAT(DISTINCT main_table.id SEPARATOR ",")'),
                'sku' => ('GROUP_CONCAT(DISTINCT sku_table.sku SEPARATOR " , ")'),
                'name' => ('GROUP_CONCAT(DISTINCT name_table.value SEPARATOR " , ")'),

                'customer_email' => ('GROUP_CONCAT(DISTINCT main_table.customer_email SEPARATOR ",")'),
                'increment_id' => ('GROUP_CONCAT(DISTINCT main_table.increment_id SEPARATOR ",")'),
                'realized_cogs' => new Zend_Db_Expr('SUM(main_table.realized_cogs)'),
                'potential_cogs' => new Zend_Db_Expr('SUM(main_table.potential_cogs)'),
                'cogs' => new Zend_Db_Expr('SUM(main_table.cogs)'),
                'realized_profit' => new Zend_Db_Expr('SUM(main_table.realized_profit)'),
                'potential_profit' => new Zend_Db_Expr('SUM(main_table.potential_profit)'),
                'profit' => new Zend_Db_Expr('SUM(main_table.profit)'),
                'realized_tax' => new Zend_Db_Expr('SUM(main_table.realized_tax)'),
                'potential_tax' => new Zend_Db_Expr('SUM(main_table.potential_tax)'),
                'tax' => new Zend_Db_Expr('SUM(main_table.tax)'),
                'unit_discount' => new Zend_Db_Expr('AVG(main_table.unit_discount)'),
                'unit_tax' => new Zend_Db_Expr('AVG(main_table.unit_tax)'),
                'unit_cost' => new Zend_Db_Expr('AVG(main_table.unit_cost)'),
                'unit_price' => new Zend_Db_Expr('AVG(main_table.unit_price)'),
                'unit_profit' => new Zend_Db_Expr('AVG(main_table.unit_profit)'),
                'realized_discount' => new Zend_Db_Expr('SUM(main_table.realized_discount)'),
                'potential_discount' => new Zend_Db_Expr('SUM(main_table.potential_discount)'),
                'total_sale' => new Zend_Db_Expr('SUM(main_table.total_sale)'),
                'realized_sold_qty' => new Zend_Db_Expr('SUM(main_table.realized_sold_qty)'),
                'potential_sold_qty' => new Zend_Db_Expr('SUM(main_table.potential_sold_qty)'),
            ));
        }
        return $collection;
    }
}