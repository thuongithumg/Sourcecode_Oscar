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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */


/**
 * Webpos Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @author      Magestore Developer
 */
class Magestore_Webpos_Model_Observer {

    public function skipPaymentMethod($observers) {
        $result = $observers->getResult();
//        $quote = $observers->getQuote();
        $methodInstance = $observers->getMethodInstance();
        $module = Mage::app()->getRequest()->getRouteName();
        $isPosSession = Mage::helper('webpos/permission')->validateRequestSession();
        if ($module == 'webpos' || $isPosSession == true) {
            $defaultpayment = Mage::getStoreConfig('webpos/payment/defaultpayment');
            $allowPayments = Mage::getModel('webpos/source_adminhtml_payment')->getAllowPaymentMethods();
            if (Mage::getStoreConfig('webpos/payment/allowspecific_payment', Mage::app()->getStore()->getId()) == '1') {
                $specificpayment = Mage::getStoreConfig('webpos/payment/specificpayment');
                $specificpayment = explode(',', $specificpayment);
                if (in_array($methodInstance->getCode(), $specificpayment) || ($methodInstance->getCode() == $defaultpayment)) {
                    $result->isAvailable = true;
                    $result->isDeniedInConfig = false;
                } else {
                    $result->isAvailable = false;
                    $result->isDeniedInConfig = true;
                }
            } else {
                if (in_array($methodInstance->getCode(), $allowPayments) || $methodInstance->getCode() == $defaultpayment) {
                    $result->isAvailable = true;
                    $result->isDeniedInConfig = false;
                } else {
                    $result->isAvailable = false;
                    $result->isDeniedInConfig = true;
                }
            }
        }
        return $this;
    }

    public function orderPlaceAfter($observers) {

    }

    public function sales_convert_quote_item_to_order_item($observers) {
        $isPosSession = Mage::helper('webpos/permission')->validateRequestSession();
        if($isPosSession){
            $orderItem = $observers->getOrderItem();
            if(!$orderItem->getBaseOriginalPrice()){
                $orderItem->setBaseOriginalPrice($observers->getItem()->getBasePriceInclTax());
                $orderItem->setOriginalPrice($observers->getItem()->getPriceInclTax());
            }
        }
    }

    public function quoteItemSetProduct($observer) {
        $product = $observer['product'];
        $isWebposApi = Mage::helper('webpos/permission')->getCurrentSession();
        $routeName = Mage::app()->getRequest()->getRouteName();
        if ($routeName == "webpos" || $isWebposApi){
            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
            $product->setStockItem($stockItem);
        }

        if (strpos($product->getSku(), 'webpos-customsale') === false) {
            return;
        }
        $tax_class_id = $product->getCustomOption('tax_class_id');
        if ($tax_class_id && $tax_class_id->getValue()) {
            $item = $observer['quote_item'];
            $item->getProduct()->setTaxClassId($tax_class_id->getValue());
        }
        $name = $product->getCustomOption('name');
        if ($name && $name->getValue()) {
            $item = $observer['quote_item'];
            $item->setName($name->getValue());
        }
    }

    public function salesOrderInvoicePay($observer) {
        $invoice = $observer->getEvent()->getInvoice();
        /* capture online payment */
        if($invoice->getRequestedCaptureCase() == Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE) {
            return $this;
        }

        $order = $invoice->getOrder();

        /* not WebPOS order */
        if(!$order->getWebposOrderId()) {
            if(!$this->_isPaidByWebPOSPayment($order)) {
                return $this;
            }
        }

        /* Paid all */
        if($order->getBaseTotalPaid() == $order->getBaseGrandTotal()) {
            return $this;
        }


        /* reset total_paid & base_total_paid in order */
        $order->setTotalPaid($order->getTotalPaid() - $invoice->getGrandTotal());
        $order->setBaseTotalPaid($order->getBaseTotalPaid() - $invoice->getBaseGrandTotal());



        /* calculate rewards and giftcard discount */
        $orderItems = $order->getAllItems();
        $itemsDiscount = array();
        if(count($orderItems) > 0){
            foreach ($orderItems as $item){
                $itemId = $item->getId();
                $itemsDiscount[$itemId]['point']['base'] = $item->getData('rewardpoints_base_discount');
                $itemsDiscount[$itemId]['point']['amount'] = $item->getData('rewardpoints_discount');
                $itemsDiscount[$itemId]['voucher']['base'] = $item->getData('base_gift_voucher_discount');
                $itemsDiscount[$itemId]['voucher']['amount'] = $item->getData('gift_voucher_discount');
            }
            $invoiceItems = $invoice->getAllItems();
            $totalPointDiscount = $baseTotalPointDiscount = 0;
            $totalVoucherDiscount = $baseTotalVoucherDiscount = 0;
            $totalDiscountAmount = $baseTotalDiscountAmount = 0;
            foreach ($invoiceItems as $item){
                $itemId = $item->getData('order_item_id');
                if(isset($itemsDiscount[$itemId])){
                    $totalPointDiscount += $itemsDiscount[$itemId]['point']['amount'];
                    $baseTotalPointDiscount += $itemsDiscount[$itemId]['point']['base'];
                    $totalVoucherDiscount += $itemsDiscount[$itemId]['voucher']['amount'];
                    $baseTotalVoucherDiscount += $itemsDiscount[$itemId]['voucher']['base'];
                    $totalDiscountAmount += $totalPointDiscount;
                    $totalDiscountAmount += $totalVoucherDiscount;
                    $baseTotalDiscountAmount += $baseTotalPointDiscount;
                    $baseTotalDiscountAmount += $baseTotalVoucherDiscount;
                }
            }
            $invoice->setData('rewardpoints_discount',$totalPointDiscount);
            $invoice->setData('rewardpoints_base_discount',$baseTotalPointDiscount);

            $invoice->setData('gift_voucher_discount',$totalVoucherDiscount);
            $invoice->setData('base_gift_voucher_discount',$baseTotalVoucherDiscount);

            $invoice->setGrandTotal($invoice->getGrandTotal() - $totalDiscountAmount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalDiscountAmount);

        }
    }

    /**
     * Check order is paid from WebPOS
     *
     * @param \Magento_Sales_Model_Order $order
     * @return bool
     */
    protected function _isPaidByWebPOSPayment($order)
    {
        $orderPayment = Mage::getModel('webpos/payment_orderPayment');
        $posPayments = $orderPayment->getCollection()->addFieldToFilter('order_id', $order->getId());
        if($posPayments->getSize()) {
            return true;
        }
        return false;
    }

    /**
     * save address for default customer on webpos
     * @param $observer
     */
    public function webposConfigurationChange($observer)
    {
        return Mage::helper("webpos/config")->generateGuestCustomerAccount();
    }

    /**
     * @param $observer
     */
    public function beforeBlockToHtml($observer){
        if (($block = $observer->getEvent()->getBlock())&&
            ($block instanceof Mage_Authorizenet_Block_Directpost_Iframe))
        {
            $router = Mage::app()->getRequest()->getRouteName();
            $controller = Mage::app()->getRequest()->getControllerName();
            $action = Mage::app()->getRequest()->getActionName();
            $actionName = Mage::app()->getRequest()->getParam('controller_action_name');
//            if ($actionName == 'webpos' && $router == 'authorizenet' && $controller == 'directpost_payment' && $action == 'redirect') {
            if ($actionName == 'webpos') {
                $block->setTemplate('webpos/iframe.phtml');
            }
        }
    }
}
