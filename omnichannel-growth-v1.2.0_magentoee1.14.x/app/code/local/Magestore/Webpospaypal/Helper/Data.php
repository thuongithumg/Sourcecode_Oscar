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
 * @package     Magestore_Webpospaypal
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Webpospaypal Helper
 * 
 * @category    Magestore
 * @package     Magestore_Webpospaypal
 * @author      Magestore Developer
 */
class Magestore_Webpospaypal_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * get config by path
     *
     * @param $path
     * @return mixed
     */
    public function getConfig($path)
    {
        return Mage::getStoreConfig('webpos/'.$path, Mage::app()->getStore()->getId());
    }

    /**
     * get paypal config by key
     *
     * @param $key
     * @return mixed
     */
    public function getPaypalConfig($key)
    {
        return $this->getConfig('payment/paypal_'.$key);
    }

    /**
     * is enable paypal
     *
     * @return boolean
     */
    public function isEnablePaypal()
    {
        return $this->getConfig('payment/paypal_enable');
    }

    /**
     * is allow paypal here
     *
     * @return boolean
     */
    public function isAllowPaypalHere()
    {
        return $this->getConfig('payment/paypal_enable_paypalhere');
    }
}