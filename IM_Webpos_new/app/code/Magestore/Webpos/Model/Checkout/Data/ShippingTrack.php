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
class ShippingTrack extends \Magento\Framework\Model\AbstractExtensibleModel implements \Magestore\Webpos\Api\Data\Checkout\ShippingTrackInterface
{
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCarrierCode(){
        return $this->getData(self::KEY_CODE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCarrierCode($code){
        return $this->setData(self::KEY_CODE, $code);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getNumber(){
        return $this->getData(self::KEY_NUMBER);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setNumber($number){
        return $this->setData(self::KEY_NUMBER, $number);
    }
    
        /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getTitle(){
        return $this->getData(self::KEY_TITLE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setTitle($title){
        return $this->setData(self::KEY_TITLE, $title);
    }
}