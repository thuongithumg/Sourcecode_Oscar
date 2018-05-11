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
class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Shortfallitem_Grid
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Abstractgrid
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Purchaseorder
     */
    protected $purchaseOrder;

    public function __construct()
    {
        parent::__construct();
        $this->setId('purchase_order_shortfall_item_list');
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
        $collection = $this->purchaseOrder->getItems();
        $collection->getSelect()
            ->columns(array(
                'shortfall_qty' => new \Zend_Db_Expr('qty_orderred - qty_received')
            ))->where('qty_orderred - qty_received > ?',  0);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Transferred item grid columns
     *
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn("product_sku",
            array(
                "header" => $this->__("Product SKU"),
                "index" => "product_sku",
                "sortable" => true,
            )
        )->addColumn("product_name",
            array(
                "header" => $this->__("Product Name"),
                "index" => "product_name",
                "sortable" => true,
            )
        )->addColumn("product_supplier_sku",
            array(
                "header" => $this->__("Supplier SKU"),
                "index" => "product_supplier_sku",
                "sortable" => true,
            )
        )->addColumn("qty_orderred",
            array(
                "header" => $this->__("Ordered Qty"),
                "index" => "qty_orderred",
                "sortable" => true,
                'type' => 'number'
            )
        )->addColumn("qty_received",
            array(
                "header" => $this->__("Received Qty"),
                "index" => "qty_received",
                "sortable" => true,
                'type' => 'number'
            )
        )->addColumn("shortfall_qty",
            array(
                "header" => $this->__("Shortfall Qty"),
                "index" => "shortfall_qty",
                "sortable" => true,
                'type' => 'number'
            )
        );

        $this->addExportType('*/purchaseordersuccess_purchaseorder_shortfallitem/exportCsv', $this->__('CSV'));
        $this->addExportType('*/purchaseordersuccess_purchaseorder_shortfallitem/exportXml', $this->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            "*/*/*",
            array("_current" => true, 'supplier_id' => $this->purchaseOrder->getSupplierId())
        );
    }
}