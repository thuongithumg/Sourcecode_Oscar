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
 * @package     Magestore_Barcodesuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 *
 */
class Magestore_Barcodesuccess_Block_Adminhtml_Import_Notice extends
    Mage_Adminhtml_Block_Template
{
    /**
     * @return Mage_Core_Block_Abstract
     */
    public function _prepareLayout()
    {
        $this->setTemplate('barcodesuccess/notice/message.phtml');
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
        return $this->getUrl('adminhtml/barcodesuccess_barcode/downloadInvalidCsv');
    }

//    /**
//     * get unvalid url
//     *
//     * @return string
//     */
//    public function getInvalidFileCsvFileName()
//    {
//        return 'import_product_to_barcode.csv';
//    }
}

