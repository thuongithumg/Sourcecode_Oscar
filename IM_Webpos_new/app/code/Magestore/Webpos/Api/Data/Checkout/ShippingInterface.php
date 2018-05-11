<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Checkout;


/**
 * Interface ShippingInterface
 * @package Magestore\Webpos\Api\Data\Checkout
 */
interface ShippingInterface extends \Magento\Framework\Api\ExtensibleDataInterface
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
     * @param string[] $datetime
     * @return $this
     */
    public function setDatetime($datetime);
}
