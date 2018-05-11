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

/**
 * Webpos Helper
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @author      Magestore Developer
 */
class Magestore_Webpos_Helper_Shipping extends Mage_Core_Helper_Abstract {

    public function isAllowOnWebPOS($code) {
        $specificshipping = Mage::getStoreConfig('webpos/shipping/specificshipping', Mage::app()->getStore()->getId());
        $specificshipping = explode(',', $specificshipping);
        $defaultShipping = $this->getDefaultShippingMethod();
        if (in_array($code, $specificshipping) || $defaultShipping == $code) {
            return true;
        } else {
            return false;
        }
        return true;
    }

    public function getDefaultShippingMethod() {
        return Mage::getStoreConfig('webpos/shipping/defaultshipping');
    }
}
