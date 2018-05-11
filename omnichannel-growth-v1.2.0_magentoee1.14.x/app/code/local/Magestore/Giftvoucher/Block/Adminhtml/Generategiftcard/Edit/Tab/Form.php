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
 * Adminhtml Giftvoucher Generategiftcard Edit Tab Form Block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */

class Magestore_Giftvoucher_Block_Adminhtml_Generategiftcard_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::registry('template_data')) {
            $data = Mage::registry('template_data')->getData();
        }

        $fieldset = $form->addFieldset('generategiftcard_form', array(
            'legend' => Mage::helper('giftvoucher')->__('General Information')));
        $disabled = FALSE;

        if (isset($data['is_generated']) && $data['is_generated']) {
            $disabled = TRUE;
        }
        $fieldset->addField('template_name', 'text', array(
            'label' => Mage::helper('giftvoucher')->__('Pattern name '),
            'required' => true,
            'name' => 'template_name',
            'disabled' => $disabled,
        ));
        $note = Mage::helper('giftvoucher')->__('Pattern examples:<br/><strong>[A.8] : 8 alpha<br/>[N.4] : 4 numeric<br/>[AN.6] : 6 alphanumeric<br/>GIFT-[A.4]-[AN.6] : GIFT-ADFA-12NF0O</strong>');
        $fieldset->addField('pattern', 'text', array(
            'label' => Mage::helper('giftvoucher')->__('Gift code pattern '),
            'required' => true,
            'name' => 'pattern',
            'value' => Mage::helper('giftvoucher')->getGeneralConfig('pattern'),
            'note' => $note,
            'disabled' => $disabled,
        ));

        $fieldset->addField('balance', 'text', array(
            'label' => Mage::helper('giftvoucher')->__('Gift code value'),
            'required' => true,
            'name' => 'balance',
            'disabled' => $disabled,
            'class' => 'validate-zero-or-greater',
        ));

        $fieldset->addField('currency', 'select', array(
            'label' => Mage::helper('giftvoucher')->__('Currency'),
            'required' => false,
            'name' => 'currency',
            'value' => Mage::app()->getStore()->getDefaultCurrencyCode(),
            'values' => Mage::helper('giftvoucher')->getAllowedCurrencies(),
            'disabled' => $disabled,
        ));

        $fieldset->addField('expired_at', 'date', array(
            'label' => Mage::helper('giftvoucher')->__('Expired on'),
            'required' => false,
            'name' => 'expired_at',
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'disabled' => $disabled,
        ));

        $template = $this->getGiftTemplate();
        if (isset($data['giftcard_template_image']) && isset($data['giftcard_template_id'])) {
            $script = '<script> Event.observe(window, "load", '
                . 'function(){loadImageTemplate(\'' . $data['giftcard_template_id'] . '\',\'' . 
                $data['giftcard_template_image'] . '\');});</script>';
        } else {
            $script = '';
        }
        if ($template && count($template)) {
            $fieldset->addField('giftcard_template_id', 'select', array(
                'label' => Mage::helper('giftvoucher')->__('Template'),
                'name' => 'giftcard_template_id',
                'values' => $template,
                'required' => true,
                'onchange' => 'loadImageTemplate(this.value)',
                'after_element_html' => $script,
            ));
            $fieldset->addField('list_images', 'note', array(
                'label' => Mage::helper('giftvoucher')->__('Template image'),
                'name' => 'list_images',
                'text' => sprintf(''),
            ));
            $fieldset->addField('giftcard_template_image', 'hidden', array(
                'name' => 'giftcard_template_image',
            ));
        }


        $fieldset->addField('amount', 'text', array(
            'label' => Mage::helper('giftvoucher')->__('Gift code Qty'),
            'required' => true,
            'name' => 'amount',
            'disabled' => $disabled,
            'class' => 'validate-zero-or-greater',
        ));


        $fieldset->addField('store_id', 'select', array(
            'label' => Mage::helper('giftvoucher')->__('Store view'),
            'name' => 'store_id',
            'required' => false,
            'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            'disabled' => $disabled,
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }

    /**
     * Get Gift Card template options
     *
     * @return array
     */
    public function getGiftTemplate()
    {
        $dataTemp = Mage::getModel('giftvoucher/gifttemplate')->getCollection();
        $option = array();
        $option[] = array('value' => '',
            'label' => Mage::helper('giftvoucher')->__('Please select a template')
        );
        foreach ($dataTemp as $template) {
            $option[] = array('value' => $template->getGiftcardTemplateId(),
                'label' => $template->getTemplateName()
            );
        }
        return $option;
    }

}
