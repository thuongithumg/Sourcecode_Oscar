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
 * Class Magestore_Giftvoucher_Block_Adminhtml_Giftproduct_Edit_Tab_Form
 */
class Magestore_Giftvoucher_Block_Adminhtml_Giftproduct_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
        protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('giftvoucher_form', array('legend' => Mage::helper('giftvoucher')->__('Create Product Settings')));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                ->load()
                ->toOptionHash();
        $fieldset->addField('attribute_set_id', 'select', array(
            'label' => Mage::helper('giftvoucher')->__('Attribute Set'),
            'required' => true,
            'name' => 'attribute_set_id',
            'values' => $sets,
        ));
        $url = $this->getUrl('adminhtml/catalog_product/new', array('type' => 'giftvoucher'));
        $js = '<script type="text/javascript">
            //<![CDATA[
            function setAttributeGiftProduct(){
                    var set_id = $(\'attribute_set_id\').options[$(\'attribute_set_id\').selectedIndex].value;
                    var url=\'' . $url . '\'+"set/"+set_id;
                    setLocation(url);
            }
            //]]>
        </script>';
        $fieldset->addField('product_type', 'note', array(
            'label' => Mage::helper('giftvoucher')->__('Product Type'),
            'name' => 'product_type',
            'text' => Mage::helper('giftvoucher')->__('Gift Card') .
            '</br><button type="button" class="scalable save" onclick="setAttributeGiftProduct()"><span>' . Mage::helper("giftvoucher")->__("Continue") . '</span></button>' . $js,
        ));


        return parent::_prepareForm();
    }

}