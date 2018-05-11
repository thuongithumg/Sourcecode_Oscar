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

class Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        if (Mage::getSingleton('adminhtml/session')->getStocktakingData()) {
            $data = Mage::getSingleton('adminhtml/session')->getInventorysuccessData();
            Mage::getSingleton('adminhtml/session')->setStocktakingData(null);
        } elseif (Mage::registry('stocktaking_data')) {
            $data = Mage::registry('stocktaking_data')->getData();
        }
        $data['warehouse_id_label'] = isset($data['warehouse_id']) ? $data['warehouse_id'] : null;        
        $formTitle = 'General information';
        $textType = 'text';
        $editorType = 'editor';

        if($this->getStocktaking()->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_COMPLETED){
            $formTitle = 'Information';
            $textType = 'label';
            $editorType = 'label';
        }
        if(!$this->getRequest()->getParam('id')){
            $stocktakingService = Mage::getModel('coresuccess/service')->stocktakingService();
            $data['stocktaking_code'] = $stocktakingService->generateCode();
        }
        $fieldset = $form->addFieldset('general_form', array(
            'legend'=>Mage::helper('inventorysuccess')->__($formTitle)
        ));

        $resourceId = 'admin/inventorysuccess/stockcontrol/create_stocktaking';
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
        }else{
            $fieldset->addField('warehouse_id', 'select', array(
                'label' => Mage::helper('inventorysuccess')->__('Warehouse'),
                'class' => 'required-entry',
                'required' => true,
                'values' => Magestore_Coresuccess_Model_Service::transferStockService()->getAvailableWarehousesArray($resourceId),
                'name' => 'warehouse_id',
            ));
        }

        $fieldset->addField('stocktaking_code', $textType, array(
            'label'    => Mage::helper('inventorysuccess')->__('Stocktaking Code'),
            'class'    => 'required-entry',
            'required' => true,
            'name'     => 'stocktaking_code',
        ));

        $fieldset->addField('participants', $textType, array(
            'label'    => Mage::helper('inventorysuccess')->__('Participants'),
            'name'     => 'participants',
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        if($this->getStocktaking()->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_COMPLETED) {
            $fieldset->addField('stocktake_at', 'date', array(
                'label' => Mage::helper('inventorysuccess')->__('Stocktaking Time'),
                'name' => 'stocktake_at',
                'time' => false,
                'readonly' => true,
                'style' => 'border:none; background: transparent; cursor: text; margin-left:-4px;',
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format' => $dateFormatIso,
            ));
        }else {
            $fieldset->addField('stocktake_at', 'date', array(
                'label' => Mage::helper('inventorysuccess')->__('Stocktaking Time'),
                'name' => 'stocktake_at',
                'time' => false,
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format' => $dateFormatIso,
            ));
        }
        $fieldset->addField('reason', $editorType, array(
            'label'       => Mage::helper('inventorysuccess')->__('Reason'),
            'class'       => 'required-entry',
            'required'    => true,
            'name'        => 'reason',
        ));

        $fieldset->addField('status', 'hidden', array(
            'label'       => Mage::helper('inventorysuccess')->__('Status'),
            'name'        => 'status',
        ));


        $form->setValues($data);
        return parent::_prepareForm();
    }

    /**
     * get current stocktaking
     *
     * @return Magestore_Inventory_Model_Stocktaking
     */
    public function getStocktaking()
    {
        if (Mage::registry('stocktaking_data')
            && Mage::registry('stocktaking_data')->getId()
        ) {
            return Mage::registry('stocktaking_data');
        }
        return Mage::getModel('inventorysuccess/stocktaking')->load($this->getRequest()->getParam('id'));
    }
}