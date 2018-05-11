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
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
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
class Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Generate_Edit_Tab_Form extends
    Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Class constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('barcodesuccess/barcode/generate/form.phtml');
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild('grid',
                        $this->getLayout()
                             ->createBlock('barcodesuccess/adminhtml_barcode_generate_edit_tab_grid', 'generate.form.grid')
        );
        $this->setChild('grid_serializer',
                        $this->getLayout()
                             ->createBlock('adminhtml/widget_grid_serializer', 'product_grid_serializer'));
        $this->getChild('grid_serializer')->initSerializerBlock('generate.form.grid', 'getSelectedProducts', 'products', 'product_selected');
        $this->getChild('grid_serializer')->addColumnInputName(array(
                                                                   'item_qty',
                                                                   'supplier',
                                                                   'product_id',
                                                                   'product_sku',
                                                                   'purchased_time',
                                                               ));
        return parent::_prepareLayout();
    }

    /**
     * prepare tab form's information
     *
     * @return Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $data = array();
        if ( Mage::getSingleton('adminhtml/session')->getBarcodesuccessData() ) {
            $data = Mage::getSingleton('adminhtml/session')->getBarcodesuccessData();
            Mage::getSingleton('adminhtml/session')->setBarcodesuccessData(null);
        } elseif ( Mage::registry('barcodesuccess_data') ) {
            $data = Mage::registry('barcodesuccess_data')->getData();
        }
        $fieldset         = $form->addFieldset('barcodesuccess_form', array(
            'legend' => Mage::helper('barcodesuccess')->__('General information'),
        ));
        $oneBarcodePerSku = Mage::helper('barcodesuccess')->isOneBarcodePerSku();
        $note             = $oneBarcodePerSku
            ? Mage::helper('barcodesuccess')->__('Your current setting is 1 barcode/SKU.')
            : Mage::helper('barcodesuccess')->__('Your current setting is multiple barcodes/SKU');
        $fieldset->addField('note', 'note', array(
            'label' => Mage::helper('barcodesuccess')->__(' '),
            'text'  => $note,
        ));

        if ( $oneBarcodePerSku ) {
            $fieldset->addField('generate_new', 'checkbox', array(
                'label' => Mage::helper('barcodesuccess')->__('Generate a new one if selected SKU already had barcode'),
                'name'  => 'generate_new',
            ));

            $fieldset->addField('remove_old', 'checkbox', array(
                'label' => Mage::helper('barcodesuccess')->__('Remove existed barcodes of selected SKUs'),
                'name'  => 'remove_old',
            ));
        } else {
            $fieldset->addField('generate_type', 'radios', array(
                'label'  => Mage::helper('barcodesuccess')->__('Generating Type'),
                'name'   => 'generate_type',
                'values' => array(
                    array(
                        'value' => Magestore_Barcodesuccess_Model_Source_GenerateType::ITEM,
                        'label' => 'Generate barcode per item (each item will generate a barcode with qty = 1)',
                    ),
                    array(
                        'value' => Magestore_Barcodesuccess_Model_Source_GenerateType::PURCHASE,
                        'label' => 'Generate barcode per purchase (each product sku will generate a barcode)',
                    ),
                ),
                'after_element_html',
            ));
        }

        $fieldset->addField('reason', 'text', array(
            'label' => Mage::helper('barcodesuccess')->__('Reason'),
            'name'  => 'reason',
        ));
        $form->setValues($data);
        return parent::_prepareForm();
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        $script = "<script>jQuery('input[name=generate_type]:first-child').prop('checked','checked');</script>";
        return parent::_toHtml() . $script;
    }

}