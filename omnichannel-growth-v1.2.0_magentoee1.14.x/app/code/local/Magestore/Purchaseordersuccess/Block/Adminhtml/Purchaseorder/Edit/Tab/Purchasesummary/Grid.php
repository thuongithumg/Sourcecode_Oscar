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

class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Purchaseorder
     */
    protected $purchaseOrder;

    /**
     * @var array
     */
    protected $selectedId;

    /**
     * @var array
     */
    protected $selectedItemData = array();
    /**
     * @var string
     */
    protected $hiddenInputField = 'selected_items';

    /**
     * @var array
     */
    protected $editFields = array('cost', 'tax', 'discount', 'qty_orderred');

    public function __construct()
    {
        parent::__construct();
        $this->setId('purchase_order_item_list');
        $this->setDefaultSort('purchase_order_item_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->purchaseOrder = Mage::registry('current_purchase_order');
    }

    /**
     * Set hidden input field name for selected products
     *
     * @param $name
     */
    protected function setHiddenInputField($name)
    {
        $this->hiddenInputField = $name;
    }

    /**
     * get hidden input field name for selected products
     *
     * @return string
     */
    public function getHiddenInputField()
    {
        return $this->hiddenInputField;
    }

    /**
     * Prepare collection for grid product
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->getDataColllection();
        $collection = $this->modifyCollection($collection);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Item_Collection
     */
    protected function getDataColllection()
    {
        $collection = $this->purchaseOrder->getItems();
        return $collection;
    }

    /**
     * @return Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Item_Collection
     */
    protected function modifyCollection($collection)
    {
        $selectedItems = $this->_getSelectedItems();
        $filter = $this->getParam($this->getVarNameFilter(), null);
        $data = $this->helper('adminhtml')->prepareFilterString($filter);
        if (array_key_exists('in_purchase_order', $data)) {
            $condition = 'nin';
            if ($data['in_purchase_order'] == '1')
                $condition = 'in';
            $collection->addFieldToFilter('product_id', array($condition => $selectedItems));
        }
        $collection->getSelect()
            ->order(new \Zend_Db_Expr('FIELD(product_id, "' . implode('","', $selectedItems) . '") DESC'));
        return $collection;
    }

    /**
     * Prepare Purchase Item grid columns
     *
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $editable = false;
        $status = $this->purchaseOrder->getStatus();
        if ($status == PurchaseorderStatus::STATUS_PENDING) {
            $editable = true;
            $this->addColumn(
                "in_purchase_order",
                array(
                    "type" => "checkbox",
                    "name" => "in_purchase_order",
                    "values" => $this->_getSelectedItems(),
                    "index" => "product_id",
                    "use_index" => true,
                    "header_css_class" => "col-select col-massaction a-center",
                    "column_css_class" => "col-select col-massaction",
                    'filter' => false,
                    "align" => "center"
                )
            );
        }
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
        $this->addColumn("original_cost",
            array(
                "header" => $this->__("Current Cost") . ' (' . $this->purchaseOrder->getCurrencyCode() . ')',
                "index" => "original_cost",
                'type' => 'number',
                "sortable" => true,
            )
        );
        $this->addColumn("cost",
            array(
                "header" => $this->__("Purchase Cost") . ' (' . $this->purchaseOrder->getCurrencyCode() . ')',
                'renderer' => 'purchaseordersuccess/adminhtml_grid_column_renderer_text',
                "index" => "cost",
                'type' => 'number',
                "editable" => $editable,
                "sortable" => true
            )
        );
        $this->addColumn("tax",
            array(
                "header" => $this->__("Tax (%)"),
                'renderer' => 'purchaseordersuccess/adminhtml_grid_column_renderer_text',
                "index" => "tax",
                'type' => 'number',
                "editable" => $editable,
                "sortable" => true
            )
        );
        $this->addColumn("discount",
            array(
                "header" => $this->__("Discount (%)"),
                'renderer' => 'purchaseordersuccess/adminhtml_grid_column_renderer_text',
                "index" => "discount",
                'type' => 'number',
                "editable" => $editable,
                "sortable" => true
            )
        );
        $this->addColumn("qty_orderred",
            array(
                "header" => $this->__("Qty Ordering"),
                'renderer' => 'purchaseordersuccess/adminhtml_grid_column_renderer_text',
                "index" => "qty_orderred",
                'type' => 'number',
                "editable" => $editable,
                "sortable" => true
            )
        );
        if ($status == PurchaseorderStatus::STATUS_PENDING) {
            $this->addColumn("delete",
                array(
                    "header" => $this->__("Action"),
                    'renderer' => 'purchaseordersuccess/adminhtml_grid_column_renderer_delete',
                    'filter' => false,
                    'sortable' => false,
                )
            );
        } else {
            $this->addColumn("qty_received",
                array(
                    "header" => $this->__("Received Qty"),
                    "index" => "qty_received",
                    'type' => 'number',
                    "sortable" => true
                )
            );
            $this->addColumn("qty_returned",
                array(
                    "header" => $this->__("Returned Qty"),
                    "index" => "qty_returned",
                    'type' => 'number',
                    "sortable" => true
                )
            );
            $this->addColumn("qty_billed",
                array(
                    "header" => $this->__("Billed Qty"),
                    "index" => "qty_billed",
                    'type' => 'number',
                    "sortable" => true
                )
            );
        }
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            "*/purchaseordersuccess_purchaseorder_item/grid",
            array("_current" => true, 'supplier_id' => $this->purchaseOrder->getSupplierId())
        );
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl(
            '*/purchaseordersuccess_purchaseorder_item/save',
            array("_current" => true, 'supplier_id' => $this->purchaseOrder->getSupplierId())
        );
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/purchaseordersuccess_purchaseorder_item/delete');
    }

    /**
     * @return string
     */
    public function getReloadTotalUrl()
    {
        return $this->getUrl('*/purchaseordersuccess_purchaseorder_item/reloadtotal', array('_current' => true));
    }

    /**
     * @return array
     */
    protected function _getSelectedItems()
    {
        if (empty($this->selectedId)) {
            $direction = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            if($direction == 'desc')
                $direction = 'asc';
            else
                $direction = 'desc';
            $collection = $this->getDataColllection()
                ->addFieldToFilter(PurchaseorderItem::QTY_ORDERRED, array('lteq' => 0))
                ->addOrder(
                    $this->getParam($this->getVarNameSort(), $this->_defaultSort),
                    $direction
                );
            /** @var PurchaseorderItem $item */
            foreach ($collection as $item) {
                $this->selectedItemData[$item->getProductId()] = array(
                    PurchaseorderItem::COST => $item->getCost(),
                    PurchaseorderItem::COST . '_old' => $item->getCost(),
                    PurchaseorderItem::TAX => $item->getTax(),
                    PurchaseorderItem::TAX . '_old' => $item->getTax(),
                    PurchaseorderItem::DISCOUNT => $item->getDiscount(),
                    PurchaseorderItem::DISCOUNT . '_old' => $item->getDiscount(),
                    PurchaseorderItem::QTY_ORDERRED => $item->getQtyOrderred(),
                    PurchaseorderItem::QTY_ORDERRED . '_old' => $item->getQtyOrderred(),
                );
            }
            $this->selectedId = array_keys($this->selectedItemData);
        }
        return $this->selectedId;
    }

    /**
     * @return string
     */
    public function getSelectedItems()
    {
        return Zend_Json::encode($this->_getSelectedItems());
    }

    /**
     * @return array
     */
    public function getEditFields()
    {
        return Zend_Json::encode($this->editFields);
    }

    /**
     * @return string
     */
    public function getPriceListJson()
    {
//        if (!Mage::getStoreConfig('suppliersuccess/pricelist/enable')) {
//            return Zend_Json::encode(array());
//        }
        $time = $this->purchaseOrder->getPurchasedAt();
        $supplierId = $this->purchaseOrder->getSupplierId();
        $priceListJson = Mage::getModel('purchaseordersuccess/supplier')
            ->getPriceListJson($supplierId, null, $time, $this->purchaseOrder);
        return Zend_Json::encode($priceListJson);
    }
}