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
class Magestore_Webpos_Adminhtml_WebposController extends Mage_Adminhtml_Controller_Action
{
    /**
     * index action
     */
    public function indexAction()
    {
        $url = Mage::app()->getDefaultStoreView()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true);
        $websites = Mage::app()->getWebsites();
        $defaultWebsite = Mage::getStoreConfig('webpos/general/webpos_website');
        if (count($websites) > 1 && $defaultWebsite){
            foreach ($websites as $website){
                if($website->getCode() == $defaultWebsite){
                    $url = $website->getDefaultStore()->getBaseUrl();
                }
            }
        }
        $this->_redirectUrl(rtrim($url, '/').'/webpos');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/webpos/gotopos');
    }
}