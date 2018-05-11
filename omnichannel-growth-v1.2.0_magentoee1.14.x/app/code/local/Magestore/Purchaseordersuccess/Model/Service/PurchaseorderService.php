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
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Type as PurchaseorderType;
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Status as PurchaseorderStatus;
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Code as PurchaseorderCode;
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Item as PurchaseorderItem;

class Magestore_Purchaseordersuccess_Model_Service_PurchaseorderService
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
     * @param null|int $purchaseId
     * @return Exception|null
     */
    public function registerPurchaseOrder($purchaseId = null)
    {
        $purchaseOrder = Mage::getModel('purchaseordersuccess/purchaseorder');
        if ($purchaseId)
            try {
                $purchaseOrder->load($purchaseId);
            } catch (\Exception $e) {
                return $e;
            }
        Mage::register('current_purchase_order', $purchaseOrder, true);
        return null;
    }

    /**
     * Convert quotation to purchase order with
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @return bool
     */
    public function convert($purchaseOrder)
    {
        try {
            $purchaseOrder
                ->setType(PurchaseorderType::TYPE_PURCHASE_ORDER)
                ->setStatus(PurchaseorderStatus::STATUS_PENDING)
                ->setId($purchaseOrder->getId())
                ->save();
            Mage::getSingleton('adminhtml/session')->addSuccess('Convert to purchase order successfully.');
        } catch (\Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError('Can not convert to purchase order.');
        }
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @param int $status
     * @return bool
     */
    public function savePurchaseOrderStatus($purchaseOrder, $status)
    {
        try {
            $purchaseOrder
                ->setStatus($status)
                ->setId($purchaseOrder->getId())
                ->save();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Confirm a specified purchase order
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @return bool
     */
    public function confirm($purchaseOrder)
    {
        $label = ($purchaseOrder->getType() == PurchaseorderType::TYPE_QUOTATION) ? 'quotation' : 'purchase order';
        if($purchaseOrder->getTotalQtyOrderred() <= 0)
            throw new Exception(Mage::helper('purchaseordersuccess')->__('Please select at least one item to purchase.'));
        if ($this->savePurchaseOrderStatus($purchaseOrder, PurchaseorderStatus::STATUS_PROCESSING))
            Mage::getSingleton('adminhtml/session')
                ->addSuccess(Mage::helper('purchaseordersuccess')->__('Confirm '.$label.' successfully.'));
        else
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('purchaseordersuccess')->__('Cannot confirm '.$label.'.'));
    }

    /**
     * Revert a specified quotation
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @return bool
     */
    public function revert($purchaseOrder)
    {
        if ($this->savePurchaseOrderStatus($purchaseOrder, PurchaseorderStatus::STATUS_PENDING))
            Mage::getSingleton('adminhtml/session')
                ->addSuccess(Mage::helper('purchaseordersuccess')->__('Revert quotation successfully.'));
        else
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('purchaseordersuccess')->__('Cannot revert quotation.'));;
    }

    /**
     * Complete a specified purchase order
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @return bool
     */
    public function complete($purchaseOrder)
    {
        if ($this->savePurchaseOrderStatus($purchaseOrder, PurchaseorderStatus::STATUS_COMPLETED))
            Mage::getSingleton('adminhtml/session')
                ->addSuccess(Mage::helper('purchaseordersuccess')->__('Complete purchase order successfully.'));
        else
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('purchaseordersuccess')->__('Cannot complete purchase order.'));
    }

    /**
     * Cancel a specified purchase order
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @return bool
     */
    public function cancel($purchaseOrder)
    {
        $type = $purchaseOrder->getType();
        if ($this->savePurchaseOrderStatus($purchaseOrder, PurchaseorderStatus::STATUS_CANCELED))
            if ($type == PurchaseorderType::TYPE_PURCHASE_ORDER)
                Mage::getSingleton('adminhtml/session')
                    ->addSuccess(Mage::helper('purchaseordersuccess')->__('Cancel purchase order successfully.'));
            else
                Mage::getSingleton('adminhtml/session')
                    ->addSuccess(Mage::helper('purchaseordersuccess')->__('Cancel quotation successfully.'));
        else
            if ($type == PurchaseorderType::TYPE_PURCHASE_ORDER)
                Mage::getSingleton('adminhtml/session')
                    ->addError(Mage::helper('purchaseordersuccess')->__('Cannot cancel purchase order.'));
            else
                Mage::getSingleton('adminhtml/session')
                    ->addError(Mage::helper('purchaseordersuccess')->__('Cannot cancel quotation.'));
    }

    /**
     * Cancel a specified purchase order
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @return bool
     */
    public function delete($purchaseOrder)
    {
        $purchaseOrder->delete();
        return true;
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @return bool
     */
    public function sendEmailToSupplier($purchaseOrder)
    {
        /** @var Magestore_Suppliersuccess_Model_Supplier $supplier */
        $supplier = Mage::getModel('suppliersuccess/supplier')->load($purchaseOrder->getSupplierId());
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
            $mailer->setTemplateId(Mage::getStoreConfig('purchaseordersuccess/email_template/email_to_supplier'));
            $mailer->setTemplateParams(
                array(
                    'purchase_order' => $purchaseOrder,
                    'supplier' => $supplier
                )
            );
            $mailer->send();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Get purchase code
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @return Magestore_Purchaseordersuccess_Model_Purchaseorder
     */
    public function getPurchaseCode($purchaseOrder)
    {
        $type = $purchaseOrder->getType();
        $code = $purchaseOrder->getPurchaseCode();
        if ($type == PurchaseorderType::TYPE_QUOTATION) {
            if (!$code) {
                $codePrefix = PurchaseorderCode::QUOTATION_CODE_PREFIX;
            }
        }
        if ($type == PurchaseorderType::TYPE_PURCHASE_ORDER) {
            if (strpos($code, PurchaseorderCode::PURCHASE_ORDER_CODE_PREFIX) === false)
                $codePrefix = PurchaseorderCode::PURCHASE_ORDER_CODE_PREFIX;
        }
        if (isset($codePrefix))
            $code = Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_CodeService::generateCode($codePrefix);
        $purchaseOrder->setPurchaseCode($code);
        return $purchaseOrder;
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @throws \Exception
     */
    public function updatePurchaseTotal($purchaseOrder)
    {
        $taxType = Magestore_Purchaseordersuccess_Model_Service_Config_TaxAndShipping::getTaxType();
//        $defaulShippingCost = Magestore_Purchaseordersuccess_Model_Service_Config_TaxAndShipping::getDefaultShippingCost()
//            * $purchaseOrder->getCurrencyRate();
        /** @var Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Item_Collection $purchaseItems */
        $purchaseItems = $purchaseOrder->getItems();
        $totalQty = $subtotal = $discount = $tax = $grandTotalExclTax = $grandTotalInclTax = 0;
        $shippingCost = $purchaseOrder->getShippingCost();
//        if ($shippingCost == 0 && $defaulShippingCost !== '' && $defaulShippingCost > 0) {
//            $purchaseOrder->setShippingCost($defaulShippingCost);
//            $shippingCost = $defaulShippingCost;
//        }
        /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $item */
        foreach ($purchaseItems as $item) {
            $itemQty = $item->getQtyOrderred();
            if (!$itemQty)
                continue;
            $totalQty += $itemQty;
            $itemTotal = ($itemQty * $item->getCost());
            $subtotal += $itemTotal;
            $itemDiscount = $itemTotal * $item->getDiscount() / 100;
            $discount += $itemDiscount;
            if ($taxType == 0) {
                $taxItem = $itemTotal * $item->getTax() / 100;
            } else {
                $taxItem = ($itemTotal - $itemDiscount) * $item->getTax() / 100;
            }
            $tax += $taxItem;
        }
        if ($totalQty == 0) {
            $purchaseOrder->setShippingCost(0);
        }
        $grandTotalExclTax = $subtotal - $discount + $shippingCost;
        $grandTotalInclTax = $grandTotalExclTax + $tax;
        $purchaseOrder->setTotalQtyOrderred($totalQty);
        $purchaseOrder->setSubtotal($subtotal);
        $purchaseOrder->setTotalDiscount(-$discount);
        $purchaseOrder->setTotalTax($tax);
        $purchaseOrder->setGrandTotalExclTax($grandTotalExclTax);
        $purchaseOrder->setGrandTotalInclTax($grandTotalInclTax);
        try {
            $purchaseOrder->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * set products to selection
     * @param $purchaseOrder
     * @param $supplier
     * @param $data
     * @return $this
     * @throws Exception
     */
    public function addProductsToPurchaseOrder($purchaseOrder, $supplier, $data)
    {
        if (!$purchaseOrder->getId() || !$supplier->getId()) {
            throw new \Exception($this->__('Cannot set item(s) for purchase order.'));
        }
        if (count($data)) {
            if(Mage::helper('purchaseordersuccess')->isProductFromSupplier()) {
                $products = Mage::getResourceModel('suppliersuccess/supplier_product_collection')
                    ->addFieldToFilter('supplier_id', $supplier->getId())
                    ->addFieldToFilter('product_id', array_keys($data))
                    ->getItems();
            } else {
                /** @var Mage_Catalog_Model_Resource_Product_Collection $products */
                $products = Mage::getResourceModel('catalog/product_collection');
                $products->addAttributeToSelect('name');
                $products->addFieldToFilter('entity_id', ['in' => array_keys($data)]);
            }
            $productData = array();
//            $defaultTax = Magestore_Purchaseordersuccess_Model_Service_Config_TaxAndShipping::getDefaultTax();
            /** @var Magestore_Suppliersuccess_Model_Supplier_Product $product */
            foreach ($products as $product) {
                if(Mage::helper('purchaseordersuccess')->isProductFromSupplier()) {
                    $productId = $product->getProductId();
                    $productSku = $product->getProductSku();
                    $productSupplierSku = $product->getProductSupplierSku();
                    $productName = $product->getProductName();
                    $productCost = $product->getCost();
                    $productTax = $product->getTax();
                } else {
                    /** @var Mage_Catalog_Model_Product $product */
                    $productId = $product->getId();
                    $productSku = $product->getSku();
                    $productSupplierSku = '';
                    $productName = $product->getName();
                    $productCost = 0;
                    $productTax = 0;
                }

                $cost = $productCost * $purchaseOrder->getCurrencyRate();
                $tax = isset($data[$productId][PurchaseorderItem::TAX]) ?
                    $data[$productId][PurchaseorderItem::TAX] : $productTax;
//                if($tax == 0 && $defaultTax > 0){
//                    $tax = $defaultTax;
//                }
                $productData[$productId] = array(
                    PurchaseorderItem::PURCHASE_ORDER_ID => $purchaseOrder->getPurchaseOrderId(),
                    PurchaseorderItem::PRODUCT_ID => $productId,
                    PurchaseorderItem::PRODUCT_SKU => $productSku,
                    PurchaseorderItem::PRODUCT_NAME => $productName,
                    PurchaseorderItem::PRODUCT_SUPPLIER_SKU => $productSupplierSku,
                    PurchaseorderItem::QTY_ORDERRED => isset($data[$productId][PurchaseorderItem::QTY_ORDERRED]) ?
                        $data[$productId][PurchaseorderItem::QTY_ORDERRED] : 0,
                    PurchaseorderItem::ORIGINAL_COST => $cost,
                    PurchaseorderItem::COST => isset($data[$productId][PurchaseorderItem::COST]) ?
                        $data[$productId][PurchaseorderItem::COST] : $cost,
                    PurchaseorderItem::TAX => $tax,
                    PurchaseorderItem::DISCOUNT => isset($data[$productId][PurchaseorderItem::DISCOUNT]) ?
                        $data[$productId][PurchaseorderItem::DISCOUNT] : 0
                );
            }
            $this->addProducts($purchaseOrder, $productData);
        }
        return $this;
    }

    /**
     * @param Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $purchaseOrder
     * @param array $productData
     * @return $this
     */
    public function addProducts(Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $purchaseOrder, $productData)
    {
        parent::addProducts($purchaseOrder, $productData);
//        $this->updatePurchaseTotal($purchaseOrder);
        return $this;
    }

    /**
     * @param int $purchaseId
     * @return $this
     * @throws Exception|$this
     */
    public function receiveAllProduct($purchaseId)
    {
        /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder */
        $purchaseOrder = Mage::getModel('purchaseordersuccess/purchaseorder')->load($purchaseId);
        if (!$purchaseOrder || !$purchaseOrder->getPurchaseOrderId())
            throw new \Exception(Mage::helper('purchaseordersuccess')->__('Can not find this purchase order'));
        $productSkus = array();
        $purchaseItems = $purchaseOrder->getItems();
        $receivedTime = strftime('%Y-%m-%d', Mage::app()->getLocale()->date()->getTimestamp());
        $userName = $this->getAdminSession()->getUser()->getUsername();
        $updateStock = !Mage::helper('purchaseordersuccess')->isModuleEnabled('Magestore_Inventorysuccess');
        foreach ($purchaseItems as $item) {
            $result = Magestore_Coresuccess_Model_Service::purchaseorderItemReceivedService()
                ->receiveItem($purchaseOrder, $item, null, $receivedTime, $userName, $updateStock);
            if (!$result)
                $productSkus[] = $item->getProductSku();
        }
        $purchaseOrder->save();
        if (!empty($productSkus))
            throw new \Exception(
                Mage::helper('purchaseordersuccess')->__('Can not receive product(s): %s', implode(', ', $productSkus))
            );
        return $this;
    }

    /**
     * @param $purchaseId
     * @param array $receivedData
     * @param string $receivedTime
     * @param null $createdBy
     * @return $this
     */
    public function receiveProducts($purchaseId, $receivedData = array(), $receivedTime = null)
    {
        /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder */
        $purchaseOrder = Mage::getModel('purchaseordersuccess/purchaseorder')->load($purchaseId);
        if (!$purchaseOrder || !$purchaseOrder->getPurchaseOrderId())
            throw new \Exception(Mage::helper('purchaseordersuccess')->__('Can not find this purchase order'));
        $purchaseItems = $purchaseOrder->getItems($purchaseOrder->getId(), array_keys($receivedData));
        if (!$receivedTime)
            $receivedTime = strftime('%Y-%m-%d', Mage::app()->getLocale()->date()->getTimestamp());
        $userName = $this->getAdminSession()->getUser()->getUsername();
        $updateStock = !Mage::helper('purchaseordersuccess')->isModuleEnabled('Magestore_Inventorysuccess');
        foreach ($purchaseItems as $item) {
            $productId = $item->getProductId();
            if (!in_array($productId, array_keys($receivedData)))
                continue;
            $result = Magestore_Coresuccess_Model_Service::purchaseorderItemReceivedService()->receiveItem(
                $purchaseOrder, $item, $receivedData[$productId], $receivedTime, $userName, $updateStock
            );
            if (!$result)
                $productSkus[] = $item->getProductSku();
        }
        $purchaseOrder->save();
        if (!empty($productSkus))
            throw new \Exception(
                Mage::helper('purchaseordersuccess')->__('Can not receive product(s): %s', implode(', ', $productSkus))
            );
        return $this;
    }

    /**
     * @param $purchaseId
     * @param array $receivedData
     * @param string $receivedTime
     * @param null $createdBy
     * @return $this
     */
    public function returnProducts($purchaseId, $returnedData = array(), $returnedTime = null, $createdBy = null)
    {
        /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder */
        $purchaseOrder = Mage::getModel('purchaseordersuccess/purchaseorder')->load($purchaseId);
        if (!$purchaseOrder || !$purchaseOrder->getPurchaseOrderId())
            throw new \Exception(Mage::helper('purchaseordersuccess')->__('Can not find this purchase order'));
        $purchaseItems = $purchaseOrder->getItems($purchaseOrder->getId(), array_keys($returnedData));
        if (!$returnedTime)
            $returnedTime = strftime('%Y-%m-%d', Mage::app()->getLocale()->date()->getTimestamp());
        $userName = $this->getAdminSession()->getUser()->getUsername();
        $updateStock = !Mage::helper('purchaseordersuccess')->isModuleEnabled('Magestore_Inventorysuccess');
        /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $item */
        foreach ($purchaseItems as $item) {
            $productId = $item->getProductId();
            if (!in_array($productId, array_keys($returnedData)))
                continue;
            $result = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_purchaseorder_item_returnedService')
                ->returnItem($purchaseOrder, $item, $returnedData[$productId], $returnedTime, $userName, $updateStock);
            if (!$result)
                $productSkus[] = $item->getProductSku();
        }
        $purchaseOrder->save();
        if (!empty($productSkus))
            throw new \Exception(Mage::helper('purchaseordersuccess')->__('Can not receive product(s): %s', implode(', ', $productSkus)));
        return $this;
    }

    /**
     * @param array $transferredData
     * @param array $params
     * @param null $createdBy
     * @return array
     * @throws \Exception
     */
    public function transferProducts(
        $purchaseId, $transferredData = array(), $params = array(), $createdBy = null
    )
    {
        /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder */
        $purchaseOrder = Mage::getModel('purchaseordersuccess/purchaseorder')->load($purchaseId);
        if (!$purchaseOrder || !$purchaseOrder->getPurchaseOrderId())
            throw new \Exception(Mage::helper('purchaseordersuccess')->__('Can not find this purchase order'));
        $transferStockItemData = array();
        $purchaseItems = $purchaseOrder->getItems($purchaseOrder->getId(), array_keys($transferredData));
        foreach ($purchaseItems as $item) {
            $productId = $item->getProductId();
            if (!in_array($productId, array_keys($transferredData)))
                continue;
            $transferData = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_purchaseorder_item_transferredService')
                ->transferItem($purchaseOrder, $item, $transferredData[$productId], $params, $createdBy);
            if ($transferData)
                $transferStockItemData[$productId] = $transferData;
        }
        $purchaseOrder->save();
        if (empty($transferStockItemData))
            throw new \Exception(Mage::helper('purchaseordersuccess')->__('Can not transfer product(s).'));
        return $transferStockItemData;
    }

    /**
     * Create an invoice
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @param array $invoiceData
     * @param null $invoiceTime
     * @param null $createdBy
     * @return $this
     * @throws \Exception
     */
    public function createInvoice($purchaseOrder, $invoiceData = array(), $invoiceTime = null){
        $purchaseItems = $purchaseOrder->getItems($purchaseOrder->getPurchaseOrderId(), array_keys($invoiceData));
        if(empty($purchaseItems))
            throw new \Exception(Mage::helper('purchaseordersuccess')->__('Please select at least one product to create invoice.'));
        /** @var Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_InvoiceService $invoiceService */
        $invoiceService = Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_purchaseorder_invoiceService');
        $invoiceService->createInvoice($purchaseOrder, $purchaseItems, $invoiceData, $invoiceTime);
        return $this;
    }
}