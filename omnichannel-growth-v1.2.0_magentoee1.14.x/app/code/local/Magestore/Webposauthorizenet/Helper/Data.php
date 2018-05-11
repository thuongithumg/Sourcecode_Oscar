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
 * @package     Magestore_Webposauthorizenet
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Webposauthorizenet Helper
 * 
 * @category    Magestore
 * @package     Magestore_Webposauthorizenet
 * @author      Magestore Developer
 */
class Magestore_Webposauthorizenet_Helper_Data extends Mage_Core_Helper_Abstract
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
     * get authorizenet config by key
     *
     * @param $key
     * @return mixed
     */
    public function getAuthorizenetConfig($key)
    {
        return $this->getConfig('payment/authorizenet_'.$key);
    }

    /**
     * is enable authorizenet
     *
     * @return boolean
     */
    public function isEnableAuthorizenet()
    {
        return $this->getConfig('payment/authorizenet_enable');
    }

    /**
     * is allow authorizenet here
     *
     * @return boolean
     */
    public function isAllowAuthorizenetHere()
    {
        return $this->getConfig('payment/authorizenet_enable_authorizenethere');
    }
}