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
class Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Incomingstock_Grid extends
    Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_AbstractGridProduct {
    /**
     * @var bool
     */
    protected $_countTotals = true;

    /**
     * Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Incomingstock_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('incomingstockGrid');
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
     * @return Varien_Object
     */
    public function getTotals()
    {
        return $this->service()->modifiTotals($this,Data::INCOMING_STOCK);
    }

    /**
     * @param $collection
     * @return mixed
     */
    public function modifyCollection($collection)
    {
        return $this->service()->getCollection(null,$collection,Data::INCOMING_STOCK);
    }

    /**
     *
     */
    public function modifyColumns()
    {
       $this->service()->modifiColumns($this,Data::INCOMING_STOCK);
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
     * @param $collection
     * @param $column
     * @return $this
     */
    public function _filterSupplierCallback($collection, $column){
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $supplier = Mage::getModel('suppliersuccess/supplier')->getCollection();
        $supplier->getSelect()->where('supplier_name like  ? ',"%".$value."%");
        $pId = $supplier->getColumnValues('supplier_id');
        $collection->getSelect()->where('purchaseorder.supplier_id in (?)',$pId);
        return $this;
    }

}
