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

class Magestore_Inventorysuccess_Block_Adminhtml_Adjuststock_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Adjuststock_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        if (Mage::getSingleton('adminhtml/session')->getAdjuststockData()) {
            $data = Mage::getSingleton('adminhtml/session')->getInventorysuccessData();
            Mage::getSingleton('adminhtml/session')->setAdjuststockData(null);
        } elseif (Mage::registry('adjuststock_data')) {
            $data = Mage::registry('adjuststock_data')->getData();
        }
        $data['warehouse_id_label'] = isset($data['warehouse_id']) ? $data['warehouse_id'] : null;
        $formTitle = 'General information';
        $textType = 'text';
        $editorType = 'editor';

        if($this->getCurrentAdjustment()->getStatus() == Magestore_Inventorysuccess_Model_Adjuststock::STATUS_COMPLETED){
            $formTitle = 'Information';
            $textType = 'label';
            $editorType = 'label';
        }
        if(!$this->getRequest()->getParam('id')){
            $adjustStockService = Mage::getModel('coresuccess/service')->adjustStockService();
            $data['adjuststock_code'] = $adjustStockService->generateCode();
        }
        $fieldset = $form->addFieldset('general_form', array(
            'legend'=>Mage::helper('inventorysuccess')->__($formTitle)
        ));

        $resourceId = 'admin/inventorysuccess/stockcontrol/create_adjuststock';
        if($this->getRequest()->getParam('id')) {
            $fieldset->addField('warehouse_id_label', 'select', array(
                'label' => Mage::helper('inventorysuccess')->__('Warehouse'),
                'class' => 'required-entry',
                'required' => true,
                'disabled' => true,
                'style' => 'border:none; background: transparent; cursor: text; 
                            color: #2f2f2f; -webkit-appearance: none;',
                //'values' => Mage::getModel('inventorysuccess/warehouse_options_warehouse')->getOptionHash(),
                'values' => Magestore_Coresuccess_Model_Service::transferStockService()->getAvailableWarehousesArray($resourceId),
                'name' => 'warehouse_id_label',
            ));
            $fieldset->addField('warehouse_id', 'hidden', array(
                'label' => Mage::helper('inventorysuccess')->__('Warehouse'),
                'values' => Magestore_Coresuccess_Model_Service::transferStockService()->getAvailableWarehousesArray($resourceId),
                'name' => 'warehouse_id',
            ));
        } else{
            $fieldset->addField('warehouse_id', 'select', array(
                'label' => Mage::helper('inventorysuccess')->__('Warehouse'),
                'class' => 'required-entry',
                'required' => true,
                'values' => Magestore_Coresuccess_Model_Service::transferStockService()->getAvailableWarehousesArray($resourceId),
                'name' => 'warehouse_id',
            ));
        }

        $fieldset->addField('adjuststock_code', $textType, array(
            'label'    => Mage::helper('inventorysuccess')->__('Adjustment Code'),
            'class'    => 'required-entry',
            'required' => true,
            'name'     => 'adjuststock_code',
        ));

        $fieldset->addField('reason', $editorType, array(
            'label'       => Mage::helper('inventorysuccess')->__('Reason'),
            'class'       => 'required-entry',
            'required'    => true,
            'name'        => 'reason',
        ));


        $form->setValues($data);
        return parent::_prepareForm();
    }

    /**
     * get current stock adjustment
     *
     * @return Magestore_Inventorysuccess_Model_Adjuststock
     */
    public function getCurrentAdjustment()
    {
        if (Mage::registry('adjuststock_data')
            && Mage::registry('adjuststock_data')->getId()
        ) {
            return Mage::registry('adjuststock_data');
        }
        return Mage::getModel('inventorysuccess/adjuststock')->load($this->getRequest()->getParam('id'));
    }
}