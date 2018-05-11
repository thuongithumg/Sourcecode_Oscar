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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Adminhtml_Purchaseordersuccess_Return_TransferreditemController
    extends Magestore_Purchaseordersuccess_Controller_ReturnAbstract
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Service_Return_Item_TransferredService
     */
    protected $transferredService;

    public function _construct()
    {
        parent::_construct();
        $this->transferredService = Mage::getSingleton(
            'purchaseordersuccess/service_return_item_transferredService'
        );
    }

    public function transferAction()
    {
        $returnId = $this->getRequest()->getParam('id');
        if (!$returnId) {
            $this->getAdminSession()->addError($this->__('Please select a return request to transfer item'));
        } else {
            $modal = $this->getRequest()->getParam('modal');
            $barcodes = $this->getRequest()->getParam('barcodes');
            $postItems = $this->getRequest()->getParam('selected_items');
            $params = $this->getRequest()->getParams();
            $selectedItems = array();
            if ($modal == 'scanbarcode' && !empty($barcodes)) {
                $barcodes = Zend_Json::decode($barcodes);
                $returnRequest = Mage::getModel('purchaseordersuccess/return')->load($returnId);
                /** @var Magestore_Purchaseordersuccess_Model_Service_Barcode_ScanService $scanService */
                $scanService = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_barcode_scanService');
                $selectedItems = $scanService->prepareSelectedItemReturn($returnRequest, $barcodes, 'transfer_qty');
            } else if (empty($postItems)) {
                $this->getAdminSession()->addError($this->__('Please select at least one item to transfer.'));
                return $this->getResponse()->setBody(\Zend_Json::encode($this->getDisplayButton()));
            } else {
                $selectedItems = \Zend_Json::decode($postItems);
            }
            if (!empty($selectedItems)) {
                $transferredData = $this->transferredService->processTransferredData($selectedItems);
                if (empty($transferredData)) {
                    $this->getAdminSession()->addError($this->__('Please select at least one item to transfer.'));
                } else {
                    try {
                        $userName = Mage::getSingleton('admin/session')->getUser()->getUsername();
                        $transferStockItemData = $this->returnService->transferProducts(
                            $returnId, $transferredData, $params, $userName
                        );
                        if (!empty($transferStockItemData)) {
                            if($params['is_subtract'] == 'true') {
                                $transferStock = $this->transferredService->createTransferStock($params, $userName);
                                $this->transferredService->saveTransferStockData($transferStock, $transferStockItemData);
                            }

                            /* add by Kai - update Mac value for sale report*/
//                            Mage::dispatchEvent('update_mac_values', array('productInfo' => $transferStockItemData));
                            /* end by Kai */

                            $this->getAdminSession()->addSuccess($this->__('Transfer product(s) successfully.'));
                        }
                    } catch (\Exception $e) {
                        $this->getAdminSession()->addError($e->getMessage());
                    }
                }
            } else {
                $this->getAdminSession()->addError($this->__('Please select at least one item to transfer.'));
            }
        }
        return $this->getResponse()->setBody(\Zend_Json::encode($this->getDisplayButton()));
    }

    public function gridmodalAction()
    {
        return $this->gridAction();
    }

    /**
     * Export purchase order grid to csv file
     */
    public function exportCsvAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->returnService->registerReturnRequest($id);
        $fileName = 'return_transferred_item.csv';
        $content = $this->getLayout()
            ->createBlock('purchaseordersuccess/adminhtml_return_edit_tab_transferreditem_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export purchase order grid to xml file
     */
    public function exportXmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->returnService->registerReturnRequest($id);
        $fileName = 'return_transferred_item.xml';
        $content = $this->getLayout()
            ->createBlock('purchaseordersuccess/adminhtml_return_edit_tab_transferreditem_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function loadBarcodeAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $returnId = $this->getRequest()->getParam('id');
        $supplierId = $this->getRequest()->getParam('supplier_id');
        $warehouseId = $this->getRequest()->getParam('warehouse_id');
        $barcode = $this->getRequest()->getParam('barcode');
        /** @var Magestore_Purchaseordersuccess_Model_Service_Barcode_ScanService $scanService */
        $scanService = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_barcode_scanService');
        $collection = $scanService->searchBarcode($supplierId, $barcode, $warehouseId);
        /** @var Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Item_Collection $purchaseItems */
        $purchaseItems = Mage::getResourceModel('purchaseordersuccess/purchaseorder_item_collection');
        $productIds = $purchaseItems->addFieldToFilter('purchase_order_id', $returnId)
            ->addFieldToFilter(
                new \Zend_Db_Expr('main_table.qty_received - (main_table.qty_returned + main_table.qty_transferred)'),
                array('gt' => 0)
            )->getColumnValues('product_id');
        $collection->addFieldToFilter('barcode.product_id', array('in' => $productIds));
        $barcode = $collection->getFirstItem();
        $data = $barcode->getId() ? $barcode->getData() : array();
        return $this->getResponse()->setBody(json_encode($data));
    }
}