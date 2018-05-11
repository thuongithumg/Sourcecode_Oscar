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
class CartItemOption extends \Magento\Framework\Model\AbstractExtensibleModel implements \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface
{
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCode(){
        return $this->getData(self::KEY_CODE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCode($code){
        return $this->setData(self::KEY_CODE, $code);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getValue(){
        return $this->getData(self::KEY_VALUE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setValue($value){
        return $this->setData(self::KEY_VALUE, $value);
    }
}