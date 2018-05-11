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
use Magestore_Reportsuccess_Helper_Data as Data;
class Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Locations_Grid extends
    Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_AbstractGridProduct {

    /**
     * Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Locations_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('locatonsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }
    /**
     * @return Magestore_Reportsuccess_Model_Service_Inventoryreport_InventoryService
     */
    public function service(){
        return Magestore_Coresuccess_Model_Service::reportInventoryService();
    }
    /**
     * @param $collection
     * @return mixed
     */
    public function modifyCollection($collection)
    {
        return $this->service()->getCollection($this->getWarehouses(),$collection,Data::LOCATIONS);
    }

    /**
     * @return $this
     */
    public function modifyColumns()
    {
                if(!$this->getWarehouses()  ){
                    $idsXname = Mage::getResourceModel('reportsuccess/costofgood_collection')->getWarehouseIds();
                    foreach($idsXname as $id){
                        $value = Mage::getResourceModel('reportsuccess/costofgood_collection')->getWarehouseName($id);
                        $this->addColumn('available_qty_'.$id,
                            array(
                                'header' => $this->__($value),
                                'index' => 'available_qty_'.$id,
                                'type' => 'number',
                                "sortable" => true,
//                                'align' => 'left',
//                                'width' => '25%',
                                'filter'    => false,
                            )
                        );
                    }
                }
                if($this->getWarehouses()){
                    $w_isd = explode(',', $this->getWarehouses());
                    $type = $this->_getType();
                    foreach($w_isd as $id ){
                        $name = Mage::getResourceModel('reportsuccess/costofgood_collection')->getWarehouseName($id);
                        $this->addColumn($type.'_'.$id,
                            array(
                                'header' => $this->__($name),
                                'index' => $type.'_'.$id,
                                'type' => 'number',
                                "sortable" => true,
//                                'align' => 'left',
//                                'width' => '25%',
                                'filter'    => false,
                            )
                        );
                    }
                }
                $this->removeColumn('sum_total_qty');
                $this->removeColumn('mac');
                return $this;
    }


    /**
     * @return mixed
     */
    public function getGridUrl() {
        return $this->getUrl('*/*/indexgrid', array(
            '_current' => true
        ));
    }
    /**
     * @param $row
     * @return bool
     */
    public function getRowUrl($row) {
        return false;
    }

    /**
     * @return bool
     */
    public function getWarehouses(){
        $warehouseId = $this->getRequest()->getParam('warehouse_id', null);
        return $this->service()->getWarehouse($warehouseId,Data::LOCATIONS);
    }

    /**
     * @return bool
     */
    public function _getType(){
        $type = $this->getRequest()->getParam('type',null);
        return $this->service()->getType($type,Data::LOCATIONS);
    }
    /**
     * @param $collection
     * @param $column
     * @return $this
     */
    public function _filterSupplierCallback($collection, $column){
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $supplier = Mage::getModel('suppliersuccess/supplier_product')->getCollection();
        $supplier->getSelect()->joinLeft(
            array('supplier' => $supplier->getTable('suppliersuccess/supplier')), 'main_table.supplier_id = supplier.supplier_id',
            array('supplier_name')
        );
        $supplier->getSelect()->where('supplier.supplier_name like  ? ',"%".$value."%");
        $pId = $supplier->getColumnValues('product_id');
        $collection->getSelect()->where('main_table.product_id in (?)',$pId);
        return $this;
    }

}
