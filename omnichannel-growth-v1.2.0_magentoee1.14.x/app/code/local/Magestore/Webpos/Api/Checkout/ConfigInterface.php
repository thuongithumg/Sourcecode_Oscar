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
 * Interface Magestore_Webpos_Api_Checkout_ConfigInterface
 */
interface Magestore_Webpos_Api_Checkout_ConfigInterface
{
    /**#@+
     * Constants for field names
     */
    const KEY_NOTE = 'note';
    const KEY_APPLY_PROMOTION = 'apply_promotion';
    const KEY_APPLY_PROMOTION_YES = '1';
    const KEY_APPLY_PROMOTION_NO = '0';
    const KEY_CREATE_INVOICE = 'create_invoice';
    const KEY_CREATE_SHIPMENT = 'create_shipment';
    const KEY_CART_BASE_DISCOUNT_AMOUNT = 'cart_base_discount_amount';
    const KEY_CART_DISCOUNT_AMOUNT = 'cart_discount_amount';
    const KEY_CART_DISCOUNT_NAME = 'cart_discount_name';
    const KEY_CURRENCY_CODE = 'currency_code';
    /**#@-*/
    
    /**
     * Returns the apply promotion mode.
     *
     * @return int . Otherwise, null.
     */
    public function getApplyPromotion();

    /**
     * Sets the apply promotion mode.
     *
     * @param int $apply_promotion
     * @return $this
     */
    public function setApplyPromotion($apply_promotion);
    
    /**
     * Returns the cart note/comment.
     *
     * @return string . Otherwise, null.
     */
    public function getNote();

    /**
     * Sets the apply promotion mode.
     *
     * @param string $note
     * @return $this
     */
    public function setNote($note);
    
    /**
     * Returns the flag to create invoice.
     *
     * @return boolean . Otherwise, null.
     */
    public function getCreateInvoice();

    /**
     * Sets the flag to create invoice.
     *
     * @param boolean $createInvoice
     * @return $this
     */
    public function setCreateInvoice($createInvoice);
    
    /**
     * Returns the flag to create shipment.
     *
     * @return boolean . Otherwise, null.
     */
    public function getCreateShipment();

    /**
     * Sets the flag to create shipment.
     *
     * @param boolean $createShipment
     * @return $this
     */
    public function setCreateShipment($createShipment);
    
    /**
     * Returns the cart discount amount.
     *
     * @return string . Otherwise, null.
     */
    public function getCartDiscountAmount();

    /**
     * Sets the cart discount amount.
     *
     * @param string $cartDiscountAmount
     * @return $this
     */
    public function setCartDiscountAmount($cartDiscountAmount);
    
    /**
     * Returns the cart discount name.
     *
     * @return string . Otherwise, null.
     */
    public function getCartDiscountName();

    /**
     * Sets the cart discount name.
     *
     * @param string $cartDiscountName
     * @return $this
     */
    public function setCartDiscountName($cartDiscountName);
    
    /**
     * Returns the currency code.
     *
     * @return string . Otherwise, null.
     */
    public function getCurrencyCode();

    /**
     * Sets the currency code.
     *
     * @param string $currencyCode
     * @return $this
     */
    public function setCurrencyCode($currencyCode);
    
    /**
     * Returns the cart base discount amount.
     *
     * @return string . Otherwise, null.
     */
    public function getCartBaseDiscountAmount();

    /**
     * Sets the cart base discount amount.
     *
     * @param string $cartBaseDiscountAmount
     * @return $this
     */
    public function setCartBaseDiscountAmount($cartBaseDiscountAmount);
}
