<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Barcodesuccess Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @author      Magestore Developer
 */
class Magestore_Barcodesuccess_Adminhtml_Barcodesuccess_BarcodeController extends
    Mage_Adminhtml_Controller_Action
{

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/barcodesuccess');
    }

    /**
     * index action
     */
    public function indexAction()
    {
        $this->loadLayout()
             ->_setActiveMenu('barcodesuccess/listing')
             ->_addBreadcrumb(
                 Mage::helper('barcodesuccess')->__('Barcode Listing'),
                 Mage::helper('barcodesuccess')->__('Barcode Listing')
             )->_title(Mage::helper('barcodesuccess')->__('Barcode Listing'));
        if ( $this->getRequest()->getParam('action') == 'generate' ) {
            $this->_setActiveMenu('barcodesuccess/generate')
                 ->_title(Mage::helper('barcodesuccess')->__('Generate Barcode'));
        }
        if ( $this->getRequest()->getParam('action') == 'import' ) {
            $this->_setActiveMenu('barcodesuccess/import')
                 ->_title(Mage::helper('barcodesuccess')->__('Import Barcode'));
        }
        $this->renderLayout();
    }

    /**
     * index action
     */
    public function gridAction()
    {
        return $this->loadLayout()->renderLayout();
    }

    /**
     * render product list when generate
     */
    public function productgridAction()
    {
        return $this->loadLayout()->renderLayout();
    }

    /**
     * @return Magestore_Barcodesuccess_Model_Service_Barcode_HistoryService
     */
    protected function _historyService()
    {
        return Magestore_Coresuccess_Model_Service::barcodeHistoryService();
    }

    /**
     * @return Magestore_Barcodesuccess_Model_Service_Barcode_GenerateService
     */
    protected function _generateService()
    {
        return Magestore_Coresuccess_Model_Service::barcodeGenerateService();
    }


    /**
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName = 'barcodesuccess.csv';
        $content  = $this->getLayout()
                         ->createBlock('barcodesuccess/adminhtml_barcode_grid')
                         ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName = 'barcodesuccess.xml';
        $content  = $this->getLayout()
                         ->createBlock('barcodesuccess/adminhtml_barcode_grid')
                         ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     *
     */
    public function downloadsampleAction()
    {
        $fileName = 'import_product_to_barcode.csv';
        $content  = $this->getLayout()
                         ->createBlock('barcodesuccess/adminhtml_barcode_sample')
                         ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }


    /**
     * after clicking generate button.
     */
    public function generateAction()
    {
        $params   = $this->getRequest()->getParams();
        $products = Mage::helper('adminhtml/js')->decodeGridSerializedInput($this->getRequest()->getParam('products'));
        $barcodes = array();
        $totalQty = 0;
        $reason   = array_key_exists('reason', $params) ? $params['reason'] : '';
        if ( count($products) ) {
            foreach ( $products as $productData ) {
                $data                   = array();
                $data['product_id']     = $productData['product_id'];
                $data['product_sku']    = $productData['product_sku'];
                $data['supplier_code']  = !empty($productData['supplier']) ? $productData['supplier'] : '';
                $data['qty']            = !empty($productData['item_qty']) ? $productData['item_qty'] : 1;
                $data['purchased_time'] = !empty($productData['purchased_time']) ? Mage::getModel('core/date')->gmtDate(null, $productData['purchased_time']) : date("Y-m-d H:i:s");
                $data['created_at']     = date("Y-m-d H:i:s");
                $totalQty += floatval($data['qty']);
                $barcodes[] = $data;
            }
            /** Save history first */
            $historyId = $this->_historyService()->saveHistory($totalQty, Magestore_Barcodesuccess_Model_History::TYPE_GENERATED, $reason);
            $path      = '*/barcodesuccess_history/view/id/' . $historyId;

            /** Save barcodes */
            $oneBarcodePerSku = Mage::helper('barcodesuccess')->isOneBarcodePerSku();
            if ( $oneBarcodePerSku ) {
                $result = $this->_generateService()->generateTypeItem($barcodes, $historyId, array_key_exists('generate_new', $params), array_key_exists('remove_old', $params));
            } else {
                if ( !array_key_exists('generate_type', $params)
                     || $params['generate_type'] == Magestore_Barcodesuccess_Model_Source_GenerateType::ITEM
                ) {
                    $result = $this->_generateService()->generateTypeItem($barcodes, $historyId, true, false);
                } else {
                    $result = $this->_generateService()->generateTypePurchase($barcodes, $historyId);
                }
            }

            /** Message */
            if ( count($result) > 0 ) {
                if ( isset($result['success'])) {
                    $this->_getSession()->addSuccess(Mage::helper('barcodesuccess')->__("%s barcode(s) has been generated.", $result['success']['count']));
                } else {
                    $this->_historyService()->removeHistory($historyId);
                    $path = '*/*/index';
                }
                if (isset($result['fail'])) {
                    $this->_getSession()->addError(Mage::helper('barcodesuccess')->__('Cannot generate %s barcode(s), please change Barcode Pattern from the configuration to increase the maximum barcode number', count($result['fail'])));
                }
            }
        } else {
            $path = '*/*/index';
            $this->_getSession()->addError(Mage::helper('barcodesuccess')->__('You must select the product to generate the barcode'));
        }
        $this->_redirect($path);
    }

    /**
     * Process import
     */
    public function importAction()
    {
        $importService  = Magestore_Coresuccess_Model_Service::barcodeImportService();
        $barcodeService = Magestore_Coresuccess_Model_Service::barcodeService();
        $params         = $this->getRequest()->getParams();
        $barcodes       = $importService->importFromCsvFile($_FILES['import-barcode']);

        /** save history first */
        $reason    = array_key_exists('reason', $params) ? $params['reason'] : '';
        $historyId = $this->_historyService()->saveHistory(
            $this->_getSession()->getData('total_qty_import', true),
            Magestore_Barcodesuccess_Model_History::TYPE_IMPORTED,
            $reason
        );

        /** save barcodes */
        if ( count($barcodes) ) {
            $generateNew = array_key_exists('generate_new', $params) ? true : null;
            $removeOld   = array_key_exists('remove_old', $params) ? true : null;
            $count       = $barcodeService->saveBarcodes($barcodes, $historyId, $generateNew, $removeOld);
            $this->_getSession()->addSuccess(Mage::helper('barcodesuccess')->__("%s barcode(s) has been imported.", $count));
            if ( $count ) {
                return $this->_redirect('*/barcodesuccess_history/view/id/' . $historyId);
            }
        }
        return $this->_redirect('*/*/index');
    }

    /**
     *
     */
    public function downloadInvalidCsvAction()
    {
        $fileName = Magestore_Barcodesuccess_Model_Service_Barcode_ImportService::INVALID_FILE_NAME;
        $this->_prepareDownloadResponse($fileName,
                                        file_get_contents(Mage::getBaseDir('media') . DS . 'barcodesuccess' . DS . $fileName));
    }

    /**
     * view and edit item action
     */
    public function viewAction()
    {
        $barcodeId = $this->getRequest()->getParam('id');
        $model     = Mage::getModel('barcodesuccess/barcode')->load($barcodeId);
        if ( $model->getId() ) {
            $this->loadLayout();
            $this->_setActiveMenu('barcodesuccess/listing');
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Barcode Information'),
                Mage::helper('adminhtml')->__('Barcode Information')
            );
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('barcodesuccess/adminhtml_barcode_edit'))
                 ->_addLeft($this->getLayout()->createBlock('barcodesuccess/adminhtml_barcode_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('barcodesuccess')->__('Item does not exist')
            );
            $this->_redirect(' */*/');
        }
    }

    /**
     *
     */
    public function renderviewAction()
    {
        $barcodeId = $this->getRequest()->getParam('id');
        $model     = Mage::getModel('barcodesuccess/barcode')->load($barcodeId);
        if ( $model->getId() ) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if ( !empty($data) ) {
                $model->setData($data);
            }
            Mage::register('barcode_data', $model);
        }
        return $this->loadLayout()->renderLayout();
    }

    /**
     *
     */
    public function loadPreviewAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $barcodeId     = $this->getRequest()->getParam('barcodeId');
        $barcode       = Mage::getModel('barcodesuccess/barcode')->load($barcodeId);
        $barcodeData   = array();
        $barcodeData[] = $barcode->getData();

        $content = Mage::getBlockSingleton('barcodesuccess/barcode_template')
                       ->setTemplateId($this->getRequest()->getParam('templateId'))
                       ->setBarcodes($barcodeData)
                       ->toHtml();
        $this->getResponse()->appendBody(json_encode(array('content' => $content)));
    }

    /**
     *
     */
    public function printDetailAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $barcodeId   = $this->getRequest()->getParam('barcodeId');
        $barcode     = Mage::getModel('barcodesuccess/barcode')->load($barcodeId);
        $printQty    = $this->getRequest()->getParam('printQty');
        $barcodeData = array();
        for ( $i = 0; $i < $printQty; $i++ ) {
            $barcodeData[] = $barcode->getData();
        }
        $content = Mage::getBlockSingleton('barcodesuccess/barcode_template')
                       ->setTemplateId($this->getRequest()->getParam('templateId'))
                       ->setBarcodes($barcodeData)
                       ->toHtml();
        $this->getResponse()->appendBody(json_encode(array('content' => $content)));
    }


    /**
     *
     */
    public function loadBarcodeAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $barcode = $this->getRequest()->getParam('barcode');
        $barcode = Mage::getModel('barcodesuccess/barcode')->load($barcode, Magestore_Barcodesuccess_Model_Barcode::BARCODE);
        $data    = array();
        if ( $barcode->getId() ) {
            $data                 = $barcode->getData();
            $product              = Mage::getModel('catalog/product')->load($barcode->getProductId());
            $data['thumbnail']    = Magestore_Coresuccess_Model_Service::barcodeProductService()->getThumbnailHtml($product);
            $data['price']        = $product->getPrice();
            $data['name']         = $product->getName();
            $data['qty']          = Magestore_Coresuccess_Model_Service::barcodeProductService()->getQtyHtml($product);
            $data['availability'] = Magestore_Coresuccess_Model_Service::barcodeProductService()->getAvailabilityText($product);
            $data['status']       = Magestore_Coresuccess_Model_Service::barcodeProductService()->getStatusText($product);
            $data['detail']       = Magestore_Coresuccess_Model_Service::barcodeProductService()->getDetailUrlHtml($product);
        }
        $this->getResponse()->appendBody(json_encode($data));
    }

    /**
     *
     */
    public function printBarcodeAction()
    {
        $barcode    = $this->getRequest()->getParam('barcode');
        $templateId = $this->getRequest()->getParam('templateId');
        $barcode    = Mage::getModel('barcodesuccess/barcode')->load($barcode, Magestore_Barcodesuccess_Model_Barcode::BARCODE);
        $template   = Mage::getModel('barcodesuccess/template')->load($templateId);
        $data       = array();
        if ( $barcode->getId() ) {
            $data['content'] = Mage::getBlockSingleton('barcodesuccess/barcode_template')
                                   ->setTemplateData($template->getData())
                                   ->setBarcodes(array($barcode->getData()))
                                   ->toHtml();
        }
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->getResponse()->appendBody(json_encode($data));
    }

    /**
     * @return Zend_Controller_Response_Abstract
     */
    public function loadPrintPreviewAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $templateId        = $this->getRequest()->getParam('templateId');
        $template          = Mage::getModel('barcodesuccess/template')->load($templateId);
        $row               = $template->getData('label_per_row') ? $template->getData('label_per_row') : 1;
        $barcodeCollection = Mage::getModel('barcodesuccess/barcode')->getCollection()
                                 ->setCurPage(1)->setPageSize($row);
        $barcodeData = array();
        foreach ( $barcodeCollection as $barcode ) {
            $barcodeData[] = $barcode->getData();
        }
        $data = array();
        if (count($barcodeData)) {
            $data['content'] = Mage::getBlockSingleton('barcodesuccess/barcode_template')
                                   ->setTemplateData($template->getData())
                                   ->setBarcodes($barcodeData)
                                   ->toHtml();
        }
        return $this->getResponse()->appendBody(json_encode($data));
    }

    /******* MASS ACTIONS  *******/


    /**
     * @return $this|Mage_Core_Controller_Varien_Action
     */
    public function massPrintBarcodesAction()
    {
        $products = $this->getRequest()->getParam('product');
        $this->_getSession()->setData('print_products', $products);
        return $this->_redirect('*/barcodesuccess_barcode_print/index');
    }


    /**
     * @return Zend_Controller_Response_Abstract
     */
    public function massDeleteAction()
    {
        $barcodes = Mage::helper('adminhtml/js')->decodeGridSerializedInput($this->getRequest()->getParam('barcodes'));
        $data     = array();
        if ( !is_array($barcodes) || empty($barcodes) ) {
            $data['error']   = true;
            $data['message'] = $this->__('Please select item(s)');
        } else {
            try {
                $success = 0;
                foreach ( $barcodes as $barcode ) {
                    $model = Mage::getModel('barcodesuccess/barcode');
                    $model->load($barcode['barcode'], Magestore_Barcodesuccess_Model_Barcode::BARCODE);
                    $model->delete();
                    $success++;
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d record(s) has been successfully deleted.', $success)
                );
//                $data['message'] = $this->__('Total of %d record(s) were successfully deleted', $success);
            } catch ( Exception $e ) {
                $data['error']   = true;
                $data['message'] = $e->getMessage();
            }
        }
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        return $this->getResponse()->appendBody(json_encode($data));
    }

    /**
     * @return Zend_Controller_Response_Abstract
     */
    public function massUpdateAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $barcodes = Mage::helper('adminhtml/js')->decodeGridSerializedInput($this->getRequest()->getParam('barcodes'));
        if ( !is_array($barcodes) || empty($barcodes) ) {
            $data['error']   = true;
            $data['message'] = $this->__('Please select item(s)');
        } else {
            try {
                $success = 0;
                foreach ( $barcodes as $barcode ) {
                    $model = Mage::getModel('barcodesuccess/barcode');
                    /** Check barcode first */
                    $model->load($barcode['barcode'], Magestore_Barcodesuccess_Model_Barcode::BARCODE);
                    if ( $model->getId() && $model->getId() != $barcode['barcode_id'] ) {
                        $data['error']   = true;
                        $data['message'] = $this->__('The Barcode already exists.');
                        return $this->getResponse()->appendBody(json_encode($data));
                    }
                    /** Check SKU then */
                    $productCollection = Mage::getModel('catalog/product')->getCollection()
                                             ->addFieldToFilter('sku', $barcode['product_sku']);
                    if ( !$productCollection->getSize() ) {
                        $data['error']   = true;
                        $data['message'] = $this->__("The Product SKU doesn't exist.");
                        return $this->getResponse()->appendBody(json_encode($data));
                    }
                    $model->load($barcode['barcode_id']);
                    $model->addData($barcode);
                    $model->save();
                    $success++;
                }
                $data['message'] = $this->__('Total of %d record(s) has been successfully updated.', $success);
            } catch ( Exception $e ) {
                $data['error']   = true;
                $data['message'] = $e->getMessage();
            }
        }
        return $this->getResponse()->appendBody(json_encode($data));
    }

    /**
     * @return Zend_Controller_Response_Abstract
     */
    public function massPrintAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $barcodes = Mage::helper('adminhtml/js')->decodeGridSerializedInput($this->getRequest()->getParam('barcodes'));
        if ( !is_array($barcodes) || empty($barcodes) ) {
            $data['error']   = true;
            $data['message'] = $this->__('Please select item(s)');
        } else {
            $this->_getSession()->setData('massprint_barcodes', $barcodes);
            $data['redirectUrl'] = Mage::helper('adminhtml')->getUrl('*/barcodesuccess_barcode_print/index');
        }
        return $this->getResponse()->appendBody(json_encode($data));
    }

}
