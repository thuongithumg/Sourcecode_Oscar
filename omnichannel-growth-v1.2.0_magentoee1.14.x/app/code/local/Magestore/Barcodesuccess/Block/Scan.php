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
class Magestore_Barcodesuccess_Block_Scan extends
    Mage_Core_Block_Template
{
    /**
     * @return Mage_Core_Block_Abstract
     */
    public function _prepareLayout()
    {
        $this->setTemplate('barcodesuccess/scan.phtml');
        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return Mage::helper('barcodesuccess')->__("Scan Barcode");
    }

    /**
     * @return string
     */
    public function getBarcodesJSON()
    {

        return json_encode($this->getBarcodes());
    }

    /**
     * @return stdClass
     */
    public function getBarcodes()
    {
        $barcodes = Mage::getSingleton('adminhtml/session')->getData('scan_barcodes');
        if ( $barcodes ) {
            return $barcodes;
        }
        return new stdClass();
    }


}