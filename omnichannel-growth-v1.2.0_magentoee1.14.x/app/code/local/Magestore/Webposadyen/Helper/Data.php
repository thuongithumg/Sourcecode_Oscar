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
 * @package     Magestore_Webposadyen
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Webposadyen Helper
 * 
 * @category    Magestore
 * @package     Magestore_Webposadyen
 * @author      Magestore Developer
 */
class Magestore_Webposadyen_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * get config by path
     *
     * @param $path
     * @return mixed
     */
    public function getConfig($path)
    {
        return Mage::getStoreConfig('webpos/' . $path, Mage::app()->getStore()->getId());
    }

    /**
     * get adyen config by key
     *
     * @param $key
     * @return mixed
     */
    public function getAdyenConfig($key)
    {
        return $this->getConfig('payment/adyen_' . $key);
    }

    /**
     * is enable adyen
     *
     * @return boolean
     */
    public function isEnableAdyen()
    {
        return $this->getConfig('payment/adyen_enable');
    }

}