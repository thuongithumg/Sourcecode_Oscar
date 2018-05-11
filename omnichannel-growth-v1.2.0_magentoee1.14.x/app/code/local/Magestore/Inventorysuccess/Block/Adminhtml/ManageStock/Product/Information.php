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
 * Warehouse Edit Stock On Hand Tab Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_ManageStock_Product_Information
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId("warehouse_products_information");
        $this->setDefaultSort("warehouse_product_id");
        $this->setUseAjax(true);
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
        $collection = Mage::getResourceModel('inventorysuccess/warehouse_product_collection');
        $warehouses = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        $warehouseIds = Magestore_Coresuccess_Model_Service::permissionService()->filterPermission(
            $warehouses, 'admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/view_stock_on_hand'
        )->getAllIds();
        $collection->getSelect()->where("main_table.stock_id IN ('" . implode("','", $warehouseIds) . "')");
        $id = $this->getRequest()->getParam('id', null);
        $collection->retrieveWarehouseStocks($id);
        return $collection;
    }

    /**
     * prepare columns for grid product
     *
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn("warehouse",
            array(
                "header" => $this->__("Warehouse"),
                "index" => "warehouse",
                "sortable" => true,
            )
        );
        $this->addColumn("total_qty",
            array(
                "header" => $this->__("Qty in Warehouse"),
                "index" => "total_qty",
                'type' => 'number',
                "sortable" => true,
                'filter_condition_callback' => array($this, '_filterTotalQtyCallback')
            )
        );
        $this->addColumn("qty_to_ship",
            array(
                "header" => $this->__("Qty to Ship"),
                "index" => "qty_to_ship",
                'type' => 'number',
                "sortable" => true,
                'filter_condition_callback' => array($this, '_filterTotalQtyCallback')
            )
        );
        $this->addColumn("available_qty",
            array(
                "header" => $this->__("Available Qty"),
                "index" => "available_qty",
                'type' => 'number',
                "sortable" => true,
                'filter_condition_callback' => array($this, '_filterTotalQtyCallback')
            )
        );
        $this->addColumn("shelf_location",
            array(
                "header" => $this->__("Shelf Location"),
                "index" => "shelf_location",
                "sortable" => true,
            )
        );
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl("*/inventorysuccess_managestock_product/information", array("_current" => true));
    }

    /**
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
}