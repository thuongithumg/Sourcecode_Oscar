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
 * Barcodesuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @author      Magestore Developer
 */
class Magestore_Barcodesuccess_Block_Adminhtml_Barcode extends
    Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Magestore_Barcodesuccess_Block_Adminhtml_Barcode constructor.
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_barcode';
        $this->_blockGroup = 'barcodesuccess';
        $this->_headerText = Mage::helper('barcodesuccess')->__('Barcode Listing');
        $this->_addButton('import', array(
            'label'   => Mage::helper('barcodesuccess')->__('Import Barcode'),
            'onclick' => "jQuery('#barcode-import').modal()",
            'class'   => 'add',
        ));
        $this->_addButton('generate', array(
            'label'   => Mage::helper('barcodesuccess')->__('Generate Barcode'),
            'onclick' => "jQuery('#barcode-generate').modal()",
            'class'   => 'add',
        ));
        $this->_addButton('update', array(
            'label'   => Mage::helper('barcodesuccess')->__('Update Barcode'),
            'onclick' => "barcodeGrid.massUpdate();",
            'class'   => 'save',
        ));
        $this->_addButton('print', array(
            'label'   => Mage::helper('barcodesuccess')->__('Print Barcode'),
            'onclick' => "barcodeGrid.massPrint();",
            'class'   => 'task',
        ));
        $this->_addButton('delete', array(
            'label'   => Mage::helper('barcodesuccess')->__('Delete Barcode'),
            'onclick' => "barcodeGrid.massDelete();",
            'class'   => 'delete',
        ));
        parent::__construct();
        $this->_removeButton('add');
    }

    /**
     * add action after load page.
     * @return string
     */
    public function _toHtml()
    {
        $actionCode = '';
        if ( $this->getRequest()->getParam('action') == 'generate' ) {
            $actionCode = "<script type='text/javascript'>jQuery(function(){jQuery('#barcode-generate').modal();})</script>";
        }
        if ( $this->getRequest()->getParam('action') == 'import' ) {
            $actionCode = "<script type='text/javascript'>jQuery(function(){jQuery('#barcode-import').modal();})</script>";
        }
        return parent::_toHtml() . $actionCode;
    }
}