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
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Item as PurchaseorderItem;

class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Abstractmodalgrid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Purchaseorder
     */
    protected $purchaseOrder;

    /**
     * @var Magestore_Suppliersuccess_Model_Supplier
     */
    protected $supplier;

    /**
     * Grid ID
     *
     * @var string
     */
    protected $gridId = 'purchase_order_all_supplier_product_list';
    /**
     * @var string
     */
    protected $hiddenInputField = 'selected_all_supplier_products';

    /**
     * @var string
     */
    protected $modalName = 'allsupplierproduct';

    /**
     * @var array
     */
    protected $editFields = array('cost', 'qty_orderred');

    protected $availableField = 'main_table.qty_orderred - main_table.qty_received';


    public function __construct()
    {
        parent::__construct();
        $this->setId($this->gridId);
        $this->setDefaultSort('supplier_product_id');
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
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Item_Collection
     */
    protected function getDataColllection()
    {
        $rate = $this->purchaseOrder->getCurrencyRate();
        $supplierId = $this->getRequest()->getParam('supplier_id', null);
        if(Mage::helper('purchaseordersuccess')->isProductFromSupplier()) {
            /** @var Magestore_Suppliersuccess_Model_Mysql4_Supplier_Product_Collection $collection */
            $collection = Mage::getResourceModel('suppliersuccess/supplier_product_collection');
            $collection->getSelect()->columns(array(
                'cost' => "ROUND(main_table.cost * {$rate}, 2)",
                'qty_orderred' => "(0)",
            ));
            $collection->addFieldToFilter('supplier_id', $supplierId);
        } else {
            /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
            $collection = Mage::getResourceModel('catalog/product_collection');
            $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'name');
            $attributeNameId = $attribute->getId();
            $collection->getSelect()->joinLeft(
                array(
                    'product_entity_varchar' => Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_varchar')
                ),
                'product_entity_varchar.attribute_id = ' . $attributeNameId .
                ' AND product_entity_varchar.entity_id = e.entity_id' .
                ' AND product_entity_varchar.store_id = 0',
                array('value')
            );
            $collection->getSelect()->columns(array(
                'cost' => "(0)",
                'qty_orderred' => "(0)",
                'product_id' => "e.entity_id",
                'product_sku' => "e.sku",
                'product_supplier_sku' => "",
                'product_name' => "product_entity_varchar.value",
            ));
        }
        if ($supplierId) {
            $productIds = $this->purchaseOrder->getItems()->getColumnValues('product_id');
            if (!empty($productIds)) {
                if(Mage::helper('purchaseordersuccess')->isProductFromSupplier()) {
                    $collection->addFieldToFilter('product_id', array('nin' => $productIds));
                } else {
                    $collection->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            }
        }
        return $collection;
    }

    /**
     * Prepare grid columns
     *
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            "product_id",
            array(
                "type" => "checkbox",
                "name" => "product_id",
                "index" => "product_id",
                "use_index" => true,
                "header_css_class" => "col-select col-massaction a-center",
                "column_css_class" => "col-select col-massaction",
                "align" => "center",
                "filter"    => false
            )
        );
        $this->addColumn("product_sku",
            array(
                "header" => $this->__("Product SKU"),
                "index" => "product_sku",
                "width" => '120',
                'sortable' => false
            )
        );
        if(Mage::helper('purchaseordersuccess')->isProductFromSupplier()) {
            $this->addColumn("product_supplier_sku",
                array(
                    "header" => $this->__("Supplier SKU"),
                    "index" => "product_supplier_sku",
                    "width" => '120',
                    'sortable' => false
                )
            );
        }
        $this->addColumn("product_name",
            array(
                "header" => $this->__("Product Name"),
                "index" => "product_name",
                "width" => '200',
                'sortable' => false
            )
        );
        $this->addColumn("current_cost",
            array(
                "header" => $this->__("Current Cost") . ' (' . $this->purchaseOrder->getCurrencyCode() . ')',
                "index" => "cost",
                'type' => 'currency',
                'currency_code' => (string)$this->purchaseOrder->getCurrencyCode(),
                'rate' => 1,
                'filter' => false,
                'sortable' => false
            )
        );
        $this->addColumn("cost",
            array(
                "header" => $this->__("Purchase Cost") . ' (' . $this->purchaseOrder->getCurrencyCode() . ')',
                'renderer' => 'purchaseordersuccess/adminhtml_grid_column_renderer_text',
                "index" => "cost",
                'type' => 'number',
                "editable" => true,
                'filter'    => false,
                'sortable'  => false
            )
        );
        $this->addColumn("qty_orderred",
            array(
                "header" => $this->__("Qty Ordered"),
                'renderer' => 'purchaseordersuccess/adminhtml_grid_column_renderer_text',
                "index" => "qty_orderred",
                'type' => 'number',
                "editable" => true,
                "sortable"  => false,
                "filter"    => false
            )
        );

        $this->modifyColumn();

        return parent::_prepareColumns();
    }

    /**
     * Modify modal grid columns
     *
     * @return $this
     */
    protected function modifyColumn()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            "*/purchaseordersuccess_purchaseorder_item/modal",
            array("_current" => true, 'supplier_id' => $this->purchaseOrder->getSupplierId(), 'modal' => $this->modalName)
        );
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl(
            '*/purchaseordersuccess_purchaseorder_item/save',
            array("_current" => true, 'supplier_id' => $this->purchaseOrder->getSupplierId(), 'modal' => $this->modalName)
        );
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
    public function getSelectedItems()
    {
        return Zend_Json::encode(array());
    }

    /**
     * @return string
     */
    public function getReloadTotalUrl()
    {
        return $this->getUrl('*/purchaseordersuccess_purchaseorder_item/reloadtotal', array('_current' => true));
    }

    /**
     * Apply `qty` filter to product grid.
     *
     * @param $collection
     * @param $column
     */
    protected function _filterAvailableQtyCallback($collection, $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        if ($column->getIndex() == 'available_qty') {
            if (isset($value['from'])) {
                $collection->addFieldToFilter(
                    new \Zend_Db_Expr($this->availableField),
                    array(
                        'gteq' => $value['from']
                    )
                );
            }
            if (isset($value['to'])) {
                $collection->addFieldToFilter(
                    new \Zend_Db_Expr($this->availableField),
                    array(
                        'lteq' => $value['to']
                    )
                );
            }
        }
        return $collection;
    }

    protected function _addColumnFilterToCollection($column) {
        if (!Mage::app()->getRequest()->getParam('supplier_id')) {
            return $column;
        }

        $column = $this->verifyIndexColumn($column);

        parent::_addColumnFilterToCollection($column);
    }

    protected function _setCollectionOrder($column) {
        if (!Mage::app()->getRequest()->getParam('supplier_id')) {
            return $column;
        }

        $column = $this->verifyIndexColumn($column);

        parent::_setCollectionOrder($column);
    }

    protected function verifyIndexColumn($column) {
        if(!Mage::helper('purchaseordersuccess')->isProductFromSupplier()) {
            if ($column->getFilterIndex() == 'product_sku' || $column->getIndex() == 'product_sku') {
                $column->setFilterIndex('sku');
            }
            if (in_array($column->getFilterIndex(), ['product_name'])
                || in_array($column->getIndex(), ['product_name'])) {
                $column->setFilterIndex('name');
            }
        }

        return $column;
    }
}