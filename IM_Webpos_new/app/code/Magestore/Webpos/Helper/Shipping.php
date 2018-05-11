<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Helper;

use \Magento\Store\Model\ScopeInterface;

/**
 * class \Magestore\Webpos\Helper\Shipping
 *
 * Web POS Shipping helper
 * Methods:
 *  isAllowOnWebPOS
 *  getDefaultShippingMethod
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Shipping extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magestore\Webpos\Model\Transaction $modelTransaction
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magestore\Webpos\Model\Transaction $modelTransaction
    ) {
        $this->_modelTransaction = $modelTransaction;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct($context);
    }

    /**
     * check shipping method for pos
     *
     * @param string $code
     * @return boolean
     */
    public function isAllowOnWebPOS($code)
    {
        if ($this->scopeConfig->getValue('webpos/shipping/allowspecific_shipping', ScopeInterface::SCOPE_STORE) == '1') {
            $specificshipping = $this->scopeConfig->getValue('webpos/shipping/specificshipping', ScopeInterface::SCOPE_STORE);
            $specificshipping = explode(',', $specificshipping);
            if (in_array($code, $specificshipping)) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * get default shipping method for pos
     *
     * @return string
     */
    public function getDefaultShippingMethod()
    {
        return $this->scopeConfig->getValue('webpos/shipping/defaultshipping', ScopeInterface::SCOPE_STORE);
    }

    /**
     * get all shipping method
     *
     * @return string
     */
    public function getShippingCarriers()
    {
        return $this->scopeConfig->getValue('carriers');
    }

    /**
     *
     * @param string $path
     * @return string
     */
    public function getStoreConfig($path){
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }


}
