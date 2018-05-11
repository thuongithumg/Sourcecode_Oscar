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
 * Purchaseorder Item Returned Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Purchaseorder_Item_Returned extends Mage_Core_Model_Abstract
{
    /**#@+
     * Constants defined for keys of  data array
     */

    const PURCHASE_ORDER_ITEM_RETURNED_ID = 'purchase_order_item_returned_id';

    const PURCHASE_ORDER_ITEM_ID = 'purchase_order_item_id';

    const QTY_RETURNED = 'qty_returned';

    const RETURNED_AT = 'returned_at';

    const CREATED_AT = 'created_at';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'purchaseordersuccess_purchaseorder_item_returned';

    /**
     * Initialization
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchaseordersuccess/purchaseorder_item_returned');
    }

    /**
     * Get purchase order item returned id
     *
     * @return int
     */
    public function getPurchaseOrderItemReturnedId()
    {
        return $this->_getData(self::PURCHASE_ORDER_ITEM_RETURNED_ID);
    }

    /**
     * Set purchase order item returned id
     *
     * @param int $purchaseOrderItemReturnedId
     * @return $this
     */
    public function setPurchaseOrderItemReturnedId($purchaseOrderItemReturnedId){
        return $this->setData(self::PURCHASE_ORDER_ITEM_RETURNED_ID, $purchaseOrderItemReturnedId);
    }

    /**
     * Get purchase order item id
     *
     * @return int
     */
    public function getPurchaseOrderItemId(){
        return $this->_getData(self::PURCHASE_ORDER_ITEM_ID);
    }

    /**
     * Set purchase order item id
     *
     * @param int $purchaseOrderItemId
     * @return $this
     */
    public function setPurchaseOrderItemId($purchaseOrderItemId){
        return $this->setData(self::PURCHASE_ORDER_ITEM_ID, $purchaseOrderItemId);
    }

    /**
     * Get qty returned
     *
     * @return float
     */
    public function getQtyReturned(){
        return $this->_getData(self::QTY_RETURNED);
    }

    /**
     * Set qty returned
     *
     * @param float $qtyReturned
     * @return $this
     */
    public function setQtyReturned($qtyReturned){
        return $this->setData(self::QTY_RETURNED, $qtyReturned);
    }

    /**
     * Get returned at
     *
     * @return string
     */
    public function getReturnedAt(){
        return $this->_getData(self::RETURNED_AT);
    }

    /**
     * Set returned at
     *
     * @param string $returnedAt
     * @return $this
     */
    public function setReturnedAt($returnedAt){
        return $this->setData(self::RETURNED_AT, $returnedAt);
    }

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt(){
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Set created at
     *
     * @param string|null $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt){
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}