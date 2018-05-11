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

class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Invoice_Edit_Tabs 
    extends Mage_Adminhtml_Block_Widget_Tabs
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
        parent::__construct();
        $this->setId('purchase_order_invoice_tabs');
        $this->setDestElementId('edit_form');
        $this->purchaseOrder = Mage::registry('current_purchase_order');
        $this->invoice = Mage::registry('current_purchase_order_invoice');
        $this->setTitle($this->__('Invoice Information'));
    }

    /**
     * prepare before render block to html
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
//        $this->addTab('general_form', array(
//            'label' => $this->__('Billed From'),
//            'title' => $this->__('Billed From'),
//            'content' => $this->getLayout()
//                ->createBlock('purchaseordersuccess/adminhtml_purchaseorder_invoice_edit_tab_general')
//                ->toHtml(),
//        ));
        $this->addTab('summary_information', array(
            'label' => $this->__('Summary'),
            'title' => $this->__('Summary'),
            'content' => $this->getLayout()
                ->createBlock('purchaseordersuccess/adminhtml_purchaseorder_invoice_edit_tab_summary')
                ->toHtml(),
        ));
            $this->addTab('payment_list', array(
                'label' => $this->__('Payment List'),
                'url' => $this->getUrl('*/*/payment', array('_current' => true)),
                'class' => 'ajax'
            ));
            $this->addTab('refund_list', array(
                'label' => $this->__('Refund List'),
                'url' => $this->getUrl('*/*/refund', array('_current' => true)),
                'class' => 'ajax'
            ));

        return parent::_beforeToHtml();
    }
}