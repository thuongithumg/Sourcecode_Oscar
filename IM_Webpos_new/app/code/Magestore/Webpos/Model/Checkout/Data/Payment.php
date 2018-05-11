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
class Payment extends \Magento\Framework\Model\AbstractExtensibleModel implements \Magestore\Webpos\Api\Data\Checkout\PaymentInterface
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
    public function getMethodData(){
        return $this->getData(self::KEY_DATA);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setMethodData($data){
        return $this->setData(self::KEY_DATA, $data);
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
}