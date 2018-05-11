<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Cart;

interface CheckoutInterface
{
    const ITEMS = 'items';
    const SHIPPING = 'shipping';
    const PAYMENT = 'payment';
    const QUOTE_INIT = 'quote_init';
    const TOTALS = 'totals';
    const REWARDPOINTS = 'rewardpoints';
    const GIFTCARD = 'giftcard';
    const STORECREDIT = 'storecredit';

    const EVENT_WEBPOS_EMPTY_CART_BEFORE = 'webpos_empty_cart_before';
    const EVENT_WEBPOS_EMPTY_CART_AFTER = 'webpos_empty_cart_after';
    const EVENT_WEBPOS_SAVE_CART_AFTER = 'webpos_save_cart_after';
    const EVENT_WEBPOS_SEND_RESPONSE_BEFORE = 'webpos_send_response_before';
    const EVENT_WEBPOS_GET_PAYMENT_AFTER = 'webpos_get_payment_after';
    const EVENT_WEBPOS_ORDER_PLACE_BEFORE = 'webpos_order_place_before';
    const EVENT_WEBPOS_ORDER_PLACE_AFTER = 'webpos_order_place_after';
    const EVENT_WEBPOS_SUBMIT_ORDER_AFTER = 'webpos_submit_order_after';

    /**
     * Sets items
     *
     * @param \Magestore\Webpos\Api\Data\Cart\QuoteItemInterface[] Arrray of $items
     * @return $this
     */
    public function setItems(array $items = null);
    
    /**
     * Gets items
     *
     * @return \Magestore\Webpos\Api\Data\Cart\QuoteItemInterface[] Arrray of $items
     */
    public function getItems();

    /**
     * Sets shipping
     *
     * @param \Magestore\Webpos\Api\Data\Shipping\ShippingInterface[] Arrray of $shipping
     * @return $this
     */
    public function setShipping(array $shipping = null);

    /**
     * Gets shipping
     *
     * @return \Magestore\Webpos\Api\Data\Shipping\ShippingInterface[] Arrray of $shipping
     */
    public function getShipping();

    /**
     * Sets payment
     *
     * @param \Magestore\Webpos\Api\Data\Payment\PaymentInterface[] Arrray of $payment
     * @return $this
     */
    public function setPayment(array $payment = null);

    /**
     * Gets payment
     *
     * @return \Magestore\Webpos\Api\Data\Payment\PaymentInterface[] Arrray of $payment
     */
    public function getPayment();

    /**
     * Sets quote
     *
     * @param \Magestore\Webpos\Api\Data\Cart\QuoteInterface $quote
     * @return $this
     */
    public function setQuoteInit($quote);

    /**
     * Gets quote
     *
     * @return \Magestore\Webpos\Api\Data\Cart\QuoteInterface
     */
    public function getQuoteInit();

    /**
     * Sets total
     *
     * @param \Magestore\Webpos\Api\Data\Cart\TotalInterface[] $totals
     * @return $this
     */
    public function setTotals(array $totals = null);

    /**
     * Gets quote
     *
     * @return \Magestore\Webpos\Api\Data\Cart\TotalInterface[]
     */
    public function getTotals();

    /**
     * Sets gift card
     *
     * @param \Magestore\Webpos\Api\Integration\Response\GiftcardInterface $giftcard
     * @return $this
     */
    public function setGiftcard($giftcard);

    /**
     * Gets gift card
     *
     * @return \Magestore\Webpos\Api\Integration\Response\GiftcardInterface
     */
    public function getGiftcard();

    /**
     * Sets rewardpoints
     *
     * @param \Magestore\Webpos\Api\Integration\Response\RewardpointsInterface $rewardpoints
     * @return $this
     */
    public function setRewardpoints($rewardpoints);

    /**
     * Gets rewardpoints
     *
     * @return \Magestore\Webpos\Api\Integration\Response\RewardpointsInterface
     */
    public function getRewardpoints();

    /**
     * Sets storecredit
     *
     * @param \Magestore\Webpos\Api\Integration\Response\StorecreditInterface $storecredit
     * @return $this
     */
    public function setStorecredit($storecredit);

    /**
     * Gets storecredit
     *
     * @return \Magestore\Webpos\Api\Integration\Response\StorecreditInterface
     */
    public function getStorecredit();

}
