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
class Magestore_Reportsuccess_Block_Adminhtml_Salesreport_AbstractGridReport extends Mage_Adminhtml_Block_Widget_Grid
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
        $collection = Mage::helper('reportsuccess')->service()->getSalesReportCollection();
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
            'index' => 'sku',
            'align' => 'left',
            'width' => '250px',
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('reportsuccess')->__('Name'),
            'index' => 'name',
            'align' => 'left',
            'width' => '250px',
            'filter_condition_callback' => array($this, '_filterAttributeCallback')
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
    protected function _filterAttributeCallback($collection,$column){
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        return  Mage::helper('reportsuccess')->service()->fillterAttributeMapping($collection,$column->getId(),$value);
    }

    /**
     * @return mixed
     */
    public function editColumnUrl(){
        return  Mage::helper('adminhtml')->getUrl('adminhtml/dashboard/editMetricsAndDimensions');
    }


}