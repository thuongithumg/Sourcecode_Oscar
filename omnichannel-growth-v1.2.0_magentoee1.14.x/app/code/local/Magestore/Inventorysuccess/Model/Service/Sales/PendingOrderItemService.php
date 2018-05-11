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
class Magestore_Inventorysuccess_Model_Service_Sales_PendingOrderItemService
{
    /**
     * @var string
     */
    protected $_pendingQty;

    /**
     * Magestore_Inventorysuccess_Model_Service_Sales_PendingOrderItemService constructor.
     */
    public function __construct()
    {
        $cr = Mage::getSingleton('core/resource');
        $order_item_table = $cr->getTableName('sales/order_item');
        $on_hold_qty_sql = " (select (qty_ordered-qty_shipped-qty_refunded-qty_canceled) from {$order_item_table} where item_id = main_table.parent_item_id) ";
        $on_hold_only_simple = "main_table.qty_ordered - main_table.qty_shipped - main_table.qty_refunded - main_table.qty_canceled";
        $foreignField = "(select product_type from {$order_item_table} where item_id = main_table.parent_item_id)";
        $configuration_code = Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;
        $this->_pendingQty = '(IF(('.$foreignField.' = "'.$configuration_code.'" ), IF('.$on_hold_qty_sql.' > 0, '.$on_hold_qty_sql.', 0), IF('.$on_hold_only_simple.' > 0, '.$on_hold_only_simple.', 0) ) )';
    }

    /**
     * 
     * @param int $productId
     * @return collection
     */
    public function getCollection($productId = null)
    {
        /* Start SQL : select all simple products and group by product_id , if exist parent_item_id -> calculate in configuration products */
        $collection = Mage::getModel('sales/order_item')->getCollection()
            ->addFieldToFilter('product_id',$productId);
        $collection->getSelect()->columns(array(
            'pending_qty' => new Zend_Db_Expr($this->_pendingQty),
        ))
            ->where("{$this->_pendingQty} > 0")
            ->where('product_type = ?', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE);
        $collection->getSelect()->joinLeft(
            array('order' => Mage::getSingleton('core/resource')->getTableName('sales/order')),
            'main_table.order_id = order.entity_id',
            array('increment_id'=> 'order.increment_id'));
        return $collection;
    }

    /**
     * @param $collection
     * @param $columnId
     * @param $value
     * @return $collection
     */
    public function _filterQtyCallback($collection,$columnId,$value){
        if(isset($value['from'])){
            $collection->getSelect()->where("{$this->_pendingQty} >= ?", $value['from']);
        }
        if(isset($value['to'])){
            $collection->getSelect()->where("{$this->_pendingQty} <= ?", $value['to']);
        }
        return $collection;
    }
}