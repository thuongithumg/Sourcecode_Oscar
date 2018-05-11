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
 *
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Block_Adminhtml_Salesreport_Grid_Grid extends
    Magestore_Reportsuccess_Block_Adminhtml_Salesreport_AbstractGridReport {

    protected $_countTotals = true;

    /**
     * contruct
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('salesreportGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    /**
     * @return Varien_Object
     */
    public function getTotals()
    {
        $totals = new Varien_Object();
        $fields = array(
            'realized_sold_qty' => 0,
            'potential_sold_qty' => 0,
            'realized_cogs'=>0,
            'potential_cogs'=>0,
            'realized_profit' => 0,
            'potential_profit' => 0,
            'total_sale'=>0,
        );
        foreach ($this->getCollection() as $item) {
            foreach($fields as $field=>$value){
                $fields[$field]+=$item->getData($field);
            }
        }
        $fields['sku']='Totals';
        $fields['action']='Totals';
        $totals->setData($fields);
        return $totals;
    }


    /**
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = parent::getMainButtonsHtml();//get the parent class buttons
        $addButton = $this->getLayout()->createBlock('adminhtml/widget_button')//create the add button
        ->setData(array(
            'label' => Mage::helper('reportsuccess')->__('Select Columns'),
            'onclick' => 'SalesreportCeritial.modifiColumn(this)',
            'class' => 'task peter_delete'
        ))->toHtml();
        return $addButton . $html;
    }


    /**
     * @param $collection
     * @return mixed
     */
    public function modifyCollection($collection)
    {
        $collection->getInformation();
        $warehouse = $this->_isNotAllWarehouse();
        $date = $this->_selectDate();
        if($warehouse && $warehouse != Magestore_Reportsuccess_Helper_Data::ALL_WAREHOUSE ){
            $warehouse = explode(',',$warehouse);
            $collection->addFieldToFilter('warehouse_id',array('in' => $warehouse));
        }else{
            $collection->addFieldToFilter('warehouse_id',array('in' => array(Magestore_Reportsuccess_Helper_Data::ALL_WAREHOUSE)));
        }
        if($date){
            $date_to = date("Y-m-d", strtotime($date['date_to'])) . ' 23:59:59';
            $collection->addFieldToFilter('main_table.created_at', array('from'=> $date['date_from'] ,'to'=> $date_to,'datetime' => true));
        }
        return $collection;
    }

    /**
     * @return $this
     */
    public function modifyColumns()
    {
        Mage::helper('reportsuccess')->service()->modifiColumns($this,Magestore_Reportsuccess_Helper_Data::SALESREPORT);
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
     * @return mixed
     */
    public function _isNotAllWarehouse(){
        $warehouseId = $this->getRequest()->getParam('warehouse_id', null);
        return Mage::helper('reportsuccess')->service()->getWarehouse($warehouseId,Magestore_Reportsuccess_Helper_Data::LOCATIONS);
        //return  $warehouseId ? $warehouseId : false ;
    }

    /**
     * @return mixed
     */
    public function _selectDate(){
          $date_from = $this->getRequest()->getParam('date_from',null);
          $date_to = $this->getRequest()->getParam('date_to',null);
            if($date_from && $date_to){
                $date_from = date("Y-m-d", strtotime($date_from));
                $date_to = date("Y-m-d", strtotime($date_to));
            }
        return Mage::helper('reportsuccess')->service()->salesReportSelectDate($date_from,$date_to,Magestore_Reportsuccess_Helper_Data::SALESREPORT);
    }

}
