<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Cart;
/**
 * Interface BuyRequestInterface
 * @package Magestore\Webpos\Api\Data\Cart
 */
interface CustomerInterface
{
    /**#@+
     * Data key
     */
    const CUSTOMER_ID = 'customer_id';
    const BILLIBNG_ADDRESS = 'billing_address';
    const SHIPPING_ADDRESS = 'shipping_address';
    /**#@- */

    /**
     * @return string
     */
    public function getCustomerId();

    /**
     * @param string $customerId
     * @return string
     */
    public function setCustomerId($customerId);

    /**
     * @return \Magestore\Webpos\Api\Data\Checkout\AddressInterface
     */
    public function getBillingAddress();

    /**
     * @param \Magestore\Webpos\Api\Data\Checkout\AddressInterface $billingAddress
     * @return $this
     */
    public function setBillingAddress($billingAddress);

    /**
     * @return \Magestore\Webpos\Api\Data\Checkout\AddressInterface
     */
    public function getShippingAddress();

    /**
     * @param \Magestore\Webpos\Api\Data\Checkout\AddressInterface $shippingAddress
     * @return $this
     */
    public function setShippingAddress($shippingAddress);
}
