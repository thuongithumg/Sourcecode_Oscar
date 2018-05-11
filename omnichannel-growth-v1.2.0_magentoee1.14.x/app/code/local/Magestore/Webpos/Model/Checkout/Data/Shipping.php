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

class Magestore_Webpos_Model_Checkout_Data_Shipping extends Magestore_Webpos_Model_Abstract implements Magestore_Webpos_Api_Checkout_ShippingInterface
{
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getMethod(){
        return $this->getData(self::KEY_METHOD);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setMethod($method){
        return $this->setData(self::KEY_METHOD, $method);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getTracks(){
        return $this->getData(self::KEY_TRACKS);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setTracks($tracks){
        return $this->setData(self::KEY_TRACKS, $tracks);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getAddress(){
        return $this->getData(self::KEY_ADDRESS);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setAddress($address){
        return $this->setData(self::KEY_ADDRESS, $address);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getDatetime(){
        return $this->getData(self::KEY_DATETIME);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setDatetime($datetime){
        return $this->setData(self::KEY_DATETIME, $datetime);
    }
}