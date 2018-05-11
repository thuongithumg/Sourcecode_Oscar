<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposVantiv\Helper;

/**
 * Class Data
 * @package Magestore\WebposVantiv\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     * @param string $path
     * @return string
     */
    public function getStoreConfig($path){
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isEnableVantiv(){
        $enable = $this->getStoreConfig('webpos/payment/vantiv/enable');
        return ($enable)?true:false;
    }

    /**
     * @param string $path
     * @param array $params
     * @return string
     */
    public function getUrl($path, $params = array()){
        return $this->_getUrl($path, $params);
    }
}