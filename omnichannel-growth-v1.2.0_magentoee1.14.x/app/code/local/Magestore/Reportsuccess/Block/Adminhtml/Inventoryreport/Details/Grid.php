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
 * Class Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Details_Grid
 */
use Magestore_Reportsuccess_Helper_Data as Data;
class Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Details_Grid extends
    Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_AbstractGridProduct {

    /**
     * @var bool
     */
    protected $_countTotals = true;

    /**
     * Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Details_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('detailsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    /**
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = parent::getMainButtonsHtml();//get the parent class buttons
        $addButton = $this->getLayout()->createBlock('adminhtml/widget_button')//create the add button
        ->setData(array(
            'label' => Mage::helper('adminhtml')->__('Select Columns'),
            'onclick' => 'SelectedWarehouse.modifiColumn(this)',
            'class' => 'task'
        ))->toHtml();
        return $addButton . $html;
    }

    /**
     * @return Magestore_Reportsuccess_Model_Service_Inventoryreport_InventoryService
     */
    public function service(){
        return Magestore_Coresuccess_Model_Service::reportInventoryService();
    }

    /**
     * @return Varien_Object
     */
    public function getTotals()
    {
        return $this->service()->modifiTotals($this,Data::DETAILS);
    }

    /**
     * @param $collection
     * @return mixed
     */
    public function modifyCollection($collection)
    {
        return $this->service()->getCollection($this->_isNotAllWarehouse(),$collection,Data::DETAILS);
    }

    /**
     *
     */
    public function modifyColumns()
    {
        $this->service()->modifiColumns($this,Data::DETAILS);
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
    public function _isNotAllWarehouse(){
        $warehouseId = $this->getRequest()->getParam('warehouse_id', null);
        return $this->service()->getWarehouse($warehouseId,Data::DETAILS);
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
