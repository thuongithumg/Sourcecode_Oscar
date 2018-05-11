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
 * Purchaseorder Item Transferred Resource Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Item_Transferred extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Purchase order item transferred table
     */
    const TABLE_NAME = 'os_purchase_order_item_transferred';

    public function _construct()
    {
        $this->_init('purchaseordersuccess/purchaseorder_item_transferred', 'purchase_order_item_transferred_id');
    }
}