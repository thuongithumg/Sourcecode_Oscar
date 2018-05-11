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
if (! function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( !array_key_exists($columnKey, $value)) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( !array_key_exists($indexKey, $value)) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}

class Magestore_Webpos_Service_Checkout_Response extends Magestore_Webpos_Service_Abstract
{
    /**
     * @param Mage_Sales_Model_Quote $quote
     */
    public function initQuote($data){
        if($data instanceof Mage_Sales_Model_Quote){
            $this->getCheckoutModel()->setQuote($data);
        }elseif(is_array($data) && isset($data[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_ID])){
            $this->getCheckoutModel()->initData($data);
        }
    }

    /**
     * @return mixed
     */
    public function getQuoteItemsSummaryQty(){
        return $this->getQuote()->getItemsSummaryQty();
    }

    /**
     * @return array
     */
    public function getQuoteItems(){
        $result = array();
        $items = $this->getQuote()->getAllVisibleItems();
        if(count($items)){
            foreach ($items as $item){
                $itemId = $item->getId();
                $data = $item->getData();
                $data['options_data'] =  $this->getItemOptionsData($item);
                $data['options_label'] =  $this->getItemOptionsLable($item);
                $data['buy_request'] =  $item->getBuyRequest()->getData();
                $data['move_from_shopping_cart'] =  $item->getBuyRequest()->getData('move_from_shopping_cart');
                $data['warehouse_id'] =  $item->getData('ordered_warehouse_id');
                $data['offline_item_id'] =  $item->getBuyRequest()->getData('item_id');
                $data['image_url'] =  $this->_getHelper('catalog/image')->init($item->getProduct(), 'thumbnail')->resize('500')->__toString();
                $data['minimum_qty'] =  $item->getProduct()->getStockItem()->getMinSaleQty();
                $data['maximum_qty'] =  $item->getProduct()->getStockItem()->getMaxSaleQty();
                $data['qty_increment'] =  $item->getProduct()->getStockItem()->getQtyIncrements();
                if(!$item->getData('original_price')) {
                    $data['original_price'] = $this->getProductOriginalPrice($item);
                }
                $childrens = $item->getChildren();
                if($childrens && ($item->getProductType() != 'bundle')){
                    foreach ($childrens as $children){
                        $data['child_id'] = $children->getProductId();
                    }
                }
                if($item->getProductType() == "configurable"){
                    $childProduct = Mage::getModel('catalog/product')->loadByAttribute('sku',$item->getSku());
                    $data['image_url'] = Mage::helper('catalog/image')->init($childProduct, 'thumbnail')->resize('500')->__toString();
                }
                $data['sku'] = Mage::getModel('catalog/product')->load($item->getProductId())->getSku();
                $dataObject = new Varien_Object($data);
                $this->_dispatchEvent('webpos_prepare_quote_item_data_for_api_after', array('item' => $item, 'itemData' => $dataObject));
                $result[$itemId] = $dataObject->getData();
            }
        }
        return $result;
    }

    /**
     * get product original price
     */
    public function getProductOriginalPrice($item)
    {
        $price = 0;
        $currentCurrency = $this->getQuote()->getQuoteCurrencyCode();
        $baseCurrency = $this->getQuote()->getBaseCurrencyCode();
        if($item->getData('base_original_price')) {
            $price =  $this->_helper->convertPrice($item->getData('base_original_price'), $baseCurrency, $currentCurrency);
        }
        return $price;
    }

    /**
     * @return array
     */
    public function getTotals(){
        $totals = $this->getQuote()->getTotals();
        $totalsResult = array();
        foreach ($totals as $total) {
            $data = $total->getData();
            if($this->_helper->isRewardPointsEnable()){
                if($data['code'] == 'rewardpoints_label') {
                    if($this->_helper->isGuestCustomer($this->getQuote())){
                        continue;
                    }
                    $data['title'] = $this->_helper->__('Customer will earn');
                    $data['value'] = $this->_getHelper('rewardpoints/calculation_earning')->getTotalPointsEarning($this->getQuote());
                }
            }
//            if($data['code'] == 'shipping') {
//                /** @var Mage_Sales_Model_Quote_Address $address */
//                $address = $data['address'];
//                $data['value'] = $address->getShippingInclTax();
//            }
            $totalsResult[] = $data;
        }
        $totalsObject = new Varien_Object();
        $totalsObject->setList($totalsResult);
        $data = array(
            'totals' => $totalsObject
        );
        $this->_dispatchEvent(Magestore_Webpos_Api_CheckoutInterface::EVENT_WEBPOS_GET_TOTALS_AFTER, $data);
        return $totalsObject->getList();
    }

