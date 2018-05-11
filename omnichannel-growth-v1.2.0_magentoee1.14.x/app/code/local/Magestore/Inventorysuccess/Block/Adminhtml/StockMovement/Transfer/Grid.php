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
class Magestore_Inventorysuccess_Block_Adminhtml_StockMovement_Transfer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('stock_transfer_list');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    /**
     * prepare collection for block to display
     * 
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var Magestore_Inventorysuccess_Model_Mysql4_StockMovement_StockTransfer_Collection $collection */
        $collection = Mage::getResourceModel('inventorysuccess/stockMovement_stockTransfer_collection');
        /** @var Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection $warehouses */
        $warehouses = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        $warehouses = Magestore_Coresuccess_Model_Service::permissionService()
            ->filterPermission(
                $warehouses, 'admin/inventorysuccess/stockcontrol/stock_movement_history'
            );
        $warehouseIds = $warehouses->getAllIds();
        if(!empty($warehouseIds)){
            $collection->getSelect()->where("main_table.warehouse_id IN ('" . implode("','", $warehouseIds) . "')");
        }

        $collection = $this->modifyCollection($collection);
        $this->setCollection($collection);
        return parent::_prepareCollection();
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
        $rendererReferenceNumber = '';
        if(!$this->_isExport){
            $rendererReferenceNumber = 'inventorysuccess/adminhtml_stockMovement_grid_column_renderer_referenceNumber';
        }
        $this->addColumn('transfer_code',
            array(
                'header'    => $this->__('Transfer Code'),
                'align'     =>'left',
                'index'     => 'transfer_code',
        ))->addColumn('warehouse_id',
            array(
                'header'    => $this->__('Warehouse'),
                'align'     =>'left',
                'index'     => 'warehouse_id',
                'type'      => 'options',
                'options'   => Mage::getModel('inventorysuccess/warehouse_options_warehouse')->getOptionArray()
        ))->addColumn('qty',
            array(
                'header'    => $this->__('Total Qty'),
                'align'     =>'left',
                'index'     => 'qty',
                'type' => 'number',
        ))->addColumn('total_sku',
            array(
                'header'    => $this->__('Total SKU'),
                'align'     =>'left',
                'index'     => 'total_sku',
                'type' => 'number',
        ))->addColumn('action_code',
            array(
                'header'    => $this->__('Type'),
                'align'     =>'left',
                'index'     => 'action_code',
                'type'      => 'options',
                'options'   => Magestore_Coresuccess_Model_Service::stockMovementProviderService()->toActionOptionHash()
        ))->addColumn('action_number',
            array(
                'header'    => $this->__('Reference Number'),
                'align'     =>'left',
                'index'     => 'action_number',
                'width'     => '200px',
                'renderer'  => $rendererReferenceNumber
        ))->addColumn('created_at',
            array(
                'header'    => $this->__('Date'),
                'align'     =>'left',
                'index'     => 'created_at',
                'type'      => 'datetime'
        ));
        
        $this->modifyColumn();
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
        return $this->getUrl('*/inventorysuccess_stockmovement_transfer/grid', array('_current' => true));
    }
    
    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('stock_transfer_id' => $row->getId()));
    }
}