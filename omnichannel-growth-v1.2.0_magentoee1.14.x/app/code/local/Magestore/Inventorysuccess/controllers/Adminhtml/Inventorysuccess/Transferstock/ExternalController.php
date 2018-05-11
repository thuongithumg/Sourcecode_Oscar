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
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Transferstock_ExternalController
    extends
    Mage_Adminhtml_Controller_Action
{
    /**
     * get type of external ( from || to )
     * @return string
     */
    protected function _getType()
    {
        if ( $this->getRequest()->getParam('type') ) {
            if ( $this->getRequest()->getParam('type') == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL ) {
                return Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL;
            }
            return Magestore_Inventorysuccess_Model_Transferstock::TYPE_FROM_EXTERNAL;
        } elseif ( $this->getRequest()->getParam('id') ) {
            return Mage::getModel('inventorysuccess/transferstock')
                       ->load($this->getRequest()->getParam('id'))
                       ->getType();
        } else {
            $model = Mage::registry('external_data');
            if ( $model && $model->getId() ) {
                if ( $model->getType() == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL ) {
                    return Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL;
                }
                return Magestore_Inventorysuccess_Model_Transferstock::TYPE_FROM_EXTERNAL;
            }
        }
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        if ( $this->_getType() == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL ) {
            $resource = 'inventorysuccess/view_transferstock/view_toexternal';
        } else {
            $resource = 'inventorysuccess/view_transferstock/view_fromexternal';
        }
        switch ( $this->getRequest()->getActionName() ) {
            case 'new' :
            case 'save':
                if ( $this->_getType() == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL ) {
                    $resource = 'inventorysuccess/create_transferstock/create_toexternal';
                } else {
                    $resource = 'inventorysuccess/create_transferstock/create_fromexternal';
                }
                break;
        }
        return Mage::getSingleton('admin/session')->isAllowed($resource);
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function indexAction()
    {
        $this->loadLayout();
        if ( $this->getRequest()->getParam('type') == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL ) {
            $this->_setActiveMenu('inventorysuccess/view_transferstock/view_toexternal');
        } else {
            $this->_setActiveMenu('inventorysuccess/view_transferstock/view_fromexternal');
        }
        return $this->renderLayout();
    }

    /**
     * render ajax view
     * @return Mage_Core_Controller_Varien_Action
     */
    public function gridAction()
    {
        return $this->loadLayout()->renderLayout();
    }

    /**
     *
     */
    public function newAction()
    {
        $this->renderForm();
    }

    /**
     *
     */
    public function editAction()
    {
        $this->renderForm();
    }

    protected function renderForm()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var Magestore_Inventorysuccess_Model_Transferstock $model */
        $model = Mage::getModel('inventorysuccess/transferstock')->load($id);
        if ( $model->getId() || !$id ) {
            $data = $this->_getSession()->getFormData(true);
            if ( !empty($data) ) {
                $model->setData($data);
            }
            if ( !$id ) {
                $model->setData('transferstock_code',
                                Magestore_Coresuccess_Model_Service::incrementIdService()->getNextCode(Magestore_Inventorysuccess_Model_Transferstock::TRANSFER_CODE_PREFIX));
            }
            Mage::register('external_data', $model);
            $this->_initEditForm();

            $this->renderLayout();
        } else {
            $this->_getSession()->addError(
                Mage::helper('inventorysuccess')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    /**
     * @return $this
     */
    protected function _initEditForm()
    {
        $this->loadLayout();
        $this->setActiveMenu();
        $this->_addBreadcrumb($this->getBreadcrumb(), $this->getBreadcrumb());
        $this->_title($this->getTitle());
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('inventorysuccess/adminhtml_transferstock_external_edit'))
             ->_addLeft($this->getLayout()->createBlock('inventorysuccess/adminhtml_transferstock_external_edit_tabs'));
        return $this;
    }

    /**
     *
     */
    protected function setActiveMenu()
    {
        if ( $this->_getType() == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL ) {
            $this->_setActiveMenu('inventorysuccess/create_transferstock/create_toexternal');
        } else {
            $this->_setActiveMenu('inventorysuccess/create_transferstock/create_fromexternal');
        }
    }

    /**
     * @return string
     */
    protected function getTitle()
    {
        /** @var Magestore_Inventorysuccess_Model_Transferstock $model */
        $model = Mage::registry('external_data');
        if ( $model->getId() ) {
            if ( $model->getType() == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL ) {
                $title = $this->__('Transfer to External location #%s', $model->getData('transferstock_code'));
            } else {
                $title = $this->__('Transfer from External location #%s', $model->getData('transferstock_code'));
            }
        } else {
            if ( $this->getRequest()->getParam('type') == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL ) {
                $title = $this->__('New Transfer to External Location');
            } else {
                $title = $this->__('New Transfer from External Location');
            }
        }
        return $title;
    }

    /**
     * @return string
     */
    protected function getBreadcrumb()
    {
        /** @var Magestore_Inventorysuccess_Model_Transferstock $model */
        if ( $this->getRequest()->getParam('type') == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL ) {
            $title = $this->__('Transfer to External location');
        } else {
            $title = $this->__('Transfer from External location');
        }
        return $title;
    }

    /**
     *
     */
    public function saveAction()
    {
        $step = $this->getRequest()->getParam('step');
        switch ( $step ) {
            case "save_general" :
                $this->_saveGeneral();
                break;
            case "start_transfer" :
                $this->_startTransfer();
                break;

            case "send_mail" :
                $this->_sendMail();
                break;
            case "cancel_transfer":
                $this->cancelTransfer();
                break;
            case "re_open":
                $this->reopenTransfer();
                break;
            case "delete_transfer":
                $this->deleteTransfer();
                break;
            default:
                $this->_save();
        }
    }

    public function reopenTransfer(){
        $id = $this->getRequest()->getParam('id');
        $transfer = $this->_getCurrentTransfer();
        $transfer->setStatus(Magestore_Inventorysuccess_Model_Transferstock::STATUS_PENDING)->save();
        $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('Stock transfer #"%s" has been re-opened.',$transfer->getTransferstockCode()));
        return $this->_redirect('*/*/edit', array('id' => $id));
    }

    public function deleteTransfer(){
        $id = $this->getRequest()->getParam('id');
        $transfer = $this->_getCurrentTransfer();
        $type=$transfer->getType();
        $transfer->delete();
        $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('Stock transfer #"%s" has been deleted.',$transfer->getTransferstockCode()));
        if($type == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL){
            return $this->_redirect('*/*/index',array('type'=>Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL));
        }
        if($type == Magestore_Inventorysuccess_Model_Transferstock::TYPE_FROM_EXTERNAL){
            return $this->_redirect('*/*/index',array('type'=>Magestore_Inventorysuccess_Model_Transferstock::TYPE_FROM_EXTERNAL));
        }
        return $this->_redirect('*/*/index');
    }
    /**
     * @return mixed
     */
    public function cancelTransfer(){
        $id = $this->getRequest()->getParam('id');
        $transfer = $this->_getCurrentTransfer();
        $transfer->setStatus(Magestore_Inventorysuccess_Model_Transferstock::STATUS_CANCELED)->save();
        $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('Stock transfer #"%s" has been canceled.',$transfer->getTransferstockCode()));
        return $this->_redirect('*/*/edit', array('id' => $id));
    }

    /**
     * Process send mail
     */
    public function _sendMail(){
        $id = $this->getRequest()->getParam('id');
        $transfer = $this->_getCurrentTransfer();
        return  $this->_redirect('*/inventorysuccess_transferstock_sendmail/execute',array('id' => $id,'type' => $transfer->getType()));
    }

    /**
     * @return $this
     */
    protected function _saveGeneral()
    {
        if ( $data = $this->getRequest()->getPost() ) {
            $validateResult = $this->_getService()->validateTranferGeneralForm($data);
            if ( !$validateResult['is_validate'] ) {
                foreach ( $validateResult['errors'] as $error ) {
                    $this->_getSession()->addError($error);
                }
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => null));
                return $this;
            }
            /** @var Magestore_Inventorysuccess_Model_Transferstock $model */
            $model = Mage::getModel('inventorysuccess/transferstock');
            $this->_getService()->initTransfer($model, $data);
            try {
                $model->getResource()->save($model);
                $this->_getSession()->setFormData(false);
                $this->_getSession()->setData("external_products", false);
                $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('General information has been saved successfully.'));
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return $this;
            } catch ( Exception $e ) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => null));
                return $this;
            }
        }
        $this->_getSession()->addError(
            Mage::helper('inventorysuccess')->__('Unable to find item to save')
        );
        return $this;
    }

    /**
     * @return $this
     */
    protected function _startTransfer()
    {
        $transfer = $this->_getCurrentTransfer();
        $products = $this->_getSelectedProducts();
        if ( !count($products) ) {
            $this->_getSession()->addError(Mage::helper('inventorysuccess')->__("There is no product to transfer."));
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return $this;
        }
        if ( $this->_getType() == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL ) {
            $isValid = $this->_getService()->validateStockDelivery($transfer, $products);
            if ( !$isValid ) {
                $this->_getSession()->setData("external_products", $products);
                $this->_redirect('*/*/edit', array('id' => $transfer->getId()));
                return $this;
            }
        }
        $transfer->setData("status", Magestore_Inventorysuccess_Model_Transferstock::STATUS_COMPLETED);
        $this->_getService()->saveTransferStockProduct($transfer, $products, true);
        $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('Transfered  ' . count($products) . ' product(s) successfully!'));
        $this->_getSession()->setData("external_products", null);
        $this->_redirect('*/*/edit', array('id' => $transfer->getId()));
        return $this;
    }

    /**
     * @return $this
     */
    protected function _save()
    {
        $transfer = $this->_getCurrentTransfer();
        $products = $this->_getSelectedProducts();
        /** save general information first */
        $transfer->setData(Magestore_Inventorysuccess_Model_Transferstock::NOTIFIER_EMAILS, $this->getRequest()->getParam('notifier_emails'));
        $transfer->setData(Magestore_Inventorysuccess_Model_Transferstock::REASON, $this->getRequest()->getParam('reason'));
        $transfer->save();
        $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('General information has been saved successfully.'));
        /** save products */
        if ( !count($products) ) {
//            $this->_getSession()->addError(Mage::helper('inventorysuccess')->__("There is no product to transfer."));
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return $this;
        }
        try {
            $this->_getService()->setProducts($transfer, $products);
            $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('Stock transfer has been successfully saved.'));
        } catch ( \Exception $e ) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_getSession()->setFormData(false);
        $this->_redirect('*/*/edit', array('id' => $transfer->getId()));
        return $this;
    }


    /**
     * @return mixed
     */
    protected
    function _getSelectedProducts()
    {
        $products = $this->getRequest()->getParam('products');
        $products = Mage::helper('adminhtml/js')->decodeGridSerializedInput($products);
        return $products;
    }

    /**
     * @return Magestore_Inventorysuccess_Model_Transferstock
     */
    protected
    function _getCurrentTransfer()
    {
        $transferId = $this->getRequest()->getParam('id');
        $transfer   = Mage::getModel('inventorysuccess/transferstock')->load($transferId);
        return $transfer;
    }

    /**
     * @return Magestore_Inventorysuccess_Model_Service_Transfer_TransferService
     */
    protected function _getService()
    {
        return Magestore_Coresuccess_Model_Service::transferStockService();
    }


    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function productlistAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('external.productlist')
             ->setProductsSelected($this->getRequest()->getPost('products_selected', null));
        return $this->renderLayout();
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function productlistgridAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('external.productlist')
             ->setProductsSelected($this->getRequest()->getPost('products_selected', null));
        return $this->renderLayout();
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function stocksummaryAction()
    {
        $this->loadLayout();
        return $this->renderLayout();
    }

    /**
     *
     */
    public function exportSummaryAction()
    {
        $fileName = 'stock_summary.csv';
        $content  = $this->getLayout()->createBlock('inventorysuccess/adminhtml_transferstock_external_edit_tab_stocksummary')->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     *
     */
    public function exportCsvAction()
    {
        $fileName = 'transfer_stock.csv';
        $content  = $this->getLayout()->createBlock('inventorysuccess/adminhtml_transferstock_external_grid')->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     *
     */
    public function downloadsampleAction()
    {
        $fileName = 'import_product_to_transfer.csv';
        $content  = $this->getLayout()
                         ->createBlock('inventorysuccess/adminhtml_transferstock_external_sample')
                         ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     *
     */
    public function importAction()
    {
        $transfer = $this->_getCurrentTransfer();
        if ( $this->getRequest()->isPost() ) {
            try {
                $importHandler = Magestore_Coresuccess_Model_Service::transferImportService();
                $data          = $importHandler->importFromCsvFile($_FILES['import-external'],
                                                                   Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_EXTERNAL_TO);
                if ( !$this->_getService()->saveTransferStockProduct($transfer, $data, false, false) ) {
                } else {
                    $this->_getSession()->addSuccess($this->__('The product transfer has been imported.'));
                }
            } catch
            ( \Exception $e ) {
                $this->_getSession()->addError($e->getMessage());
            }
        } else {
            $this->_getSession()->addError($this->__('Invalid file upload attempt'));
        }
        $this->_redirect('*/*/edit', array('id' => $transfer->getId()));
    }

    /**
     *
     */
    public function downloadInvalidAction()
    {
        $fileName = Magestore_Inventorysuccess_Model_ImportType::INVALID_TRANSFER_STOCK_EXTERNAL_TO;
        $this->_prepareDownloadResponse($fileName,
                                        file_get_contents(Mage::getBaseDir('media') . DS . 'inventorysuccess' . DS . $fileName));
    }

    /**
     * handle after scan barcode
     */
    public function handleBarcodeAction()
    {
        $barcodes        = $this->_getSession()->getData('scan_barcodes',true);
        $transferstockId = $this->getRequest()->getParam('transferstock_id');
        $transferStock   = Mage::getModel('inventorysuccess/transferstock')->load($transferstockId);
        Magestore_Coresuccess_Model_Service::transferStockService()->addProductFromBarcode($transferStock, $barcodes);
        return $this->_redirect('*/*/edit', array('id' => $transferstockId));
    }
}
