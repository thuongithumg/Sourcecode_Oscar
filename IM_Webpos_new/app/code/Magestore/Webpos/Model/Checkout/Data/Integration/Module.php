<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Checkout\Data\Integration;

/**
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Module extends \Magento\Framework\Model\AbstractExtensibleModel implements \Magestore\Webpos\Api\Data\Checkout\Integration\ModuleInterface
{

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getModule(){
        return $this->getData(self::KEY_MODULE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setModule($module){
        return $this->setData(self::KEY_MODULE, $module);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getOrderData(){
        return $this->getData(self::KEY_ORDER_DATA);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setOrderData($orderData){
        return $this->setData(self::KEY_ORDER_DATA, $orderData);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getExtensionData(){
        return $this->getData(self::KEY_EXTENSION_DATA);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setExtensionData($extensionData){
        return $this->setData(self::KEY_EXTENSION_DATA, $extensionData);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getEventName(){
        return $this->getData(self::KEY_EVENT_NAME);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setEventName($eventName){
        return $this->setData(self::KEY_EVENT_NAME, $eventName);
    }

}