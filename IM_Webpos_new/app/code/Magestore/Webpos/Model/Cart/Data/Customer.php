<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Cart\Data;

/**
 * Class Customer
 * @package Magestore\Webpos\Model\Cart\Data
 */
class Customer extends \Magento\Framework\Model\AbstractExtensibleModel implements \Magestore\Webpos\Api\Data\Cart\CustomerInterface
{
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getBillingAddress()
    {
        return $this->getData(self::BILLIBNG_ADDRESS);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setBillingAddress($billingAddress)
    {
        return $this->setData(self::BILLIBNG_ADDRESS, $billingAddress);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getShippingAddress()
    {
        return $this->getData(self::SHIPPING_ADDRESS);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setShippingAddress($shippingAddress)
    {
        return $this->setData(self::SHIPPING_ADDRESS, $shippingAddress);
    }

}