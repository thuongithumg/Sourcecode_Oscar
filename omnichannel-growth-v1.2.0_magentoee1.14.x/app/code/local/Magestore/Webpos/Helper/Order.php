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
 * Webpos Helper
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @author      Magestore Developer
 */
class Magestore_Webpos_Helper_Order extends Mage_Core_Helper_Abstract {
    /* @var Magestore_Webpos_Helper_Product $productHelper */
    private $productHelper;

    /**
     * Magestore_Webpos_Helper_Order constructor.
     */
    public function __construct()
    {
        $this->productHelper = Mage::helper('webpos/product');
    }


    /**
     * @param Mage_Sales_Model_Order $order
     * @return Mage_Sales_Model_Order
     */
    public function getAllOrderInfo($order) {
        $order->setData('items', $this->getItemsInfo($order));
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();
        $payment = $this->getPayment($order);
        $itemInfoBuy = $this->getItemsInfoBuy($order);
        $commentsHistory =  Mage::getResourceModel('sales/order_status_history_collection')
        ->addFieldToFilter('parent_id', $order->getId())
        ->setOrder('created_at', 'DESC');
        $comments = array();
        $i = 0;
        foreach ($commentsHistory as $comment) {
            $comments[$i]['comment'] = $comment->getComment();
            $comments[$i]['created_at'] = $comment->getCreatedAt();
            $i++;
        }
        $paymentrObject = Mage::getModel('webpos/payment_orderPayment')->getPaymentListOfOrder($order);
        $order->setData('webpos_order_payments', $paymentrObject);
        $order->setData('status_histories', $comments);
        $order->setData('items_info_buy', array('items' => $itemInfoBuy));
        $order->setData('payment', $payment);
        $order->setData('total_paid', (float)$order->getTotalPaid());
        $order->setData('base_total_paid', (float)$order->getBaseTotalPaid());
        $order->setData('total_refunded', (float)$order->getTotalRefunded());
        $order->setData('base_total_refunded', (float)$order->getBaseTotalRefunded());
        $order->setData('total_due', (float)$order->getTotalDue());
        $order->setData('base_total_due', (float)$order->getBaseTotalDue());
        $order->setData('total_invoiced', (float)$order->getTotalInvoiced());
        $order->setData('base_total_invoiced', (float)$order->getBaseTotalInvoiced());
        //$order->setData('created_at', $order->getCreatedAtStoreDate()->toString("MM/dd/YYYY HH:mm:ss"));
        $order->setData('full_tax_info', $order->getFullTaxInfo());
//        $order->setData('created_at', $order->getCreatedAtStoreDate()->toString("MM/dd/YYYY HH:mm:ss a"));

        if ($billingAddress)
            $order['billing_address'] = $billingAddress->getData();
        if ($shippingAddress)
            $order->setData('extension_attributes', array('shipping_assignments'=> array(array('shipping' => array('address' => $shippingAddress->getData())))));

        $paynlInstoreReceipt = $this->getOrderPaymentAdditionalData($order, 'paynl_receipt');
        if($paynlInstoreReceipt){
            $order->setData('paynl_instore_receipt', $paynlInstoreReceipt);
        }

        if($paynlStatusUrl = $this->getOrderPaymentAdditionalData($order, 'paynl_url')){
            $order->setData('paynl_status_url', $paynlStatusUrl);
        }

        /* @var Mage_Sales_Model_Resource_Order_Invoice_Collection $invoices */
        /* @var Mage_Sales_Model_Order $order */
        $invoices = $order->getInvoiceCollection();

        if ($invoices->getSize()) {
            /* @var Mage_Sales_Model_Order_Invoice $firstInvoice */
            $firstInvoice = $invoices->getFirstItem();
            $order->setData('invoice_increment_id', $firstInvoice->getIncrementId());
            $order->setData('invoice_id', $firstInvoice->getId());
        }

        Mage::dispatchEvent('webpos_prepare_order_for_api_after', array('order' => $order));
        return $order;
    }
    
