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
 * Class Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher_Edit_Tab_Form
 */
class Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('giftvoucher_form', array('legend' => Mage::helper('giftvoucher')->__('General Information')));
        if (Mage::getSingleton('adminhtml/session')->getGiftvoucherData()) {
            $data = Mage::getSingleton('adminhtml/session')->getGiftvoucherData();
            Mage::getSingleton('adminhtml/session')->setGiftvoucherData(null);
        } elseif (Mage::registry('giftvoucher_data')) {
            $data = Mage::registry('giftvoucher_data')->getData();
        }
        $fieldset->addField('gift_code', 'text', array(
            'label' => Mage::helper('giftvoucher')->__('Gift Code Pattern '),
            'required' => true,
            'name' => 'gift_code',
            'value' => Mage::helper('giftvoucher')->getGeneralConfig('pattern'),
            'note' => Mage::helper('giftvoucher')->__('Examples:<br/><strong>[A.8] : 8 alpha<br/>[N.4] : 4 numeric<br/>[AN.6] : 6 alphanumeric<br/>GIFT-[A.4]-[AN.6] : GIFT-ADFA-12NF0O</strong>'),
        ));

        $fieldset->addField('balance', 'text', array(
            'label' => Mage::helper('giftvoucher')->__('Gift Code Value '),
            'required' => true,
            'name' => 'balance',
        ));

        $fieldset->addField('currency', 'select', array(
            'label' => Mage::helper('giftvoucher')->__('Currency'),
            'required' => false,
            'name' => 'currency',
            'value' => Mage::app()->getStore()->getDefaultCurrencyCode(),
            'values' => Mage::helper('giftvoucher')->getAllowedCurrencies(),
        ));
        $template = $this->getGiftTemplate();

        if (isset($data['giftcard_custom_image']) && $data['giftcard_custom_image']) {
            $fieldset->addField('giftcard_template_id', 'hidden', array(
                'label' => Mage::helper('giftvoucher')->__('Template'),
                'name' => 'giftcard_template_id',
                'values' => (isset($data['giftcard_template_id'])) ? $data['giftcard_template_id'] : '',
                'after_element_html' => (isset($data['giftcard_template_image']) && isset($data['giftcard_template_id'])) ? '<script> Event.observe(window, "load", function(){loadImageTemplate(\'' . $data['giftcard_template_id'] . '\',\'' . $data['giftcard_template_image'] . '\',true);});</script>' : '',
            ));

            $fieldset->addField('list_images', 'note', array(
                'label' => Mage::helper('giftvoucher')->__('Customer\'s Image'),
                'name' => 'list_images',
                'text' => sprintf(''),
            ));

            $fieldset->addField('giftcard_template_image', 'hidden', array(
                'name' => 'giftcard_template_image',
                'value' => $data['giftcard_template_image'],
            ));
        } elseif ($template && count($template)) {
            $fieldset->addField('giftcard_template_id', 'select', array(
                'label' => Mage::helper('giftvoucher')->__('Template'),
                'name' => 'giftcard_template_id',
                'values' => $template,
                'onchange' => 'loadImageTemplate(this.value)',
                'after_element_html' => (isset($data['giftcard_template_image']) && isset($data['giftcard_template_id'])) ? '<script> Event.observe(window, "load", function(){loadImageTemplate(\'' . $data['giftcard_template_id'] . '\',\'' . $data['giftcard_template_image'] . '\',false);});</script>' : '',
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

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('giftvoucher')->__('Status'),
            'name' => 'giftvoucher_status',
            'values' => Mage::getModel('giftvoucher/status')->getOptions(),
        ));

        $fieldset->addField('expired_at', 'date', array(
            'label' => Mage::helper('giftvoucher')->__('Expired on'),
            'required' => false,
            'name' => 'expired_at',
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
//            'format' => Mage::getModel('core/date')->date('d.m.Y', strtotime($data['expired_at'])),
        ));

        $fieldset->addField('store_id', 'select', array(
            'label' => Mage::helper('giftvoucher')->__('Store view'),
            'name' => 'store_id',
            'required' => false,
            'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
        ));

        $fieldset->addField('giftvoucher_comments', 'editor', array(
            'label' => Mage::helper('giftvoucher')->__('Last comment'),
            'required' => false,
            'name' => 'giftvoucher_comments',
            'style' => 'height:100px;',
        ));


        if(isset($data['set_id'])) {
            $fieldset->addField(
                'used',
                'select',
                array(
                    'label' => Mage::helper('giftvoucher')->__('Used'),
                    'name' => 'used',
                    'style' =>  'min-width: 250px',
                    'values' => $this->getOptionArray(),
                    'note' => Mage::helper('giftvoucher')->__('Yes:The gift code has been purchased;<br/>No:The gift code has not been purchased'),
                )
            );
        }
        $form->setValues($data);
        return parent::_prepareForm();
    }

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        return array(
            Magestore_Giftvoucher_Model_Giftvoucher::STATUS_YES => Mage::helper('giftvoucher')->__('Yes'),
            Magestore_Giftvoucher_Model_Giftvoucher::STATUS_NO => Mage::helper('giftvoucher')->__('No'),

        );
    }


    /**
     * @return array
     */
    public function getGiftTemplate() {
        /**
         * gifttemplate
         */
        $data_temp = Mage::getModel('giftvoucher/gifttemplate')->getCollection();
        $option = array();
        $option[] = array('value' => '',
            'label' => Mage::helper('giftvoucher')->__('Please select a template')
        );
        foreach ($data_temp as $template) {
            $option[] = array('value' => $template->getGiftcardTemplateId(),
                'label' => $template->getTemplateName()
            );
        }
        return $option;
    }

}
