<?php

/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Suppliersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 *
 */
class Magestore_Suppliersuccess_Block_Adminhtml_Pricelist_Notice extends
    Mage_Adminhtml_Block_Template
{
    /**
     * @return Mage_Core_Block_Abstract
     */
    public function _prepareLayout()
    {
        $this->setTemplate('suppliersuccess/pricelist/notice.phtml');
        return parent::_prepareLayout();
    }

    /**
     * get admin session
     *
     * @return mixed
     */
    public function getAdminSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }


    /**
     * get number of invalid skus
     *
     * @return mixed
     */
    public function getNumberSkuInvalid()
    {
        return $this->getAdminSession()->getData('sku_invalid', true);
    }

    /**
     * check error
     *
     * @return mixed
     */
    public function isHasError()
    {
        return $this->getAdminSession()->getData('error_import', true);
    }

    /**
     * get unvalid url
     *
     * @return string
     */
    public function getInvalidFileCsvUrl()
    {
        return $this->getUrl('adminhtml/suppliersuccess_pricelist/downloadInvalidCsv');
    }

}

