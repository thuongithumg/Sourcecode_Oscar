<?php

class Magestore_Debugsuccess_Block_Adminhtml_Debug_Movement_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('movementGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setVarNameFilter('filter');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }


    protected function _prepareCollection() {
        $collection = Mage::getModel('debugsuccess/stockmovement')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $warehouses = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        $warehouseIds = array('1'=>"Catalog");
        foreach($warehouses as $key => $value){
            $warehouseIds[$value['warehouse_id']] = $value['warehouse_name'];
        }

        $this->addColumn('product_id', array(
            'header' => Mage::helper('debugsuccess')->__('ID'),
            'width' => '60',
            'type' => 'number',
            'index' => 'product_id'
        ));
        $this->addColumn('warehouse_id', array(
            'header' => Mage::helper('debugsuccess')->__('Warehouse'),
            'index' => 'warehouse_id',
            'type' => 'options',
            'options' => $warehouseIds
        ));
        $this->addColumn('old_total_qty', array(
            'header' => Mage::helper('debugsuccess')->__('Old Total Qty'),
            'index' => 'old_total_qty'
        ));
        $this->addColumn('total_qty', array(
            'header' => Mage::helper('debugsuccess')->__('Total Qty'),
            'index' => 'total_qty'
        ));
        $this->addColumn('old_qty', array(
            'header' => Mage::helper('debugsuccess')->__('Old Available Qty'),
            'index' => 'old_qty'
        ));
        $this->addColumn('qty', array(
            'header' => Mage::helper('debugsuccess')->__('Available Qty'),
            'index' => 'qty'
        ));
        $this->addColumn('reason', array(
            'header' => Mage::helper('debugsuccess')->__('Reason'),
            'index' => 'reason'
        ));
        $this->addColumn('updated_at', array(
            'header' => Mage::helper('debugsuccess')->__('Update Time'),
            'index' => 'updated_at'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/reportdebuggrid', array(
            '_current' => true
        ));
    }

    public function getRowUrl($row) {
        return false;
    }

}
