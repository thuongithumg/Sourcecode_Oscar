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
class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Shippingpayment
    extends Mage_Adminhtml_Block_Widget_Form
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
        }
        $data['shipping_method'] = !isset($data['shipping_method']) ?
            Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_ShippingMethod::OPTION_NONE_VALUE :
            $data['shipping_method'];
        $data['payment_term'] = !isset($data['payment_term']) ?
            Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_PaymentTerm::OPTION_NONE_VALUE :
            $data['payment_term'];

        $shippingMethod = Mage::getModel('purchaseordersuccess/purchaseorder_options_shippingMethod')->getOptionArray();
        $paymentTerm = Mage::getModel('purchaseordersuccess/purchaseorder_options_paymentTerm')->getOptionArray();
        $orderSource = Mage::getModel('purchaseordersuccess/purchaseorder_options_orderSource')->getOptionArray();
        
        $fieldset = $form->addFieldset('purchase_order_shipping_payment_form', array(
            'legend' => $this->__('Shipping and Payment')
        ));
        
        $fieldset->addField('shipping_address',
            'textarea',
            array(
                'name' => 'shipping_address',
                'label' => $this->__('Shipping Address')
            )
        );
        $shippingMethodField = $fieldset->addField('shipping_method',
            'select',
            array(
                'name' => 'shipping_method',
                'label' => $this->__('Shipping Method'),
                'values' => $shippingMethod,
            )
        );
        $newShippingMethodField = $fieldset->addField('new_shipping_method',
            'text',
            array(
                'name' => 'new_shipping_method',
                'label' => $this->__('New Shipping Method'),
                'required' => true,
            )
        );
        $fieldset->addField('shipping_cost',
            'text',
            array(
                'name' => 'shipping_cost',
                'label' => $this->__('Shipping Cost'),
            )
        );
        $fieldset->addField('started_at',
            'date',
            array(
                'name' => 'started_at',
                'label' => $this->__('Shipment Start Date'),
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format' => Varien_Date::DATE_INTERNAL_FORMAT
            )
        );
        $fieldset->addField('expected_at',
            'date',
            array(
                'name' => 'expected_at',
                'label' => $this->__('Expected Delivery Date'),
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format' => Varien_Date::DATE_INTERNAL_FORMAT
            )
        );
        $paymentTermField = $fieldset->addField('payment_term',
            'select',
            array(
                'name' => 'payment_term',
                'label' => $this->__('Payment Term'),
                'values' => $paymentTerm,
            )
        );
        $newPaymentTermField = $fieldset->addField('new_payment_term',
            'text',
            array(
                'name' => 'new_payment_term',
                'label' => $this->__('New Payment Term'),
                'required' => true,
            )
        );
        $fieldset->addField('placed_via',
            'select',
            array(
                'name' => 'placed_via',
                'label' => $this->__('Order placed via'),
                'values' => $orderSource,
            )
        );
        
        $form->setValues($data);
        $this->setForm($form);
        // field dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($shippingMethodField->getHtmlId(), $shippingMethodField->getName())
            ->addFieldMap($newShippingMethodField->getHtmlId(), $newShippingMethodField->getName())
            ->addFieldMap($paymentTermField->getHtmlId(), $paymentTermField->getName())
            ->addFieldMap($newPaymentTermField->getHtmlId(), $newPaymentTermField->getName())
            ->addFieldDependence(
                $newShippingMethodField->getName(),
                $shippingMethodField->getName(),
                Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_ShippingMethod::OPTION_NEW_VALUE)
            ->addFieldDependence(
                $newPaymentTermField->getName(),
                $paymentTermField->getName(),
                Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_PaymentTerm::OPTION_NEW_VALUE)
        );
        return parent::_prepareForm();
    }
}