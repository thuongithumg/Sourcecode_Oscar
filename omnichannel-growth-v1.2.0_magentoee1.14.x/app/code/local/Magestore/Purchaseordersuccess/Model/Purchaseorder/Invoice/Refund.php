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
 * Purchaseorder Invoice Refund Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice_Refund extends Mage_Core_Model_Abstract
{
    /**#@+
     * Constants defined for keys of  data array
     */

    const PURCHASE_ORDER_INVOICE_REFUND_ID = 'purchase_order_invoice_refund_id';

    const PURCHASE_ORDER_INVOICE_ID = 'purchase_order_invoice_id';

    const REFUND_AMOUNT = 'refund_amount';

    const REASON = 'reason';

    const REFUND_AT = 'refund_at';

    const CREATED_AT = 'created_at';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'purchaseordersuccess_purchaseorder_invoice_refund';

    /**
     * Initialization
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchaseordersuccess/purchaseorder_invoice_refund');
    }

    /**
     * Get purchase order invoice refund id
     *
     * @return int
     */
    public function getPurchaseOrderInvoiceRefundId(){
        return $this->_getData(self::PURCHASE_ORDER_INVOICE_REFUND_ID);
    }

    /**
     * Set purchase order invoice refund id
     *
     * @param int $purchaseOrderInvoiceRefundId
     * @return $this
     */
    public function setPurchaseOrderInvoiceRefundId($purchaseOrderInvoiceRefundId){
        return $this->setData(self::PURCHASE_ORDER_INVOICE_REFUND_ID, $purchaseOrderInvoiceRefundId);
    }

    /**
     * Get purchase order invoice id
     *
     * @return int
     */
    public function getPurchaseOrderInvoiceId(){
        return $this->_getData(self::PURCHASE_ORDER_INVOICE_ID);
    }

    /**
     * Set purchase order invoice id
     *
     * @param int $purchaseOrderInvoiceId
     * @return $this
     */
    public function setPurchaseOrderInvoiceId($purchaseOrderInvoiceId){
        return $this->setData(self::PURCHASE_ORDER_INVOICE_ID, $purchaseOrderInvoiceId);
    }

    /**
     * Get refund amount
     *
     * @return float
     */
    public function getRefundAmount(){
        return $this->_getData(self::REFUND_AMOUNT);
    }

    /**
     * Set refund amount
     *
     * @param float $refundAmount
     * @return $this
     */
    public function setRefundAmount($refundAmount){
        return $this->setData(self::REFUND_AMOUNT, $refundAmount);
    }

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason(){
        return $this->_getData(self::REASON);
    }

    /**
     * Set reason
     *
     * @param string $reason
     * @return $this
     */
    public function setReason($reason){
        return $this->setData(self::REASON, $reason);
    }

    /**
     * Get refund at
     *
     * @return string
     */
    public function getRefundAt(){
        return $this->_getData(self::REFUND_AT);
    }

    /**
     * Set refund at
     *
     * @param string $refundAt
     * @return $this
     */
    public function setRefundAt($refundAt){
        return $this->setData(self::REFUND_AT, $refundAt);
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