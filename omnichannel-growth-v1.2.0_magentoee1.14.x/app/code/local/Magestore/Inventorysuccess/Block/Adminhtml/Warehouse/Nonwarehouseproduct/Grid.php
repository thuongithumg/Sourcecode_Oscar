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
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Nonwarehouseproduct_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('nonwarehouse_product_list');
        $this->setDefaultSort('product_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setNoFilterMassactionColumn(true);

    }

    /**
     * Get hidden input field name for grid
     *
     * @return string
     */
    public function getHiddenInputField(){
        return 'selected_products';
    }

    /**
     * @return array
     */
    public function getEditFields(){
        return Zend_Json::encode(array('warehouse_id'));
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Inventorysuccess_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('inventorysuccess/warehouse_nonwarehouseproduct_collection');
        $collection->setIsGrid(true);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Rule_Grid
     */
    protected function _prepareColumns()
    {
//        $warehouse = Mage::getSingleton('inventorysuccess/warehouse_options_warehouse')->getOptionArray();
        $this->addColumn("entity_id",
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
        )
//            ->addColumn("warehouse_id",
//            array(
//                "header"    => $this->__("Warehouse"),
//                "index"     => "warehouse_id",
//                "type"      => 'select',
//                "sortable"  => true,
//                'align'     => 'left',
//                'options'   => $warehouse
//            )
//        )
            ->addColumn('status',
                array(
                    'header'    => $this->__('Status'),
                    'align'     =>'left',
                    'index'     => 'status',
                    'type'      => 'options',
                    'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
                    //array('1'=> 'Enabled',
                    //    '2'=> 'Disabled'),
                    'filter_condition_callback' => array($this, '_filterStatusCallback'),

                ))

            ->addColumn('action',
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
     * @param $collection
     * @param $column
     */
    protected function _filterStatusCallback($collection,$column){
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        return $collection->getSelect()->where('catalog_product_entity_int.value = ?', $value);
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

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Nonwarehouseproduct_Grid
     */

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product_id');
        $this->getMassactionBlock()->setErrorText($this->__('Please select products.'));
        $this->getMassactionBlock()->setUseAjax(true);

        //$warehouse = Mage::getSingleton('inventorysuccess/warehouse_options_warehouse')->getOptionArray();
        $resourceId = 'admin/inventorysuccess/stocklisting/non_warehouse_product';
        $warehouse = Magestore_Coresuccess_Model_Service::transferStockService()->getAvailableWarehousesArray($resourceId);

        $this->getMassactionBlock()->addItem('warehouses', array(
            'label'         => $this->__('Add to Warehouse'),
            'complete'      => 'this.checkedString = ""; this.grid.reload();',
            'url'           => $this->getUrl('*/*/masswarehouse', array('_current'=>true)),
            'confirm'       => $this->__('Are you sure you want to add selected products to warehouse?'),
            'additional'    => array(
                'visibility'    => array(
                    'name'      => 'warehouse_id',
                    'type'      => 'select',
                    'class'     => 'required-entry',
                    'label'     => $this->__('Warehouse'),
                    'options'    => $warehouse
                ))
        ));
        return $this;
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
        return $this->getUrl('*/*/grid', $this->getParamsUrl());
    }

    /**
     * Grid save url
     *
     * @return string grid save url
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', $this->getParamsUrl());
    }
}