<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Cart;

use Magento\Quote\Model\Quote;

/**
 * Interface CartInterface
 * @package Magestore\Webpos\Api\Cart
 */
interface CheckoutInterface
{
    /**
     * @param string $customerId
     * @param string $quoteId
     * @param string $currencyId
     * @param string $storeId
     * @param string $tillId
     * @param string[] $section
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     */
    public function getCartDataByCustomer($customerId, $quoteId, $currencyId, $storeId, $tillId, $section);

    /**
     * @param string $customerId
     * @param string $quoteId
     * @param string $currencyId
     * @param string $storeId
     * @param string $tillId
     * @param \Magestore\Webpos\Api\Data\Cart\CustomerInterface $customer
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemInterface[] $items
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function saveCart($customerId, $quoteId, $currencyId, $storeId, $tillId, $customer, $items);

    /**
     * @param string $quoteId
     * @return \Magestore\Webpos\Api\Data\Cart\QuoteInterface
     * @throws \Exception
     */
    public function removeCart($quoteId);

    /**
     * @param string $quoteId
     * @param string $shippingMethod
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function saveShippingMethod($quoteId, $shippingMethod);

    /**
     * @param string $quoteId
     * @param string $paymentMethod
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function savePaymentMethod($quoteId, $paymentMethod);

    /**
     * @param string $quoteId
     * @param \Magestore\Webpos\Api\Data\Cart\QuoteDataInterface $quoteData
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function saveQuoteData($quoteId, $quoteData);

    /**
     * @param string $quoteId
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @param string $shippingMethod
     * @param \Magestore\Webpos\Api\Data\Cart\QuoteDataInterface $quoteData
     * @param \Magestore\Webpos\Api\Data\Cart\ActionInterface $actions
     * @param \Magestore\Webpos\Api\Data\Checkout\Integration\ModuleInterface[] $integration
     * @param \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface[] $extensionData
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface
     * @throws \Exception
     */
    public function placeOrder($quoteId, $payment, $shippingMethod, $quoteData, $actions, $integration, $extensionData);

    /**
     * @param string $quoteId
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @return string
     * @throws \Exception
     */
    public function getPaymentRequest($quoteId, $payment);

    /**
     * @param string $itemId
     * @param string $quoteId
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function removeItemById($itemId, $quoteId);

    /**
     * @param string $quoteId
     * @param string $couponCode
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function applyCouponCode($quoteId, $couponCode);

    /**
     *
     * @param string $quoteId
     * @param string $code
     * @param string $amount
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     */
    public function applyGiftcard($quoteId, $code, $amount);

    /**
     *
     * @param string $quoteId
     * @param string $code
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     */
    public function removeGiftcard($quoteId, $code);

    /**
     * @param string $customerId
     * @param string $quoteId
     * @param string $currencyId
     * @param string $storeId
     * @param string $tillId
     * @param string[] $section
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     */
    public function getCartData($customerId, $quoteId, $currencyId, $storeId, $tillId, $section);

    /**
     *
     * @param string $quoteId
     * @param string $usedPoint
     * @param string $ruleId
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     */
    public function spendPoint($quoteId, $usedPoint, $ruleId);
}