    /**
     * @param object $order
     * @return array
     */
    public function getItemsInfoBuy($order){
        $items = $order->getAllVisibleItems();
        $totalItems = array();
        if(count($items) > 0){
            foreach ($items as $item) {
                $labels = array();
                $itemInfo = new Varien_Object();
                if(!is_null($item->getProduct()) && $item->getProduct()->getTypeId() != 'customsale'){
                    $itemInfo->setId($item->getProduct()->getId());
                }
                $baseOriginalPrice = ($item->getBaseOriginalPrice())?$item->getBaseOriginalPrice():"";
                $originalPrice = ($item->getOriginalPrice())?$item->getOriginalPrice():"";
                $itemInfo->setBaseOriginalPrice($baseOriginalPrice);
                $itemInfo->setOriginalPrice($originalPrice);
                $itemInfo->setUnitPrice($item->getPrice());
                $itemInfo->setBaseUnitPrice($item->getBasePrice());

                $labels = array_merge($labels,$this->getBundleOptionsLabel($item->getProductOptionByCode("bundle_options")));
                $labels = array_merge($labels,$this->getOptionsLabel($item->getProductOptionByCode("attributes_info")));
                $labels = array_merge($labels,$this->getOptionsLabel($item->getProductOptionByCode("options")));
                $info = $item->getBuyRequest()->toArray();
                if(count($info) > 0){
                    foreach ($info as $key => $value) {
                        if(is_array($value)){
                            $options = array();
                            foreach ($value as $code => $data) {
                                $options[] = array(
                                    "id" => $code,
                                    "value" => $data
                                );
                            }
                            $value = $options;
                        }
                        $itemInfo->setData($key,$value);
                    }
                }
                if(!is_null($item->getProduct()) && $item->getProduct()->getTypeId() == 'customsale'){
                    $itemInfo->setId('custom_item');
                    $itemInfo->setCustomSalesInfo(
                        array(
                            array(
                                'product_id' => 'customsale',
                                'product_name' => $item->getName(),
                                'unit_price' => (float)$item->getPrice(),
                                'tax_class_id' => $item->getCustomTaxClassId(),
                                'is_virtual' => $item->getIsVirtual(),
                                'qty' => (int)$item->getQtyOrdered()
                            )
                        )
                    );
                }
                $childs = $item->getChildrenItems();
                if(count($childs) > 0){
                    $child = $childs[0];
                    $itemInfo->setChildId($child->getProductId());
                }
                $itemInfo->setData('options_label', implode(', ',$labels));
                $totalItems[] = $itemInfo->getData();
            }
        }
        return $totalItems;
    }

    /**
     * @param object $order
     * @return array
     */
    public function getItemInfoBuy($item){
        $infoBuy = array();
        if($item){
            $labels = array();
            $itemInfo = new Varien_Object();
            if(!is_null($item->getProduct()) && $item->getProduct()->getTypeId() != 'customsale'){
                $itemInfo->setId($item->getProduct()->getId());
            }
            $baseOriginalPrice = ($item->getBaseOriginalPrice())?$item->getBaseOriginalPrice():"";
            $originalPrice = ($item->getOriginalPrice())?$item->getOriginalPrice():"";
            $itemInfo->setBaseOriginalPrice($baseOriginalPrice);
            $itemInfo->setOriginalPrice($originalPrice);
            $itemInfo->setUnitPrice($item->getPrice());
            $itemInfo->setBaseUnitPrice($item->getBasePrice());

            $labels = array_merge($labels,$this->getBundleOptionsLabel($item->getProductOptionByCode("bundle_options")));
            $labels = array_merge($labels,$this->getOptionsLabel($item->getProductOptionByCode("attributes_info")));
            $labels = array_merge($labels,$this->getOptionsLabel($item->getProductOptionByCode("options")));
            $info = $item->getBuyRequest()->toArray();
            if(count($info) > 0){
                foreach ($info as $key => $value) {
                    if(is_array($value)){
                        $options = array();
                        foreach ($value as $code => $data) {
                            $options[] = array(
                                "id" => $code,
                                "value" => $data
                            );
                        }
                        $value = $options;
                    }
                    $itemInfo->setData($key,$value);
                }
            }
            if(!is_null($item->getProduct()) && $item->getProduct()->getTypeId() == 'customsale'){
                $itemInfo->setId('custom_item');
                $itemInfo->setCustomSalesInfo(
                    array(
                        array(
                            'product_id' => 'customsale',
                            'product_name' => $item->getName(),
                            'unit_price' => (float)$item->getPrice(),
                            'tax_class_id' => $item->getCustomTaxClassId(),
                            'is_virtual' => $item->getIsVirtual(),
                            'qty' => (int)$item->getQtyOrdered()
                        )
                    )
                );
            }
            $childs = $item->getChildrenItems();
            if(count($childs) > 0){
                $child = $childs[0];
                $itemInfo->setChildId($child->getProductId());
            }
            $itemInfo->setData('options_label', implode(', ',$labels));
            $infoBuy = $itemInfo->getData();
        }
        return $infoBuy;
    }

