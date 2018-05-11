<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Payment;
use Magestore\Webpos\Api\Data\Payment\OrderPaymentInterface;


/**
 * Class Staff
 * @package Magestore\Webpos\Model\Payment
 */
class OrderPayment extends \Magento\Framework\Model\AbstractModel implements \Magestore\Webpos\Api\Data\Payment\OrderPaymentInterface
{

    
    /**
     * @var string
     */
    protected $_eventPrefix = 'webpos_order_payment';
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\ResourceModel\Payment\OrderPayment');
    }

    /**
     * Sets the Webpos payment ID for the order.
     *
     * @param int $paymentId
     * @return $this
     */
    public function setPaymentId($paymentId)
    {
        return $this->setData(OrderPaymentInterface::PAYMENT_ID, $paymentId);
    }
    
    /**
     * Gets the Webpos payment ID for the order.
     *
     * @return int|null Webpos payment ID.
     */
    public function getPaymentId()
    {
        return $this->getData(OrderPaymentInterface::PAYMENT_ID);
    }

    /**
     * Sets the Webpos payment order ID for the order.
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        return $this->setData(OrderPaymentInterface::ORDER_ID, $orderId);
    }

    /**
     * Gets the Webpos payment order ID for the order.
     *
     * @return int|null Webpos payment order ID.
     */
    public function getOrderId()
    {
        return $this->getData(OrderPaymentInterface::ORDER_ID);
    }

    /**
     * Sets the Webpos payment base amount.
     *
     * @param float $basePaymentAmount
     * @return $this
     */
    public function setBasePaymentAmount($basePaymentAmount)
    {
        return $this->setData(OrderPaymentInterface::BASE_PAYMENT_AMOUNT, $basePaymentAmount);
    }

    /**
     * Gets the Webpos payment base amount.
     *
     * @return float|null Webpos payment base amount.
     */
    public function getBasePaymentAmount()
    {
        return $this->getData(OrderPaymentInterface::BASE_PAYMENT_AMOUNT);
    }

    /**
     * Sets the Webpos payment amount.
     *
     * @param float $paymentAmount
     * @return $this
     */
    public function setPaymentAmount($paymentAmount)
    {
        return $this->setData(OrderPaymentInterface::PAYMENT_AMOUNT, $paymentAmount);
    }

    /**
     * Gets the Webpos payment amount.
     *
     * @return float|null Webpos payment amount.
     */
    public function getPaymentAmount()
    {
        return $this->getData(OrderPaymentInterface::PAYMENT_AMOUNT);
    }

    /**
     * Gets the Webpos payment display amount.
     *
     * @return float|null Webpos display base amount.
     */
    public function getBaseDisplayAmount(){
        return $this->getData(OrderPaymentInterface::BASE_DISPLAY_AMOUNT);
    }

    /**
     * Gets the Webpos display amount.
     *
     * @return float|null Webpos display amount.
     */
    public function getDisplayAmount(){
        return $this->getData(OrderPaymentInterface::DISPLAY_AMOUNT);
    }

    /**
     * Sets the Webpos payment method.
     *
     * @param int $method
     * @return $this
     */
    public function setMethod($method)
    {
        return $this->setData(OrderPaymentInterface::METHOD, $method);
    }

    /**
     * Gets the Webpos payment method.
     *
     * @return string|null Webpos payment method.
     */
    public function getMethod()
    {
        return $this->getData(OrderPaymentInterface::METHOD);
    }

    /**
     * Sets the Webpos payment method title.
     *
     * @param int $methodTitle
     * @return $this
     */
    public function setMethodTitle($methodTitle)
    {
        return $this->setData(OrderPaymentInterface::METHOD_TITLE, $methodTitle);
    }

    /**
     * Gets the Webpos payment method title.
     *
     * @return string|null Webpos payment method title.
     */
    public function getMethodTitle()
    {
        return $this->getData(OrderPaymentInterface::METHOD_TITLE);
    }

    /**
     * Sets the Webpos payment transaction id.
     *
     * @param int $transactionId
     * @return $this
     */
    public function setTransactionId($transactionId)
    {
        return $this->setData(OrderPaymentInterface::TRANSACTION_ID, $transactionId);
    }

    /**
     * Gets the Webpos payment transaction id.
     *
     * @return string|null Webpos payment transaction id.
     */
    public function getTransactionId()
    {
        return $this->getData(OrderPaymentInterface::TRANSACTION_ID);
    }

    /**
     * Sets the Webpos payment invoice id.
     *
     * @param int $invoiceId
     * @return $this
     */
    public function setInvoiceId($invoiceId)
    {
        return $this->setData(OrderPaymentInterface::INVOICE_ID, $invoiceId);
    }

    /**
     * Gets the Webpos payment invoice id.
     *
     * @return string|null Webpos payment invoice id.
     */
    public function getInvoiceId()
    {
        return $this->getData(OrderPaymentInterface::INVOICE_ID);
    }

    /**
     * Sets the Webpos payment shift id.
     *
     * @param int $shiftId
     * @return $this
     */
    public function setShiftId($shiftId){
        return $this->setData(OrderPaymentInterface::SHIFT_ID, $shiftId);
    }

    /**
     * Gets the Webpos payment invoice id.
     *
     * @return string|null Webpos payment shift id.
     */
    public function getShiftId(){
        return $this->getData(OrderPaymentInterface::SHIFT_ID);
    }

    /**
     * Sets reference number
     *
     * @param string $referenceNumber
     * @return $this
     */
    public function setReferenceNumber($referenceNumber){
        return $this->setData(OrderPaymentInterface::REFERENCE_NUMBER, $referenceNumber);
    }

    /**
     * Gets reference number
     *
     * @return string
     */
    public function getReferenceNumber(){
        return $this->getData(OrderPaymentInterface::REFERENCE_NUMBER);
    }

    /**
     * Sets card type
     *
     * @param string $cardType
     * @return $this
     */
    public function setCardType($cardType){
        return $this->setData(OrderPaymentInterface::CARD_TYPE, $cardType);
    }

    /**
     * Gets card type
     *
     * @return string
     */
    public function getCardType(){
        return $this->getData(OrderPaymentInterface::CARD_TYPE);
    }
}