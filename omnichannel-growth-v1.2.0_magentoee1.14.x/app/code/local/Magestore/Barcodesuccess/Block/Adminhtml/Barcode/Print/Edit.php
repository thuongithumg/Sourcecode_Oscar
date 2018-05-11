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

/**
 * Barcodesuccess Edit Block
 *
 * @category     Magestore
 * @package     Magestore_Barcodesuccess
 * @author      Magestore Developer
 */
class Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Print_Edit extends
    Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Print_Edit constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->_objectId   = 'print_id';
        $this->_blockGroup = 'barcodesuccess';
        $this->_controller = 'adminhtml_barcode_print';

        $this->_updateButton('save', 'label', Mage::helper('barcodesuccess')->__('Print'));
        $this->_updateButton('save', 'onclick', 'printForm.print()');
        $this->_updateButton('save', 'class', 'task');
        $this->_removeButton('back');
        $this->_removeButton('reset');
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('barcodesuccess')->__('Barcode Print Configuration');
    }
}