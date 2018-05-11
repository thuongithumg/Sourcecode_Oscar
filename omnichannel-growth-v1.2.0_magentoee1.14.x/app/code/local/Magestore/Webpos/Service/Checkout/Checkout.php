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

class Magestore_Webpos_Service_Checkout_Checkout extends Magestore_Webpos_Service_Abstract
{
    /**
     * Magestore_Webpos_Service_Checkout_Checkout constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->_responseService = $this->_createService('checkout_response');
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param array|string $section
     * @return mixed
     */
    public function getCartData($quoteData, $section){
        $data = array();
        $message = array();
        if(!empty($quoteData) && is_array($quoteData)){
            $orderCreateModel = $this->_startAction($quoteData);
            $this->_finishAction(false);
            if(is_array($section)){
                $section[] = Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_INIT;
            }else{
                $section = array(Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_INIT, $section);
            }
            $data = $this->_getQuoteData($section, $orderCreateModel);
            $message = $this->_getQuoteErrors($orderCreateModel);
        }
        $status = (empty($message))?Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS:Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @return mixed
     */
    public function removeCart($quoteData){
        $data = array();
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if(!empty($quoteData)){
            $orderCreateModel = $this->getCheckoutModel();
            $eventData = array(
                'quote' => $this->getQuote()
            );
            $this->_dispatchEvent(Magestore_Webpos_Api_CheckoutInterface::EVENT_WEBPOS_EMPTY_CART_BEFORE, $eventData);
            //$orderCreateModel->removeQuote($quoteData);
            $orderCreateModel->getSession()->clear();
            $data[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_INIT] = array(
                Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_ID => '',
                Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_ID => ''
            );
            $this->_assignQuoteToStaff(array());
            $this->_dispatchEvent(Magestore_Webpos_Api_CheckoutInterface::EVENT_WEBPOS_EMPTY_CART_AFTER, array());
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param string $itemId
     */
    public function removeItem($quoteData, $itemId){
        $data = array();
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if(!empty($itemId)){
            $orderCreateModel = $this->_startAction($quoteData);
            $orderCreateModel->removeQuoteItem($itemId);
            $this->_finishAction();
            $data = $this->_getQuoteData(array(Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_INIT, Magestore_Webpos_Api_Cart_QuoteDataInitInterface::ITEMS, Magestore_Webpos_Api_Cart_QuoteDataInitInterface::TOTALS), $orderCreateModel);
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param Magestore_Webpos_Api_Cart_ItemRequestInterface[] $buyRequests
     * @param array $customerData
     * @param array $updateSections
     * @return mixed
     */
    public function saveCart($quoteData, $buyRequests, $customerData, $updateSections)
    {
        $data = array();
        $message = array();
        $errors = array();
        $warning = array();
        if(!empty($buyRequests) && is_array($buyRequests)){
            $orderCreateModel = $this->_startAction($quoteData);
            try{
                $this->_saveCart($buyRequests, $orderCreateModel);
                $this->_setCustomer($customerData, $orderCreateModel);
                $this->_setDefaultData($orderCreateModel);
            }catch (Exception $e){
                $errors[] = $e->getMessage();
            }
            $eventData = array(
                'quote' => $this->getQuote()
            );
            $this->_dispatchEvent(Magestore_Webpos_Api_CheckoutInterface::EVENT_WEBPOS_SAVE_CART_AFTER, $eventData);
            $this->_finishAction();
            $data = $this->_getQuoteData($updateSections, $orderCreateModel);
            $warning = $this->_getQuoteErrors($orderCreateModel);
        }
        if(!empty($error)) {
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
            $message = $errors;
        } else if (!empty($warning)) {
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_WARNING;
            $message = $warning;
        } else {
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param array $customerData
     * @return mixed
     */
    public function selectCustomer($quoteData, $customerData)
    {
        $data = array();
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if(!empty($customerData) && is_array($customerData)){
            $orderCreateModel = $this->_startAction($quoteData);
            $this->_setCustomer($customerData, $orderCreateModel);
            $this->_finishAction();
            $data = $this->_getQuoteData(array(), $orderCreateModel);
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param string $method
     * @return mixed
     */
    public function saveShippingMethod($quoteData, $method){
        $data = array();
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if(!empty($method)){
            $orderCreateModel = $this->_startAction($quoteData);
            $orderCreateModel->saveShippingMethod($method);
            // $this->_getCheckoutApi('shipping')->setShippingMethod($orderCreateModel->getQuote()->getId(), $method);
            $this->_finishAction();
            $data = $this->_getQuoteData(array(
                Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_INIT, Magestore_Webpos_Api_Cart_QuoteDataInitInterface::TOTALS, Magestore_Webpos_Api_Cart_QuoteDataInitInterface::PAYMENT, Magestore_Webpos_Api_Cart_QuoteDataInitInterface::ITEMS
            ), $orderCreateModel);
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param string $method
     * @return mixed
     */
    public function savePaymentMethod($quoteData, $method){
        $data = array();
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if(!empty($method)){
            $payment = array(Magestore_Webpos_Api_Checkout_PaymentInterface::METHOD => $method);
            $orderCreateModel = $this->_startAction($quoteData);
            $orderCreateModel->setPaymentData($payment);
            $orderCreateModel->getQuote()->getPayment()->addData($payment);
            $this->_finishAction();
            $data = $this->_getQuoteData(array(
                Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_INIT, Magestore_Webpos_Api_Cart_QuoteDataInitInterface::TOTALS, Magestore_Webpos_Api_Cart_QuoteDataInitInterface::ITEMS
            ), $orderCreateModel);
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param array $fields
     * @return mixed
     */
    public function saveQuoteData($quoteData, $fields){
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if(!empty($fields)){
            $orderCreateModel = $this->_startAction($quoteData);
            $orderCreateModel->addQuoteData($fields);
            $this->checkDiscount($quoteData);
            $this->_finishAction();
            $data = $this->_getQuoteData(array(), $orderCreateModel);
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param string $couponCode
     * @return mixed
     */
    public function applyCoupon($quoteData, $couponCode){
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if(!empty($couponCode)){
            $orderCreateModel = $this->_startAction($quoteData);
            $quote = $orderCreateModel->getQuote();
            try {
                $quote->getShippingAddress()->setCollectShippingRates(true);
                $quote->setCouponCode(strlen($couponCode) ? $couponCode : '')
                    ->collectTotals();
            } catch (Exception $e) {
                $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
                $message[] = $e->getMessage();
            }
            if (!$couponCode == $quote->getCouponCode()) {
                $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
                $message[] = $this->__('Coupon code is not valid');
            }
            $this->_finishAction();
            $data = $this->_getQuoteData(array(), $orderCreateModel);
        }else{
            return $this->cancelCoupon($quoteData);
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @return mixed
     */
    public function cancelCoupon($quoteData){
        $data = array();
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if(!empty($quoteData)){
            $orderCreateModel = $this->_startAction($quoteData);
            $quote = $orderCreateModel->getQuote();
            try {
                $quote->getShippingAddress()->setCollectShippingRates(true);
                $quote->setCouponCode('')->collectTotals();
            } catch (Exception $e) {
                $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
                $message[] = $e->getMessage();
            }
            $this->_finishAction();
            $data = $this->_getQuoteData(array(), $orderCreateModel);
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param array $payment
     * @param array $fields
     * @param array $actions
     * @param array $integration
     * @return mixed
     */
    public function placeOrder($quoteData, $payment, $fields, $actions, $integration){
        $data = array();
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if(!empty($quoteData)){
            $orderCreateModel = $this->_startAction($quoteData);
            if(!empty($fields)){
                $orderCreateModel->addQuoteData($fields);
            }
            if(isset($payment) && !empty($payment)){
                if (Mage::getVersion() >= '1.8') {
                    $payment['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
                        | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
                        | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
                        | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;
                }
                $orderCreateModel->getQuote()->getPayment()->addData($payment);
                foreach ($payment as $key => $value){
                    if(in_array($key, array('checks', 'method'))){
                        continue;
                    }
                    $orderCreateModel->getQuote()->getPayment()->setAdditionalInformation($key, $value);
                }
            }
            if($orderCreateModel->getQuote()->getCustomer()){
                $orderCreateModel->getSession()->setCustomerId($orderCreateModel->getQuote()->getCustomer()->getId());
            }
            if (!$this->_getHelper('webpos/config')->isEnableAutoSendEmail('order')) {
                Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_ENABLED, "0");
                $orderCreateModel->setSendConfirmation(false);
            }
            $template_order = Mage::helper('webpos/config')->getWebposEmailTemplate('order');
            if (isset($template_order['guest']) && $template_order['guest'] != '') {
                Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_GUEST_TEMPLATE, $template_order['guest']);
            }
            if (isset($template_order['customer']) && $template_order['customer'] != '') {
                Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_TEMPLATE, $template_order['customer']);
            }

            try {
                $paymentHelper = Mage::helper('webpos/payment');
                if (Mage::helper('core')->isModuleEnabled('Magestore_Webposstripe') && !$paymentHelper->isRetailerPos()) {
                    if (isset($payment['method_data'])) {
                        $methodData = $payment['method_data'];
                        foreach ($methodData as $key=>$method) {
                            if ($method['code'] == 'stripe_integration') {
                                $transactionId = $this->placeOrderStripeCard($payment, $method['base_amount']);
                                if ($transactionId) {
                                    $method['reference_number'] = $transactionId;
                                    $payment['method_data'][$key] = $method;
                                }
                            }
                        }
                    }
                }

                $order = $orderCreateModel
                    ->setIsValidate(true)
                    ->createOrder();
                    
                /* todo: BUG till_id  */
                if (!empty($quoteData['till_id'])) {
                    $order->setWebposTillId($quoteData['till_id']);
                }

                if ($order && $order->getId()) {
                    if(!$order->getCustomerId() ||
                        $order->getCustomerId() == $this->_config->getDefaultCustomerId()) {
                        $order->setRewardpointsEarn(0);
                    }
                    $orderCreateModel->processPaymentAfterCreateOrder($order, $payment);
                    $orderCreateModel->processActionsAfterCreateOrder($order, $actions);
                    $orderCreateModel->processIntegration($order, $integration);
                    if ($this->_getHelper('webpos/config')->isEnableAutoSendEmail('order')) {
                        $orderCreateModel->sendEmail($order);
                    }
                    $eventData = array(
                        'order_id' => $order->getId()
                    );
                    $this->_dispatchEvent(Magestore_Webpos_Api_CheckoutInterface::EVENT_WEBPOS_PLACE_ORDER_AFTER, $eventData);

                    $data = $this->_responseService->getOrderSuccessData($order);
                    $this->_assignQuoteToStaff(false);
                    $payment = $order->getPayment();




                    if ($payment && $payment->getMethod() == Mage::getModel('authorizenet/directpost')->getCode()) {
                        //return json with data.
                        $session = $this->_getDirectPostSession();
                        $session->addCheckoutOrderIncrementId($order->getIncrementId());
                        $session->setLastOrderIncrementId($order->getIncrementId());

                        $requestToPaygate = $payment->getMethodInstance()->generateRequestFromOrder($order);
                        $requestToPaygate->setControllerActionName('webpos');
                        $requestToPaygate->setOrderSendConfirmation(false);
                        $requestToPaygate->setStoreId($orderCreateModel->getQuote()->getStoreId());

                        $adminUrl = Mage::getSingleton('adminhtml/url');
                        if ($adminUrl->useSecretKey()) {
                            $requestToPaygate->setKey(
                                $adminUrl->getSecretKey('authorizenet_directpost_payment', 'redirect')
                            );
                        }
                        $requestData = $requestToPaygate->getData();
                        $requestData['x_relay_url'] = Mage::getUrl('webpos/directpost_payment/response');
                        $data['directpost'] = $requestData;
                    }
                } else {
                    $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
                    $message[] = $order;
                }
            }catch (Exception $e){
                // createOrder throws an empty exception object
                // we grab the messages from the session stack
                $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
                /* Mage_Core_Model_Message_Collection */
                $errors = Mage::getSingleton('adminhtml/session_quote')->getMessages(true);
                foreach ($errors->getErrors() as $error) {
                    /* Mage_Core_Model_Message_Error */
                    $message[] = $error->getText();
                }
                $message[] = $e->getMessage();
            }
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     *
     * @param string $customerId
     * @param Magestore_Webpos_Api_Cart_BuyRequestInterface[] $items
     * @param Magestore_Webpos_Api_Checkout_PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingInterface $shipping
     * @param \Magestore\Webpos\Api\Data\Checkout\ConfigInterface $config
     * @param string $couponCode
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface
     * @throws \Exception
     */
    public function checkPromotion($customerId, $items, $payment, $shipping, $config, $couponCode = "")
    {
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        $checkout = $this->getCheckoutModel();
        $data = $checkout->checkPromotion($customerId, $items, $payment, $shipping, $config, $couponCode);
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param $orderIncrementId
     * @param $customerEmail
     * @return mixed
     */
    public function sendOrderEmail($orderIncrementId, $customerEmail)
    {
        $message = array();
        $checkout = $this->getCheckoutModel();
        $data = $checkout->sendEmail($orderIncrementId, $customerEmail);
        $status = ($data['error'] == true)?Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR:Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if(!empty($data['message'])){
            $message[] = $data['message'];
        };
        return $this->getResponseData($data, $message, $status);
    }

    /**
     *
     * @param string $customerId
     * @param Magestore_Webpos_Api_Cart_BuyRequestInterface[] $items
     * @param Magestore_Webpos_Api_Checkout_PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingInterface $shipping
     * @param \Magestore\Webpos\Api\Data\Checkout\ConfigInterface $config
     * @param string $couponCode
     * @param \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface[] $extensionData
     * @param \Magestore\Webpos\Api\Data\Checkout\SessionDataInterface[] $sessionData
     * @param \Magestore\Webpos\Api\Data\Checkout\Integration\ModuleInterface[] $integration
     * @param string $tillId
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface
     * @throws \Exception
     */
    public function syncOrder($customerId, $items, $payment, $shipping, $config, $couponCode = "", $extensionData, $sessionData, $integration, $tillId)
    {
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        $checkout = $this->getCheckoutModel();
        $order = $checkout->prepareOrder($customerId, $items, $payment, $shipping, $config, $couponCode, $extensionData, $sessionData, $integration, $tillId);
        $data = ($order)?$this->_responseService->getOrderSuccessData($order):array();
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param $sections
     * @param $model
     * @return array
     */
    protected function _getQuoteData($sections, $model){
        $data = array();
        $orderCreateModel = ($model)?$model:$this->getCheckoutModel();
        if(empty($sections) || $sections == Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_INIT || (is_array($sections) && in_array(Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_INIT, $sections))){
            $data[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_INIT] = $orderCreateModel->getQuoteInitData();
        }
        if(empty($sections) || $sections == Magestore_Webpos_Api_Cart_QuoteDataInitInterface::ITEMS || (is_array($sections) && in_array(Magestore_Webpos_Api_Cart_QuoteDataInitInterface::ITEMS, $sections))){
            $data[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::ITEMS] = $this->_responseService->getQuoteItems();
        }
        if(empty($sections) || $sections == Magestore_Webpos_Api_Cart_QuoteDataInitInterface::TOTALS || (is_array($sections) && in_array(Magestore_Webpos_Api_Cart_QuoteDataInitInterface::TOTALS, $sections))){
            $data[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::TOTALS] = $this->_responseService->getTotals();
        }
        if(empty($sections) || $sections == Magestore_Webpos_Api_Cart_QuoteDataInitInterface::SHIPPING || (is_array($sections) && in_array(Magestore_Webpos_Api_Cart_QuoteDataInitInterface::SHIPPING, $sections))){
            $data[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::SHIPPING] = $this->_responseService->getShipping();
        }
        if(empty($sections) || $sections == Magestore_Webpos_Api_Cart_QuoteDataInitInterface::PAYMENT || (is_array($sections) && in_array(Magestore_Webpos_Api_Cart_QuoteDataInitInterface::PAYMENT, $sections))){
            $data[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::PAYMENT] = $this->_responseService->getPayment();
        }
        return $data;
    }

    /**
     * @param $buyRequests
     * @param $orderCreateModel
     */
    protected function _saveCart($buyRequests, $orderCreateModel){
        $newItems = array();
        $updateItems = array();
        foreach ($buyRequests as $request){
            $itemId = $request->getItemId();
            $item = $orderCreateModel->getQuoteItem($itemId);
            if($item){
                $updateItems[$itemId] = $request->getData();
            }else{
                if($request->getIsCustomSale()){
                    $options = $request->getOptions();
                    $taxClassId = isset($options['tax_class_id'])?$options['tax_class_id']:'';
                    $product = $this->_helper->createCustomSaleProduct($taxClassId);
                    if($product instanceof Mage_Catalog_Model_Product){
                        $request->setId($product->getId());
                    }
                }
                $newItems[] = $request->getData();
            }
        }
        if(!empty($newItems)){
            $orderCreateModel->addProducts($newItems);
        }
        if(!empty($updateItems)){
            $orderCreateModel->updateQuoteItems($updateItems);
        }
    }

    /**
     * @param $customerData
     * @param $orderCreateModel
     * @return bool|string
     */
    protected function _setCustomer($customerData, $orderCreateModel){
        $result = true;
        try{
            $customerId = (is_array($customerData) && !empty($customerData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_ID]))
                        ?$customerData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_ID]
                        :$this->_config->getDefaultCustomerId();
            if($customerId){
                $customer = $this->_getModel('customer/customer')->load($customerId);
                if($customer->getId()){
                    $orderCreateModel->getSession()->setCustomerId($customerId);
                    $orderCreateModel->getQuote()->setCustomerId($customerId);
                    $orderCreateModel->getSession()->setCustomer($customer);
                    $orderCreateModel->getQuote()->setCustomer($customer);
                }
            }
            if(isset($customerData) && isset($customerData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::BILLING_ADDRESS])){
                $billingAddress = $customerData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::BILLING_ADDRESS];
                if(isset($billingAddress['region']) && is_array($billingAddress['region']) && isset($billingAddress['region']['region'])){
                    $billingAddress['region'] = $billingAddress['region']['region'];
                }
                $orderCreateModel->setBillingAddress($billingAddress);
            }else{
                $orderCreateModel->useDefaultAddresses('billing');
            }
            if(isset($customerData) && isset($customerData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::SHIPPING_ADDRESS])){
                $shippingAddress = $customerData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::SHIPPING_ADDRESS];
                if(isset($shippingAddress['region']) && is_array($shippingAddress['region']) && isset($shippingAddress['region']['region'])){
                    $shippingAddress['region'] = $shippingAddress['region']['region'];
                }
                $orderCreateModel->setShippingAddress($shippingAddress);
                $orderCreateModel->getShippingAddress()->setCollectShippingRates(true)->setSameAsBilling(0);
                $reCollectTotal = true;
            }else{
                $orderCreateModel->useDefaultAddresses('shipping');
                $reCollectTotal = false;
            }
            if($reCollectTotal){
                $orderCreateModel->getQuote()->collectTotals();
            }
        }catch (Exception $e){
            Mage::log($e->getMessage());
            $result = $e->getMessage();
        }
        return $result;
    }

    /**
     * @param $orderCreateModel
     * @return bool|string
     */
    protected function _setDefaultData($orderCreateModel){
        $result = true;
        try{
            $defaultPaymentMethod = $this->_config->getDefaultPaymentMethod();
            $defaultShippingMethod = $this->_config->getDefaultShippingMethod();
            $payment = array(Magestore_Webpos_Api_Checkout_PaymentInterface::METHOD => $defaultPaymentMethod);
            $paymentMethod = $orderCreateModel->getQuote()->getPayment()->getMethod();
            if(!isset($paymentMethod) && $defaultPaymentMethod){
                try{
                    $orderCreateModel->setPaymentData($payment);
                    $orderCreateModel->getQuote()->getPayment()->addData($payment);
                }catch(Exception $e){
                    $result = $e->getMessage();
                }
            }
            if (!$orderCreateModel->getQuote()->isVirtual()) {
                if (!$orderCreateModel->getQuote()->getShippingAddress()->getShippingMethod() && $defaultShippingMethod) {
                    $orderCreateModel->saveShippingMethod($defaultShippingMethod);
                }
            }
        }catch (Exception $e){
            $result = $e->getMessage();
        }
        return $result;
    }

    /**
     * @param $model
     * @return array
     */
    protected function _getQuoteErrors($model = false){
        $messages = array();
        $orderCreateModel = ($model)?$model:$this->getCheckoutModel();
        $quote = $orderCreateModel->getQuote();
        $items = $quote->getAllVisibleItems();
        if(!empty($items)) {
            $oldSuperMode = $quote->getIsSuperMode();
            $quote->setIsSuperMode(false);
            foreach ($items as $item) {
                $item->setQty($item->getQty());
                $stockItem = $item->getProduct()->getStockItem();
                if ($stockItem instanceof Mage_CatalogInventory_Model_Stock_Item) {
                    $check = $stockItem->checkQuoteItemQty($item->getQty(), $item->getQty(), $item->getQty());
//                    $messages[] = $check->getMessage();
                }
            }
            $quote->setIsSuperMode($oldSuperMode);
        }
        $errors = $quote->getErrors();
        if(!empty($errors)) {
            foreach ($errors as $error) {
                $messages[] = $error->getText();
            }
        }
        return $messages;
    }

    /**
     * @return mixed
     */
    protected function _getDirectPostSession(){
        return Mage::getSingleton('authorizenet/directpost_session');
    }

    /**
     * @param array $quoteData
     * @return void
     * @throws \Exception
     */
    public function checkDiscount($quoteData)
    {
        if (!isset($quoteData['webpos_cart_discount_value']) || $quoteData['webpos_cart_discount_value'] <= 0) {
            $this->getQuote()->setCouponCode('');
        }
    }

    /**
     * @param $payment
     * @param $orderCreateModel
     */
    public function placeOrderStripeCard($payment, $amount)
    {
        $params = array(
            "card" => array(
                "name" => $payment['cc_owner'],
                "number" => $payment['cc_number'],
                "cvc" => $payment['cc_cid'],
                "exp_month" => $payment['cc_exp_month'],
                "exp_year" => $payment['cc_exp_year']
            )
        );

        $token = Mage::getModel('webposstripe/webposstripe')->createToken($params);
        if ($token) {
            return Mage::getModel('webposstripe/webposstripe')->completePayment($token, $amount);
        } else {
            return null;
        }
    }

}