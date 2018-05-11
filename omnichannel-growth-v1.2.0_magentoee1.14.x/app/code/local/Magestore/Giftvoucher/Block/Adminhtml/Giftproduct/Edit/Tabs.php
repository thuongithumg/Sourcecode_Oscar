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
 * Class Magestore_Giftvoucher_Block_Adminhtml_Giftproduct_Edit_Tabs
 */
class Magestore_Giftvoucher_Block_Adminhtml_Giftproduct_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    /**
     * Magestore_Giftvoucher_Block_Adminhtml_Giftproduct_Edit_Tabs constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->setId('giftproduct_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('giftvoucher')->__('Product Information'));
    }

    /**
     * @return Mage_Core_Block_Abstract
     * @throws Exception
     */
    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('giftvoucher')->__('Settings'),
            'title' => Mage::helper('giftvoucher')->__('Settings'),
            'content' => $this->getLayout()->createBlock('giftvoucher/adminhtml_giftproduct_edit_tab_form')->toHtml(),
        ));


        return parent::_beforeToHtml();
    }

}