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
class Magestore_Suppliersuccess_Adminhtml_Suppliersuccess_PricelistController extends
    Mage_Adminhtml_Controller_Action
{

    /**
     * @var Magestore_Suppliersuccess_Model_Service_Supplier_PricelistService
     */
    protected $pricelistService;

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pricelistService = Magestore_Coresuccess_Model_Service::supplierPricelistService();
    }


    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Suppliersuccess_Adminhtml_Suppliersuccess_PricelistController
     */
    protected function _initAction()
    {
        $this->loadLayout()
             ->_setActiveMenu('suppliersuccess/pricelist')
             ->_addBreadcrumb(
                 Mage::helper('adminhtml')->__('Pricelist Management'),
                 Mage::helper('adminhtml')->__('Pricelist Management')
             );
        return $this;
    }

    /**
     * index action
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->getLayout()->getBlock('adminhtml_pricelist.grid')
             ->setProducts($this->getRequest()->getPost('pricelists', null));
        $this->renderLayout();
    }

    /**
     * grid action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('adminhtml_pricelist.grid')
             ->setProducts($this->getRequest()->getPost('pricelists', null));
        $this->renderLayout();
    }

    /**
     * listing pricelist in supplier
     *
     */
    public function supplierAction()
    {
        $this->_initAction();
        $this->getLayout()->getBlock('adminhtml_pricelist.grid')
             ->setProducts($this->getRequest()->getPost('pricelists', null));
        $this->renderLayout();
    }

    /**
     * grid action in supplier tab
     *
     */
    public function suppliergridAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('adminhtml_pricelist.grid')
             ->setProducts($this->getRequest()->getPost('pricelists', null));
        $this->renderLayout();
    }

    /**
     * export sample file
     */
    public function downloadsampleAction()
    {
        try {
            $fileName = 'supplier_pricelist_import_sample.csv';
            $content  = $this->pricelistService->getSampleCSV();
            $this->_prepareDownloadResponse($fileName, $content);
        } catch ( Exception $e ) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/index');
        }
    }

    /**
     * import pricelist
     */
    public function importAction()
    {
        $result = array();
        if ( $data = $this->getRequest()->isPost() ) {
            if ( isset($_FILES['import_product']['name']) && $_FILES['import_product']['name'] != '' ) {
                try {
                    $this->pricelistService->importFromCsvFile($_FILES['import_product']['tmp_name'], $_FILES['import_product']['name']);
                    $result['message'] = Mage::helper('suppliersuccess')->__('The pricelist has been imported.');
                } catch ( Exception $e ) {
                    $result['error']   = 1;
                    $result['message'] = $e->getMessage();
                }
            }
        }
        if ( !count($result) ) {
            $result['error']   = 1;
            $result['message'] = Mage::helper('suppliersuccess')->__('Invalid file format upload attempt');
        }
        if ( isset($result['error']) && $result['error'] ) {
            Mage::getSingleton('adminhtml/session')->addError($result['message']);
        } else {
            Mage::getSingleton('adminhtml/session')->addSuccess($result['message']);
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass update item(s) action
     */
    public function massupdateAction()
    {
        $pricelists = $this->getRequest()->getPost('pricelists');

        if ( !$pricelists ) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select pricelist records'));
        } else {
            $pricelists = Mage::helper('adminhtml/js')->decodeGridSerializedInput($pricelists);
            try {
                $this->pricelistService->massUpdate($pricelists);
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully updated',
                                                  count($pricelists))
                );
            } catch ( Exception $e ) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }


    /**
     * delete item action
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('suppliersuccess/supplier_pricelist');
                $model->setId($this->getRequest()->getParam('id'))
                      ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The record was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch ( Exception $e ) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * mass delete item(s) action
     */
    public function massdeleteAction()
    {
        $pricelists = $this->getRequest()->getPost('pricelists');

        if ( !$pricelists ) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select pricelist records'));
        } else {
            $pricelists = Mage::helper('adminhtml/js')->decodeGridSerializedInput($pricelists);
            try {
                $this->pricelistService->massRemove(array_keys($pricelists));
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',
                                                  count($pricelists))
                );
            } catch ( Exception $e ) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass change status for item(s) action
     */
    public function massStatusAction()
    {
        $ids = $this->getRequest()->getParam('pricelist');
        if ( !is_array($ids) ) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ( $ids as $id ) {
                    Mage::getSingleton('suppliersuccess/supplier_pricelist')
                        ->load($id)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($ids))
                );
            } catch ( Exception $e ) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName = 'pricelist.csv';
        $content  = $this->getLayout()
                         ->createBlock('suppliersuccess/adminhtml_pricelist_grid')
                         ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName = 'pricelist.xml';
        $content  = $this->getLayout()
                         ->createBlock('suppliersuccess/adminhtml_pricelist_grid')
                         ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * download invalid csv file after importig
     */
    public function downloadInvalidCsvAction()
    {

        $columns = array(
            'SUPPLIER_CODE',
            'PRODUCT_SKU',
            'PRODUCT_SUPPLIER_SKU',
            'MINIMAL_QTY',
            'COST',
            'START_DATE',
            'END_DATE',
        );
        $csv     = implode(',', $columns) . "\n";;
        $data = $this->_getSession()->getData('invalid_pricelist', true);
        if ( count($data) ) {
            foreach ( $data as $item ) {
                $csv .= implode(',', $item) . "\n";
            }
        }
        return $this->_prepareDownloadResponse('invalid_pricelist.csv', $csv);
    }


    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('suppliersuccess/pricelist');
    }
}