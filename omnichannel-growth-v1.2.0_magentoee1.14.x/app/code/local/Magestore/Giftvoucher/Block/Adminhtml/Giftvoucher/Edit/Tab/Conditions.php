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
 * Class Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher_Edit_Tab_Conditions
 */
class Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
        protected function _prepareForm() {
        if (Mage::registry('giftvoucher_data')) {
            $model = Mage::registry('giftvoucher_data');
        } else {
            $model = Mage::getModel('giftvoucher/giftvoucher');
        }
        $data = $model->getData();
        $model->setData('conditions', $model->getData('conditions_serialized'));

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('giftvoucher_');

        $configSettings = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
                array(
                    'add_widgets' => false,
                    'add_variables' => false,
                    'add_images' => false,
                    'files_browser_window_url' => $this->getBaseUrl() . 'admin/cms_wysiwyg_images/index/',
        ));

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
                ->setTemplate('promo/fieldset.phtml')
                ->setNewChildUrl($this->getUrl('adminhtml/promo_quote/newConditionHtml/form/giftvoucher_conditions_fieldset'));
        $fieldset = $form->addFieldset('description_fieldset', array('legend' => Mage::helper('giftvoucher')->__('Description')));

        $fieldset->addField('description', 'editor', array(
            'label' => Mage::helper('giftvoucher')->__('Describe conditions applied to shopping cart when using this gift code'),
            'title' => Mage::helper('giftvoucher')->__('Describe conditions applied to shopping cart when using this gift code'),
            'name' => 'description',
            'wysiwyg' => true,
            'config' => $configSettings,
        ));
        $fieldset = $form->addFieldset('conditions_fieldset', array('legend' => Mage::helper('giftvoucher')->__('Allow using the gift code only if the following shopping cart conditions are met (leave blank for all shopping carts)')))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('giftvoucher')->__('Conditions'),
            'title' => Mage::helper('giftvoucher')->__('Conditions'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
