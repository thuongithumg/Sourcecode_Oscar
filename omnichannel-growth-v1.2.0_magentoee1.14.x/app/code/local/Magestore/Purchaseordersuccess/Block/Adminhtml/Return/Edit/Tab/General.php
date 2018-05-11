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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Return
     */
    protected $returnRequest;

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->returnRequest = Mage::registry('current_return_request');
        $data = Mage::getSingleton('adminhtml/session')->getData('return_request_param');
        Mage::getSingleton('adminhtml/session')->setData('return_request_param', null);
        if (!$data) {
            $data = $this->returnRequest->getData();
            // initialize return date when create new request
            if (!$data) {
                $data['returned_at'] = (new DateTime())->format('Y-m-d');
            }
        }

        $disable = $this->returnRequest->getReturnOrderId() ? true : false;
        $supplierOptions = $disable ?
            Mage::getModel('purchaseordersuccess/purchaseorder_options_supplier')->getOptionArray() :
            Mage::getModel('purchaseordersuccess/purchaseorder_options_supplierEnable')->getOptionArray();
        $warehouseOptions = Mage::getModel('purchaseordersuccess/purchaseorder_options_warehouse')->getOptionArray();
        $fieldset = $form->addFieldset('return_request_general_form', array(
            'legend' => $this->__('General Information')
        ));
        $fieldset->addField('return_id',
            'hidden',
            array(
                'name' => 'return_id',
            )
        );
        $fieldset->addField('returned_at',
            'date',
            array(
                'name' => 'returned_at',
                'label' => $this->__('Created Time'),
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'required' => true,
                'disabled' => $disable,
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format' => Varien_Date::DATE_INTERNAL_FORMAT
            )
        );
        $fieldset->addField('warehouse_id',
            'select',
            array(
                'name' => 'warehouse_id',
                'label' => $this->__('Warehouse'),
                'values' => $warehouseOptions,
                'required' => true,
                'disabled' => $disable
            )
        );
        $fieldset->addField('supplier_id',
            'select',
            array(
                'name' => 'supplier_id',
                'label' => $this->__('Supplier'),
                'values' => $supplierOptions,
                'required' => true,
                'disabled' => $disable
            )
        );
        $fieldset->addField('reason',
            'textarea',
            array(
                'name' => 'reason',
                'label' => $this->__('Reason')
            )
        );


        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}