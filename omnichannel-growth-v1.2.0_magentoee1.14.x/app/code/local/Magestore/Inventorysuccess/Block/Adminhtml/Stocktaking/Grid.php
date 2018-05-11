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
 * Stocktaking Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('stocktakingGrid');
        $this->setDefaultSort('stocktaking_id');
        $this->setUseAjax(true);
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('inventorysuccess/stocktaking')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('stocktaking_id', array(
            'header'    => Mage::helper('inventorysuccess')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'stocktaking_id',
        ));

        $this->addColumn('stocktaking_code', array(
            'header'    => Mage::helper('inventorysuccess')->__('Stocktaking Code'),
            'align'     =>'left',
            'index'     => 'stocktaking_code',
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Created on'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
            'frame_callback' => array( $this,'styleDate')
        ));

        $this->addColumn('created_by', array(
            'header'    => Mage::helper('inventorysuccess')->__('Created By'),
            'align'     =>'left',
            'index'     => 'created_by',
        ));

        $this->addColumn('warehouse_id', array(
            'header'    => Mage::helper('inventorysuccess')->__('Warehouse'),
            'align'     =>'left',
            'index'     => 'warehouse_id',
            'type'        => 'options',
            'options'     => Mage::getModel('inventorysuccess/warehouse_options_warehouse')->getOptionArray()
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('inventorysuccess')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'        => 'options',
            'options'     => Mage::getModel('inventorysuccess/stocktaking_options_status')->toOptionHash()
        ));

        $this->addColumn('action',
            array(
                'header'    =>    Mage::helper('inventorysuccess')->__('Action'),
                'width'        => '100',
                'type'        => 'action',
                'getter'    => 'getId',
                'actions'    => array(
                    array(
                        'caption'    => Mage::helper('inventorysuccess')->__('Edit'),
                        'url'        => array('base'=> '*/*/edit'),
                        'field'        => 'id'
                    )),
                'filter'    => false,
                'sortable'    => false,
                'index'        => 'stores',
                'is_system'    => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('inventorysuccess')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('inventorysuccess')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * Prepare datetime type to date
     *
     * @return $date
     */
    public function styleDate($value)
    {
        $locale = Mage::app()->getLocale();
        $date = $locale->date($value, $locale->getDateFormat(), $locale->getLocaleCode(), false )
            ->toString($locale->getDateFormat()) ;
        return $date;
    }
    
    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Grid
     */
    protected function _prepareMassaction()
    {
        return $this;
    }
    
    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * get grid ajax url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array(
            '_current' => true
        ));
    }
}