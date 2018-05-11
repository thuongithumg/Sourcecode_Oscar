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
 * Purchaseorder Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Status as PurchaseorderStatus;

class Magestore_Purchaseordersuccess_Model_Purchaseorder extends Mage_Core_Model_Abstract
    implements Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const PURCHASE_ORDER_ID = 'purchase_order_id';

    const PURCHASE_CODE = 'purchase_code';

    const SUPPLIER_ID = 'supplier_id';

    const TYPE = 'type';

    const STATUS = 'status';

    const SEND_EMAIL = 'send_email';

    const IS_SENT = 'is_sent';

    const COMMENT = 'comment';

    const SHIPPING_ADDRESS = 'shipping_address';

    const SHIPPING_METHOD = 'shipping_method';

    const SHIPPING_COST = 'shipping_cost';

    const PAYMENT_TERM = 'payment_term';

    const PLACED_VIA = 'placed_via';

    const USER_ID = 'user_id';

    const CREATED_BY = 'created_by';

    const TOTAL_QTY_ORDERRED = 'total_qty_orderred';

    const TOTAL_QTY_RECEIVED = 'total_qty_received';

    const TOTAL_QTY_BILLED = 'total_qty_billed';

    const TOTAL_QTY_TRANSFERRED = 'total_qty_transferred';

    const TOTAL_QTY_RETURNED = 'total_qty_returned';

    const SUBTOTAL = 'subtotal';

    const TOTAL_TAX = 'total_tax';

    const TOTAL_DISCOUNT = 'total_discount';

    const GRAND_TOTAL_EXCL_TAX = 'grand_total_excl_tax';

    const GRAND_TOTAL_INCL_TAX = 'grand_total_incl_tax';

    const TOTAL_BILLED = 'total_billed';

    const TOTAL_DUE = 'total_due';

    const CURRENCY_CODE = 'currency_code';

    const CURRENCY_RATE = 'currency_rate';

    const PURCHASED_AT = 'purchased_at';

    const STARTED_AT = 'started_at';

    const EXPECTED_AT = 'expected_at';

    const CANCELED_AT = 'canceled_at';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    const PURCHASE_KEY = 'purchase_key';

    const ITEMS = 'items';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'purchaseordersuccess_purchaseorder';

    /**
     * Initialization
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchaseordersuccess/purchaseorder');
    }

    /**
     * Get purchase order id
     *
     * @return int
     */
    public function getPurchaseOrderId()
    {
        return $this->_getData(self::PURCHASE_ORDER_ID);
    }

    /**
     * Set purchase order id
     *
     * @param int $purchaseOrderId
     * @return $this
     */
    public function setPurchaseOrderId($purchaseOrderId)
    {
        return $this->setData(self::PURCHASE_ORDER_ID, $purchaseOrderId);
    }

    /**
     * Get purchase code
     *
     * @return string|null
     */
    public function getPurchaseCode()
    {
        return $this->_getData(self::PURCHASE_CODE);
    }

    /**
     * Set purchase code
     *
     * @param string $purchaseCode
     * @return $this
     */
    public function setPurchaseCode($purchaseCode)
    {
        return $this->setData(self::PURCHASE_CODE, $purchaseCode);
    }

    /**
     * Get supplier id
     *
     * @return int
     */
    public function getSupplierId()
    {
        return $this->_getData(self::SUPPLIER_ID);
    }

    /**
     * Set supplier id
     *
     * @param int $supplierId
     * @return $this
     */
    public function setSupplierId($supplierId)
    {
        return $this->setData(self::SUPPLIER_ID, $supplierId);
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->_getData(self::TYPE);
    }

    /**
     * Set type
     *
     * @param int $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->_getData(self::STATUS);
    }

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get send email
     *
     * @return int
     */
    public function getSendEmail()
    {
        return $this->_getData(self::SEND_EMAIL);
    }

    /**
     * Set send email
     *
     * @param int $sendEmail
     * @return $this
     */
    public function setSendEmail($sendEmail)
    {
        return $this->setData(self::SEND_EMAIL, $sendEmail);
    }

    /**
     * Get is sent email
     *
     * @return boolean
     */
    public function getIsSent()
    {
        return $this->_getData(self::IS_SENT);
    }

    /**
     * Set is sent email
     *
     * @param int $isSent
     * @return $this
     */
    public function setIsSent($isSent)
    {
        return $this->setData(self::IS_SENT, $isSent);
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->_getData(self::COMMENT);
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return $this
     */
    public function setComment($comment)
    {
        return $this->setData(self::COMMENT, $comment);
    }

    /**
     * Get shipping address
     *
     * @return string
     */
    public function getShippingAddress()
    {
        return $this->_getData(self::SHIPPING_ADDRESS);
    }

    /**
     * Set shipping address
     *
     * @param string $shippingAddress
     * @return $this
     */
    public function setShippingAddress($shippingAddress)
    {
        return $this->setData(self::SHIPPING_ADDRESS, $shippingAddress);
    }

    /**
     * Get shipping method
     *
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->_getData(self::SHIPPING_METHOD);
    }

    /**
     * Set shipping method
     *
     * @param string $shippingMethod
     * @return $this
     */
    public function setShippingMethod($shippingMethod)
    {
        return $this->setData(self::SHIPPING_METHOD, $shippingMethod);
    }

    /**
     * Get shipping cost
     *
     * @return float
     */
    public function getShippingCost()
    {
        return $this->_getData(self::SHIPPING_COST);
    }

    /**
     * Set shipping cost
     *
     * @param float $shippingCost
     * @return $this
     */
    public function setShippingCost($shippingCost)
    {
        return $this->setData(self::SHIPPING_COST, $shippingCost);
    }

    /**
     * Get payment term
     *
     * @return string
     */
    public function getPaymentTerm()
    {
        return $this->_getData(self::PAYMENT_TERM);
    }

    /**
     * Set payment term
     *
     * @param string $paymentTerm
     * @return $this
     */
    public function setPaymentTerm($paymentTerm)
    {
        return $this->setData(self::PAYMENT_TERM, $paymentTerm);
    }

    /**
     * Get placed via
     *
     * @return string
     */
    public function getPlacedVia()
    {
        return $this->_getData(self::PLACED_VIA);
    }

    /**
     * Set placed via
     *
     * @param string $placedVia
     * @return $this
     */
    public function setPlacedVia($placedVia)
    {
        return $this->setData(self::PLACED_VIA, $placedVia);
    }

    /**
     * Get user id
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->_getData(self::USER_ID);
    }

    /**
     * Set user id
     *
     * @param string $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * Get created by
     *
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->_getData(self::CREATED_BY);
    }

    /**
     * Set created by
     *
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy)
    {
        return $this->setData(self::CREATED_BY, $createdBy);
    }

    /**
     * Get total qty orderred
     *
     * @return float
     */
    public function getTotalQtyOrderred()
    {
        return $this->_getData(self::TOTAL_QTY_ORDERRED);
    }

    /**
     * Set total qty orderred
     *
     * @param float $subtotal
     * @return $this
     */
    public function setTotalQtyOrderred($totalQtyOrderred)
    {
        return $this->setData(self::TOTAL_QTY_ORDERRED, $totalQtyOrderred);
    }

    /**
     * Get total qty received
     *
     * @return float
     */
    public function getTotalQtyReceived()
    {
        return $this->_getData(self::TOTAL_QTY_RECEIVED);
    }

    /**
     * Set total qty received
     *
     * @param float $totalQtyReceived
     * @return $this
     */
    public function setTotalQtyReceived($totalQtyReceived)
    {
        return $this->setData(self::TOTAL_QTY_RECEIVED, $totalQtyReceived);
    }

    /**
     * Get total qty billed
     *
     * @return float
     */
    public function getTotalQtyBilled()
    {
        return $this->_getData(self::TOTAL_QTY_BILLED);
    }

    /**
     * Set total qty billed
     *
     * @param float $totalQtyBilled
     * @return $this
     */
    public function setTotalQtyBilled($totalQtyBilled)
    {
        return $this->setData(self::TOTAL_QTY_BILLED, $totalQtyBilled);
    }

    /**
     * Get total qty transferred
     *
     * @return float
     */
    public function getTotalQtyTransferred()
    {
        return $this->_getData(self::TOTAL_QTY_TRANSFERRED);
    }

    /**
     * Set total qty transferred
     *
     * @param float $totalQtyTransferred
     * @return $this
     */
    public function setTotalQtyTransferred($totalQtyTransferred)
    {
        return $this->setData(self::TOTAL_QTY_TRANSFERRED, $totalQtyTransferred);
    }

    /**
     * Get total qty returned
     *
     * @return float
     */
    public function getTotalQtyReturned()
    {
        return $this->_getData(self::TOTAL_QTY_RETURNED);
    }

    /**
     * Set total qty transferred
     *
     * @param float $totalQtyTransferred
     * @return $this
     */
    public function setTotalQtyReturned($totalQtyReturned)
    {
        return $this->setData(self::TOTAL_QTY_RETURNED, $totalQtyReturned);
    }

    /**
     * Get subtotal
     *
     * @return float
     */
    public function getSubtotal()
    {
        return $this->_getData(self::SUBTOTAL);
    }

    /**
     * Set subtotal
     *
     * @param float $subtotal
     * @return $this
     */
    public function setSubtotal($subtotal)
    {
        return $this->setData(self::SUBTOTAL, $subtotal);
    }

    /**
     * Get total tax
     *
     * @return float
     */
    public function getTotalTax()
    {
        return $this->_getData(self::TOTAL_TAX);
    }

    /**
     * Set total tax
     *
     * @param float $totalTax
     * @return $this
     */
    public function setTotalTax($totalTax)
    {
        return $this->setData(self::TOTAL_TAX, $totalTax);
    }

    /**
     * Get total discount
     *
     * @return float
     */
    public function getTotalDiscount()
    {
        return $this->_getData(self::TOTAL_DISCOUNT);
    }

    /**
     * Set total discount
     *
     * @param float $totalDiscount
     * @return $this
     */
    public function setTotalDiscount($totalDiscount)
    {
        return $this->setData(self::TOTAL_DISCOUNT, $totalDiscount);
    }

    /**
     * Get grand total exclude tax
     *
     * @return float
     */
    public function getGrandTotalExclTax()
    {
        return $this->_getData(self::GRAND_TOTAL_EXCL_TAX);
    }

    /**
     * Set grand total exclude tax
     *
     * @param float $grandTotalExclTax
     * @return $this
     */
    public function setGrandTotalExclTax($grandTotalExclTax)
    {
        return $this->setData(self::GRAND_TOTAL_EXCL_TAX, $grandTotalExclTax);
    }

    /**
     * Get grand total include tax
     *
     * @return float
     */
    public function getGrandTotalInclTax()
    {
        return $this->_getData(self::GRAND_TOTAL_INCL_TAX);
    }

    /**
     * Set grand total include tax
     *
     * @param float $grandTotalInclTax
     * @return $this
     */
    public function setGrandTotalInclTax($grandTotalInclTax)
    {
        return $this->setData(self::GRAND_TOTAL_INCL_TAX, $grandTotalInclTax);
    }

    /**
     * Get total billed
     *
     * @return float
     */
    public function getTotalBilled()
    {
        return $this->_getData(self::TOTAL_BILLED);
    }

    /**
     * Set total billed
     *
     * @param float $totalBilled
     * @return $this
     */
    public function setTotalBilled($totalBilled)
    {
        return $this->setData(self::TOTAL_BILLED, $totalBilled);
    }

    /**
     * Get total due
     *
     * @return float
     */
    public function getTotalDue()
    {
        return $this->_getData(self::TOTAL_DUE);
    }

    /**
     * Set total due
     *
     * @param float $totalDue
     * @return $this
     */
    public function setTotalDue($totalDue)
    {
        return $this->setData(self::TOTAL_DUE, $totalDue);
    }

    /**
     * Get currency code
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->_getData(self::CURRENCY_CODE);
    }

    /**
     * Set currency code
     *
     * @param string $currencyCode
     * @return $this
     */
    public function setCurrencyCode($currencyCode)
    {
        return $this->setData(self::CURRENCY_CODE, $currencyCode);
    }

    /**
     * Get currency rate
     *
     * @return string
     */
    public function getCurrencyRate()
    {
        return $this->_getData(self::CURRENCY_RATE);
    }

    /**
     * Set currency rate
     *
     * @param string $currencyRate
     * @return $this
     */
    public function setCurrencyRate($currencyRate)
    {
        return $this->setData(self::CURRENCY_RATE, $currencyRate);
    }

    /**
     * Get purchased at
     *
     * @return string
     */
    public function getPurchasedAt()
    {
        return $this->_getData(self::PURCHASED_AT);
    }

    /**
     * Set purchased at
     *
     * @param string $purchasedAt
     * @return $this
     */
    public function setPurchasedAt($purchasedAt)
    {
        return $this->setData(self::PURCHASED_AT, $purchasedAt);
    }

    /**
     * Get started at
     *
     * @return string
     */
    public function getStartedAt()
    {
        return $this->_getData(self::STARTED_AT);
    }

    /**
     * Set started at
     *
     * @param string $startedAt
     * @return $this
     */
    public function setStartedAt($startedAt)
    {
        return $this->setData(self::STARTED_AT, $startedAt);
    }

    /**
     * Get expected at
     *
     * @return string
     */
    public function getExpectedAt()
    {
        return $this->_getData(self::EXPECTED_AT);
    }

    /**
     * Set expected at
     *
     * @param string $expectedAt
     * @return $this
     */
    public function setExpectedAt($expectedAt)
    {
        return $this->setData(self::EXPECTED_AT, $expectedAt);
    }

    /**
     * Get canceled at
     *
     * @return string
     */
    public function getCanceledAt()
    {
        return $this->_getData(self::CANCELED_AT);
    }

    /**
     * Set canceled at
     *
     * @param string $canceledAt
     * @return $this
     */
    public function setCanceledAt($canceledAt)
    {
        return $this->setData(self::CANCELED_AT, $canceledAt);
    }

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Set created at
     *
     * @param string|null $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getPurchaseKey()
    {
        return $this->_getData(self::PURCHASE_KEY);
    }

    /**
     * Set key
     *
     * @param string $key
     * @return $this
     */
    public function setPurchaseKey($key)
    {
        return $this->setData(self::PURCHASE_KEY, $key);
    }

    /**
     * Check this purchase order can send email automatically
     *
     * @return bool
     */
    public function canSendEmail()
    {
        $status = $this->getStatus();
        if (!$status || !$this->getSendEmail() || !$this->getId())
            return false;
        if ($status == Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Status::STATUS_CANCELED)
            return false;
        return true;
    }

    /**
     * Get purchase order item
     *
     * @return Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Item_Collection
     */
    public function getItems($purchaseId = null, $productIds = array())
    {
        $collection = Mage::getResourceModel('purchaseordersuccess/purchaseorder_item_collection');
        if ($this->getPurchaseOrderId())
            $collection->addFieldToFilter('purchase_order_id', $this->getPurchaseOrderId());
        else
            $collection->addFieldToFilter('purchase_order_id', $purchaseId);
        if (!empty($productIds))
            $collection->addFieldToFilter('product_id', array('in' => $productIds));
        return $collection;
    }

    /**
     * Get purchase order invoice list
     * 
     * @param int|null $purchaseId
     * @return Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Invoice_Collection
     */
    public function getInvoiceList($purchaseId = null){
        $collection = Mage::getResourceModel('purchaseordersuccess/purchaseorder_invoice_collection');
        if ($this->getPurchaseOrderId())
            $collection->addFieldToFilter('purchase_order_id', $this->getPurchaseOrderId());
        else
            $collection->addFieldToFilter('purchase_order_id', $purchaseId);
        return $collection;
    }

    public function canAddProduct()
    {
        return $this->getStatus() == PurchaseorderStatus::STATUS_PENDING;
    }

    public function canReceiveItem()
    {
        return $this->getStatus() == PurchaseorderStatus::STATUS_PROCESSING &&
        $this->getStatus() != PurchaseorderStatus::STATUS_PENDING &&
        $this->getTotalQtyOrderred() > $this->getTotalQtyReceived();
    }

    public function canReturnItem()
    {
        return $this->getStatus() != PurchaseorderStatus::STATUS_CANCELED &&
        $this->getStatus() != PurchaseorderStatus::STATUS_PENDING &&
        $this->getTotalQtyReceived() > $this->getTotalQtyReturned() + $this->getTotalQtyTransferred();
    }

    public function canTransferItem()
    {
        return $this->canReturnItem();
    }

    public function canInvoice()
    {
        return $this->getStatus() != PurchaseorderStatus::STATUS_CANCELED && 
        $this->getStatus() != PurchaseorderStatus::STATUS_PENDING && 
        $this->getTotalQtyOrderred() > $this->getTotalQtyBilled();
    }

    /**
     * @return
     */
    public function getSelectionProductModel()
    {
        return Mage::getModel('purchaseordersuccess/purchaseorder_item');
    }
}