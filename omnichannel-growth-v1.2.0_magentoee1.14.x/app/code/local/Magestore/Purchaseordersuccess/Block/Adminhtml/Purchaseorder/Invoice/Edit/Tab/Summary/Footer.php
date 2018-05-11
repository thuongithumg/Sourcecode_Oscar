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
class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Invoice_Edit_Tab_Summary_Footer
    extends Mage_Adminhtml_Block_Template
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
     * @var Magestore_Suppliersuccess_Model_Supplier
     */
    protected $supplier;

    /**
     * @var Mage_Directory_Model_Currency
     */
    protected $currency; 
    
    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->purchaseOrder = Mage::registry('current_purchase_order');
        $this->invoice = Mage::registry('current_purchase_order_invoice');
        $this->supplier = Mage::getModel('suppliersuccess/supplier')->load($this->purchaseOrder->getSupplierId());
    }

    /**
     * @var string
     */
    protected $_template = 'purchaseordersuccess/purchaseorder/invoice/edit/tab/summary/footer.phtml';

    public function getPriceFormat($code)
    {
        if (!$this->currency)
            $this->currency = Mage::getModel('directory/currency')->load($this->purchaseOrder->getCurrencyCode());
        return $this->currency->formatTxt($this->getPrice($code));
    }

    public function getPrice($code){
        return $this->invoice->getData($code);
    }
    
    public function getBilledFrom()
    {
        $html = $this->supplier->getSupplierName() . ' (' . $this->supplier->getSupplierCode() . ')';
        return $html;
    }

    /**
     * Get purchase date of PO
     *
     * @return string
     */
    public function getBilledDate()
    {
        return $this->formatDate(
            $this->invoice->getBilledAt(),
            Mage_Core_Model_Locale::FORMAT_TYPE_LONG
        );
    }
}