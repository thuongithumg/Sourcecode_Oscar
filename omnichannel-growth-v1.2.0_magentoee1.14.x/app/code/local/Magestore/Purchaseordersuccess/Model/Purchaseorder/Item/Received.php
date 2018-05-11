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
 * Purchaseorder Item Received Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Purchaseorder_Item_Received extends Mage_Core_Model_Abstract
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const PURCHASE_ORDER_ITEM_RECEIVED_ID = 'purchase_order_item_received_id';

    const PURCHASE_ORDER_ITEM_ID = 'purchase_order_item_id';

    const QTY_RECEIVED = 'qty_received';

    const CREATED_BY = 'created_by';

    const RECEIVED_AT = 'received_at';

    const CREATED_AT = 'created_at';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'purchaseordersuccess_purchaseorder_item_received';

    /**
     * Initialization
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchaseordersuccess/purchaseorder_item_received');
    }

    /**
     * Get purchase order item received id
     *
     * @return int
     */
    public function getPurchaseOrderItemReceivedId(){
        return $this->_getData(self::PURCHASE_ORDER_ITEM_RECEIVED_ID);
    }

    /**
     * Set purchase order item received id
     *
     * @param int $purchaseOrderItemReceivedId
     * @return $this
     */
    public function setPurchaseOrderItemReceivedId($purchaseOrderItemReceivedId){
        return $this->setData(self::PURCHASE_ORDER_ITEM_RECEIVED_ID, $purchaseOrderItemReceivedId);
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
     * Get qty received
     *
     * @return float
     */
    public function getQtyReceived(){
        return $this->_getData(self::QTY_RECEIVED);
    }

    /**
     * Set qty received
     *
     * @param float $qtyReceived
     * @return $this
     */
    public function setQtyReceived($qtyReceived){
        return $this->setData(self::QTY_RECEIVED, $qtyReceived);
    }

    /**
     * Get created by
     *
     * @return string
     */
    public function getCreatedBy(){
        return $this->getData(self::CREATED_BY);
    }

    /**
     * Set created by
     *
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy){
        return $this->setData(self::CREATED_BY, $createdBy);
    }

    /**
     * Get received at
     *
     * @return string
     */
    public function getReceivedAt(){
        return $this->_getData(self::RECEIVED_AT);
    }

    /**
     * Set received at
     *
     * @param string $receivedAt
     * @return $this
     */
    public function setReceivedAt($receivedAt){
        return $this->setData(self::RECEIVED_AT, $receivedAt);
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