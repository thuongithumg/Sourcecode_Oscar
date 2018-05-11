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
 * @package     Magestore_Suppliersuccess
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Suppliersuccess Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Suppliersuccess
 * @author      Magestore Developer
 */
class Magestore_Suppliersuccess_Adminhtml_Suppliersuccess_ProductController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var Magestore_Suppliersuccess_Model_Service_Supplier_ImportService 
     */
    protected $importService;

    public function _construct()
    {
        $this->importService = Magestore_Coresuccess_Model_Service::supplierImportService();
        parent::_construct();
    }
    
    /**
     * product action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('suppliersuccess.supplier.edit.tab.products')
            ->setProducts($this->getRequest()->getPost('products', null));
        $this->renderLayout();
        $this->importService->resetImportProducts();
    }

    /**
     * grid action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('suppliersuccess.supplier.edit.tab.products')
            ->setProducts($this->getRequest()->getPost('products', null));        
        $this->renderLayout();
    }
    
    /**
     * export sample file
     */
    public function downloadsampleAction()
    {
        $fileName = 'supplier_products_import_sample.csv';
        $content = $this->importService->getSampleCSV(); 
        $this->_prepareDownloadResponse($fileName, $content); 
    }
    
    /**
     * import products to supplier
     */
    public function importAction()
    {
        $result = array();
        if ($data = $this->getRequest()->isPost()) {
            if (isset($_FILES['import_product']['name']) && $_FILES['import_product']['name'] != '') {
                try {
                    $this->importService->resetImportProducts();
                    $this->importService->importFromCsvFile($_FILES['import_product']['tmp_name'], $_FILES['import_product']['name']);
                    $result['message'] = Mage::helper('suppliersuccess')->__('The products have been imported.');  
                } catch (Exception $e) {
                    $result['error'] = 1;
                    $result['message'] = $e->getMessage();                    
                }
            }
        }
        if(!count($result)) {
            $result['error'] = 1;
            $result['message'] = Mage::helper('suppliersuccess')->__('Invalid file format upload attempt');
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));   
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('suppliersuccess/supplier');
    }    

}