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
class Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Edit_Tab_General extends
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
        $data = array();
        $timezone = Mage::getStoreConfig('general/locale/timezone');
        if ( Mage::getSingleton('adminhtml/session')->getBarcodeData() ) {
            $data = Mage::getSingleton('adminhtml/session')->getBarcodeData();
            Mage::getSingleton('adminhtml/session')->setBarcodeData(null);
        } elseif ( Mage::registry('barcode_data') ) {
            $data = Mage::registry('barcode_data')->getData();
        }
        $fieldset = $form->addFieldset('barcodesuccess_form', array(
            'legend' => Mage::helper('barcodesuccess')->__('Barcode Information'),
        ));

        $fieldset->addField(Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID, 'hidden', array(
            'label' => Mage::helper('barcodesuccess')->__('Barcode'),
            'text'  => $data[Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID],
        ));

        $fieldset->addField(Magestore_Barcodesuccess_Model_Barcode::BARCODE, 'note', array(
            'label' => Mage::helper('barcodesuccess')->__('Barcode'),
            'text'  => $data[Magestore_Barcodesuccess_Model_Barcode::BARCODE],
        ));
        $fieldset->addField(Magestore_Barcodesuccess_Model_Barcode::PRODUCT_SKU, 'note', array(
            'label' => Mage::helper('barcodesuccess')->__('Product SKU'),
            'text'  => $data[Magestore_Barcodesuccess_Model_Barcode::PRODUCT_SKU],
        ));
        $fieldset->addField(Magestore_Barcodesuccess_Model_Barcode::SUPPLIER_CODE, 'note', array(
            'label' => Mage::helper('barcodesuccess')->__('Supplier'),
            'text'  => $data[Magestore_Barcodesuccess_Model_Barcode::SUPPLIER_CODE],
        ));
        $fieldset->addField(Magestore_Barcodesuccess_Model_Barcode::CREATED_AT, 'note', array(
            'label' => Mage::helper('barcodesuccess')->__('Created Date'),
            'text'  => ((new DateTime($data[Magestore_Barcodesuccess_Model_Barcode::CREATED_AT], new DateTimeZone('Europe/London')))->setTimezone(new DateTimeZone($timezone)))->format('Y-m-d H:i:s'),
        ));
        $fieldset->addField(Magestore_Barcodesuccess_Model_Barcode::PURCHASED_TIME, 'note', array(
            'label' => Mage::helper('barcodesuccess')->__('Purchased Time'),
            'text'  => ((new DateTime($data[Magestore_Barcodesuccess_Model_Barcode::PURCHASED_TIME], new DateTimeZone('Europe/London')))->setTimezone(new DateTimeZone($timezone)))->format('Y-m-d H:i:s'),
        ));

        $fieldset->setReadonly(true, true);
        $form->setValues($data);
        return parent::_prepareForm();
    }
}