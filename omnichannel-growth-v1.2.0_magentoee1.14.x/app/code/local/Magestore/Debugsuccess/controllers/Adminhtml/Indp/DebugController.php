<?php
/**
 * Created by PhpStorm.
 * User: duongdiep
 * Date: 18/02/2017
 * Time: 18:03
 */

class Magestore_Debugsuccess_Adminhtml_Indp_DebugController extends Mage_Adminhtml_Controller_Action {


    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('debugsuccess/all_wrong_qty')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('DebugSuccess'), Mage::helper('adminhtml')->__('DebugSuccess')
            ) ->_title($this->__('Inventory'))
            ->_title($this->__('DebugSuccess'));
        $this->_title($this->__('DebugSuccess'));
        return $this;
    }

    protected function _isAllowed()
    {
        return true ;// Mage::getSingleton('admin/session')->isAllowed('admin/inventoryreport_historics/');
    }
    public function exportCsvAction() {
        $fileName = 'wrongqty_report.csv';
        $content = $this->getLayout()
            ->createBlock('debugsuccess/adminhtml_debug_wrongqty_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function wrongqtyAction(){
        if($product = $this->getRequest()->getParam('product')){
                 $id_wrongs = Mage::getModel('admin/session')->getData('session_list_wrong_qty');
                     if($id_wrongs){
                         $check =  array_intersect ($id_wrongs,$product);
                         if(!$check){
                             $this->_initAction();
                             $this->renderLayout();
                             return;
                         }
                     }
                 $total_size = sizeof($product);
                 $productString = implode(',',$product);
                 $warehouse = $this->getRequest()->getParam('warehouse_id');
                 $product_encode = Mage::helper('debugsuccess')->base64Encode($productString);
                 $warehouse_encode = Mage::helper('debugsuccess')->base64Encode($warehouse);
            $this->loadLayout()
                ->_setActiveMenu('debugsuccess/all_wrong_qty')
                ->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('DebugSuccess'), Mage::helper('adminhtml')->__('DebugSuccess')
                ) ->_title($this->__('Inventory'))
                ->_title($this->__('DebugSuccess'));
            $this->_title($this->__('DebugSuccess'));

            $this->getLayout()->getBlock('report_wrongqtyGrids')
                ->setProductsEncode($product_encode)
                ->setWarehouseEncode($warehouse_encode)
                ->setTotalSize($total_size);
            $this->renderLayout();
            return;
        }
        $this->_initAction();
        $this->renderLayout();
        return;
    }
    public function wrongqtygridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function reportdebugAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    public function reportdebuggridAction(){
        $this->loadLayout();
        $this->renderLayout();
    }

    public function correctqtyAction(){
        $product = $this->getRequest()->getParam('product');
        $warehouse = $this->getRequest()->getParam('warehouse');
        $body = $this->service()->correctWrongQty($product,$warehouse);
        return $this->getResponse()->setBody($body);
    }

    /**
     * @return Magestore_Reportsuccess_Model_Service_Inventoryreport_InventoryService
     */
    public function service(){
        return Magestore_Debugsuccess_Model_Service::debugInventoryService();
    }

}
