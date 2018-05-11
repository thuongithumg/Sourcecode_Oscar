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
 * Inventorysuccess Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Managestock_ProductController extends Mage_Adminhtml_Controller_Action
{
    public function gridAction()
    {
        return $this->loadLayout()
            ->renderLayout();
    }

    /**
     * Update stock in grid Stock in Warehouses
     */
    public function saveAction(){
        $warehouseId = $this->getRequest()->getParam('warehouse_id', false);
        $selectedProduct = json_decode($this->getRequest()->getParam('selected_items'), true);
        if($warehouseId && count($selectedProduct)>0){
            Magestore_Coresuccess_Model_Service::warehouseStockService()
                ->updateStockInGrid($warehouseId, $selectedProduct);
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The stocks information has been saved.'));
        }
        return $this->_forward('grid');
    }

    /**
     * Reload modal product in warehouse in grid Stock in Warehouses
     *
     * @return Mage_Core_Controller_Varien_Action
     */
    public function informationAction(){
        return $this->gridAction();
    }

    /**
     * Reload modal stock movement in warehouse in grid Stock in Warehouses
     * 
     * @return Mage_Core_Controller_Varien_Action
     */
    public function stockmovementAction(){
        return $this->gridAction();
    }

    /**
     * Export stock movement csv file
     */
    public function exportStockMovementCsvAction()
    {
        $fileName = 'stock_movement.csv';
        $content  = $this->getLayout()->createBlock('inventorysuccess/adminhtml_manageStock_product_stockMovement')->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export stock movement xml file
     */
    public function exportStockMovementXmlAction()
    {
        $fileName = 'stock_movement.xml';
        $content  = $this->getLayout()->createBlock('inventorysuccess/adminhtml_manageStock_product_stockMovement')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    /**
     * Export stock onhand csv file
     */    
    public function exportStockOnHandCsvAction()
    {
        $warehouseId = $this->getRequest()->getParam('warehouse_id', false);
        $warehouseinfo = '';
        if($warehouseId) {
            $warehouse = Mage::getModel('inventorysuccess/warehouse')->load($warehouseId);
            $warehouseinfo = $warehouse->getWarehouseCode() . '-';
        }
        $fileName = 'warehouse-stock-onhand-'. $warehouseinfo . date('Ymd') .'.csv';
        $content  = $this->getLayout()->createBlock('inventorysuccess/adminhtml_manageStock_product_grid')->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);        
    }
    
    /**
     * Export stock onhand xml file
     */        
    public function exportStockOnHandXmlAction()
    {
        $warehouseId = $this->getRequest()->getParam('warehouse_id', false);
        $warehouseinfo = '';
        if($warehouseId) {
            $warehouse = Mage::getModel('inventorysuccess/warehouse')->load($warehouseId);
            $warehouseinfo = $warehouse->getWarehouseCode() . '-';
        }        
        $fileName = 'warehouse-stock-onhand-'. $warehouseinfo . date('Ymd') .'.xml';
        $content  = $this->getLayout()->createBlock('inventorysuccess/adminhtml_manageStock_product_grid')->getXml();
        $content = str_replace('sum_', '', $content);
        $this->_prepareDownloadResponse($fileName, $content);         
    }    
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
            ->isAllowed('admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/view_stock_on_hand');
    }
}