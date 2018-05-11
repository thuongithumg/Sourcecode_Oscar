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
 * Inventorysuccess Resource Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_Sales_Order extends Magestore_Coresuccess_Model_Mysql4_Base
{
    /**
     * 
     * @param int $productId
     * @return array
     */
    public function getPendingShipOrderIds($productId = null)
    {
        $orderIds = array();
        $connection = $this->_getConnection('read');
        /* Get order items */    
        $select = $connection->select()->from(array('main_table' => $this->getTable('sales/order_item')), array(
                                            'order_id',
                                            'product_id',
                                            'qty_ordered',
                                            'qty_canceled',
                                            'qty_shipped',
                                            'qty_refunded',
                                        ))
                                        ->where(new Zend_Db_Expr("IF(qty_ordered-qty_shipped-qty_refunded-qty_canceled > '0', qty_ordered-qty_shipped-qty_refunded-qty_canceled, 0) > 0"))
                                        ;
        if($productId) {
            $select->where('product_id = ?', $productId);
        }
        $query = $connection->query($select);
        while ($row = $query->fetch()) {
            $orderIds[$row['order_id']] = $row['order_id'];
        }     
        return $orderIds;
    }
}