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
 * Class Magestore_Giftvoucher_Block_Adminhtml_Giftproduct_Edit
 */
class Magestore_Giftvoucher_Block_Adminhtml_Giftproduct_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    /**
     * Magestore_Giftvoucher_Block_Adminhtml_Giftproduct_Edit constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'giftvoucher';
        $this->_controller = 'adminhtml_giftproduct';

        $this->_removeButton('save');
        $this->_removeButton('delete');
    }

    /**
     * @return string
     */
    public function getHeaderText() {
        return Mage::helper('giftvoucher')->__('Add New Gift Card Product');
    }

}