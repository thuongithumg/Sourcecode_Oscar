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
class ExtensionData extends \Magento\Framework\Model\AbstractExtensibleModel implements \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface
{
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getKey(){
        return $this->getData(self::KEY_FIELD_KEY);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setKey($key){
        return $this->setData(self::KEY_FIELD_KEY, $key);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getValue(){
        return $this->getData(self::KEY_FIELD_VALUE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setValue($value){
        return $this->setData(self::KEY_FIELD_VALUE, $value);
    }
}