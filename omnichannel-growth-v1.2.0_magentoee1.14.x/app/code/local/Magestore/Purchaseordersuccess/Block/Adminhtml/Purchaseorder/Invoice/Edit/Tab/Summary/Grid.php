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

class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Invoice_Edit_Tab_Summary_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->setId('purchase_order_invoice_item_list');
        $this->setDefaultSort('purchase_order_invoice_item_id');
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
        $collection = $this->invoice->getItems();
        $collection->getSelect()->joinLeft(
            array('purchase_item' => Mage::getSingleton('core/resource')->getTableName('os_purchase_order_item')),
            'main_table.purchase_order_item_id = purchase_item.purchase_order_item_id',
            array('product_id', 'product_sku', 'product_name', 'product_supplier_sku')
        );
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
        $this->addColumn("product_sku",
            array(
                "header" => $this->__("SKU"),
                "index" => "product_sku",
                "sortable" => true,
            )
        );
        $this->addColumn("product_name",
            array(
                "header" => $this->__("Product Name"),
                "index" => "product_name",
                "sortable" => true,
            )
        );
        $this->addColumn("product_supplier_sku",
            array(
                "header" => $this->__("Supplier SKU"),
                "index" => "product_supplier_sku",
                "sortable" => true,
            )
        );
        $this->addColumn("qty_billed",
            array(
                "header" => $this->__('Billed Qty'),
                "index" => "qty_billed",
                'type' => 'number',
                "sortable" => true,
                'filter_condition_callback' => array($this, '_filterQtyBilledCallback')
            )
        );
        $this->addColumn("unit_price",
            array(
                "header" => $this->__('Unit Price'),
                "index" => "unit_price",
                'type' => 'currency',
                'currency_code' => (string)$this->purchaseOrder->getCurrencyCode(),
                "rate" => 1
            )
        );
        $this->addColumn("tax",
            array(
                "header" => $this->__("Tax (%)"),
                "index" => "tax",
                'type' => 'number',
            )
        );
        $this->addColumn("discount",
            array(
                "header" => $this->__("Discount (%)"),
                "index" => "discount",
                'type' => 'number',
            )
        );

        $this->addExportType('*/purchaseordersuccess_purchaseorder_invoice_item/exportCsv', $this->__('CSV'));
        $this->addExportType('*/purchaseordersuccess_purchaseorder_invoice_item/exportXml', $this->__('Excel XML'));
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            "*/purchaseordersuccess_purchaseorder_invoice_item/grid",
            array("_current" => true, 'purchase_id' => $this->purchaseOrder->getPurchaseOrderId())
        );
    }
    
    /**
     * Apply `qty` filter to product grid.
     *
     * @param $collection
     * @param $column
     */
    protected function _filterQtyBilledCallback($collection, $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        if ($column->getIndex() == 'qty_billed') {
            if (isset($value['from'])) {
                $collection->addFieldToFilter(
                    new \Zend_Db_Expr('main_table.qty_billed'),
                    array(
                        'gteq' => $value['from']
                    )
                );
            }
            if (isset($value['to'])) {
                $collection->addFieldToFilter(
                    new \Zend_Db_Expr('main_table.qty_billed'),
                    array(
                        'lteq' => $value['to']
                    )
                );
            }
        }
        return $collection;
    }
}