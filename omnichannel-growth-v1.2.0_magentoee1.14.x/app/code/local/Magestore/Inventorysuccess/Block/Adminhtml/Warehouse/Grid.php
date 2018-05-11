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
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('warehouse_list');
        $this->setDefaultSort('warehouse_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Inventorysuccess_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('inventorysuccess/warehouse_collection')
            ->getTotalSkuAndQtyCollection();

        $collection = Magestore_Coresuccess_Model_Service::permissionService()->filterPermission(
            $collection,
            'admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse'
        );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     * 
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('warehouse_id',
            array(
                'header'    => $this->__('ID'),
                'align'     =>'right',
                'width'     => '50px',
                'index'     => 'warehouse_id',
        ))->addColumn('warehouse',
            array(
                'header'    => $this->__('Warehouse'),
                'align'     =>'left',
                'index'     => 'warehouse',
        ))->addColumn('total_sku',
            array(
                'header'    => $this->__('Total SKUs'),
                'align'     =>'left',
                'index'     => 'total_sku',
                'type'      => 'number',
                'filter_condition_callback' => array($this, '_filterTotalQtyCallback')
        ))->addColumn('total_qty',
            array(
                'header'    => $this->__('Total Qty'),
                'align'     =>'left',
                'index'     => 'total_qty',
                'type'      => 'number',
                'filter_condition_callback' => array($this, '_filterTotalQtyCallback')
        ))->addColumn('contact_email',
            array(
                'header'    => $this->__('Contact Email'),
                'align'     =>'left',
                'index'     => 'contact_email',
                'width'     => '200px'
        ))->addColumn('telephone',
            array(
                'header'    => $this->__('Telephone'),
                'align'     =>'left',
                'index'     => 'telephone',
                'width'     => '150px'
        ))->addColumn('street',
            array(
                'header'    => $this->__('Street'),
                'align'     =>'left',
                'index'     => 'street',
        ))->addColumn('city',
            array(
                'header'    => $this->__('City'),
                'align'     =>'left',
                'index'     => 'city',
                'width'     => '150px'
        ))->addColumn('country_id',
            array(
                'header'    => $this->__('Country'),
                'align'     =>'left',
                'index'     => 'country_id',
                'type'      => 'country',
                'width'     => '150px'
        ));
        
        if(Magestore_Coresuccess_Model_Service::stockService()->isLinkWarehouseToStore()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('cms')->__('Magento Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => false,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),                
            ));        
        }
        
        if(!$this->_isExport)
            $this->addColumn('action',
                array(
                    'header'    =>    $this->__('Action'),
                    'width'     => '100',
                    'type'      => 'action',
                    'getter'    => 'getId',
                    'actions'   => array(
                        array(
                            'caption'    => $this->__('View'),
                            'url'        => array('base'=> '*/*/edit'),
                            'field'      => 'id'
                        )),
                    'filter'    => false,
                    'sortable'    => false,
            ));

        $this->addExportType('*/*/exportCsv', $this->__('CSV'));
        $this->addExportType('*/*/exportXml', $this->__('Excel XML'));
        
        return parent::_prepareColumns();
    }
    
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
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
     * get url for each row in grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * @param $collection
     * @param $column
     */
    protected function _filterTotalQtyCallback($collection, $column) {

        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        $collection->addQtyToFilter($column->getId(), $value);
    }

    /**
     * 
     * @param  $collection
     * @param $column
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }    
    
}