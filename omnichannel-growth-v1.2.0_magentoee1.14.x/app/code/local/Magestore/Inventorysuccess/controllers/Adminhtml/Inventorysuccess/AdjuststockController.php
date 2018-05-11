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
 * Adjuststock Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_AdjuststockController extends
    Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_AdjuststockController
     */
    protected function _initAction()
    {
        $this->loadLayout()
             ->_setActiveMenu('inventorysuccess/stockcontrol/adjuststock_history')
             ->_addBreadcrumb(
                 Mage::helper('inventorysuccess')->__('Manage Stock Adjustments'),
                 Mage::helper('inventorysuccess')->__('Manage Stock Adjustments')
             );
        return $this;
    }

    /**
     * index action
     */
    public function indexAction()
    {
        $this->_title(Mage::helper('inventorysuccess')->__('Inventorysuccess'))
             ->_title(Mage::helper('inventorysuccess')->__('Manage Stock Adjustments'));
        $this->_initAction()
             ->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction()
    {
        $adjustStockId = $this->getRequest()->getParam('id');
        $model         = Mage::getModel('inventorysuccess/adjuststock')->load($adjustStockId);
        $this->_title(Mage::helper('inventorysuccess')->__('Inventorysuccess'));
        if ( $model->getId() || $adjustStockId == 0 ) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if ( !empty($data) ) {
                $model->setData($data);
            }
            Mage::register('adjuststock_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('inventorysuccess/stockcontrol/create_adjuststock');

            if ( $adjustStockId ) {
                $this->_title(Mage::helper('inventorysuccess')->__('Edit Stock Adjustment'));
            } else {
                $this->_title(Mage::helper('inventorysuccess')->__('New Stock Adjustment'));
            }

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('inventorysuccess/adminhtml_adjuststock_edit'))
                 ->_addLeft($this->getLayout()->createBlock('inventorysuccess/adminhtml_adjuststock_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventorysuccess')->__('Stock adjustment does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    /**
     * new action
     */
    public function newAction()
    {
        $this->_title(Mage::helper('inventorysuccess')->__('Inventorysuccess'))
             ->_title(Mage::helper('inventorysuccess')->__('New Stock Adjustment'));
        Mage::getSingleton('adminhtml/session')->setData('adjuststock_products', false);
        $this->_forward('edit');
    }

    /**
     * save adjust stock action
     */
    public function saveAction()
    {
        if ( $data = $this->getRequest()->getPost() ) {
            $adjustStock        = Mage::getModel('inventorysuccess/adjuststock');
            $adjustStockService = Magestore_Coresuccess_Model_Service::adjustStockService();
            $adjustStockId      = $this->getRequest()->getParam('id');
            if ( isset($data['products']) ) {
                $warehouseProducts         = array();
                $warehouseProductsExplodes = explode('&', urldecode($data['products']));
                if ( count($warehouseProductsExplodes) <= 1000 ) {
                    $warehouseProducts = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['products']);
                } else {
                    foreach ( $warehouseProductsExplodes as $warehouseProductsExplode ) {
                        $warehouseProduct  = Mage::helper('adminhtml/js')->decodeGridSerializedInput($warehouseProductsExplode);
                        $warehouseProducts = $warehouseProducts + $warehouseProduct;
                    }
                }
                $data['products'] = $warehouseProducts;
            }
            $adjustData = $adjustStockService->getAdjustData($data);
            try {
                if ( $adjustStockId ) {
                    if ( $this->getRequest()->getParam('back') == 'export' ) {
                        return $this->_redirect('*/*/export', array('id' => $adjustStockId));
                    }
                    $adjustStock->setId($adjustStockId);
                }
                $adjustStockService->createAdjustment($adjustStock, $adjustData);
                if ( $adjustStock->getId() ) {
                    if ( $this->getRequest()->getParam('back') == 'confirm' ) {
                        $this->processAdjustment($adjustStock, $adjustData, $adjustStockService);
                        return $this->_redirect('*/*/edit', array('id' => $adjustStock->getId()));
                    } else {
                        Mage::getSingleton('adminhtml/session')->addSuccess(
                            Mage::helper('inventorysuccess')->__('The adjustment has been saved.')
                        );
                        if ( $this->getRequest()->getParam('back') == 'edit' ) {
                            return $this->_redirect('*/*/edit', array('id' => $adjustStock->getId()));
                        }
                    }
                }
                return $this->_redirect('*/*/');
            } catch ( Exception $e ) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('inventorysuccess')
                                                                      ->__('Adjustment code already exists.'));
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventorysuccess')->__('Unable to find item to save')
            );
        }
        return $this->_redirect('*/*/');
    }

    /**
     *  adjust stock process
     *
     * @param $adjustStock
     * @param $adjustData
     * @param $adjustStockService
     */
    public function processAdjustment(
        $adjustStock,
        $adjustData,
        $adjustStockService
    ) {
        if ( count($adjustData['products']) <= 0 ) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventorysuccess')->__('No product to adjust stock.'));
        }else {
            $adjustStockService->complete($adjustStock);
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('inventorysuccess')->__('The adjustment has been confirmed.')
            );
        }
    }

    /**
     * product action
     */
    public function productAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventorysuccess.adjuststock.edit.tab.products')
             ->setProducts($this->getRequest()->getPost('adjuststock_products', null));
        $this->renderLayout();
        if ( Mage::getModel('admin/session')->getData('adjuststock_product_import') ) {
            Mage::getModel('admin/session')->setData('adjuststock_product_import', null);
        }
    }

    /**
     * grid action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * product grid action
     */
    public function productGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * export grid stock adjustments to CSV type
     */
    public function exportCsvAction()
    {
        $fileName = 'adjuststock.csv';
        $content  = $this->getLayout()
                         ->createBlock('inventorysuccess/adminhtml_adjuststock_grid')
                         ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid stock adjustments to XML type
     */
    public function exportXmlAction()
    {
        $fileName = 'adjuststock.xml';
        $content  = $this->getLayout()
                         ->createBlock('inventorysuccess/adminhtml_adjuststock_grid')
                         ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid products to CSV type
     */
    public function exportProductCsvAction()
    {
        $fileName = 'adjuststock_products.csv';
        $content  = $this->getLayout()
                         ->createBlock('inventorysuccess/adminhtml_adjuststock_edit_tab_products')
                         ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid products to XML type
     */
    public function exportProductXmlAction()
    {
        $fileName = 'adjuststock_products.xml';
        $content  = $this->getLayout()
                         ->createBlock('inventorysuccess/adminhtml_adjuststock_edit_tab_products')
                         ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * @return boolean
     */
    protected function _isAllowed()
    {
        $resource = 'inventorysuccess/stockcontrol';
        switch ( $this->getRequest()->getActionName() ) {
            case 'index':
                $resource = 'inventorysuccess/stockcontrol/adjuststock_history';
                break;
            case 'new' :
            case 'edit':
            case 'save':
                $resource = 'inventorysuccess/stockcontrol/create_adjuststock';
                break;
        }
        return Mage::getSingleton('admin/session')->isAllowed($resource);
    }

    /**
     * add product after scan barcode
     * @return $this|Mage_Core_Controller_Varien_Action
     */
    public function handleBarcodeAction()
    {
        $barcodes      = $this->_getSession()->getData('scan_barcodes', true);
        $adjustStockId = $this->getRequest()->getParam('adjuststock_id');
        if ( $barcodes ) {
            $adjustStock = Mage::getModel('inventorysuccess/adjuststock')->load($adjustStockId);
            Magestore_Coresuccess_Model_Service::adjustStockService()->addProductFromBarcode($adjustStock, $barcodes);
        }
        return $this->_redirect('*/*/edit', array('id' => $adjustStockId));
    }
}