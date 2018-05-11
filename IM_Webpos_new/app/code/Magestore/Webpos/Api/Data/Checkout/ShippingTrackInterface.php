<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Checkout;


/**
 * Interface ShippingTrackInterface
 * @package Magestore\Webpos\Api\Data\Checkout
 */
interface ShippingTrackInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for field names
     */
    const KEY_CODE = 'carrier_code';
    const KEY_TITLE = 'title';
    const KEY_NUMBER = 'number';
    /**#@-*/
    
    /**
     * Returns the carrier code.
     *
     * @return string carrier code. Otherwise, null.
     */
    public function getCarrierCode();

    /**
     * Sets the carrier code.
     *
     * @param string $code
     * @return $this
     */
    public function setCarrierCode($code);
    
    /**
     * Returns the track number.
     *
     * @return string track number. Otherwise, null.
     */
    public function getNumber();

    /**
     * Sets the track number.
     *
     * @param string $number
     * @return $this
     */
    public function setNumber($number);
    
    /**
     * Returns the shipping title.
     *
     * @return string title. Otherwise, null.
     */
    public function getTitle();

    /**
     * Sets the shipping title.
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

}
