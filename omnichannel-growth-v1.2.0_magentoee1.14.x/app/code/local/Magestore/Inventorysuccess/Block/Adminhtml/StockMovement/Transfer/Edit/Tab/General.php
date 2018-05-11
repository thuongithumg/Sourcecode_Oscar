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
 * Stock Transfer General Tab Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_StockMovement_Transfer_Edit_Tab_General
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $stockTransfer = Mage::registry('current_stock_transfer');

        $fieldset = $form->addFieldset('general_form', array(
            'legend' => $this->__('General Information')
        ));
        $fieldset->addField('warehouse_id',
            'select',
            array(
                'name' => 'warehouse_id',
                'label' => $this->__('Warehouse Name'),
                'title' => $this->__('Warehouse Name'),
                'options'     => Mage::getModel('inventorysuccess/warehouse_options_warehouse')->getOptionArray(),
                'style' => 'border:none; background: transparent; cursor: text; 
                            color: #2f2f2f; -webkit-appearance: none;',
            )
        );
        $fieldset->addField('action_code',
            'select',
            array(
                'name' => 'action_code',
                'label' => $this->__('Type'),
                'title' => $this->__('Type'),
                'values' => Magestore_Coresuccess_Model_Service::stockMovementProviderService()->toActionOptionHash(),
                'style' => 'border:none; background: transparent; cursor: text; 
                            color: #2f2f2f; -webkit-appearance: none;',
            )
        );
        $fieldset->addField('action_number',
            'text',
            array(
                'name' => 'action_number',
                'label' => $this->__('Reference Number'),
                'title' => $this->__('Reference Number'),
                'style' => 'border:none; background: transparent; cursor: text; 
                            color: #2f2f2f; -webkit-appearance: none;',
            )
        );

        $fieldset->addField('created_at',
            'date',
            array(
                'name' => 'created_at',
                'label' => $this->__('Created Time'),
                'title' => $this->__('Created Time'),
                'time' => true,
                'format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                'style' => 'border:none; background: transparent; cursor: text; 
                            color: #2f2f2f; -webkit-appearance: none;',
            )
        );
        $form->setValues($stockTransfer->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}