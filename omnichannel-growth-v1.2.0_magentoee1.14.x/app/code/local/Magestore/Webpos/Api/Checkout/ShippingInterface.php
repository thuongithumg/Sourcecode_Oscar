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
 * Interface Magestore_Webpos_Api_Checkout_ShippingInterface
 */
interface Magestore_Webpos_Api_Checkout_ShippingInterface
{
    /**#@+
     * Constants for field names
     */
    const KEY_METHOD = 'method';
    const KEY_TRACKS = 'tracks';
    const KEY_ADDRESS = 'address';
    const KEY_DATETIME = 'datetime';
    /**#@-*/
    
    /**
     * Returns the shipping method.
     *
     * @return string method. Otherwise, null.
     */
    public function getMethod();

    /**
     * Sets the shipping method.
     *
     * @param string $method
     * @return $this
     */
    public function setMethod($method);
        
    /**
     * Sets the track data.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingTrackInterface[] $tracks
     * @return $this
     */
    public function setTracks($tracks);
    
    /**
     * Returns the track data.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\ShippingTrackInterface[] $tracks. Otherwise, null.
     */
    public function getTracks();

    /**
     * Sets the shipping address.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\AddressInterface $address
     * @return $this
     */
    public function setAddress($address);
    
    /**
     * Returns the shipping address.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\AddressInterface address. Otherwise, null.
     */
    public function getAddress();

    /**
     * Returns the date time.
     *
     * @return string. Otherwise, null.
     */
    public function getDatetime();

    /**
     * Sets the date time.
     *
     * @param array $datetime
     * @return $this
     */
    public function setDatetime($datetime);
}
