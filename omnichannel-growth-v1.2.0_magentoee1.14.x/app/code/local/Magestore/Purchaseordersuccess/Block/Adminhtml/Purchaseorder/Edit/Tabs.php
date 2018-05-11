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
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Status as PurchaseorderStatus;

class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * @var int
     */
    protected $purchaseType;

    /**
     * @var Magestore_Purchaseordersuccess_Model_Purchaseorder
     */
    protected $purchaseOrder;

    public function __construct()
    {
        parent::__construct();
        $this->setId('purchase_order_tabs');
        $this->setDestElementId('edit_form');
        $title = '';
        /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder */
        $this->purchaseOrder = Mage::registry('current_purchase_order');
        if ($this->purchaseOrder->getPurchaseOrderId()) {
            $this->purchaseType = $this->purchaseOrder->getType();
        } else {
            $this->purchaseType = $this->getRequest()->getParam('type');
        }
        if ($this->purchaseType) {
            if ($this->purchaseType == Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Type::TYPE_QUOTATION)
                $title = 'Quotation Information';
            else
                $title = 'Purchase Order Information';
        }
        $this->setTitle($title);
    }

    /**
     * prepare before render block to html
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $status = $this->purchaseOrder->getStatus();
        if ($this->purchaseOrder->getPurchaseOrderId()) {
            $this->addTab('summary_information', array(
                'label' => $this->__('Summary'),
                'title' => $this->__('Summary'),
                'content' => $this->getLayout()
                    ->createBlock('purchaseordersuccess/adminhtml_purchaseorder_edit_tab_purchasesummary')
                    ->toHtml(),
            ));
            if ($status != PurchaseorderStatus::STATUS_PENDING) {
                $this->addTab('received_item', array(
                    'label' => $this->__('Received Item'),
                    'url' => $this->getUrl('*/*/receiveditem', array('_current' => true)),
                    'class' => 'ajax'
                ));
                if (in_array($status, array(PurchaseorderStatus::STATUS_COMPLETED, PurchaseorderStatus::STATUS_CANCELED))
                    && ($this->purchaseOrder->getTotalQtyOrderred() - $this->purchaseOrder->getTotalQtyReceived()) > 0
                )
                    $this->addTab('shortfall_item', array(
                        'label' => $this->__('Shortfall Item'),
                        'url' => $this->getUrl('*/*/shortfallitem', array('_current' => true)),
                        'class' => 'ajax'
                    ));
                $this->addTab('returned_item', array(
                    'label' => $this->__('Returned Item'),
                    'url' => $this->getUrl('*/*/returneditem', array('_current' => true)),
                    'class' => 'ajax'
                ));
                $this->addTab('invoice', array(
                    'label' => $this->__('Invoices'),
                    'url' => $this->getUrl('*/*/invoice', array('_current' => true)),
                    'class' => 'ajax'
                ));
                if (Mage::helper('purchaseordersuccess')->isModuleEnabled('Magestore_Inventorysuccess')) {
                    $this->addTab('transferred_item', array(
                        'label' => $this->__('Transferred Item'),
                        'url' => $this->getUrl('*/*/transferreditem', array('_current' => true)),
                        'class' => 'ajax'
                    ));
                }
            }
            $this->addTab('shipping_and_payment', array(
                'label' => $this->__('Shipping and Payment'),
                'title' => $this->__('Shipping and Payment'),
                'content' => $this->getLayout()
                    ->createBlock('purchaseordersuccess/adminhtml_purchaseorder_edit_tab_shippingpayment')
                    ->toHtml(),
            ));
        }

        /** information form */
        $this->addTab('general_information', array(
            'label' => $this->__('General Information'),
            'title' => $this->__('General Information'),
            'content' => $this->getLayout()
                ->createBlock('purchaseordersuccess/adminhtml_purchaseorder_edit_tab_general')
                ->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}