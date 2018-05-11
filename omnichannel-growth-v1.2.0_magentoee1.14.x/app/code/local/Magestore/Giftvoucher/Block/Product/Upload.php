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
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Giftvoucher Product Upload Block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */

class Magestore_Giftvoucher_Block_Product_Upload extends Mage_Adminhtml_Block_Media_Uploader
{

    /**
     * Magestore_Giftvoucher_Block_Product_Upload constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId($this->getId() . '_Uploader');
        $this->setTemplate('');
        $magentoVersion = Mage::getVersion();
        if (version_compare($magentoVersion, '1.9.3', '>=') || method_exists($this, 'getUploaderConfig')){
            $this->getUploaderConfig()->setUrl($this->getUrl('giftvoucher/index/customUpload'));
            $this->getUploaderConfig()->setParams();
            $this->getUploaderConfig()->setFileField('image');
            $this->getUploaderConfig()->setFilters(array(
                'images' => array(
                    'label' => Mage::helper('adminhtml')->__('Images (.gif, .jpg, .png)'),
                    'files' => array('*.gif', '*.jpg', '*.png')
                )
            ));
            $this->getUploaderConfig()->setWidth(32);
        }
        else {
            $this->getConfig()->setUrl($this->getUrl('giftvoucher/index/customUpload'));
            $this->getConfig()->setParams();
            $this->getConfig()->setFileField('image');
            $this->getConfig()->setFilters(array(
                'images' => array(
                    'label' => Mage::helper('adminhtml')->__('Images (.gif, .jpg, .png)'),
                    'files' => array('*.gif', '*.jpg', '*.png')
                )
            ));
            $this->getConfig()->setWidth(32);
        }

    }

    /**
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        $this->setChild(
            'delete_button', $this->getLayout()->createBlock('adminhtml/widget_button')
            ->addData(array(
                'id' => '{{id}}-delete',
                'class' => 'delete',
                'type' => 'button',
                'label' => Mage::helper('adminhtml')->__(''),
                'onclick' => $this->getJsObjectName() . '.removeFile(\'{{fileId}}\')',
                'style' => 'display:none'
            ))
        );
        return $this->getChildHtml('delete_button');
    }

    /**
     * @return string
     */
    public function getDataMaxSize()
    {
        $dataSize = Mage::helper('giftvoucher')->getInterfaceConfig('upload_max_size');
        if (is_nan($dataSize) || $dataSize <= 0) {
            $dataSize = 500;
        }
        return $dataSize;
    }

    /**
     * @return mixed
     */
    public function getDataMaxSizeInBytes(){
        if (version_compare(Mage::getVersion(), '1.9.3', '>=')){
            $maxSize = Mage::helper('uploader/file')->getDataMaxSizeInBytes();
        }else{
            $maxSize = ini_get('upload_max_filesize');
        }
        $dataSizeAllow = $this->getDataMaxSize() * 1024;

        if ($dataSizeAllow > $maxSize) {
            return $maxSize;
        } else {
            return $dataSizeAllow;
        }
    }
}
