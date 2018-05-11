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
class Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Rule_Edit_Tab_Conditions
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('lowstocknotification_rule_data');

        //$form = new Varien_Data_Form(array('id' => 'edit_form1', 'action' => $this->getData('action'), 'method' => 'post'));
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('adminhtml/promo_catalog/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset',
            array(
                'legend' => Mage::helper('inventorysuccess')->__('Product Conditions (leave blank for all products)'))
        )->setRenderer($renderer);

        $fieldset->addField(
            'conditions',
            'text',
            array(
                'name' => 'conditions',
                'label' => Mage::helper('inventorysuccess')->__('Conditions'),
                'title' => Mage::helper('inventorysuccess')->__('Conditions'),
                'required' => true,
            )
        )->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $fieldset = $form->addFieldset('lowstocknotification_rule_form_conditions', array(
            'legend' => Mage::helper('inventorysuccess')->__('Low-stock Conditions')
        ));

        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Source_Rule_LowStockThresholdType $sourceowStockThresholdType */
        $sourceowStockThresholdType = Mage::getSingleton('inventorysuccess/lowStockNotification_source_rule_lowStockThresholdType');
        $lowStockThresholdTypeField = $fieldset->addField('lowstock_threshold_type',
            'select',
            array(
                'label'        => Mage::helper('inventorysuccess')->__('Low-stock Threshold Type'),
                'name'        => 'lowstock_threshold_type',
                'required' => true,
                'values'    => $sourceowStockThresholdType->getOptionHash(),
            )
        );

        $lowStockThresholdQtyField = $fieldset->addField('lowstock_threshold_qty',
            'text',
            array(
                'label'        => Mage::helper('inventorysuccess')->__('Threshold (quantity)'),
                'name'        => 'lowstock_threshold_qty',
                'required' => true,
                'note' => Mage::helper('inventorysuccess')->__('Set low stock notification threshold per product by product Qty')
            )
        );

        $lowStockThresholdDayField = $fieldset->addField('lowstock_threshold',
            'text',
            array(
                'label'        => Mage::helper('inventorysuccess')->__('Threshold (days)'),
                'name'        => 'lowstock_threshold',
                'required' => true,
                'note' => Mage::helper('inventorysuccess')->__('Set low stock notification threshold per product by day to sell')
            )
        );

        $salesPeriodField = $fieldset->addField('sales_period',
            'text',
            array(
                'label'        => Mage::helper('inventorysuccess')->__('Sales Period (days)'),
                'name'        => 'sales_period',
                'required' => true,
                'note' => Mage::helper('inventorysuccess')->__('Time range to collect data')
            )
        );

        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Source_Rule_UpdateType $updateType */
        $updateType = Mage::getSingleton('inventorysuccess/lowStockNotification_source_rule_updateType');
        $updateTypeField = $fieldset->addField('update_type',
            'select',
            array(
                'label'        => Mage::helper('inventorysuccess')->__('Notification Scope'),
                'name'        => 'update_type',
                'required' => true,
                'values'    => $updateType->getOptionHash(),
            )
        );

        $warehouse = Mage::getSingleton('inventorysuccess/lowStockNotification_source_rule_warehouse');
        $warehouseField = $fieldset->addField('warehouse_ids',
            'multiselect',
            array(
                'label'        => Mage::helper('inventorysuccess')->__('Warehouse(s)'),
                'name'        => 'warehouse_ids',
                'required' => true,
                'values'    => $warehouse->getOptionHash(),
            )
        );

        $form->setValues($model->getData());

        // field dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($lowStockThresholdTypeField->getHtmlId(), $lowStockThresholdTypeField->getName())
            ->addFieldMap($lowStockThresholdQtyField->getHtmlId(), $lowStockThresholdQtyField->getName())
            ->addFieldMap($lowStockThresholdDayField->getHtmlId(), $lowStockThresholdDayField->getName())
            ->addFieldMap($salesPeriodField->getHtmlId(), $salesPeriodField->getName())
            ->addFieldMap($updateTypeField->getHtmlId(), $updateTypeField->getName())
            ->addFieldMap($warehouseField->getHtmlId(), $warehouseField->getName())
            ->addFieldDependence(
                $lowStockThresholdQtyField->getName(),
                $lowStockThresholdTypeField->getName(),
                Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_LOWSTOCK_THRESHOLD_PRODUCT_QTY
            )->addFieldDependence(
                $lowStockThresholdDayField->getName(),
                $lowStockThresholdTypeField->getName(),
                Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_LOWSTOCK_THRESHOLD_SALE_DAY
            )->addFieldDependence(
                $salesPeriodField->getName(),
                $lowStockThresholdTypeField->getName(),
                Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_LOWSTOCK_THRESHOLD_SALE_DAY
            )->addFieldDependence(
                $warehouseField->getName(),
                $updateTypeField->getName(),
                array (
                    (string) Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_ONLY_WAREHOUSE,
                    (string) Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_BOTH_SYSTEM_AND_WAREHOUSE
                )
            )
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
