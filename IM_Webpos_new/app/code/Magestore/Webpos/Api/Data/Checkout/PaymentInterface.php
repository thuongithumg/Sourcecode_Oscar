<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Checkout;


/**
 * Interface PaymentInterface
 * @package Magestore\Webpos\Api\Data\Checkout
 */
interface PaymentInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for field names
     */
    const KEY_METHOD = 'method';
    const KEY_DATA = 'method_data';
    const KEY_ADDRESS = 'address';
    /**#@-*/
    
    /**
     * Returns the payment method.
     *
     * @return string method. Otherwise, null.
     */
    public function getMethod();

    /**
     * Sets the payment method.
     *
     * @param string $method
     * @return $this
     */
    public function setMethod($method);
        
    /**
     * Sets the payments data.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentItemInterface[] $data
     * @return $this
     */
    public function setMethodData($data);
    
    /**
     * Returns the payments data.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\PaymentItemInterface[] data. Otherwise, null.
     */
    public function getMethodData();
    
    /**
     * Sets the billing address.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\AddressInterface $address
     * @return $this
     */
    public function setAddress($address);
    
    /**
     * Returns the billing address.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\AddressInterface address. Otherwise, null.
     */
    public function getAddress();


}
