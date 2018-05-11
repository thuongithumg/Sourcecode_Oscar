<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Checkout;


/**
 * Interface PaymentItemInterface
 * @package Magestore\Webpos\Api\Data\Checkout
 */
interface PaymentItemInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for field names
     */
    const KEY_CODE = 'code';
    const KEY_TITLE = 'title';
    const KEY_AMOUNT = 'amount';
    const KEY_REAL_AMOUNT = 'real_amount';
    const KEY_BASE_REAL_AMOUNT = 'base_real_amount';
    const KEY_BASE_AMOUNT = 'base_amount';
    const KEY_REFERENCE_NUMBER = 'reference_number';
    const KEY_CARD_TYPE = 'card_type';
    const KEY_IS_PAYLATER = 'is_pay_later';
    const KEY_ADDITIONAL_DATA = 'additional_data';
    const KEY_SHIFT_ID = 'shift_id';
    /**#@-*/
    
    /**
     * Returns the payment code.
     *
     * @return string option code. Otherwise, null.
     */
    public function getCode();

    /**
     * Sets the payment code.
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code);
    
    /**
     * Returns the real amount.
     *
     * @return string amount. Otherwise, null.
     */
    public function getRealAmount();

    /**
     * Sets the real amount.
     *
     * @param string $amount
     * @return $this
     */
    public function setRealAmount($amount);
    
    /**
     * Returns the base real amount.
     *
     * @return string amount. Otherwise, null.
     */
    public function getBaseRealAmount();

    /**
     * Sets the base real amount.
     *
     * @param string $baseAmount
     * @return $this
     */
    public function setBaseRealAmount($baseAmount);

    /**
     * Returns the payment amount.
     *
     * @return string amount. Otherwise, null.
     */
    public function getAmount();

    /**
     * Sets the payment value.
     *
     * @param string $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * Returns the payment base amount.
     *
     * @return string amount. Otherwise, null.
     */
    public function getBaseAmount();

    /**
     * Sets the payment base value.
     *
     * @param string $baseAmount
     * @return $this
     */
    public function setBaseAmount($baseAmount);
    
    /**
     * Returns the payment title.
     *
     * @return string title. Otherwise, null.
     */
    public function getTitle();

    /**
     * Sets the payment title.
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Returns the payment reference number.
     *
     * @return string title. Otherwise, null.
     */
    public function getReferenceNumber();

    /**
     * Sets the payment reference number.
     *
     * @param string $title
     * @return $this
     */
    public function setReferenceNumber($referenceNumber);
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
    
    /**
     * Returns true the method is pay later.
     *
     * @return boolean
     */
    public function getIsPayLater();

    /**
     * Sets the method is pay later.
     *
     * @param string $isPayLater
     * @return $this
     */
    public function setIsPayLater($isPayLater);

    /**
     * Returns shift Id
     *
     * @return string
     */
    public function getShiftId();

    /**
     * Sets shift Id 
     *
     * @param string $shiftId
     * @return $this
     */
    public function setShiftId($shiftId);

    /**
     * Returns the payment additional data.
     *
     * @return string[]
     */
    public function getAdditionalData();

    /**
     * Sets the payment additional data.
     *
     * @param string[] $additionalData
     * @return $this
     */
    public function setAdditionalData($additionalData);

}
