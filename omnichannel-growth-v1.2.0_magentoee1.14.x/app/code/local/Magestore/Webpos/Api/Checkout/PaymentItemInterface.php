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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Interface Magestore_Webpos_Api_Checkout_PaymentItemInterface
 * @package Magestore\Webpos\Api\Data\Checkout
 */
interface Magestore_Webpos_Api_Checkout_PaymentItemInterface
{
    /**#@+
     * Constants for field names
     */
    const CODE = 'code';
    const TITLE = 'title';
    const AMOUNT = 'amount';
    const REAL_AMOUNT = 'real_amount';
    const BASE_REAL_AMOUNT = 'base_real_amount';
    const BASE_AMOUNT = 'base_amount';
    const REFERENCE_NUMBER = 'reference_number';
    const IS_PAYLATER = 'is_pay_later';
    const ADDITIONAL_DATA = 'additional_data';
    const TILL_ID = 'till_id';
    const SHIFT_ID = 'shift_id';
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
     * Returns till Id
     *
     * @return string
     */
    public function getTillId();

    /**
     * Sets till Id
     *
     * @param string $tillId
     * @return $this
     */
    public function setTillId($tillId);

    /**
     * Returns the payment additional data.
     *
     * @return string[]
     */
    public function getAdditionalData();

    /**
     * Sets the payment additional data.
     *
     * @param array $additionalData
     * @return $this
     */
    public function setAdditionalData($additionalData);

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
}
