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
class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Invoice_Edit_Tab_General
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Purchaseorder
     */
    protected $purchaseOrder;
    /**
     * @var Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice
     */
    protected $invoice;

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->purchaseOrder = Mage::registry('current_purchase_order');
        $this->invoice = Mage::registry('current_purchase_order_invoice');
        $data = $this->invoice->getData();
        if(!isset($data['billed_from'])){
            $supplier = Mage::getModel('suppliersuccess/supplier')->load($this->purchaseOrder->getSupplierId());
            $data['billed_from'] = $supplier->getSupplierName() . ' (' . $supplier->getSupplierCode() . ')';
        }
        $fieldset = $form->addFieldset('purchase_order_invoice_general_form', array(
            'legend' => $this->__('Billed From')
        ));
        $fieldset->addField('billed_from',
            'label',
            array(
                'name'      => 'billed_from',
                'time'      => false,
                'label'     => $this->__('Billed From'),
            )
        );
        $fieldset->addField('billed_at',
            'date',
            array(
                'name' => 'billed_at',
                'label' => $this->__('Bill Date'),
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'required' => true,
                'disabled' => true,
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format' => Varien_Date::DATE_INTERNAL_FORMAT
            )
        );
        
        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}