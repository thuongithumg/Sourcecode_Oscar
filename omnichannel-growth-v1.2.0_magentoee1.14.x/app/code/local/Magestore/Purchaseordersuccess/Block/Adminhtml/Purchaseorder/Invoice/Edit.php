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
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Type as PurchaseorderType;
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Status as PurchaseorderStatus;

class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Invoice_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Purchaseorder
     */
    protected $purchaseOrder;
    /**
     * @var Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice
     */
    protected $invoice;

    public function __construct()
    {
        $this->purchaseOrder = Mage::registry('current_purchase_order');
        $this->invoice = Mage::registry('current_purchase_order_invoice');
        
        parent::__construct();
        $this->_objectId = 'purchase_order_invoice_id';
        $this->_controller = 'adminhtml_purchaseorder_invoice';
        $this->_blockGroup = 'purchaseordersuccess';

        $this->_removeButton('back');
        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('save');

        $this->_addButton('back', array(
            'label' => Mage::helper('adminhtml')->__('Back'),
            'onclick' => 'setLocation(\'' . $this->getBackUrl() . '\')',
            'class' => 'back',
        ), -1);
    }


    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            '*/purchaseordersuccess_purchaseorder/view',
            array('id' => $this->purchaseOrder->getPurchaseOrderId())
        );
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__(
            'View Invoice #%s (Purchase Sale #%s)',
            $this->purchaseOrder->getPurchaseCode(),
            $this->invoice->getInvoiceCode()
        );
    }
}