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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */


/**
 * Inventorysuccess Warehouses Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_ManageStock_AbstractGridProduct extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var string
     */
    protected $hiddenInputField = 'selected_products';

    /**
     * @var array
     */
    protected $editFields = array('sum_total_qty', 'shelf_location');

    public function __construct()
    {
        parent::__construct();
        $this->setId('warehouse_stock_list');
        $this->setDefaultSort('product_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
//        $this->setSaveParametersInSession(true);
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
     * Get Collection for grid product
     *
     * @return Collection
     */
    public function getDataColllection()
    {
        $collection = Magestore_Coresuccess_Model_Service::warehouseStockService()->getAllStocksWithProductInformation();
        $collection = $this->modifyCollection($collection);
        $sort = $this->getRequest()->getParam('sort');
        $dir = $this->getRequest()->getParam('dir');
        $mappingFields = Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Product_Collection::getMappingFields();
        if (array_key_exists($sort, $mappingFields)) {
            $collection->getSelect()->order($mappingFields[$sort] . ' ' . $dir);
        }
        return $collection;
    }

    /**
     * function to modify collection
     *
     * @param $collection
     * @return $collection
     */
    public function modifyCollection($collection)
    {
        return $collection;
    }

    /**
     * Prepare warehouse product grid columns
     *
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $permission = Magestore_Coresuccess_Model_Service::permissionService();
        if (!$warehouseId = $this->getRequest()->getParam('warehouse_id'))
            $warehouseId = $this->getRequest()->getParam('id');
        $warehouse = Mage::getModel('inventorysuccess/warehouse')->load($warehouseId);
        $this->addColumn(
            "in_warehouse",
            array(
                "type" => "checkbox",
                "name" => "in_warehouse",
                "filter" => false,
                "index" => "product_id",
                'use_index' => true,
                'header_css_class' => 'a-center',
                'align' => 'center',
                'is_system' => true,
            )
        );
        $this->addColumn("sku",
            array(
                "header" => $this->__("SKU"),
                "index" => "sku",
                "sortable" => true,
                'align' => 'left',
            )
        );
        $this->addColumn("name",
            array(
                "header" => $this->__("Name"),
                "index" => "name",
                "sortable" => true,
                'align' => 'left',
            )
        );
        $editable = false;
        if($permission->checkPermission(
            'admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/view_stock_on_hand/edit_qty',
            $warehouse
        ))
        $editable = true;
        $this->addColumn("sum_total_qty",
            array(
                "header" => $this->__("Qty in Warehouse(s)"),
                'renderer' => 'inventorysuccess/adminhtml_manageStock_grid_column_renderer_text',
                "index" => "sum_total_qty",
                'type' => 'number',
                "editable" => $editable,
                "sortable" => true,
                'align' => 'left',
                'filter_condition_callback' => array($this, '_filterTotalQtyCallback')
            )
        );
        $this->addColumn("sum_qty_to_ship",
            array(
                "header" => $this->__("Qty to Ship"),
                "index" => "sum_qty_to_ship",
                'type' => 'number',
                "sortable" => true,
                'align' => 'left',
                'filter_condition_callback' => array($this, '_filterTotalQtyCallback')
            )
        );
        $this->addColumn("available_qty",
            array(
                "header" => $this->__("Available Qty"),
                "index" => "available_qty",
                'type' => 'number',
                "sortable" => true,
                'align' => 'left',
                'filter_condition_callback' => array($this, '_filterTotalQtyCallback')
            )
        );
        if ($warehouseId) {
            if (!$permission->checkPermission(
                'admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/view_stock_on_hand/edit_shelf_location',
                $warehouse
            )) {
                $editable = false;
            }
        }
        $this->addColumn(
            "shelf_location",
            array(
                "header" => $this->__("Shelf Location"),
                'renderer' => 'inventorysuccess/adminhtml_manageStock_grid_column_renderer_text',
                "index" => "shelf_location",
                "editable" => $editable,
                "sortable" => true,
                'align' => 'left',
                'filter_condition_callback' => array($this, '_filterLocationCallback')
            )
        );
        $this->modifyColumns();
        Mage::dispatchEvent('prepare_warehouse_stock_columns', array('object' => $this));
        return parent::_prepareColumns();
    }

    /**
     * function to add, remove or modify product grid columns
     *
     * @return $this
     */
    public function modifyColumns()
    {
        return $this;
    }

    /**
     * Apply `qty` filter to product grid.
     *
     * @param $collection
     * @param $column
     */
    protected function _filterTotalQtyCallback($collection, $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        $collection->addQtyToFilter($column->getId(), $value);
    }

    /**
     * Apply `shelf_location` filter to product grid.
     *
     * @param $collection
     * @param $column
     */
    protected function _filterLocationCallback($collection, $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        $collection->addSheldLocationToFilter($column->getId(), $value);
    }

    /**
     * @return array
     */
    protected function _getSelectedProducts()
    {
        return $this->getRequest()->getParam('selected_products', array());
    }

    /**
     * @return string
     */
    public function getSelectedItems()
    {
        return Zend_Json::encode($this->_getSelectedProducts());
    }

    /**
     * @return array
     */
    public function getEditFields()
    {
        return Zend_Json::encode($this->editFields);
    }
}