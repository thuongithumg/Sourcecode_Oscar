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
 * Barcodesuccess Edit Form Content Tab Block
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @author      Magestore Developer
 */
class Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Scan_Edit_Tab_Print extends
    Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('barcodesuccess_form', array(
            'legend' => Mage::helper('barcodesuccess')->__('Barcode Print Configuration'),
        ));

        $fieldset->addField('template_id', 'select', array(
            'label'  => Mage::helper('barcodesuccess')->__('Select Barcode Template'),
            'name'   => 'template_id',
            'values' => Magestore_Coresuccess_Model_Service::barcodeTemplateService()->getTemplateOptionArray(),
            'onchange' => 'barcodeForm.loadPreview();'
        ));

//        $fieldset->addField('buttons', 'note', array(
//            'label' => Mage::helper('barcodesuccess')->__('Preview'),
//            'text'  => "
//                        <span class='form-button' onclick='scanForm.loadPreview();'>Preview</span>
//                        ",
//        ));
        $fieldset->addField('preview', 'note', array(
            'label' => Mage::helper('barcodesuccess')->__(' '),
            'text'  => "
            <div class='barcode_preview_container'></div>
            ",
        ));

        $fieldset->addField('print_qty', 'text', array(
            'label'  => Mage::helper('barcodesuccess')->__('Qty to print'),
            'name'   => 'print_qty',
            'class' => 'validate-greater-than-zero required-entry',
        ));
        $fieldset->addField('print_button', 'note', array(
            'label' => Mage::helper('barcodesuccess')->__(' '),
            'text'  => "
                        <span class='form-button' onclick='scanForm.print();'>Print</span>
                        ",
        ));

        return parent::_prepareForm();
    }
}