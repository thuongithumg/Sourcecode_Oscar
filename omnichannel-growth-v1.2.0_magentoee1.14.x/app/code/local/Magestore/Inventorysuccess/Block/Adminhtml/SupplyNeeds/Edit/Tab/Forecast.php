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

class Magestore_Inventorysuccess_Block_Adminhtml_SupplyNeeds_Edit_Tab_Forecast extends Mage_Adminhtml_Block_Widget_Form
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
        $data = null;
        if ($this->getRequest()->getParam('top_filter')) {
            $topFilter = $this->getRequest()->getParam('top_filter');
            $data = unserialize(base64_decode($topFilter));
        }
        $fieldset = $form->addFieldset('supplyneeds_forecast', array(
            'legend' => Mage::helper('inventorysuccess')->__('Select criteria for supply forecasting')
        ));

        /** @var Magestore_Inventorysuccess_Model_SupplyNeeds_Source_Warehouse $sourceWarehouse */
        $sourceWarehouse = Mage::getSingleton('inventorysuccess/supplyNeeds_source_warehouse');
        $fieldset->addField('warehouse_ids',
            'multiselect',
            array(
                'label'        => Mage::helper('inventorysuccess')->__('Warehouse(s)'),
                'class'        => 'required-entry',
                'required'    => true,
                'name'        => 'warehouse_ids',
                'values'    => $sourceWarehouse->getOptionHash(),
                'style'      => 'height: 100px',
                'note' => Mage::helper('inventorysuccess')->__('Choose warehouse to calculate supply needs')
            )
        );

        /** @var Magestore_Inventorysuccess_Model_SupplyNeeds_Source_SalesPeriod $sourceSalesPeriod */
        $sourceSalesPeriod = Mage::getSingleton('inventorysuccess/supplyNeeds_source_salesPeriod');
        $salesPeriodField = $fieldset->addField('sales_period',
            'select',
            array(
                'label'        => Mage::helper('inventorysuccess')->__('Sales Period'),
                'class'        => 'required-entry',
                'required'    => true,
                'name'        => 'sales_period',
                'values'    => $sourceSalesPeriod->getOptionHash(),
                'note' => Mage::helper('inventorysuccess')->__('Time range to collect sales data')
            )
        );

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fromDateField = $fieldset->addField('from_date',
            'date',
            array(
                'name'      => 'from_date',
                'time'      => false,
                'required'    => true,
                'class'        => 'required-entry',
                'label'     => Mage::helper('inventorysuccess')->__('From'),
                'image'     => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format'       => Varien_Date::DATE_INTERNAL_FORMAT
//                'format'       => $dateFormatIso
            )
        );

        $toDateField = $fieldset->addField('to_date',
            'date',
            array(
                'name'      => 'to_date',
                'time'      => false,
                'required'    => true,
                'class'        => 'required-entry',
                'label'     => Mage::helper('inventorysuccess')->__('To'),
                'image'     => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format'       => Varien_Date::DATE_INTERNAL_FORMAT
//                'format'       => $dateFormatIso
            )
        );

        $fieldset->addField('forecast_date_to',
            'date',
            array(
                'name'      => 'forecast_date_to',
                'time'      => false,
                'required'    => true,
                'class'        => 'required-entry',
                'label'     => Mage::helper('inventorysuccess')->__('Forecast Supply Needs Until'),
                'image'     => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format'       => Varien_Date::DATE_INTERNAL_FORMAT,
//                'format'       => $dateFormatIso
                'note' => Mage::helper('inventorysuccess')->__('Timeline to calculate supply needs.. Date format: %s', Varien_Date::DATE_INTERNAL_FORMAT)
            )
        );

        $form->setValues($data);

        // field dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($salesPeriodField->getHtmlId(), $salesPeriodField->getName())
            ->addFieldMap($fromDateField->getHtmlId(), $fromDateField->getName())
            ->addFieldMap($toDateField->getHtmlId(), $toDateField->getName())
            ->addFieldDependence(
                $fromDateField->getName(),
                $salesPeriodField->getName(),
                Magestore_Inventorysuccess_Model_SupplyNeeds::CUSTOM_RANGE)
            ->addFieldDependence(
                $toDateField->getName(),
                $salesPeriodField->getName(),
                Magestore_Inventorysuccess_Model_SupplyNeeds::CUSTOM_RANGE)
        );
        return parent::_prepareForm();
    }
}