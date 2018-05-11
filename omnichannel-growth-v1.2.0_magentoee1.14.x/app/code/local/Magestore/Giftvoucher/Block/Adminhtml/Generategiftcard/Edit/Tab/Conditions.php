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
 * Adminhtml Giftvoucher Generategiftcard Edit Tab Conditions Block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */

class Magestore_Giftvoucher_Block_Adminhtml_Generategiftcard_Edit_Tab_Conditions
    extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     * @throws Exception
     */
    protected function _prepareForm()
    {
        if (Mage::registry('template_data')) {
            $model = Mage::registry('template_data');
        } else {
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('giftvoucher/template')->load($id);
        }
        $data = $model->getData();

        $model->setData('conditions', $model->getData('conditions_serialized'));

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('giftvoucher_');

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl(
                $this->getUrl('adminhtml/promo_quote/newConditionHtml/form/giftvoucher_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array(
                'legend' => Mage::helper('giftvoucher')->__('Allow using gift codes only if the following conditions are met (leave blank for all shopping carts)'))
            )->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('giftvoucher')->__('Shopping Cart Conditions'),
            'title' => Mage::helper('giftvoucher')->__('Shopping Cart Conditions'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
