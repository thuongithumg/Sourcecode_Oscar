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
class ItemsInfoBuy extends \Magento\Framework\Model\AbstractExtensibleModel implements \Magestore\Webpos\Api\Data\Checkout\ItemsInfoBuyInterface
{
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getItems(){
        return $this->getData(self::KEY_ITEMS);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setItems($items){
        return $this->setData(self::KEY_ITEMS,$items);
    }
}