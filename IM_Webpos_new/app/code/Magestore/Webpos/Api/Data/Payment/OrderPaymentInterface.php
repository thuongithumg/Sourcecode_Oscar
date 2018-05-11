<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Payment;

interface OrderPaymentInterface
{
    /*
     * Webpos Order payment id
     */
    const PAYMENT_ID = 'payment_id';
    /*
     * Webpos Order payment order id
     */
    const ORDER_ID = 'order_id';
    /*
     * Webpos Order payment base amount
     */
    const BASE_PAYMENT_AMOUNT = 'base_real_amount';
    /*
     * Webpos Order payment amount
     */
    const PAYMENT_AMOUNT = 'real_amount';
    /*
     * Webpos Order payment method
     */
    const BASE_DISPLAY_AMOUNT = 'base_payment_amount';
    /*
     * Webpos Order payment amount
     */
    const DISPLAY_AMOUNT = 'payment_amount';
    /*
     * Webpos Order payment method
     */
    const METHOD = 'method';
    /*
     * Webpos Order payment method title
     */
    const METHOD_TITLE = 'method_title';
    /*
     * Webpos Order payment transaction id
     */
    const TRANSACTION_ID = 'transaction_id';
    /*
     * Webpos Order payment invoice id
     */
    const INVOICE_ID = 'invoice_id';
    /*
     * Webpos Order payment invoice id
     */
    const SHIFT_ID = 'shift_id';

    /**
     * Reference number
     */
    const REFERENCE_NUMBER = 'reference_number';
    /**
     * Card type
     */
    const CARD_TYPE = 'card_type';

    /**
     * Sets the Webpos payment ID for the order.
     *
     * @param int $paymentId
     * @return $this
     */
    public function setPaymentId($paymentId);

    /**
     * Gets the Webpos payment ID for the order.
     *
     * @return int|null Webpos payment ID.
     */
    public function getPaymentId();
    
    /**
     * Sets the Webpos payment order ID for the order.
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Gets the Webpos payment order ID for the order.
     *
     * @return int|null Webpos payment order ID.
     */
    public function getOrderId();

    /**
     * Sets the Webpos payment base amount.
     *
     * @param float $basePaymentAmount
     * @return $this
     */
    public function setBasePaymentAmount($basePaymentAmount);

    /**
     * Gets the Webpos payment base amount.
     *
     * @return float|null Webpos payment base amount.
     */
    public function getBasePaymentAmount();

    /**
     * Sets the Webpos payment amount.
     *
     * @param float $paymentAmount
     * @return $this
     */
    public function setPaymentAmount($paymentAmount);

    /**
     * Gets the Webpos payment amount.
     *
     * @return float|null Webpos payment amount.
     */
    public function getPaymentAmount();

    /**
     * Gets the Webpos payment display amount.
     *
     * @return float|null Webpos display base amount.
     */
    public function getBaseDisplayAmount();

    /**
     * Gets the Webpos display amount.
     *
     * @return float|null Webpos display amount.
     */
    public function getDisplayAmount();
    
    /**
     * Sets the Webpos payment method.
     *
     * @param int $method
     * @return $this
     */
    public function setMethod($method);

    /**
     * Gets the Webpos payment method.
     *
     * @return string|null Webpos payment method.
     */
    public function getMethod();
    
    /**
     * Sets the Webpos payment method title.
     *
     * @param int $methodTitle
     * @return $this
     */
    public function setMethodTitle($methodTitle);

    /**
     * Gets the Webpos payment method title.
     *
     * @return string|null Webpos payment method title.
     */
    public function getMethodTitle();

    /**
     * Sets the Webpos payment transaction id.
     *
     * @param int $transactionId
     * @return $this
     */
    public function setTransactionId($transactionId);

    /**
     * Gets the Webpos payment transaction id.
     *
     * @return string|null Webpos payment transaction id.
     */
    public function getTransactionId();
    
    /**
     * Sets the Webpos payment invoice id.
     *
     * @param int $invoiceId
     * @return $this
     */
    public function setInvoiceId($invoiceId);

    /**
     * Gets the Webpos payment invoice id.
     *
     * @return string|null Webpos payment invoice id.
     */
    public function getInvoiceId();
    
    /**
     * Sets the Webpos payment shift id.
     *
     * @param int $shiftId
     * @return $this
     */
    public function setShiftId($shiftId);

    /**
     * Gets the Webpos payment invoice id.
     *
     * @return string|null Webpos payment shift id.
     */
    public function getShiftId();

    /**
     * Sets reference number
     *
     * @param string $referenceNumber
     * @return $this
     */
    public function setReferenceNumber($referenceNumber);

    /**
     * Gets reference number
     *
     * @return string
     */
    public function getReferenceNumber();

    /**
     * Returns the payment card type.
     *
     * @return string card type. Otherwise, null.
     */
    public function getCardType();

    /**
     * Sets the payment card type..
     *
     * @param string $cardType
     * @return $this
     */
    public function setCardType($cardType);
}
