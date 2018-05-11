<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposBambora\Helper;


/**
 * class \Magestore\WebposBambora\Helper\Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     *
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        $this->_storeManager = $storeManager;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct($context);
    }

    /**
     * get store
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore(){
        return $this->_storeManager->getStore();
    }

    /**
     * get store config
     *
     * @param string $path
     * @return string
     */
    public function getStoreConfig($path){
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * get class name
     *
     * @params string $class
     * @param $class
     * @return mixed
     */
    public function getModel($class){
        return $this->_objectManager->get($class);
    }


    /**
     * @return bool
     */
    public function isEnableBambora(){
        $enable = $this->getStoreConfig('webpos/payment/bambora/enable');
        return ($enable == 1)?true:false;
    }

    /**
     * @return bool
     */
    public function getTimeoutSession(){
        return $this->getStoreConfig('webpos/payment/bambora/timeout');
    }
}