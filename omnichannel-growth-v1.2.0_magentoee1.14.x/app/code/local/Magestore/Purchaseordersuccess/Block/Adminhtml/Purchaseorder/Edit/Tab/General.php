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
class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Purchaseorder
     */
    protected $purchaseOrder;

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->purchaseOrder = Mage::registry('current_purchase_order');
        $data = Mage::getSingleton('adminhtml/session')->getData('purchase_order_param');
        Mage::getSingleton('adminhtml/session')->setData('purchase_order_param', null);
        if (!$data) {
            $data = $this->purchaseOrder->getData();
            // initialize return date when create new request
            if (!$data) {
                $data['purchased_at'] = (new DateTime())->format('Y-m-d');
            }
        }
        $data['currency_code'] = isset($data['currency_code']) ? $data['currency_code'] : Mage::app()->getStore()->getBaseCurrencyCode();
        $data['currency_rate'] = isset($data['currency_rate']) ? $data['currency_rate'] : 1;
        $data['type'] = $this->purchaseOrder->getType() ?
            $this->purchaseOrder->getType() :
            $this->getRequest()->getParam('type');

        $disable = $this->purchaseOrder->getPurchaseOrderId() ? true : false;
        $supplierOptions = $disable ?
            Mage::getModel('purchaseordersuccess/purchaseorder_options_supplier')->getOptionArray() :
            Mage::getModel('purchaseordersuccess/purchaseorder_options_supplierEnable')->getOptionArray();
        $fieldset = $form->addFieldset('purchase_order_general_form', array(
            'legend' => $this->__('General Information')
        ));
        $fieldset->addField('purchase_order_id',
            'hidden',
            array(
                'name' => 'purchase_order_id',
            )
        );
        $fieldset->addField('type',
            'hidden',
            array(
                'name' => 'type',
            )
        );
        $fieldset->addField('purchased_at',
            'date',
            array(
                'name' => 'purchased_at',
                'label' => $this->__('Created Time'),
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'required' => true,
                'disabled' => $disable,
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format' => Varien_Date::DATE_INTERNAL_FORMAT
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
        $fieldset->addField('currency_code',
            'select',
            array(
                'name' => 'currency_code',
                'label' => $this->__('Currency'),
                'value' => Mage::app()->getLocale()->getCurrency(),
                'values' => Mage::app()->getLocale()->getOptionCurrencies(),
                'required' => true,
                'disabled' => $disable,
            )
        );
        $baseCurrencyCode = Mage::app()->getLocale()->getCurrency();
        $note = 'note';
        if($this->purchaseOrder->getPurchaseOrderId()){
            $note = "(1 {$baseCurrencyCode} = {$this->purchaseOrder->getCurrencyRate()} {$this->purchaseOrder->getCurrencyCode()})";
        }
        $fieldset->addField('currency_rate',
            'text',
            array(
                'name' => 'currency_rate',
                'label' => $this->__('Currency Exchange Rate'),
                'required' => true,
                'disabled' => $disable,
                'class' => 'validate-number validate-greater-than-zero',
                'note' => $note,
            )
        );
        $fieldset->addField('comment',
            'textarea',
            array(
                'name' => 'comment',
                'label' => $this->__('Comment')
            )
        );


        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}