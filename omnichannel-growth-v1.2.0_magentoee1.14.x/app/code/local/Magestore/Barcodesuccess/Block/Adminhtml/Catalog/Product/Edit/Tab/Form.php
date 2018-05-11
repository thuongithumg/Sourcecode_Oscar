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

class Magestore_Barcodesuccess_Block_Adminhtml_Catalog_Product_Edit_Tab_Form extends
    Mage_Adminhtml_Block_Widget_Form
{

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('barcodesuccess_form', array(
            'legend' => Mage::helper('barcodesuccess')->__('Barcode Print Configuration'),
        ));
        $fieldset->addField('template_id', 'select', array(
            'label'    => Mage::helper('barcodesuccess')->__('Select Barcode Template'),
            'name'     => 'template_id',
            'values'   => Magestore_Coresuccess_Model_Service::barcodeTemplateService()->getTemplateOptionArray(),
            'onchange' => 'productBarcodeForm.loadPreview();',
        ));
//        $fieldset->addField('buttons', 'note', array(
//            'label' => Mage::helper('barcodesuccess')->__('Preview'),
//            'text'  => "
//                        <span class='form-button' onclick='productBarcodeForm.loadPreview();'>Preview</span>
//                        ",
//        ));
        $fieldset->addField('preview', 'note', array(
            'label' => Mage::helper('barcodesuccess')->__(' '),
            'text'  => "
            <div class='barcode_preview_container'></div>
            ",
        ));
        return parent::_prepareForm();
    }

}