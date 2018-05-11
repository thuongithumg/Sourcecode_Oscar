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
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 *
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Debugsuccess_Block_Adminhtml_AbstractGridProduct extends Mage_Adminhtml_Block_Widget_Grid

{
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
        $collection = Magestore_Debugsuccess_Model_Service::debugInventoryService()->getAllStockWrongQty();
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
            'header' => Mage::helper('debugsuccess')->__('Sku'),
            'index' => 'sku',
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('debugsuccess')->__('Name'),
            'index' => 'name',
            'filter_condition_callback' => array($this, '_filterNameCallback')
        ));
        $this->modifyColumns();
        $this->addExportType('*/*/exportCsv', Mage::helper('debugsuccess')->__('CSV'));
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
     * @return Magestore_Reportsuccess_Model_Service_Inventoryreport_InventoryService
     */
    public function service(){
        return Magestore_Debugsuccess_Model_Service::debugInventoryService();
    }

    /**
     * @param $collection
     * @param $column
     */
    public function _filterDebugCallback($collection,$column){
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        return  $this->service()->filterDebugCallback($collection,$column->getId(),$value);
    }
    public function _filterNameCallback($collection,$column ){
            if (!($value = $column->getFilter()->getValue())) {
                return;
            }
            return $collection->getSelect()->where('name_table.value like ?', '%'.$value.'%');
    }

}