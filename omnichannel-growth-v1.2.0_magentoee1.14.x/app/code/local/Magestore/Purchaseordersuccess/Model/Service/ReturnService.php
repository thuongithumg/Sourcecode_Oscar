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
 * Purchaseorder Service
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
use Magestore_Purchaseordersuccess_Model_Return_Options_Status as ReturnStatus;
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Code as PurchaseorderCode;
use Magestore_Purchaseordersuccess_Model_Return_Item as ReturnItem;

class Magestore_Purchaseordersuccess_Model_Service_ReturnService
    extends Magestore_Coresuccess_Model_Service_ProductSelection_ProductSelectionService
{
    /**
     * @return Mage_Admin_Model_Session
     */
    protected function getAdminSession()
    {
        return Mage::getSingleton('admin/session');
    }

    /**
     * @param null|int $returnId
     * @return Exception|null
     */
    public function registerReturnRequest($returnId = null)
    {
        $returnRequest = Mage::getModel('purchaseordersuccess/return');
        if ($returnId)
            try {
                $returnRequest->load($returnId);
            } catch (\Exception $e) {
                return $e;
            }
        Mage::register('current_return_request', $returnRequest, true);
        return null;
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Return $returnRequest
     * @param int $status
     * @return bool
     */
    public function saveReturnRequestStatus($returnRequest, $status)
    {
        try {
            $returnRequest
                ->setStatus($status)
                ->setId($returnRequest->getId())
                ->save();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Confirm a specified purchase order
     *
     * @param Magestore_Purchaseordersuccess_Model_Return $returnRequest
     * @return bool
     */
    public function confirm($returnRequest)
    {
        if($returnRequest->getTotalQtyReturned() <= 0)
            throw new Exception(Mage::helper('purchaseordersuccess')->__('Please select at least one item to return.'));
        if ($this->saveReturnRequestStatus($returnRequest, ReturnStatus::STATUS_PROCESSING))
            Mage::getSingleton('adminhtml/session')
                ->addSuccess(Mage::helper('purchaseordersuccess')->__('Confirm return request successfully.'));
        else
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('purchaseordersuccess')->__('Cannot confirm return request.'));
    }

    /**
     * Complete a specified purchase order
     *
     * @param Magestore_Purchaseordersuccess_Model_Return $returnRequest
     * @return bool
     */
    public function complete($returnRequest)
    {
        if ($this->saveReturnRequestStatus($returnRequest, ReturnStatus::STATUS_COMPLETED))
            Mage::getSingleton('adminhtml/session')
                ->addSuccess(Mage::helper('purchaseordersuccess')->__('Complete purchase order successfully.'));
        else
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('purchaseordersuccess')->__('Cannot complete purchase order.'));
    }

    /**
     * Cancel a specified purchase order
     *
     * @param Magestore_Purchaseordersuccess_Model_Return $returnRequest
     * @return bool
     */
    public function cancel($returnRequest)
    {
        if ($this->saveReturnRequestStatus($returnRequest, ReturnStatus::STATUS_CANCELED))
            Mage::getSingleton('adminhtml/session')
                ->addSuccess(Mage::helper('purchaseordersuccess')->__('Cancel return request successfully.'));
        else
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('purchaseordersuccess')->__('Cannot cancel return request.'));
    }

    /**
     * Cancel a specified purchase order
     *
     * @param Magestore_Purchaseordersuccess_Model_Return $returnRequest
     * @return bool
     */
    public function delete($returnRequest)
    {
        $returnRequest->delete();
        return true;
    }

    /**
     * Get purchase code
     *
     * @param Magestore_Purchaseordersuccess_Model_Return $returnRequest
     * @return Magestore_Purchaseordersuccess_Model_Return
     */
    public function getReturnCode($returnRequest)
    {
        $code = $returnRequest->getReturnCode();
        $codePrefix = PurchaseorderCode::RETURN_REQUEST_CODE_PREFIX;
        if (isset($codePrefix))
            $code = Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_CodeService::generateCode($codePrefix);
        $returnRequest->setReturnCode($code);
        return $returnRequest;
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Return $returnRequest
     * @throws \Exception
     */
    public function updateReturnTotal($returnRequest)
    {
        /** @var Magestore_Purchaseordersuccess_Model_Mysql4_Return_Item_Collection $returnItems */
        $returnItems = $returnRequest->getItems();
        $totalQty = 0;

        /** @var Magestore_Purchaseordersuccess_Model_Return_Item $item */
        foreach ($returnItems as $item) {
            $itemQty = $item->getQtyReturned();
            if (!$itemQty)
                continue;
            $totalQty += $itemQty;
        }

        $returnRequest->setTotalQtyReturned($totalQty);
        try {
            $returnRequest->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * set products to selection
     * @param Magestore_Purchaseordersuccess_Model_Return $returnRequest
     * @param $warehouse
     * @param $supplier
     * @param $data
     * @return $this
     * @throws Exception
     */
    public function addProductsToReturnRequest($returnRequest, $supplier, $warehouse, $data)
    {
        if (!$returnRequest->getId() || !$supplier->getId()) {
            throw new \Exception($this->__('Cannot set item(s) for purchase order.'));
        }
        if (count($data)) {
            // get product on warehouse
            $prdOnWarehouse = Mage::getResourceModel('inventorysuccess/warehouse_product_collection');
            $prdOnWarehouse->getSelect()->where("main_table.stock_id = ". $warehouse->getId());
            $prdIdOnWarehouse = $prdOnWarehouse->getColumnValues('product_id');
            $prdIdValid = array_intersect($prdIdOnWarehouse, array_keys($data));

            $supplierProducts = Mage::getResourceModel('suppliersuccess/supplier_product_collection')
                ->addFieldToFilter('supplier_id', $supplier->getId())
                ->addFieldToFilter('product_id', $prdIdValid)
                ->getItems();
            $productData = array();

            /** @var Magestore_Suppliersuccess_Model_Supplier_Product $supplierProduct */
            foreach ($supplierProducts as $supplierProduct) {
                $productId = $supplierProduct->getProductId();
                $productData[$supplierProduct->getProductId()] = array(
                    ReturnItem::RETURN_ID => $returnRequest->getReturnOrderId(),
                    ReturnItem::PRODUCT_ID => $supplierProduct->getProductId(),
                    ReturnItem::PRODUCT_SKU => $supplierProduct->getProductSku(),
                    ReturnItem::PRODUCT_NAME => $supplierProduct->getProductName(),
                    ReturnItem::PRODUCT_SUPPLIER_SKU => $supplierProduct->getProductSupplierSku(),
                    ReturnItem::QTY_RETURNED => isset($data[$productId][ReturnItem::QTY_RETURNED]) ?
                        $data[$productId][ReturnItem::QTY_RETURNED] : 0
                );
            }
            $this->addProducts($returnRequest, $productData);
        }
        return $this;
    }

    /**
     * @param Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $returnRequest
     * @param array $productData
     * @return $this
     */
    public function addProducts(Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $returnRequest, $productData)
    {
        parent::addProducts($returnRequest, $productData);
//        $this->updatePurchaseTotal($purchaseOrder);
        return $this;
    }

    /**
     * @param int $returnId
     * @param array $transferredData
     * @param array $params
     * @param null $createdBy
     * @return array
     * @throws \Exception
     */
    public function transferProducts(
        $returnId, $transferredData = array(), $params = array(), $createdBy = null
    )
    {
        /** @var Magestore_Purchaseordersuccess_Model_Return $returnRequest */
        $returnRequest = Mage::getModel('purchaseordersuccess/return')->load($returnId);
        if (!$returnRequest || !$returnRequest->getReturnOrderId())
            throw new \Exception(Mage::helper('purchaseordersuccess')->__('Can not find this return request'));
        $transferStockItemData = array();
        $purchaseItems = $returnRequest->getItems($returnRequest->getId(), array_keys($transferredData));
        foreach ($purchaseItems as $item) {
            $productId = $item->getProductId();
            if (!in_array($productId, array_keys($transferredData)))
                continue;
            $transferData = Mage::getSingleton('purchaseordersuccess/service_return_item_transferredService')
                ->transferItem($returnRequest, $item, $transferredData[$productId], $params, $createdBy);
            if ($transferData)
                $transferStockItemData[$productId] = $transferData;
        }
        $returnRequest->save();
        if (empty($transferStockItemData))
            throw new \Exception(Mage::helper('purchaseordersuccess')->__('Can not transfer product(s).'));
        return $transferStockItemData;
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Return $returnRequest
     * @return bool
     */
    public function sendEmailToSupplier($returnRequest)
    {
        /** @var Magestore_Suppliersuccess_Model_Supplier $supplier */
        $supplier = Mage::getModel('suppliersuccess/supplier')->load($returnRequest->getSupplierId());
        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        try {
            /** @var Mage_Core_Model_Email_Info $emailInfo */
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($supplier->getContactEmail(), $supplier->getContactName());
            $mailer->addEmailInfo($emailInfo);
            // Set all required params and send emails
            $mailer->setSender(array(
                'name' => Mage::getStoreConfig('trans_email/ident_general/name'),
                'email' => Mage::getStoreConfig('trans_email/ident_general/email')
            ));
            $mailer->setTemplateId(Mage::getStoreConfig('purchaseordersuccess/email_template/return_request_email_to_supplier'));
            $mailer->setTemplateParams(
                array(
                    'return_request' => $returnRequest,
                    'supplier' => $supplier
                )
            );
            $mailer->send();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}