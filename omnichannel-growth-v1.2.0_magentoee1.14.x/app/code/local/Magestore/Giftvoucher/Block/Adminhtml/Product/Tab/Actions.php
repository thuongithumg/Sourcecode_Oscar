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
 * Class Magestore_Giftvoucher_Block_Adminhtml_Product_Tab_Actions
 */
class Magestore_Giftvoucher_Block_Adminhtml_Product_Tab_Actions extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm() {
        $product = Mage::registry('current_product');
        $model = Mage::getSingleton('giftvoucher/product');
        if (!$model->getId() && $product->getId()) {
            $model->loadByProduct($product);
        }
        $data = $model->getData();
        $model->setData('conditions', $model->getData('actions_serialized'));

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('giftvoucher_');
//        $fieldset = $form->addFieldset('description_fieldset', array('legend' => Mage::helper('giftvoucher')->__('Description')));
//
//        $fieldset->addField('giftcard_description', 'editor', array(
//            'label' => Mage::helper('giftvoucher')->__('Description of gift card conditions'),
//            'title' => Mage::helper('giftvoucher')->__('Description of gift card conditions'),
////            'class' => 'required-entry',
////            'required' => true,
//            'name' => 'giftcard_description',
//            'wysiwyg' => true,
//        ));
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
                ->setTemplate('promo/fieldset.phtml')
                ->setNewChildUrl($this->getUrl('adminhtml/promo_quote/newActionHtml/form/giftvoucher_actions_fieldset'));
        $fieldset = $form->addFieldset('actions_fieldset', array('legend' => Mage::helper('giftvoucher')->__('Allow using Gift Card only if products in cart meet the following conditions (leave blank for all products)')))->setRenderer($renderer);
        $fieldset->addField('actions', 'text', array(
            'label' => Mage::helper('giftvoucher')->__('Apply To'),
            'title' => Mage::helper('giftvoucher')->__('Apply To'),
            'name' => 'actions',
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/actions'));

        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @return string
     */
    public function getTabLabel() {
        return Mage::helper('giftvoucher')->__('Cart Item Conditions ');
    }

    /**
     * @return string
     */
    public function getTabTitle() {
        return Mage::helper('giftvoucher')->__('Cart Item Conditions ');
    }

    /**
     * @return bool
     */
    public function canShowTab() {
        if (Mage::registry('current_product')->getTypeId() == 'giftvoucher') {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isHidden() {
        if (Mage::registry('current_product')->getTypeId() == 'giftvoucher') {
            return false;
        }
        return true;
    }

}
