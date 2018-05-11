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
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Item as PurchaseorderItem;

class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Invoice_Edit_Tab_Payment_Grid
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Abstractgrid
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
        $this->setId('purchase_order_invoice_payment_list');
        $this->setDefaultSort('payment_at');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->purchaseOrder = Mage::registry('current_purchase_order');
        $this->invoice = Mage::registry('current_purchase_order_invoice');
    }

    /**
     * Prepare collection for grid product
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->invoice->getPayments();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Purchase Item grid columns
     *
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn("payment_at",
            array(
                "header" => $this->__("Payment Date"),
                "index" => "payment_at",
                "sortable" => true,
                'type' => 'date',
                'filter_condition_callback' => array($this, '_filterDateCallback')
            )
        );
        $this->addColumn("payment_amount",
            array(
                "header" => $this->__('Payment Amount'),
                "index" => "payment_amount",
                'type' => 'currency',
                'currency_code' => (string)$this->purchaseOrder->getCurrencyCode(),
                "rate" => 1
            )
        );
        $this->addColumn("payment_method",
            array(
                "header" => $this->__("Payment Method"),
                "index" => "payment_method",
            )
        );
        $this->addColumn("description",
            array(
                "header" => $this->__("Description"),
                "index" => "description",
            )
        );

        $this->addExportType('*/purchaseordersuccess_purchaseorder_invoice_payment/exportCsv', $this->__('CSV'));
        $this->addExportType('*/purchaseordersuccess_purchaseorder_invoice_payment/exportXml', $this->__('Excel XML'));
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            "*/purchaseordersuccess_purchaseorder_invoice_payment/grid",
            array("_current" => true, 'purchase_id' => $this->purchaseOrder->getPurchaseOrderId())
        );
    }
}