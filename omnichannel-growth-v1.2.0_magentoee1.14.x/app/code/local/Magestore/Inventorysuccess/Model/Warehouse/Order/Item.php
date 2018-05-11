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
 * Warehouse Producr Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Warehouse_Order_Item extends Mage_Core_Model_Abstract
{
    const WAREHOUSE_ORDER_ITEM_ID = 'warehouse_order_item_id';
    const WAREHOUSE_ID = 'warehouse_id';
    const ORDER_ID = 'order_id';
    const ITEM_ID = 'item_id';
    const PRODUCT_ID = 'product_id';
    const QTY_ORDERED = 'qty_ordered';
    const QTY_CANCELED= 'qty_canceled';
    const SUBTOTAL = 'subtotal';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    
    /**
     * 
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/warehouse_order_item');
    }    
}