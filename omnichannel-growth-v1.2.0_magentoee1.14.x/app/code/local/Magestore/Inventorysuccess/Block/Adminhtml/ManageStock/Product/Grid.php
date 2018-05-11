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
 * Managestock product grid Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_ManageStock_Product_Grid
    extends Magestore_Inventorysuccess_Block_Adminhtml_ManageStock_AbstractGridProduct
{
    public function modifyCollection($collection)
    {
        $warehouseId = $this->_isNotAllWarehouse();
        if (!$warehouseId) {
            $warehouses = Mage::getResourceModel('inventorysuccess/warehouse_collection');
            $warehouses = Magestore_Coresuccess_Model_Service::permissionService()
                ->filterPermission(
                    $warehouses, 'admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/view_stock_on_hand'
                );
            $warehouseIds = $warehouses->getAllIds();
            $collection->getSelect()->where("main_table.stock_id IN ('" . implode("','", $warehouseIds) . "')");
        } else {
            $collection->addWarehouseToFilter($warehouseId);
        }
        return $collection;
    }

    /**
     * function to add, remove or modify product grid columns
     *
     * @return $this
     */
    public function modifyColumns()
    {
        $this->addColumnAfter('price',
            array(
                'header' => $this->__('Price'),
                'index' => 'price',
                'sortable' => true,
                'type' => 'currency',
                'align' => 'right',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
                'is_system' => true,
            ), 'name'
        );
        if (!$this->_isNotAllWarehouse()) {
            
            $this->addColumnAfter('view_stock_information',
                array(
                    'header' => $this->__('Product in Warehouse(s)'),
                    'filter' => false,
                    'sortable' => false,
                    'width' => '100px',
                    'renderer' => 'inventorysuccess/adminhtml_manageStock_grid_column_renderer_viewStockInformation',
                    'align'     => 'center',
                    'is_system' => true,
                ), 'available_qty'
            );
            $this->addColumnAfter('view_stock_movement',
                array(
                    'header' => $this->__('Stock Movement'),
                    'filter' => false,
                    'sortable' => false,
                    'width' => '100px',
                    'renderer' => 'inventorysuccess/adminhtml_manageStock_grid_column_renderer_viewStockMovement',
                    'align'     => 'center',
                    'is_system' => true,
                ), 'view_stock_information'
            );
        }
        $this->addColumn('status',
            array(
                'header' => $this->__('Product Status'),
                'index' => 'status',
                'type' => 'options',
                'align' => 'left',
                'is_system' => true,
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray()
            )
        );
        if (!$this->_isNotAllWarehouse()) {
            $this->removeColumn('in_warehouse');
            $this->removeColumn('shelf_location');
        }
        
        $warehouseParam = '';
        if($warehouseId = $this->getRequest()->getParam('warehouse_id', null)) {
            $warehouseParam = '/warehouse_id/' . $warehouseId;
        }
        $this->addExportType('*/*/exportStockOnHandCsv' . $warehouseParam, $this->__('CSV'));
        $this->addExportType('*/*/exportStockOnHandXml' . $warehouseParam, $this->__('Excel XML'));    
        
        return $this;
    }

    /**
     * Grid url getter
     *
     * @deprecated after 1.3.2.3 Use getAbsoluteGridUrl() method instead
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/inventorysuccess_managestock_product/grid', array('_current' => true));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/inventorysuccess_managestock_product/save', array('_current' => true));
    }

    /**
     * @return bool|int
     */
    private function _isNotAllWarehouse()
    {
        $warehouseId = $this->getRequest()->getParam('warehouse_id', null);
        return !$warehouseId || $warehouseId == 0 ? false : $warehouseId;
    }
}