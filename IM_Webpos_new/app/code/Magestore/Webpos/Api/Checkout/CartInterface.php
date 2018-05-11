<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Checkout;

use Magento\Quote\Model\Quote;

/**
 * Interface CartInterface
 * @package Magestore\Webpos\Api\Checkout
 */
interface CartInterface
{
    /**
     * Add product to shopping cart (quote)
     *
     * @param int|\Magento\Catalog\Model\Product $productInfo
     * @param string[]|float|int|\Magento\Framework\DataObject|null $requestInfo
     * @return $this
     */
    public function addProduct($productInfo, $requestInfo = null);

    /**
     * Save cart
     *
     * @return $this
     * @abstract
     */
    public function saveQuote();

    /**
     * Associate quote with the cart
     *
     * @param Quote $quote
     * @return $this
     * @abstract
     */
    public function setQuote(Quote $quote);

    /**
     * Get quote object associated with cart
     *
     * @return Quote
     * @abstract
     */
    public function getQuote();

    /**
     *
     * @param string $customerId
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemInterface[] $items
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingInterface $shipping
     * @param \Magestore\Webpos\Api\Data\Checkout\ConfigInterface  $config
     * @param string $couponCode
     * @param \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface[] $extensionData
     * @param \Magestore\Webpos\Api\Data\Checkout\SessionDataInterface[] $sessionData
     * @param \Magestore\Webpos\Api\Data\Checkout\Integration\ModuleInterface[] $integration
     * @return string
     * @throws \Exception
     */
    public function submitOrder($customerId, $items, $payment, $shipping, $config, $couponCode = "", $extensionData, $sessionData, $integration);

    /**
     *
     * @param string $quoteId
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingInterface $shipping
     * @param string $couponCode
     * @param \Magestore\Webpos\Api\Data\Checkout\ConfigInterface $config
     * @param \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface[] $extensionData
     * @param \Magestore\Webpos\Api\Data\Checkout\SessionDataInterface[] $sessionData
     * @param \Magestore\Webpos\Api\Data\Checkout\Integration\ModuleInterface[] $integration
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface
     * @throws \Exception
     */
    public function submitQuote($quoteId, $payment, $shipping, $couponCode, $config, $extensionData, $sessionData, $integration);

        /**
     *
     * @param string $customerId
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemInterface[] $items
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingInterface $shipping
     * @param \Magestore\Webpos\Api\Data\Checkout\ConfigInterface  $config
     * @param string $couponCode
     * @param \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface[] $extensionData
     * @param \Magestore\Webpos\Api\Data\Checkout\SessionDataInterface[] $sessionData
     * @param \Magestore\Webpos\Api\Data\Checkout\Integration\ModuleInterface[] $integration
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface
     * @throws \Exception
     */
    public function prepareOrder($customerId, $items, $payment, $shipping, $config, $couponCode = "", $extensionData, $sessionData, $integration);
    
    /**
     *
     * @param string $customerId
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemInterface[] $items
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingInterface $shipping
     * @param \Magestore\Webpos\Api\Data\Checkout\ConfigInterface  $config
     * @param string $couponCode
     * @return string
     */
    public function checkPromotion($customerId, $items, $payment, $shipping, $config, $couponCode = "");

    /**
     *
     * @param string $customerId
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemInterface[] $items
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingInterface $shipping
     * @param \Magestore\Webpos\Api\Data\Checkout\ConfigInterface  $config
     * @param string $couponCode
     * @return string
     */
    public function checkGiftcard($customerId, $items, $payment, $shipping, $config, $couponCode = "");
    
    /**
     *
     * @param string $customerId
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemInterface[] $items
     * @param string $zipcode
     * @param string $country
     * @return string
     */
    public function getShippingRates($customerId, $items, $zipcode = "", $country = "");
    
    /**
     *
     * @param string $incrementId
     * @param string $email
     * @return string
     */
    public function sendEmail($incrementId, $email);

    /**
     *
     * @param string $orderIncrementId
     * @return string
     */
    public function unholdOrder($orderIncrementId);

}
