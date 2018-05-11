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
class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Invoice_Grid
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Abstractgrid
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Purchaseorder
     */
    protected $purchaseOrder;

    public function __construct()
    {
        parent::__construct();
        $this->setId('purchase_order_invoice_list');
        $this->setDefaultSort('invoice_code');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->purchaseOrder = Mage::registry('current_purchase_order');
    }

    /**
     * Prepare collection for grid product
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        /** @var Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Invoice_Collection $collection */
        $collection = $this->purchaseOrder->getInvoiceList();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Invoice grid columns
     *
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('invoice_code',
            array(
                'header' => $this->__('Invoice ID'),
                'align' => 'left',
                'index' => 'invoice_code'
            )
        )->addColumn("billed_at",
            array(
                "header" => $this->__("Bill Date"),
                "index" => "billed_at",
                "sortable" => true,
                'type' => 'date',
                'filter_condition_callback' => array($this, '_filterDateCallback')
            )
        )->addColumn("grand_total_incl_tax",
            array(
                "header" => $this->__("Invoice Total"),
                "index" => "grand_total_incl_tax",
                "sortable" => true,
                'type' => 'currency',
                'currency_code' => (string)$this->purchaseOrder->getCurrencyCode(),
                'rate' => 1
            )
        )->addColumn("total_paid",
            array(
                "header" => $this->__("Total Paid"),
                "index" => "total_paid",
                "sortable" => true,
                'type' => 'currency',
                'currency_code' => (string)$this->purchaseOrder->getCurrencyCode(),
                'rate' => 1
            )
        )->addColumn("total_refund",
            array(
                "header" => $this->__("Total Refund"),
                "index" => "total_refund",
                "sortable" => true,
                'type' => 'currency',
                'currency_code' => (string)$this->purchaseOrder->getCurrencyCode(),
                'rate' => 1
            )
        )->addColumn("total_qty_billed",
            array(
                "header" => $this->__("Billed Qty"),
                "index" => "total_qty_billed",
                "sortable" => true,
                "type" => 'number'
            )
        );
        if (!$this->_isExport)
            $this->addColumn('action',
                array(
                    'header' => $this->__('Action'),
                    'width' => '100',
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => array(
                        array(
                            'caption' => $this->__('View'),
                            'url' => array('base' => '*/purchaseordersuccess_purchaseorder_invoice/view'),
                            'field' => 'id'
                        )),
                    'filter' => false,
                    'sortable' => false,
                ));

        $this->addExportType('*/purchaseordersuccess_purchaseorder_invoice/exportCsv', $this->__('CSV'));
        $this->addExportType('*/purchaseordersuccess_purchaseorder_invoice/exportXml', $this->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            "*/purchaseordersuccess_purchaseorder_invoice/grid",
            array("_current" => true, 'supplier_id' => $this->purchaseOrder->getSupplierId())
        );
    }
}