    /**
     * @return array
     */
    public function getShipping(){
        $shippingList = array();
        $api = $this->_getCheckoutApi('shipping');
        $list = $api->getShippingMethodsList($this->getQuote()->getId());
        $allowMethods = Mage::getModel('webpos/shipping_shippingRepository')->getOfflineShippingData();
        $allowMethods = array_column($allowMethods, 'code');
        if(count($list) > 0){
            $shippingHelper = $this->_getHelper('webpos/shipping');
            foreach ($list as $data) {
                $methodCode = $data['code'];
                if (!in_array($methodCode, $allowMethods)) {
                    continue;
                }
                $isDefault = '0';
                if($methodCode == $shippingHelper->getDefaultShippingMethod()) {
                    $isDefault = '1';
                }
                $methodTitle = $data['carrier_title'].' - '.$data['method_title'];
                $methodPrice = ($data['price'] != null) ? $data['price'] : '0';
                $methodPriceType = '';
                $methodDescription = ($data['method_description'] != null) ?$data['method_description'] : '0';
                $methodSpecificerrmsg = (isset($data['error_message']) && $data['error_message'] != null) ?$data['error_message'] : '';

                $shippingModel =  $this->_getModel('webpos/shipping_shipping');
                $shippingModel->setCode($methodCode);
                $shippingModel->setTitle($methodTitle);
                $shippingModel->setPrice($methodPrice);
                $shippingModel->setDescription($methodDescription);
                $shippingModel->setIsDefault($isDefault);
                $shippingModel->setErrorMessage($methodSpecificerrmsg);
                $shippingModel->setPriceType($methodPriceType);
                $shippingList[] = $shippingModel->getData();
            }
        }
        $shippings = new Varien_Object();
        $shippings->setList($shippingList);
        $data = array(
            'shippings' => $shippings
        );
        $this->_dispatchEvent(Magestore_Webpos_Api_CheckoutInterface::EVENT_WEBPOS_GET_SHIPPINGS_AFTER, $data);
        return $shippings->getList();
    }