    /**
     *
     * @param object | Mage_Sales_Model_Order  $order
     * @return array
     */
    public function getOrderData($order) {
        $orderData = array();
        $orderObject = Mage::getModel('sales/order')->load($order->getId());
        $paymentrObject = Mage::getModel('webpos/payment_orderPayment')->getPaymentListOfOrder($order);

        /* @var Mage_Sales_Model_Resource_Order_Invoice_Collection $invoices */
        $invoices = $orderObject->getInvoiceCollection();

        if ($invoices->getSize()) {
            /* @var Mage_Sales_Model_Order_Invoice $firstInvoice */
            $firstInvoice = $invoices->getFirstItem();
            $orderData['invoice_increment_id'] = $firstInvoice->getIncrementId();
            $orderData['invoice_id'] = $firstInvoice->getId();
        }

        $orderData['increment_id'] = $order->getData('increment_id');
        $orderData['applied_rule_ids'] = $order->getData('applied_rule_ids');
        $orderData['coupon_code'] = $order->getData('coupon_code');
        $orderData['discount_description'] = $order->getData('discount_description');
        $orderData['base_currency_code'] = $order->getData('base_currency_code');
        $orderData['total_due'] = (float)$order->getData('total_due');
        $orderData['base_total_due'] = (float)$order->getData('base_total_due');
        $orderData['total_paid'] = round((float)$order->getData('total_paid'), 2);
        $orderData['total_invoiced'] = (float)$order->getData('total_invoiced');
        $orderData['base_total_invoiced'] = (float)$order->getData('base_total_invoiced');
        $orderData['webpos_change'] = (float)$order->getData('webpos_change');
        $orderData['webpos_base_change'] = (float)$order->getData('webpos_base_change');
        $orderData['webpos_change'] = (float)$order->getData('webpos_change');
        $orderData['webpos_base_change'] = (float)$order->getData('webpos_base_change');
        $orderData['amount_refunded'] = (float)$order->getData('amount_refunded');
        $orderData['total_refunded'] = (float)$order->getData('total_refunded');
        $orderData['total_offline_refunded'] = (float)$order->getData('total_offline_refunded');
        $orderData['base_total_refunded'] = (float)$order->getData('base_total_refunded');
        $orderData['base_shipping_refunded'] = (float)$order->getData('base_shipping_refunded');
        $orderData['subtotal_refunded'] = (float)$order->getData('subtotal_refunded');
        $orderData['base_amount_refunded'] = (float)$order->getData('base_amount_refunded');
        $orderData['base_total_paid'] = round((float)$order->getData('base_total_paid'),2);
        $orderData['base_customercredit_discount'] = (float)$order->getData('base_customercredit_discount');
        $orderData['base_discount_amount'] = (float)$order->getData('base_discount_amount');
        $orderData['base_gift_voucher_discount'] = (float)$order->getData('base_gift_voucher_discount');
        $orderData['gift_voucher_discount'] = (float)$order->getData('gift_voucher_discount');
        if ($orderData['base_gift_voucher_discount'] != 0)
            $orderData['gift_codes'] = $orderObject->getGiftCodes();
        $orderData['base_grand_total'] = (float)$order->getData('base_grand_total');
        $orderData['base_shipping_amount'] = (float)$order->getData('base_shipping_amount');
        $orderData['base_shipping_discount_amount'] = (float)$order->getData('base_shipping_discount_amount');
        $orderData['base_shipping_incl_tax'] = (float)$order->getData('base_shipping_incl_tax');
        $orderData['base_shipping_tax_amount'] = (float)$order->getData('base_shipping_tax_amount');
        $orderData['base_subtotal'] = (float)$order->getData('base_subtotal');
        $orderData['base_subtotal_incl_tax'] = (float)$order->getData('base_subtotal_incl_tax');
        $orderData['base_tax_amount'] = (float)$order->getData('base_tax_amount');
        $orderData['base_to_global_rate'] = (float)$order->getData('base_to_global_rate');
        $orderData['base_to_order_rate'] = (float)$order->getData('base_to_order_rate');
        $orderData['rewardpoints_base_discount'] = (float)$order->getData('rewardpoints_base_discount');
        $orderData['billing_address_id'] = $order->getData('billing_address_id');
        $orderData['created_at'] = $order->getCreatedAt();
        //$orderData['created_at'] = $order->getCreatedAtStoreDate()->toString("MM/dd/YYYY HH:mm:ss");
//        $orderData['created_at'] = $order->getCreatedAtStoreDate()->toString("MM/dd/YYYY HH:mm:ss a");
        $orderData['customer_dob'] = $order->getData('customer_dob');
        $orderData['customer_id'] = $order->getData('customer_id');
        $orderData['customer_lastname'] = $order->getData('customer_lastname');
        $orderData['customer_firstname'] = $order->getData('customer_firstname');
        $orderData['customer_gender'] = $order->getData('customer_gender');
        $orderData['customer_email'] = $order->getData('customer_email');
        $orderData['customer_group_id'] = $order->getData('customer_group_id');
        $orderData['customer_is_guest'] = $order->getData('customer_is_guest');
        $orderData['customer_note_notify'] = $order->getData('customer_note_notify');
        $orderData['customercredit_discount'] = (float)$order->getData('customercredit_discount');
        $orderData['discount_amount'] = $order->getData('discount_amount');
        $orderData['email_sent'] = $order->getData('email_sent');
        $orderData['entity_id'] = $order->getData('entity_id');
        $orderData['global_currency_code'] = $order->getData('global_currency_code');
        $orderData['grand_total'] = (float)$order->getData('grand_total');
        $orderData['is_virtual'] = $order->getData('is_virtual');
        $orderData['payment'] = $order->getData('payment');
        $orderData['protect_code'] = $order->getData('protect_code');
        $orderData['quote_id'] = $order->getData('quote_id');
        $orderData['remote_ip'] = $order->getData('remote_ip');
        $orderData['rewardpoints_base_discount'] = (float)$order->getData('rewardpoints_base_discount');
        $orderData['rewardpoints_discount'] = (float)$order->getData('rewardpoints_discount');
        $orderData['rewardpoints_earn'] = (float)$order->getData('rewardpoints_earn');
        $orderData['rewardpoints_spent'] = (float)$order->getData('rewardpoints_spent');
        $orderData['shipping_amount'] = (float)$order->getData('shipping_amount');
        $orderData['shipping_description'] = $order->getData('shipping_description');
        $orderData['shipping_discount_amount'] = $order->getData('shipping_discount_amount');
        $orderData['shipping_incl_tax'] = $order->getData('shipping_incl_tax');
        $orderData['state'] = $order->getData('state');
        $orderData['status'] = $order->getData('status');
        $orderData['store_currency_code'] = $order->getData('store_currency_code');
        $orderData['store_id'] = $order->getData('store_id');
        $orderData['store_name'] = $order->getData('store_name');
        $orderData['subtotal'] = (float)$order->getData('subtotal');
        $orderData['subtotal_incl_tax'] = (float)$order->getData('subtotal_incl_tax');
        $orderData['tax_amount'] = (float)$order->getData('tax_amount');
        $orderData['total_item_count'] = (float)$order->getData('total_item_count');
        $orderData['total_qty_ordered'] = (int)$order->getData('total_qty_ordered');
        $orderData['updated_at'] = $order->getData('updated_at');
        $orderData['webpos_order_payments'] = $paymentrObject;
        $orderData['weight'] = $order->getData('weight');
        $orderData['webpos_delivery_date'] = $order->getData('webpos_delivery_date');
        $orderData['customer_note'] = $order->getData('customer_note');
        $orderData['webpos_staff_name'] = $order->getData('webpos_staff_name');
        $orderData['webpos_staff_id'] = $order->getData('webpos_staff_id');
        $orderData['webpos_till_id'] = $order->getData('webpos_till_id');
        $orderData['order_currency_code'] = $order->getData('order_currency_code');
        $orderData['base_customercredit_discount'] = (float)$order->getData('base_customercredit_discount');
        $orderData['customercredit_discount'] = (float)$order->getData('customercredit_discount');
        $orderData['full_tax_info'] = $order->getFullTaxInfo();

        $paynlInstoreReceipt = $this->getOrderPaymentAdditionalData($order, 'paynl_receipt');
        if($paynlInstoreReceipt){
            $orderData['paynl_instore_receipt'] = $paynlInstoreReceipt;
        }

        if($paynlStatusUrl = $this->getOrderPaymentAdditionalData($order, 'paynl_url')){
            $orderData['paynl_status_url'] = $paynlStatusUrl;
        }

        $result = new Varien_Object($orderData);
        Mage::dispatchEvent('webpos_prepare_order_data_for_api_after', array('order' => $order, 'orderData' => $result));
        return $result->getData();
    }

