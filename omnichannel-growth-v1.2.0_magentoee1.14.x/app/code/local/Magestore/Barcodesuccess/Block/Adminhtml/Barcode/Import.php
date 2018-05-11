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
 *   @category    Magestore
 *   @package     Magestore_Barcodesuccess
 *   @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

class Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Import extends
    Mage_Adminhtml_Block_Template
{
    /**
     * Get adjust stock csv sample link
     *
     * @return mixed
     */
    public function getCsvSampleLink()
    {
        $url = $this->getUrl('adminhtml/barcodesuccess_barcode/downloadsample',
                             array('_secure' => true));
        return $url;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->__("Please choose a CSV file to import barcode. You can download this sample CSV file");
    }

    /**
     * Get import urk
     *
     * @return mixed
     */
    public function getImportLink()
    {
        return $this->getUrl('adminhtml/barcodesuccess_barcode/import',
                             array(
                                 '_secure' => true,
                             ));
    }

    /**
     * Get import title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->__("Import products");
    }

    /**
     *
     */
    public function oneBarcodePerSku()
    {
        return Mage::helper('barcodesuccess')->isOneBarcodePerSku();
    }
}