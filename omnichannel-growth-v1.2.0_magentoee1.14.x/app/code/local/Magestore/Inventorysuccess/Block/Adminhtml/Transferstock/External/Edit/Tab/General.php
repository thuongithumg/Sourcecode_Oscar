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
 * Inventorysuccess Edit Form Content Tab Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_External_Edit_Tab_General
    extends
    Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $data = array();
        if ( Mage::getSingleton('adminhtml/session')->getInventorysuccessData() ) {
            $data = Mage::getSingleton('adminhtml/session')->getInventorysuccessData();
            Mage::getSingleton('adminhtml/session')->setInventorysuccessData(null);
        } elseif ( Mage::registry('external_data') ) {
            $data = Mage::registry('external_data')->getData();
        }
        $fieldset = $form->addFieldset('inventorysuccess_form', array(
            'legend' => Mage::helper('inventorysuccess')->__('General information'),
        ));

        $fieldset->addField('transferstock_code', 'text', array(
            'label'    => Mage::helper('inventorysuccess')->__('Transfer Code'),
            'class'    => 'required-entry',
            'required' => true,
            'name'     => 'transferstock_code',
            'disabled' => $this->getStepIndex() > 0 ? true : false,
        ));

        $fieldset->addField('type', 'hidden', array(
            'label'    => Mage::helper('inventorysuccess')->__('Type'),
            'class'    => 'required-entry',
            'required' => true,
            'name'     => 'type',
            'disabled' => $this->getStepIndex() > 0 ? true : false,
        ));

        $type_check = $this->getRequest()->getParam('type') ? $this->getRequest()->getParam('type') : $data['type'];

        if ( $type_check == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL ) {
            $resourceId = 'admin/inventorysuccess/create_transferstock/create_toexternal';
            $fieldset->addField('source_warehouse_id', 'select', array(
                'label'    => Mage::helper('inventorysuccess')->__('Source Warehouse'),
                'name'     => 'source_warehouse_id',
                'class'    => 'required-entry',
                'required' => true,
                'values'   => Magestore_Coresuccess_Model_Service::transferStockService()->getAvailableWarehousesArray($resourceId),
                'disabled' => $this->getStepIndex() > 0 ? true : false,
            ));
        } else {
            $resourceId = 'admin/inventorysuccess/create_transferstock/create_fromexternal';
            $fieldset->addField('des_warehouse_id', 'select', array(
                'label'    => Mage::helper('inventorysuccess')->__('Destination Warehouse'),
                'name'     => 'des_warehouse_id',
                'class'    => 'required-entry',
                'required' => true,
                //'values'   => Magestore_Coresuccess_Model_Service::warehouseOptionService()->getOptionArray(),
                'values'   => Magestore_Coresuccess_Model_Service::transferStockService()->getAvailableWarehousesArray($resourceId),
                'disabled' => $this->getStepIndex() > 0 ? true : false,
            ));
        }
        $fieldset->addField('external_location', 'text', array(
            'label'    => Mage::helper('inventorysuccess')->__('External Location'),
            'required' => true,
            'name'     => 'external_location',
            'disabled' => $this->getStepIndex() > 0 ? true : false,
        ));

        $fieldset->addField('notifier_emails', 'text', array(
            'label'    => Mage::helper('inventorysuccess')->__('Notification recipients'),
            'required' => false,
            'name'     => 'notifier_emails',
            'disabled' => $this->getStepIndex() > 1 ? true : false,
        ));
        $fieldset->addField('reason', 'textarea', array(
            'label'    => Mage::helper('inventorysuccess')->__('Reason'),
            'class'    => 'required-entry',
            'required' => true,
            'name'     => 'reason',
            'disabled' => $this->getStepIndex() > 1 ? true : false,
        ));

        if ( !array_key_exists('type', $data) ) {
            $data['type'] = $this->getRequest()->getParam('type');
        }
        $form->setValues($data);
        return parent::_prepareForm();
    }

    /**
     * get Step index 0: new, 1: pending
     * @return int
     */
    protected function getStepIndex()
    {
        /** @var Magestore_Inventorysuccess_Model_Transferstock $transfer */
        $transfer = Mage::registry('external_data');
        if ( $transfer && $transfer->getId() ) {
            if ( $transfer->getStatus() == Magestore_Inventorysuccess_Model_Transferstock::STATUS_PENDING ) {
                return 1;
            }
            return 2;
        }
        return 0;
    }
}