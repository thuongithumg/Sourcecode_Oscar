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
 * @package     Magestore_Webposstripe
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Webposstripe Helper
 * 
 * @category    Magestore
 * @package     Magestore_Webposstripe
 * @author      Magestore Developer
 */
class Magestore_Webposstripe_Helper_Data extends Mage_Core_Helper_Abstract
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
     * get stripe config by key
     *
     * @param $key
     * @return mixed
     */
    public function getStripeConfig($key)
    {
        return $this->getConfig('payment/stripe_'.$key);
    }

    /**
     * is enable stripe
     *
     * @return boolean
     */
    public function isEnableStripe()
    {
        return $this->getConfig('payment/stripe_enable');
    }

    /**
     * is allow stripe here
     *
     * @return boolean
     */
    public function isAllowStripeHere()
    {
        return $this->getConfig('payment/stripe_enable_stripehere');
    }

    public function isZeroDecimal($currency)
    {
        return in_array(strtolower($currency), array(
            'bif', 'djf', 'jpy', 'krw', 'pyg', 'vnd', 'xaf',
            'xpf', 'clp', 'gnf', 'kmf', 'mga', 'rwf', 'vuv', 'xof'));
    }
}