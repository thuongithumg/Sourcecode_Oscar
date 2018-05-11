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
class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Invoice_Edit_Tab_Refund
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Invoice_Edit_Tab_Abstracttab
{
    /**
     * @var string
     */
    protected $_template = 'purchaseordersuccess/purchaseorder/invoice/edit/tab/refund.phtml';

    /**
     * @var Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Grid
     */
    protected $blockGrid;
    
    /**
     * Retrieve instance of grid block
     *
     * @return Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Invoice_Edit_Tab_Refund_Grid
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'purchaseordersuccess/adminhtml_purchaseorder_invoice_edit_tab_refund_grid',
                'purchaseorder.invoice.refund.grid'
            );
        }
        return $this->blockGrid;
    }
    
    public function canRefund(){
        return $this->invoice->canRefund();
    }

    public function addRegisterRefundForm(){
        $html = $this->addField('purchase_order_invoice_id',
            'hidden',
            array(
                'name'      => 'purchase_order_invoice_id',
                'value'     => $this->invoice->getPurchaseOrderInvoiceId()
            )
        );
        $html .= $this->addField('register_refund_at',
            'date',
            array(
                'name'      => 'refund_at',
                'time'      => false,
                'required'  => true, 
                'label'     => $this->__('Refund Date'),
                'image'     => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format'       => Varien_Date::DATE_INTERNAL_FORMAT,
                'style'     => 'width: 250px;',
                'min_date' => $this->invoice->getBilledAt(),
                'value'     => new Zend_Date($this->invoice->getBilledAt(), Varien_Date::DATE_INTERNAL_FORMAT),
                'readonly'  => true
            )
        );
        $html .= $this->addField('refund_amount',
            'text',
            array(
                'name'      => 'refund_amount',
                'label'     => $this->__('Refund Amount'),
                'required'  => true,
                'class'     => 'validate-number validate-greater-than-zero',
            )
        );
        $html .= $this->addField('reason',
            'textarea',
            array(
                'name'      => 'reason',
                'label'     => $this->__('Reason'),
                'style'     => 'width: 300px; height: 200px'
            )
        );
        return $html;
    }
}