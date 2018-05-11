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
 * Interface Magestore_Webpos_Api_Checkout_PaymentInterface
 */
interface Magestore_Webpos_Api_Checkout_PaymentInterface
{
    /**#@+
     * Constants for field names
     */
    const METHOD = 'method';
    const DATA = 'method_data';
    const ADDRESS = 'address';
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
