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
class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Invoice_Edit_Tab_Summary
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Invoice_Edit_Tab_Abstracttab
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice
     */
    protected $invoice;

    /**
     * @var string
     */
    protected $_template = 'purchaseordersuccess/purchaseorder/invoice/edit/tab/summary.phtml';

    /**
     * @var Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Grid
     */
    protected $blockGrid;

    /**
     * @var Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Header
     */
    protected $blockHeader;

    /**
     * @var Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Footer
     */
    protected $blockFooter;

    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->invoice = Mage::registry('current_purchase_order_invoice');    
    }
    
    /**
     * Retrieve instance of grid block
     *
     * @return Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Invoice_Edit_Tab_Summary_Grid
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'purchaseordersuccess/adminhtml_purchaseorder_invoice_edit_tab_summary_grid',
                'purchaseorder.invoice.summary.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }
    
    /**
     * Retrieve instance of grid block
     *
     * @return string
     */
    public function getInvoiceSummaryFooter()
    {
        if (null === $this->blockFooter) {
            $this->blockFooter = $this->getLayout()->createBlock(
                'purchaseordersuccess/adminhtml_purchaseorder_invoice_edit_tab_summary_footer',
                'purchaseorder.invoice.summary.footer'
            );
        }
        return $this->blockFooter->toHtml();
    }

    /**
     * @return string
     */
    public function getReloadTotalUrl()
    {
        return $this->getUrl('*/purchaseordersuccess_purchaseorder_invoice/reloadtotal', array('_current' => true));
    }

    /**
     * @return string
     */
    public function getSavePaymentUrl()
    {
        return $this->getUrl('*/purchaseordersuccess_purchaseorder_invoice_payment/save', array('_current' => true));
    }

    /**
     * @return string
     */
    public function getSaveRefundUrl()
    {
        return $this->getUrl('*/purchaseordersuccess_purchaseorder_invoice_refund/save', array('_current' => true));
    }
}