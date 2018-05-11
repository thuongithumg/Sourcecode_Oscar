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
class Magestore_Barcodesuccess_Block_Adminhtml_Template_Edit_Tab_Form extends
    Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Magestore_Barcodesuccess_Block_Adminhtml_Template_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        if ( Mage::registry('template_data') ) {
            $data = Mage::registry('template_data')->getData();
        } else {
            $templateId = $this->getRequest()->getParam('id');
            $data       = Mage::getModel('barcodesuccess/template')->load($templateId)->getData();
        }
        $fieldset = $form->addFieldset('barcodesuccess_form', array(
            'legend' => Mage::helper('barcodesuccess')->__('Barcode Template Information'),
        ));
        $fieldset->addField('type', 'select', array(
            'label'    => Mage::helper('barcodesuccess')->__('Select Barcode Label Format'),
            'required' => true,
            'name'     => 'type',
            'values'   => Magestore_Barcodesuccess_Model_Source_Template_Type::toOptionArray(),
            'onchange' => 'templateForm.loadDefault();',
        ));
        $fieldset->addField('name', 'text', array(
            'required' => true,
            'label'    => Mage::helper('barcodesuccess')->__('Template Name'),
            'name'     => 'name',
        ));
        $fieldset->addField('status', 'select', array(
            'required' => true,
            'label'    => Mage::helper('barcodesuccess')->__('Status'),
            'name'     => 'status',
            'values'   => Magestore_Barcodesuccess_Model_Source_Template_Status::toOptionArray(),
        ));
        $fieldset->addField('symbology', 'select', array(
            'required' => true,
            'label'    => Mage::helper('barcodesuccess')->__('Barcode symbology'),
            'name'     => 'symbology',
            'values'   => Magestore_Barcodesuccess_Model_Source_Template_Symbology::toOptionArray(),
        ));
        $fieldset->addField('measurement_unit', 'select', array(
            'required' => true,
            'label'    => Mage::helper('barcodesuccess')->__('Measurement unit'),
            'name'     => 'measurement_unit',
            'values'   => Magestore_Barcodesuccess_Model_Source_Template_Measurement::toOptionArray(),
        ));
        $fieldset->addField('label_per_row', 'text', array(
            'required' => true,
            'label'    => Mage::helper('barcodesuccess')->__('Label per row'),
            'name'     => 'label_per_row',
            'note'     => 'Only use one label per row for jewelry template',
            'class'    => 'validate-number',
        ));
        $fieldset->addField('paper_width', 'text', array(
            'required' => true,
            'label'    => Mage::helper('barcodesuccess')->__('Paper width'),
            'name'     => 'paper_width',
            'class'    => 'validate-number',
        ));
        $fieldset->addField('paper_height', 'text', array(
            'required' => true,
            'label'    => Mage::helper('barcodesuccess')->__('Paper height'),
            'name'     => 'paper_height',
            'class'    => 'validate-number',
        ));
        $fieldset->addField('label_width', 'text', array(
            'required' => true,
            'label'    => Mage::helper('barcodesuccess')->__('Label width'),
            'name'     => 'label_width',
            'class'    => 'validate-number',
        ));
        $fieldset->addField('label_height', 'text', array(
            'required' => true,
            'label'    => Mage::helper('barcodesuccess')->__('Label height'),
            'name'     => 'label_height',
            'class'    => 'validate-number',
        ));
        $fieldset->addField('font_size', 'text', array(
            'required' => true,
            'label'    => Mage::helper('barcodesuccess')->__('Font size'),
            'name'     => 'font_size',
            'class'    => 'validate-number',
        ));
        $fieldset->addField('top_margin', 'text', array(
            'required' => true,
            'label'    => Mage::helper('barcodesuccess')->__('Margin top'),
            'name'     => 'top_margin',
            'class'    => 'validate-number',
        ));
        $fieldset->addField('left_margin', 'text', array(
            'required' => true,
            'label'    => Mage::helper('barcodesuccess')->__('Margin left'),
            'name'     => 'left_margin',
            'class'    => 'validate-number',
        ));
        $fieldset->addField('bottom_margin', 'text', array(
            'required' => true,
            'label'    => Mage::helper('barcodesuccess')->__('Margin bottom'),
            'name'     => 'bottom_margin',
            'class'    => 'validate-number',
        ));
        $fieldset->addField('right_margin', 'text', array(
            'required' => true,
            'label'    => Mage::helper('barcodesuccess')->__('Margin right'),
            'name'     => 'right_margin',
            'class'    => 'validate-number',
        ));

        /* Add by Peter - 03/02/2017 */
        $fieldset->addField('product_attribute_show_on_barcode', 'multiselect', array(
            'label' => Mage::helper('barcodesuccess')->__('Product Attributes'),
            'name' => 'product_attribute_show_on_barcode',
            'values'   => Magestore_Barcodesuccess_Model_Source_Template_ProductAttributes::toOptionArray(),
        ));

        $fieldset->addField('rotate', 'select', array(
            'label'    => Mage::helper('barcodesuccess')->__('Rotate Label'),
            'required' => true,
            'name'     => 'rotate',
            'values'   => Magestore_Barcodesuccess_Model_Source_Template_Rotate::toOptionArray(),
            'onchange' => 'templateForm.loadPreview();',
        ));
        /* End by Peter */

        $fieldset->addField('buttons', 'note', array(
            'label' => Mage::helper('barcodesuccess')->__(''),
            'text'  => "
                        <span class='form-button' onclick='templateForm.loadDefault();'>Use Default</span>
                        <span class='form-button' onclick='templateForm.loadPreview();'>Preview</span>
                        <span class='form-button' onclick='templateForm.printPreview();'>Print</span>
                <div class='guide_barcode' style='font-size:14px; width:300px;' >
                    <!--<span>
                    To print the Good barcode labels, please follow the guide
                    <a href='https://youtu.be/tMJgQ9jCETE'>here</a>
                    </span>-->
                </div>
                        ",
        ));
        $fieldset->addField('preview', 'note', array(
            'label' => Mage::helper('barcodesuccess')->__('Preview'),
            'text'  => "
            <div class='barcode_preview_container'></div>
            ",
        ));
        $descriptionImage = Mage::getBaseUrl('media') . 'magestore/barcodesuccess/description.png';
        $fieldset->addField('description', 'note', array(
            'label' => Mage::helper('barcodesuccess')->__(''),
            'text'  => "
                <div class='barcode_template_description'>
                    <img src='$descriptionImage' />
                </div>
               ",
        ));


        $form->setValues($data);
        return parent::_prepareForm();
    }
}