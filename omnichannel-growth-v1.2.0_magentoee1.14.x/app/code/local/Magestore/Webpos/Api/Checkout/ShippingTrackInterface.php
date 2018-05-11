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
 * Interface Magestore_Webpos_Api_Checkout_ShippingTrackInterface
 */
interface Magestore_Webpos_Api_Checkout_ShippingTrackInterface
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
