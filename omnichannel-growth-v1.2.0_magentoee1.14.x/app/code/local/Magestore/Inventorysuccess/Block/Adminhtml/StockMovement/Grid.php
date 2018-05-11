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
class Magestore_Inventorysuccess_Block_Adminhtml_StockMovement_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('stock_movement_list');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Inventorysuccess_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('inventorysuccess/stockMovement_collection');
        $warehouses = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        $warehouses = Magestore_Coresuccess_Model_Service::permissionService()
            ->filterPermission(
                $warehouses, 'admin/inventorysuccess/stockcontrol/stock_movement_history'
            );
        $warehouseIds = $warehouses->getAllIds();
        if(!empty($warehouseIds)){
            $collection->getSelect()->where("main_table.warehouse_id IN ('" . implode("','", $warehouseIds) . "')");
        }

        if($this->getPostParams()){
            $collection->getSelect()->where("main_table.product_id = {$this->getPostParams()}");
        }
        Mage::getSingleton('inventorysuccess/service_filterCollection_filter')->mappingAttribute($collection);
        $collection = $this->modifyCollection($collection);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Get product id from Catalog Product detail -> Inventory tab
     * @return mixed
     */
    public function getPostParams(){
        return $this->getRequest()->getParam('id');
    }

    /**
     * @param $collection
     * @return mixed
     */
    protected function modifyCollection($collection)
    {
        return $collection;
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Rule_Grid
     */
    protected function _prepareColumns()
    {
        $rendererSku = $rendererReferenceNumber = '';
        if(!$this->_isExport){
            $rendererSku = 'inventorysuccess/adminhtml_stockMovement_grid_column_renderer_sku';
            $rendererReferenceNumber = 'inventorysuccess/adminhtml_stockMovement_grid_column_renderer_referenceNumber';
        }
        $this->addColumn('qty',
            array(
                'header'    => $this->__('Qty'),
                'align'     =>'left',
                'index'     => 'qty',
        ))->addColumn('product_sku',
            array(
                'header'    => $this->__('SKU'),
                'align'     =>'left',
                'index'     => 'product_sku',
        ))->addColumn('action_code',
            array(
                'header'    => $this->__('Type'),
                'align'     =>'left',
                'index'     => 'action_code',
                'type'      => 'options',
                'options'   => Magestore_Coresuccess_Model_Service::stockMovementProviderService()->toActionOptionHash()
        ))->addColumn('warehouse_id',
            array(
                'header'    => $this->__('Warehouse'),
                'align'     =>'left',
                'index'     => 'warehouse_id',
                'type'      => 'options',
                'options'   => Mage::getModel('inventorysuccess/warehouse_options_warehouse')->getOptionArray()
        ))
        ->addColumn('status',
            array(
                'header'    => $this->__('Status'),
                'align'     =>'left',
                'index'     => 'status',
                'type'      => 'options',
                'options'   => array('1'=> 'Enabled',
                                     '2'=> 'Disabled'),
                'filter_condition_callback' => array($this, '_filterStatusCallback'),

            ))
            ->addColumn('action_number',
            array(
                'header'    => $this->__('Reference Number'),
                'align'     =>'left',
                'index'     => 'action_number',
                'renderer'  => $rendererReferenceNumber
        ))->addColumn('created_at',
            array(
                'header'    => $this->__('Date'),
                'align'     =>'left',
                'index'     => 'created_at',
                'type'      => 'datetime'
        ));
        
        $this->modifyColumn();
        if($this->getPostParams()){
            $this->removeColumn('product_sku');
        }
        $this->addExportType('*/*/exportCsv', $this->__('CSV'));
        $this->addExportType('*/*/exportXml', $this->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function modifyColumn(){
        return $this;
    }
    
    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/inventorysuccess_stockmovement/grid', array('_current' => true));
    }

    /**
     * @param $collection
     * @param $column
     */

    protected function _filterStatusCallback($collection,$column){
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        return  Mage::getSingleton('inventorysuccess/service_filterCollection_filter')->filterStatus($collection,$column->getId(),$value);
    }



}