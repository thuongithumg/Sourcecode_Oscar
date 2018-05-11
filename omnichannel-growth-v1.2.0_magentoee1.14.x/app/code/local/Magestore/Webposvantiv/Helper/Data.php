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
 * @package     Magestore_Webposvantiv
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Webposvantiv Helper
 * 
 * @category    Magestore
 * @package     Magestore_Webposvantiv
 * @author      Magestore Developer
 */
class Magestore_Webposvantiv_Helper_Data extends Mage_Core_Helper_Abstract
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
     * get vantiv config by key
     *
     * @param $key
     * @return mixed
     */
    public function getVantivConfig($key)
    {
        return $this->getConfig('payment/vantiv_' . $key);
    }

    /**
     * is enable vantiv
     *
     * @return boolean
     */
    public function isEnableVantiv()
    {
        return $this->getConfig('payment/vantiv_enable');
    }

}