    /**
     *
     * @param array $options
     * @return array
     */
    protected function getBundleOptionsLabel($options){
        $labels = array();
        if($options){
            foreach ($options as $option) {
                if(is_array($option['value'])){
                    foreach ($option['value'] as $data) {
                        $labels[] = $data['qty'] ."x ". $data['title'];
                    }
                }
            }
        }
        return $labels;
    }

    /**
     *
     * @param array $options
     * @return array
     */
    protected function getOptionsLabel($options){
        $labels = array();
        if($options){
            foreach ($options as $option) {
                $labels[] = $option['value'];
            }
        }
        return $labels;
    }

    /**
     *
     * @param object $order
     * @return
     */
    public function getPayment($order) {
        try {
            $tempBlock = Mage::app()->getLayout()->createBlock('webpos/order_payment')->setOrder($order);
            return $tempBlock->toHtml();
        } catch (Mage_Core_Exception $e) {
            return "";
        }
    }

    public function convertDate($dateString){
        try {
            $dateString = Mage::app()->getLocale()
                ->date($dateString, Varien_Date::DATETIME_INTERNAL_FORMAT)->toString('M/d/Y H:m:s');
        }
        catch (Exception $e)
        {
            $dateString = Mage::app()->getLocale()
                ->date($dateString, Varien_Date::DATETIME_INTERNAL_FORMAT)->toString('M/d/Y H:m:s');
        }
        return $dateString;

    }

