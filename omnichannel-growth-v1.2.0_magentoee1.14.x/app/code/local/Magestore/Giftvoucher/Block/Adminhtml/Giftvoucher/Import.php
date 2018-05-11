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
 * Class Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher_Import
 */
class Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher_Import extends Mage_Adminhtml_Block_Widget_Form_Container {

    /**
     * Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher_Import constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->_blockGroup = 'giftvoucher';
        $this->_controller = 'adminhtml_giftvoucher';
        $this->_mode = 'import';
        $this->_updateButton('save', 'label', Mage::helper('giftvoucher')->__('Import'));
        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_addButton('print', array(
            'label' => Mage::helper('giftvoucher')->__('Import and Print'),
            'onclick' => "importAndPrint()",
            'class' => 'save'
                ), 100);

        $this->_formScripts[] = "
            function importAndPrint(){
             
//             $('edit_form').target = '_blank';
                editForm.submit('" . $this->getUrl('*/*/processImport', array(
                    'print' => 'true',
                )) . "');
               
            }
        ";
    }

    /**
     * @return string
     */
    public function getHeaderText() {
        return Mage::helper('giftvoucher')->__('Import Gift Codes');
    }

}