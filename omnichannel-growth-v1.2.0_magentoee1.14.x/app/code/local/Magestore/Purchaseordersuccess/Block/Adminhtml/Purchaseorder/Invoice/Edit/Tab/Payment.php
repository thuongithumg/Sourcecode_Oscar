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
class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Invoice_Edit_Tab_Payment
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Invoice_Edit_Tab_Abstracttab
{
    /**
     * @var string
     */
    protected $_template = 'purchaseordersuccess/purchaseorder/invoice/edit/tab/payment.phtml';

    /**
     * @var Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Grid
     */
    protected $blockGrid;
    
    /**
     * Retrieve instance of grid block
     *
     * @return Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Invoice_Edit_Tab_Payment_Grid
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'purchaseordersuccess/adminhtml_purchaseorder_invoice_edit_tab_payment_grid',
                'purchaseorder.invoice.payment.grid'
            );
        }
        return $this->blockGrid;
    }
    
    public function canPayment(){
        return $this->invoice->canPayment();
    }

    public function addRegisterPaymentForm(){
        $html = $this->addField('purchase_order_invoice_id',
            'hidden',
            array(
                'name'      => 'purchase_order_invoice_id',
                'value'     => $this->invoice->getPurchaseOrderInvoiceId()
            )
        );
        $html .= $this->addField('register_payment_at',
            'date',
            array(
                'name'      => 'payment_at',
                'time'      => false,
                'required'  => true, 
                'label'     => $this->__('Payment Date'),
                'image'     => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format'       => Varien_Date::DATE_INTERNAL_FORMAT,
                'style'     => 'width: 250px;',
                'class'     => 'validate-date',
                'min_date' => $this->invoice->getBilledAt(),
                'value'     => new Zend_Date($this->invoice->getBilledAt(), Varien_Date::DATE_INTERNAL_FORMAT),
                'readonly'  => true
            )
        );
        $paymentMethod = Mage::getModel('purchaseordersuccess/purchaseorder_options_paymentMethod')->getOptionHash();
        $html .= $this->addField('payment_method',
            'select',
            array(
                'name'      => 'payment_method',
                'label'     => $this->__('Payment Method'),
                'options'   => $paymentMethod,
                'required'  => true,
            )
        );
        $html .= $this->addField('new_payment_method',
            'text',
            array(
                'name'      => 'new_payment_method',
                'label'     => $this->__('New Payment Method'),
                'required'  => true,
            )
        );
        $html .= $this->addField('payment_amount',
            'text',
            array(
                'name'      => 'payment_amount',
                'label'     => $this->__('Payment Amount'),
                'required'  => true,
                'class'     => 'validate-number validate-greater-than-zero',
            )
        );
        $html .= $this->addField('description',
            'textarea',
            array(
                'name'      => 'description',
                'label'     => $this->__('Description'),
                'style'     => 'width: 300px; height: 200px'
            )
        );
        return $html;
    }
}