    /**
     * @param $order
     * @param string $key
     * @return array
     */
    public function getOrderPaymentAdditionalData($order, $key = ''){
        $data = array();
        if($order && $order->getPayment()){
            $data = $order->getPayment()->getAdditionalInformation($key);
        }
        return $data;
    }

    /**
     * @param $order
     * @return array
     */
    public function getItemsInfo($order)
    {
        $items = $order->getAllVisibleItems();
        $orderData = array();
        $i = 0;
        foreach ($items as $item) {
            $orderData[$i]['item_id'] = $item->getData('item_id');
            $orderData[$i]['name'] = $item->getData('name');
            $orderData[$i]['created_at'] = $item->getData('created_at');
            $orderData[$i]['amount_refunded'] = (float)$item->getData('amount_refunded');
            $orderData[$i]['base_amount_refunded'] = (float)$item->getData('base_amount_refunded');
            $orderData[$i]['base_discount_amount'] = (float)$item->getData('base_discount_amount');
            $orderData[$i]['base_discount_invoiced'] = (float)$item->getData('base_discount_invoiced');
            $orderData[$i]['base_gift_voucher_discount'] = (float)$item->getData('base_gift_voucher_discount');
            $orderData[$i]['gift_voucher_discount'] = (float)$item->getData('gift_voucher_discount');
            $orderData[$i]['base_price'] = (float)$item->getData('base_price');
            $orderData[$i]['base_price_incl_tax'] = (float)$item->getData('base_price_incl_tax');
            $orderData[$i]['base_row_invoiced'] = (float)$item->getData('base_row_invoiced');
            $orderData[$i]['base_row_total'] = (float)$item->getData('base_row_total');
            $orderData[$i]['base_row_total_incl_tax'] = (float)$item->getData('base_row_total_incl_tax');
            $orderData[$i]['base_tax_amount'] = (float)$item->getData('base_tax_amount');
            $orderData[$i]['base_tax_invoiced'] = (float)$item->getData('base_tax_invoiced');
            $orderData[$i]['base_tax_amount'] = (float)$item->getData('base_tax_amount');
            $orderData[$i]['discount_amount'] = (float)$item->getData('discount_amount');
            $orderData[$i]['discount_invoiced'] = (float)$item->getData('discount_invoiced');
            $orderData[$i]['discount_percent'] = (float)$item->getData('discount_percent');
            $orderData[$i]['discount_invoiced'] = (float)$item->getData('discount_invoiced');
            $orderData[$i]['rewardpoints_base_discount'] = (float)$item->getData('rewardpoints_base_discount');
            $orderData[$i]['free_shipping'] = $item->getData('free_shipping');
            $orderData[$i]['is_qty_decimal'] = $item->getData('is_qty_decimal');
            $orderData[$i]['is_virtual'] = $item->getData('is_virtual');
            $orderData[$i]['original_price'] = (float)$item->getData('original_price');
            $orderData[$i]['base_original_price'] = (float)$item->getData('base_original_price');
            $orderData[$i]['price'] = (float)$item->getData('price');
            $orderData[$i]['price_incl_tax'] = (float)$item->getData('price_incl_tax');
            $orderData[$i]['product_id'] = $item->getData('product_id');
            $orderData[$i]['product_type'] = $item->getData('product_type');
            $orderData[$i]['qty_canceled'] = (float)$item->getData('qty_canceled');
            $orderData[$i]['qty_invoiced'] = (float)$item->getData('qty_invoiced');
            $orderData[$i]['qty_ordered'] = (float)$item->getData('qty_ordered');
            $orderData[$i]['qty_refunded'] = (float)$item->getData('qty_refunded');
            $orderData[$i]['qty_shipped'] = (float)$item->getData('qty_shipped');
            $orderData[$i]['quote_item_id'] = $item->getData('quote_item_id');
            $orderData[$i]['row_invoiced'] = $item->getData('row_invoiced');
            $orderData[$i]['row_total'] = (float)$item->getData('row_total');
            $orderData[$i]['row_total_incl_tax'] = (float)$item->getData('row_total_incl_tax');
            $orderData[$i]['row_weight'] = $item->getData('row_weight');
            $orderData[$i]['sku'] = $item->getData('sku');
            $orderData[$i]['store_id'] = $item->getData('store_id');
            $orderData[$i]['tax_amount'] = (float)$item->getData('tax_amount');
            $orderData[$i]['tax_invoiced'] = (float)$item->getData('tax_invoiced');
            $orderData[$i]['tax_percent'] =(float) $item->getData('tax_percent');
            $orderData[$i]['updated_at'] = $item->getData('updated_at');
            $orderData[$i]['order_id'] = $order->getId();

            if ($item->getProductType() == 'giftvoucher') {
                $orderData[$i]['giftvoucher_info'] = $this->getProductHelper()
                    ->getGiftVoucherInfoFromOrderItem($item);
            }
            $orderData[$i]['ordered_warehouse_id'] =  (int) $item->getData('ordered_warehouse_id');
            $i++;
        }

        return $orderData;
    }

    /**
     * @return Magestore_Webpos_Helper_Product
     */
    public function getProductHelper()
    {
        return $this->productHelper;
    }

    /**
     * @param Magestore_Webpos_Helper_Product $productHelper
     */
    public function setProductHelper($productHelper)
    {
        $this->productHelper = $productHelper;
    }
}