    /**
     * @return mixed
     */
    public function getPayment(){
        $api = $this->_getCheckoutApi('payment');
        $list = $api->getPaymentMethodsList($this->getQuote()->getId());
        $paymentList = array();
        $multiablePayments = $this->_getModel('webpos/source_adminhtml_payment')->getMultiablePaymentsCode();
        $mobilePayments = array('paypal_integration', 'paypal_here', 'authorizenet_integration', 'stripe_integration');
        $webPayments = array('authorizenet_directpost', 'payflowpro', 'authorizenet', 'paypal_direct');
        $paymentHelper = $this->_getHelper('webpos/payment');
        if($paymentHelper->isRetailerPos()) {
            $deactivePayments = $webPayments;
        } else {
            $deactivePayments = $mobilePayments;
        }

        $ccPayments = array('authorizenet_directpost', 'payflowpro', 'authorizenet_integration', 'stripe_integration');
        $sdkPayments = array('paypal_integration', 'paypal_here');
        if(count($list) > 0) {
            foreach ($list as $data) {
                $code = $data['code'];
                $title = $data['title'];
                $ccTypes = $data['cc_types'];
                if ($code == 'multipaymentforpos') {
                    continue;
                }
                if (in_array($code, $deactivePayments)) {
                    continue;
                }
                $typeId = 0;
                $useCvv = 0;
                if (in_array($code, $ccPayments)) {
                    $typeId = '1';
                    $useCvv = $paymentHelper->useCvv($code);
                }
                if (in_array($code, $sdkPayments)) {
                    $typeId = '2';
                }
                $iconClass = 'icon-iconPOS-payment-cp1forpos';
                $isDefault = ($code == $paymentHelper->getDefaultPaymentMethod())?Magestore_Webpos_Api_PaymentInterface::YES:Magestore_Webpos_Api_PaymentInterface::NO;
                $isReferenceNumber = $paymentHelper->isReferenceNumber($code) ? '1' : '0';
                $isPayLater = $paymentHelper->isPayLater($code) ? '1' : '0';
                $isMultiable = (in_array($code, $multiablePayments))?true:false;

                $paymentModel =  $this->_getModel('webpos/payment_payment');
                $paymentModel->setCode($code);
                $paymentModel->setIconClass($iconClass);
                $paymentModel->setTitle($title);
                $paymentModel->setInformation('');
                $paymentModel->setType(($ccTypes)?$ccTypes:Magestore_Webpos_Api_PaymentInterface::NO);
                $paymentModel->setIsDefault($isDefault);
                $paymentModel->setIsReferenceNumber($isReferenceNumber);
                $paymentModel->setIsPayLater($isPayLater);
                $paymentModel->setMultiable($isMultiable);
                $paymentSource = $this->_getModel('webpos/source_adminhtml_payment');
                $formData = $paymentSource->getPaymentFormInfo($code);
                $paymentModel->setTemplate($formData['template']);
                $paymentModel->setFormData($formData['data']);
                $paymentModel->setUsecvv($useCvv);
                $paymentModel->setTypeId($typeId);
                $paymentList[] = $paymentModel->getData();
            }
        }
        $payments = new Varien_Object();
        $payments->setList($paymentList);
        $data = array(
            'payments' => $payments
        );
        $this->_dispatchEvent(Magestore_Webpos_Api_CheckoutInterface::EVENT_WEBPOS_GET_PAYMENT_AFTER, $data);
        $paymentList = $data['payments']->getList();
        return $paymentList;
    }

    /**
     * @param $order
     * @return array
     */
    public function getOrderSuccessData($order){
        $data = array();
        if($order){
            if($order instanceof Mage_Sales_Model_Order){
                //reload order to get the real data changed after create invoice / shipment
                $order = $this->_getModel('sales/order')->load($order->getId());
            }else{
                $order = $this->_getModel('sales/order')->load($order);
            }
            if($order->getId()){
                $data = $this->_getHelper('webpos/order')->getAllOrderInfo($order)->getData();
                $data['webpos_order_payments'] = $this->_getWebposPaidPayment($order);
            }
        }
        return $data;
    }

    /**
     * @param $order
     * @return array
     */
    private function _getWebposPaidPayment($order){
        $payments = array();
        if($order){
            $order = ($order instanceof Mage_Sales_Model_Order)?$order:$this->_getModel('sales/order')->load($order);
            if($order->getId()){
                $collection = $this->_getModel('webpos/payment_orderPayment')->getCollection()->addFieldToFilter('order_id', $order->getId());
                if($collection->getSize() > 0){
                    foreach ($collection as $payment) {
                        $payments[] = $payment->getData();
                    }
                }
            }
        }
        return $payments;
    }

    /**
     * @param $item
     * @return string
     */
    public function getItemOptionsLable($item){
        $itemOptionsInCart = $this->getItemOptionsData($item);
        $customOptions = '';
        if (isset($itemOptionsInCart['options'])) {
            $custom = array();
            foreach ($itemOptionsInCart['options'] as $optionData):
                if (isset($optionData['value']))
                    $custom[] = $optionData['value'];
            endforeach;
            $customOptions = implode(', ', $custom);
        }

        if (isset($itemOptionsInCart['attributes_info'])) {
            $optionsArr = array();
            foreach ($itemOptionsInCart['attributes_info'] as $info) {
                if (isset($info['value'])) {
                    $optionsArr[] = $info['value'];
                }
            }
            $optionsStr = implode(', ', $optionsArr);
            $label = ($customOptions)?$optionsStr . ', ' . $customOptions:$optionsStr;
        }else {
            $label = $customOptions;
        }
        return $label;
    }

    /**
     * @param $item
     * @return mixed
     */
    public function getItemOptionsData($item){
        $itemOptionsInCart = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
        return $itemOptionsInCart;
    }
}
