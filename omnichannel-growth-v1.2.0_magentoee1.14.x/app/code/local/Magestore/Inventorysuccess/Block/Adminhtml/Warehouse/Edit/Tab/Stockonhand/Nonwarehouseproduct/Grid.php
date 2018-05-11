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
 * Inventorysuccess Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Stockonhand_Nonwarehouseproduct_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('nonwarehouse_product_list');
        $this->setDefaultSort('product_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Get hidden input field name for grid
     *
     * @return string
     */
    public function getHiddenInputField(){
        return 'non_warehouse_products';
    } 
    
    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Inventorysuccess_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('inventorysuccess/warehouse_nonwarehouseproduct_collection');
        $warehouseId = $this->getRequest()->getParam('id');
        if(!$warehouseId)
            $collection = $collection->addFieldToFilter('product_id', 0);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            "none_warehouse_product",
            array(
                "type"      => "checkbox",
                "name"      => "none_warehouse_product",
                "filter"    => false,
                "index"     => "entity_id",
                'use_index' => true,
                'header_css_class'  => 'a-center',
                'align'     => 'center'
            )
        )->addColumn("entity_id",
            array(
                "header"    => $this->__("Product ID"),
                "index"     => "entity_id",
                "sortable"  => true,
                'align'     => 'left',
                "type"      => 'number',
                'filter_condition_callback' => array($this, '_filterNumberCallback')
            )
        )->addColumn("sku",
            array(
                "header"    => $this->__("SKU"),
                "index"     => "sku",
                "sortable"  => true,
                'align'     => 'left',
                'filter_condition_callback' => array($this, '_filterTextCallback')
            )
        )->addColumn("name",
            array(
                "header"    => $this->__("Name"),
                "index"     => "name",
                "sortable"  => true,
                'align'     => 'left',
                'filter_condition_callback' => array($this, '_filterTextCallback')
            )
        )->addColumn("qty",
            array(
                "header"    => $this->__("Qty"),
                "index"     => "qty",
                "type"      => 'number',
                "sortable"  => true,
                'align'     => 'left',
                'filter_condition_callback' => array($this, '_filterNumberCallback')
            )
        )->addColumn('action',
            array(
                'header'    =>    $this->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'    => $this->__('View'),
                        'url'        => array('base'=> '*/catalog_product/edit'),
                        'field'      => 'id'
                    )),
                'filter'    => false,
                'sortable'    => false,
        ));

        return parent::_prepareColumns();
    }

    /**
     * Apply `qty` filter to product grid.
     *
     * @param $collection
     * @param $column
     */
    protected function _filterNumberCallback($collection, $column) {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        $collection->addNumberToFilter($column->getId(), $value);
    }

    /**
     * Apply `qty` filter to product grid.
     *
     * @param $collection
     * @param $column
     */
    protected function _filterTextCallback($collection, $column) {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        $collection->addTextToFilter($column->getId(), $value);
    }

    protected function getParamsUrl(){
        $params = array('_current' => true);
        if(!$this->getRequest()->getParam('warehouse_id')){
            $params['warehouse_id'] = $this->getRequest()->getParam('id');
        }
        return $params;
    }

    /**
     * Grid url getter
     *
     * @deprecated after 1.3.2.3 Use getAbsoluteGridUrl() method instead
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/inventorysuccess_warehouse_stockonhand_nonwarehouseproduct/grid', $this->getParamsUrl());
    }

    /**
     * Grid save url
     *
     * @return string grid save url
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/inventorysuccess_warehouse_stockonhand_nonwarehouseproduct/save', $this->getParamsUrl());
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
    public function getEditFields(){
        return Zend_Json::encode(array());
    }
}