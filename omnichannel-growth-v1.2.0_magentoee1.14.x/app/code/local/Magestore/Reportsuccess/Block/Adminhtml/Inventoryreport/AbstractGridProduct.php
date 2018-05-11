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
 * @package     Magestore_Reportsuccess
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_AbstractGridProduct extends Mage_Adminhtml_Block_Widget_Grid

{

    /**
     * Prepare collection for grid product
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->getDataColllection();
        $Session=Mage::getSingleton('adminhtml/session');
        $Session->setData("collectiondata",$collection->getSelect()->__toString());
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
        $collection = Mage::helper('reportsuccess')->service()->getAllStocksWithProductInformation();
        $collection = $this->modifyCollection($collection);
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
        $this->addColumn('sku', array(
            'header' => Mage::helper('reportsuccess')->__('SKU'),
            'index' => 'sku'
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('reportsuccess')->__('Name'),
            'index' => 'name'
        ));
        $this->addColumn("sum_total_qty",
            array(
                "header" => $this->__("Qty in Warehouse(s)"),
                "index" => "sum_total_qty",
                'type' => 'number',
                "sortable" => true,
                'align' => 'left',
                'filter_condition_callback' => array($this, '_filterTotalQtyCallback')
            )
        );
        $this->addColumn('mac', array(
            'header' => Mage::helper('reportsuccess')->__('MAC (Moving Average Cost)'),
            'index' => 'mac',
            'width' => '50px',
            'type' => 'currency',
            'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            'filter_condition_callback' => array($this, '_filterInventoryCallback'),
            /* Edit value in line */
            'renderer' => 'Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Column_Renderer_MacInline',
            'column_css_class'    => 'a-right'
            /* by Kai */
        ));

        $this->modifyColumns();
        $this->addExportType('*/*/exportCsv', Mage::helper('reportsuccess')->__('CSV'));
        //Mage::dispatchEvent('prepare_warehouse_stock_columns', array('object' => $this));
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
     * @param $collection
     * @param $column
     */
    protected function _filterInventoryCallback($collection,$column){
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        return  Mage::getSingleton('reportsuccess/service_inventoryreport_inventoryService')->filterInventoryCallback($collection,$column->getId(),$value);
    }

    /**
     * @return mixed
     */
    public function editColumnUrl(){
        return  Mage::helper('adminhtml')->getUrl('adminhtml/dashboard/editColumns');
    }

}