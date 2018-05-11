<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Checkout\Data;

/**
 * 
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Shipping extends \Magento\Framework\Model\AbstractExtensibleModel implements \Magestore\Webpos\Api\Data\Checkout\ShippingInterface
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