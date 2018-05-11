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
 * Stocktaking Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_StocktakingController extends
    Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_StocktakingController
     */
    protected function _initAction()
    {
        $this->loadLayout()
             ->_setActiveMenu('inventorysuccess/stockcontrol/stocktaking_history')
             ->_addBreadcrumb(
                 Mage::helper('adminhtml')->__('Manager Stocktaking'),
                 Mage::helper('adminhtml')->__('Manager Stocktaking')
             );
        return $this;
    }

    /**
     * index action
     */
    public function indexAction()
    {
        $this->_title(Mage::helper('inventorysuccess')->__('Inventorysuccess'))
             ->_title(Mage::helper('inventorysuccess')->__('Manager Stocktaking'));
        $this->_initAction()
             ->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction()
    {
        $stocktakingId = $this->getRequest()->getParam('id');
        $model         = Mage::getModel('inventorysuccess/stocktaking')->load($stocktakingId);

        if ( $model->getId() || $stocktakingId == 0 ) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if ( !empty($data) ) {
                $model->setData($data);
            }
            Mage::register('stocktaking_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('inventorysuccess/stockcontrol/create_stocktaking');

            if ( $stocktakingId ) {
                $this->_title(Mage::helper('inventorysuccess')->__('Edit Stocktaking'));
            } else {
                $this->_title(Mage::helper('inventorysuccess')->__('New Stocktaking'));
            }

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('inventorysuccess/adminhtml_stocktaking_edit'))
                 ->_addLeft($this->getLayout()->createBlock('inventorysuccess/adminhtml_stocktaking_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventorysuccess')->__('Stocktaking does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    /**
     * new action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save stocktaking action
     */
    public function saveAction()
    {
        if ( $data = $this->getRequest()->getPost() ) {
            $stocktaking        = Mage::getModel('inventorysuccess/stocktaking');
            $stocktakingService = Magestore_Coresuccess_Model_Service::stocktakingService();
            $stocktakingId      = $this->getRequest()->getParam('id');
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
            $backParam       = $this->getRequest()->getParam('back');


            /* update Omnichannel */
            if($backParam == 'cancel' && $stocktakingId){
                $stocktaking->load($stocktakingId);
                $stocktaking->setStatus(Magestore_Inventorysuccess_Model_Stocktaking::STATUS_CANCELED)->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventorysuccess')->__('The stocktaking has been canceled.')
                );
                return $this->_redirect('*/*/edit', array('id' => $stocktakingId));
            }
            if($backParam == 'reopen' && $stocktakingId){
                $stocktaking->load($stocktakingId);
                $stocktaking->setStatus(Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PENDING)->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventorysuccess')->__('The stocktaking has been re-opened.')
                );
                return $this->_redirect('*/*/edit', array('id' => $stocktakingId));
            }
            if($backParam == 'delete' && $stocktakingId){
                $stocktaking->load($stocktakingId);
                $stocktaking_code = $stocktaking->getStocktakingCode();
                $stocktaking->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventorysuccess')->__('The stocktaking Id "%s" has been deleted.',$stocktaking_code)
                );
                return $this->_redirect('*/*/index');
            }
            /* end update Omnichannel */

            $stocktakingData = $stocktakingService->getStocktakingData($data, $backParam);
            try {
                if ( $stocktakingId ) {
                    $stocktaking->setId($stocktakingId);
                }
                if ( !isset($stocktakingData['products']) || count($stocktakingData['products']) <= 0 ) {
                    if ( $backParam == 'confirm' || $backParam == 'verify'
                    ) {
                        Mage::getSingleton('adminhtml/session')->addError(
                            Mage::helper('inventorysuccess')->__('There is no product to take stock.')
                        );
                        return $this->_redirect('*/*/edit', array('id' => $stocktaking->getId()));
                    }
                }
                $stocktakingService->createStocktaking($stocktaking, $stocktakingData);
                /* if created stocktaking then complete it */
                if ( $stocktaking->getId() ) {
                    $this->processStocktaking($stocktaking);
                    if ( $backParam != 'confirm' ) {
                        Mage::getSingleton('adminhtml/session')->addSuccess(
                            Mage::helper('inventorysuccess')->__('The stocktaking has been saved.')
                        );
                    }
                    return $this->_redirect('*/*/edit', array('id' => $stocktaking->getId()));
                }
                $this->_getSession()->setFormData(false);
                return $this->_redirect('*/*/');
            } catch ( \Exception $e ) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('inventorysuccess')
                                                                      ->__('Stocktaking code already exists.'));
                $this->_getSession()->setFormData($data);
                if ( $stocktakingId ) {
                    return $this->_redirect('*/*/edit', array('id' => $stocktakingId));
                }
                return $this->_redirect('*/*/new');
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('inventorysuccess')->__('Unable to find stocktaking to create.')
        );
        return $this->_redirect('*/*/');
    }

    /**
     * process stocktaking
     *
     * @param $stocktaking
     * @return $this
     */
    public function processStocktaking( $stocktaking )
    {
        if ( $this->getRequest()->getParam('back') == 'confirm' ) {
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('inventorysuccess')->__('The stocktaking has been completed.')
            );
            $this->_redirect('*/*/edit', array('id' => $stocktaking->getId()));
        }
    }

    /**
     * product action
     */
    public function productAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventorysuccess.stocktaking.edit.tab.products')
             ->setProducts($this->getRequest()->getPost('stocktaking_products', null));
        $this->renderLayout();
        if ( Mage::getModel('admin/session')->getData('stocktaking_product_import') ) {
            Mage::getModel('admin/session')->setData('stocktaking_product_import', null);
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
     * export grid stocktakings to CSV type
     */
    public function exportCsvAction()
    {
        $fileName = 'stocktaking.csv';
        $content  = $this->getLayout()
                         ->createBlock('inventorysuccess/adminhtml_stocktaking_grid')
                         ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid stocktakings to XML type
     */
    public function exportXmlAction()
    {
        $fileName = 'stocktaking.xml';
        $content  = $this->getLayout()
                         ->createBlock('inventorysuccess/adminhtml_stocktaking_grid')
                         ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid products to CSV type
     */
    public function exportProductCsvAction()
    {
        $fileName = 'stocktaking_products.csv';
        $content  = $this->getLayout()
                         ->createBlock('inventorysuccess/adminhtml_stocktaking_edit_tab_products')
                         ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid products to XML type
     */
    public function exportProductXmlAction()
    {
        $fileName = 'stocktaking_products.xml';
        $content  = $this->getLayout()
                         ->createBlock('inventorysuccess/adminhtml_stocktaking_edit_tab_products')
                         ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        $resource = 'inventorysuccess/stockcontrol';
        switch ( $this->getRequest()->getActionName() ) {
            case 'index':
                $resource = 'inventorysuccess/stockcontrol/stocktaking_history';
                break;
            case 'new' :
            case 'edit':
            case 'save':
                $resource = 'inventorysuccess/stockcontrol/create_stocktaking';
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
        $stocktakingId = $this->getRequest()->getParam('stocktaking_id');
        if ( $barcodes ) {
            /** @var Magestore_Inventorysuccess_Model_Stocktaking $stockTaking */
            $stockTaking = Mage::getModel('inventorysuccess/stocktaking')->load($stocktakingId);
            if ( $stockTaking->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PENDING ) {
                Magestore_Coresuccess_Model_Service::stocktakingService()->addProductFromBarcode($stockTaking, $barcodes);
            } elseif ( $stockTaking->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PROCESSING ) {
                Magestore_Coresuccess_Model_Service::stocktakingService()->addProductFromBarcode($stockTaking, $barcodes);
                Magestore_Coresuccess_Model_Service::stocktakingService()->countProductFromBarcode($stockTaking, $barcodes);
            }
        }
        return $this->_redirect('*/*/edit', array('id' => $stocktakingId));
    }
}