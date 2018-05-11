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
class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Invoice_Invoice
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Abstracttab
{
    /**
     * @var string
     */
    protected $_template = 'purchaseordersuccess/purchaseorder/edit/tab/invoice/invoice.phtml';

    /**
     * @var array
     */
    protected $reloadTabs = array('purchase_order_tabs_invoice');

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->purchaseOrder->canInvoice())
            return parent::_toHtml();
        else
            return '';
    }
    
    /**
     * Retrieve instance of grid block
     *
     * @return Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Invoice_Invoice_Grid
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'purchaseordersuccess/adminhtml_purchaseorder_edit_tab_invoice_invoice_grid',
                'purchaseorder.invoice.invoice.grid'
            );
        }
        return $this->blockGrid;
    }

    public function getPurchaseCode(){
        return $this->purchaseOrder->getPurchaseCode();
    }

    /**
     * Add Returned Time Field
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function addInvoiceField(){
        $supplier = Mage::getModel('suppliersuccess/supplier')->load($this->purchaseOrder->getSupplierId());
        $html = $this->addField('billed_from',
            'label',
            array(
                'name'      => 'billed_from',
                'time'      => false,
                'label'     => $this->__('Billed From'),
                'value'     => $supplier->getSupplierName() . ' (' . $supplier->getSupplierCode() . ')'
            )
        );
        $html .= $this->addField('billed_at',
            'date',
            array(
                'name'      => 'billed_at',
                'time'      => false,
                'label'     => $this->__('Bill Date'),
                'image'     => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format'       => Varien_Date::DATE_INTERNAL_FORMAT,
                'required' => true,
                'class'     => 'validate-date',
                'min_date' => $this->purchaseOrder->getPurchasedAt(),
                'value'     => new Zend_Date($this->purchaseOrder->getPurchasedAt(), Varien_Date::DATE_INTERNAL_FORMAT),
                'readonly'  => true
            )
        );
        return $html;
    }

    public function getForm(){
        if(!$this->form)
            $this->form = $this->getLayout()
                ->createBlock('Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Invoice_Invoice_Form');
        return $this->form;
    }
}