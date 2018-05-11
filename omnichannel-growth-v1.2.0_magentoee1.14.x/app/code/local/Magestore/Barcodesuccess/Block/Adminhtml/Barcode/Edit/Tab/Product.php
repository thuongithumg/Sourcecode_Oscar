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
class Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Edit_Tab_Product extends
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
        $barcodeData = Mage::registry('barcode_data')->getData();
        $product     = Mage::getModel('catalog/product')->loadByAttribute('sku', $barcodeData['product_sku']);
        $this->setProduct($product);
        $data        = $product->getData();
        $data['qty'] = Magestore_Coresuccess_Model_Service::barcodeProductService()->getQty($product);

        $fieldset = $form->addFieldset('barcodesuccess_form', array(
            'legend' => Mage::helper('barcodesuccess')->__('Product Information'),
        ));
        $fieldset->addField('thumbnail', 'note', array(
            'label' => Mage::helper('barcodesuccess')->__('Image'),
            'text'  => Magestore_Coresuccess_Model_Service::barcodeProductService()->getThumbnailHtml($product),
        ));
        $fieldset->addField('name', 'note', array(
            'label' => Mage::helper('barcodesuccess')->__('Name'),
            'text'  => $data['name'],
        ));
        $fieldset->addField('price', 'note', array(
            'label' => Mage::helper('barcodesuccess')->__('Price'),
            'text'  => array_key_exists('price', $data) ? $data['price'] : '',
        ));
        $fieldset->addField('qty', 'note', array(
            'label' => Mage::helper('barcodesuccess')->__('Qty'),
            'text'  => Magestore_Coresuccess_Model_Service::barcodeProductService()->getQtyHtml($product),
        ));
        $fieldset->addField('availability', 'note', array(
            'label' => Mage::helper('barcodesuccess')->__('Stock Availability'),
            'text'  => Magestore_Coresuccess_Model_Service::barcodeProductService()->getAvailabilityText($product),
        ));
        $fieldset->addField('status', 'note', array(
            'label' => Mage::helper('barcodesuccess')->__('Status'),
            'text'  => Magestore_Coresuccess_Model_Service::barcodeProductService()->getStatusText($product),
        ));

        $productUrl = $this->getUrl('*/catalog_product/edit', array('id' => $product->getId()));
        $fieldset->addField('detail', 'note', array(
            'label' => Mage::helper('barcodesuccess')->__(' '),
            'text'  => "<a href='$productUrl' class='button'>More details</a>",
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }
}