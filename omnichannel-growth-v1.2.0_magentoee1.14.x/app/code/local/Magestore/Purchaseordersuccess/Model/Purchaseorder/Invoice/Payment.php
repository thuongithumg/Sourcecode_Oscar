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
 * Purchaseorder Invoice Payment Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice_Payment extends Mage_Core_Model_Abstract
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const PURCHASE_ORDER_INVOICE_PAYMENT_ID = 'purchase_order_invoice_payment_id';

    const PURCHASE_ORDER_INVOICE_ID = 'purchase_order_invoice_id';

    const PAYMENT_AT = 'payment_at';

    const PAYMENT_METHOD = 'payment_method';

    const PAYMENT_AMOUNT = 'payment_amount';

    const DESCRIPTION = 'description';

    const CREATED_AT = 'created_at';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'purchaseordersuccess_purchaseorder_invoice_payment';

    /**
     * Initialization
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchaseordersuccess/purchaseorder_invoice_payment');
    }

    /**
     * Get purchase order invoice payment id
     *
     * @return int
     */
    public function getPurchaseOrderInvoicePaymentId(){
        return $this->_getData(self::PURCHASE_ORDER_INVOICE_PAYMENT_ID);
    }

    /**
     * Set purchase order invoice payment id
     *
     * @param int $purchaseOrderInvoicePaymentId
     * @return $this
     */
    public function setPurchaseOrderInvoicePaymentId($purchaseOrderInvoicePaymentId){
        return $this->setData(self::PURCHASE_ORDER_INVOICE_PAYMENT_ID, $purchaseOrderInvoicePaymentId);
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
     * Get payment method
     *
     * @return string
     */
    public function getPaymentMethod(){
        return $this->_getData(self::PAYMENT_METHOD);
    }

    /**
     * Set payment method
     *
     * @param string $paymentMethod
     * @return $this
     */
    public function setPaymentMethod($paymentMethod){
        return $this->setData(self::PAYMENT_METHOD, $paymentMethod);
    }

    /**
     * Get payment amount
     *
     * @return float
     */
    public function getPaymentAmount(){
        return $this->_getData(self::PAYMENT_AMOUNT);
    }

    /**
     * Set payment amount
     *
     * @param float $paymentAmount
     * @return $this
     */
    public function setPaymentAmount($paymentAmount){
        return $this->setData(self::PAYMENT_AMOUNT, $paymentAmount);
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(){
        return $this->_getData(self::DESCRIPTION);
    }

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description){
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get payment at
     *
     * @return string
     */
    public function getPaymentAt(){
        return $this->_getData(self::PAYMENT_AT);
    }

    /**
     * Set payment at
     *
     * @param string $paymentAt
     * @return $this
     */
    public function setPaymentAt($paymentAt){
        return $this->setData(self::PAYMENT_AT, $paymentAt);
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