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

class Magestore_Webpos_Model_Checkout_Data_Integration_Module extends Magestore_Webpos_Model_Abstract implements Magestore_Webpos_Api_Checkout_Integration_ModuleInterface
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