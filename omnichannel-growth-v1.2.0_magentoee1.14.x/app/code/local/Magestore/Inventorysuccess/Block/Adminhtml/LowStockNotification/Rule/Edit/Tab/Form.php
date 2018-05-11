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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * LowStockNotification Rule Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Rule_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Rule_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        if (Mage::getSingleton('adminhtml/session')->getLowstocknotificationRuleData()) {
            $data = Mage::getSingleton('adminhtml/session')->getLowstocknotificationRuleData();
            Mage::getSingleton('adminhtml/session')->setLowstocknotificationRuleData(null);
        } elseif (Mage::registry('lowstocknotification_rule_data')) {
            $data = Mage::registry('lowstocknotification_rule_data')->getData();
        }
        $fieldset = $form->addFieldset('lowstocknotification_rule_form', array(
            'legend' => Mage::helper('inventorysuccess')->__('Rule information')
        ));

        $fieldset->addField('rule_name',
            'text',
            array(
                'label'        => Mage::helper('inventorysuccess')->__('Rule Name'),
                'class'        => 'required-entry',
                'required'    => true,
                'name'        => 'rule_name',
        ));

        $fieldset->addField('description',
            'editor',
            array(
                'name'        => 'description',
                'label'        => Mage::helper('inventorysuccess')->__('Description'),
                'title'        => Mage::helper('inventorysuccess')->__('Description'),
                'style'        => 'width:500px; height:100px;',
                'wysiwyg'    => false
        ));

        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Source_Rule_Status $sourceStatus */
        $sourceStatus = Mage::getSingleton('inventorysuccess/lowStockNotification_source_rule_status');
        $fieldset->addField('status',
            'select',
            array(
                'label'        => Mage::helper('inventorysuccess')->__('Status'),
                'name'        => 'status',
                'required' => true,
                'values'    => $sourceStatus->getOptionHash(),
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('from_date',
            'date',
            array(
                'name'      => 'from_date',
                'time'      => false,
                'label'     => Mage::helper('inventorysuccess')->__('From'),
                'image'     => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format'       => $dateFormatIso,
                'note' => Mage::helper('inventorysuccess')->__('Date format %s', Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT))
        ));

        $fieldset->addField('to_date',
            'date',
            array(
                'name'      => 'to_date',
                'time'      => false,
                'label'     => Mage::helper('inventorysuccess')->__('To'),
                'image'     => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format'       => $dateFormatIso,
                'note' => Mage::helper('inventorysuccess')->__('Date format %s', Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT))
        ));

//        $fieldset->addField('priority',
//            'text',
//            array(
//                'name'      => 'priority',
//                'label'     => Mage::helper('inventorysuccess')->__('Priority')
//        ));

        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Source_Rule_UpdateTimeType $sourceUpdateTimeType */
        $sourceUpdateTimeType = Mage::getSingleton('inventorysuccess/lowStockNotification_source_rule_updateTimeType');
        $updateTimeTypeField = $fieldset->addField('update_time_type',
            'select',
            array(
                'label'        => Mage::helper('inventorysuccess')->__('Update time'),
                'name'        => 'update_time_type',
                'required' => true,
                'values'    => $sourceUpdateTimeType->getOptionHash(),
        ));

        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Source_Rule_SpecificMonth $sourceSpecificMonth */
        $sourceSpecificMonth = Mage::getSingleton('inventorysuccess/lowStockNotification_source_rule_specificMonth');
        $specificMonthField = $fieldset->addField('specific_month',
            'multiselect',
            array(
                'label'        => Mage::helper('inventorysuccess')->__('Select months'),
                'name'        => 'specific_month',
                'class'        => 'required-entry',
                'required' => true,
                'values'    => $sourceSpecificMonth->getOptionHash()
        ));

        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Source_Rule_SpecificDay $sourceSpecificDay */
        $sourceSpecificDay = Mage::getSingleton('inventorysuccess/lowStockNotification_source_rule_specificDay');
        $specificDayField = $fieldset->addField('specific_day',
            'multiselect',
            array(
                'label'        => Mage::helper('inventorysuccess')->__('Select days'),
                'name'        => 'specific_day',
                'class'        => 'required-entry',
                'required' => true,
                'values'    => $sourceSpecificDay->getOptionHash()
        ));

        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Source_Rule_SpecificHour $sourceSpecificHour */
        $sourceSpecificHour = Mage::getSingleton('inventorysuccess/lowStockNotification_source_rule_specificTime');
        $specificHourField = $fieldset->addField('specific_time',
            'multiselect',
            array(
                'label'        => Mage::helper('inventorysuccess')->__('Select hours'),
                'name'        => 'specific_time',
                'class'        => 'required-entry',
                'required' => true,
                'values'    => $sourceSpecificHour->getOptionHash()
        ));


        $form->setValues($data);

        // field dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($updateTimeTypeField->getHtmlId(), $updateTimeTypeField->getName())
            ->addFieldMap($specificMonthField->getHtmlId(), $specificMonthField->getName())
            ->addFieldMap($specificDayField->getHtmlId(), $specificDayField->getName())
            ->addFieldMap($specificHourField->getHtmlId(), $specificHourField->getName())
            ->addFieldDependence(
                $specificMonthField->getName(),
                $updateTimeTypeField->getName(),
                Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TIME_TYPE_MONTHLY)
            ->addFieldDependence(
                $specificDayField->getName(),
                $updateTimeTypeField->getName(),
                Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TIME_TYPE_MONTHLY)
        );
        return parent::_prepareForm();
    }
}