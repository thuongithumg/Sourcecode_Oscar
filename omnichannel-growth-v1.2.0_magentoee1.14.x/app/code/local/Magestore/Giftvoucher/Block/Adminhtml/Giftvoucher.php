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
 * Class Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher
 */
class Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher extends Mage_Adminhtml_Block_Widget_Grid_Container {

    /**
     * Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher constructor.
     */
        public function __construct() {
        $this->_controller = 'adminhtml_giftvoucher';
        $this->_blockGroup = 'giftvoucher';
        $this->_headerText = Mage::helper('giftvoucher')->__('Gift Code Manager');
        $this->_addButtonLabel = Mage::helper('giftvoucher')->__('Add Gift Code');
        parent::__construct();
        $this->_addButton('import_giftvoucher', array(
            'label' => Mage::helper('giftvoucher')->__('Import Gift Codes'),
            'onclick' => "setLocation('{$this->getUrl('*/*/import')}')",
            'class' => 'add'
                ), -1);
    }
}