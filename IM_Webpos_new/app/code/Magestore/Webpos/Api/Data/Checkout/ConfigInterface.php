<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Checkout;


/**
 * Interface CartItemInterface
 * @package Magestore\Webpos\Api\Data\Checkout
 */
interface ConfigInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for field names
     */
    const KEY_NOTE = 'note';
    const KEY_APPLY_PROMOTION = 'apply_promotion';
    const KEY_APPLIED_RULE_IDS = 'applied_rule_ids';
    const KEY_APPLY_PROMOTION_YES = '1';
    const KEY_APPLY_PROMOTION_NO = '0';
    const KEY_CREATE_INVOICE = 'create_invoice';
    const KEY_CREATE_SHIPMENT = 'create_shipment';
    const KEY_IS_ONHOLD = 'is_onhold';
    const KEY_INIT_DATA = 'init_data';
    const KEY_CART_BASE_DISCOUNT_AMOUNT = 'cart_base_discount_amount';
    const KEY_CART_DISCOUNT_AMOUNT = 'cart_discount_amount';
    const KEY_CART_DISCOUNT_NAME = 'cart_discount_name';
    const KEY_CURRENCY_CODE = 'currency_code';
    const KEY_DISCOUNT_APPLY = 'discount_apply';
    const KEY_SEND_SALE_EMAIL = 'send_sale_email';
    /**#@-*/
    
    /**
     * Returns the applied rules ids.
     *
     * @return string . Otherwise, null.
     */
    public function getAppliedRuleIds();

    /**
     * Sets the applied rules ids.
     *
     * @param string $appliedRuleIds
     * @return $this
     */
    public function setAppliedRuleIds($appliedRuleIds);

    /**
     * Returns set sale email.
     *
     * @return string . Otherwise, null.
     */
    public function getSendSaleEmail();

    /**
     * Sets the set sale email.
     *
     * @param string $sendSaleEmail
     * @return $this
     */
    public function setSendSaleEmail($sendSaleEmail);

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
     * Returns the flag to know if this is onhold order.
     *
     * @return boolean . Otherwise, null.
     */
    public function getIsOnhold();

    /**
     * Sets the flag to know if this is onhold order.
     *
     * @param boolean $isOnhold
     * @return $this
     */
    public function setIsOnhold($isOnhold);

    /**
     * Returns the init data json string for onhold order.
     *
     * @return string . Otherwise, null.
     */
    public function getInitData();

    /**
     * Sets the init data json string for onhold order.
     *
     * @param string $initData
     * @return $this
     */
    public function setInitData($initData);

    
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

    /**
     * Sets discount type apply.
     *
     * @param string $cartBaseDiscountAmount
     * @return $this
     */
    public function setDiscountApply($discountApply);

    /**
     * Returns discount type apply.
     *
     * @return string . Otherwise, null.
     */
    public function getDiscountApply();
}
