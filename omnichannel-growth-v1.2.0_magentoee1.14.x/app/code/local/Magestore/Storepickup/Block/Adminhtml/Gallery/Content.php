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
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Catalog product form gallery content
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magestore_Storepickup_Block_Adminhtml_Gallery_Content extends Mage_Adminhtml_Block_Widget {

    /**
     * Magestore_Storepickup_Block_Adminhtml_Gallery_Content constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    protected function _prepareLayout() {
        $version = Mage::getVersion();
        if (version_compare($version, '1.9.3', '<')){
            $this->setChild('uploader', $this->getLayout()->createBlock('adminhtml/media_uploader'));
            $this->getUploader()->getConfig()
                ->setUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/storepickup_gallery/upload'))
                ->setFileField('image')
                ->setFilters(array('images' => array(
                    'label' => Mage::helper('adminhtml')->__('Images (.gif, .jpg, .png)'),
                    'files' => array('*.gif', '*.jpg', '*.jpeg', '*.png')
                )));
        }else{
            $this->setChild('uploader', $this->getLayout()->createBlock('uploader/multiple'));
            $this->getUploader()->getUploaderConfig()
                ->setFileParameterName('image')
                ->setTarget(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/storepickup_gallery/upload'))
                ->setFilters(array('images' => array(
                    'label' => Mage::helper('adminhtml')->__('Images (.gif, .jpg, .png)'),
                    'files' => array('*.gif', '*.jpg', '*.jpeg', '*.png')
                )));
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrive uploader block
     *
     * @return Mage_Adminhtml_Block_Media_Uploader
     */
    public function getUploader() {
        return $this->getChild('uploader');
    }

    /**
     * Retrive uploader block html
     *
     * @return string
     */
    public function getUploaderHtml() {
        return $this->getChildHtml('uploader');
    }

    /**
     * @return string
     */
    public function getJsObjectName() {
        $version = Mage::getVersion();
        if (version_compare($version, '1.9.3', '<')){
            return $this->getUploader()->getJsObjectName();
        }else{
            return $this->getHtmlId() . 'JsObject';
        }
    }

    /**
     * @return mixed
     */
    public function getAddImagesButton() {
        return $this->getButtonHtml(
                        Mage::helper('storepickup')->__('Add New Images'), $this->getJsObjectName() . '.showUploader()', 'add', $this->getHtmlId() . '_add_images_button'
        );
    }

    /**
     * @return mixed
     */
    public function getImagesJson() {
        $id = $this->getRequest()->getParam('id');
        $collections = Mage::getModel('storepickup/image')->getCollection()->addFilter('store_id', $id);
        $collections->setOrder('options', 'ASC');
        $image = array();
        $i = 0;
        foreach ($collections as $obj) {
            $image[$i]['value_id'] = $obj->getImageId();
            $image[$i]['file'] = $obj->getName();
            $image[$i]['label'] = '';
            $image[$i]['position'] = $obj->getOptions();
            $image[$i]['disabled'] = '';
            $image[$i]['label_default'] = '';
            $image[$i]['position_default'] = '';
            $image[$i]['base_default'] = '';
            $image[$i]['url'] = Mage::getSingleton('storepickup/system_config_upload')->getMediaUrl($obj->getName());
            $i++;
        }
        return Mage::helper('core')->jsonEncode($image);
    }

    /**
     * @return mixed
     */
    public function getImagesValuesJson() {
        $values = array();
         $id = $this->getRequest()->getParam('id');
          $collections = Mage::getModel('storepickup/image')->getCollection()
                  ->addFilter('store_id', $id)
                  ->addFilter('statuses', 1);
           foreach ($collections as $obj) {
               $values['base'] = $obj->getName();
           }
        return Mage::helper('core')->jsonEncode($values);
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getImageTypes() {
        return array('base' => array('label' => 'Base', 'field' => 'storepickup[base_image]'));
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getMediaAttributes() {
        return array();
        //return $this->getElement()->getDataObject()->getMediaAttributes();
    }

    /**
     * @return mixed
     */
    public function getImageTypesJson() {
        return Mage::helper('core')->jsonEncode($this->getImageTypes());
    }

}
