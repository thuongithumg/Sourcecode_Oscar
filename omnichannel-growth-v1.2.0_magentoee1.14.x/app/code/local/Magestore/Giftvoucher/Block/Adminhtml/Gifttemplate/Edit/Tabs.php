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
 * Class Magestore_Giftvoucher_Block_Adminhtml_Gifttemplate_Edit_Tabs
 */
class Magestore_Giftvoucher_Block_Adminhtml_Gifttemplate_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    /**
     * Magestore_Giftvoucher_Block_Adminhtml_Gifttemplate_Edit_Tabs constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->setId('giftvoucher_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('giftvoucher')->__('Gift Card Template Information'));
    }

    /**
     * @return Mage_Core_Block_Abstract
     * @throws Exception
     */
    protected function _beforeToHtml() {

        $this->addTab('form_section', array(
            'label' => Mage::helper('giftvoucher')->__('General Information'),
            'title' => Mage::helper('giftvoucher')->__('General Information'),
            'content' => $this->getLayout()->createBlock('giftvoucher/adminhtml_gifttemplate_edit_tab_form')->toHtml(),
        ));

        $this->addTab('images_section', array(
            'label' => Mage::helper('giftvoucher')->__('Images'),
            'title' => Mage::helper('giftvoucher')->__('Images'),
            'content' => $this->getLayout()->createBlock('giftvoucher/adminhtml_gifttemplate_edit_tab_images')->toHtml(),
        ));


        return parent::_beforeToHtml();
    }

}
