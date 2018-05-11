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
class DataObject extends \Magento\Framework\Model\AbstractExtensibleModel implements \Magestore\Webpos\Api\Data\Checkout\DataObjectInterface
{
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getId(){
        return $this->getData(self::KEY_ID);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setId($id){
        return $this->setData(self::KEY_ID, $id);
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