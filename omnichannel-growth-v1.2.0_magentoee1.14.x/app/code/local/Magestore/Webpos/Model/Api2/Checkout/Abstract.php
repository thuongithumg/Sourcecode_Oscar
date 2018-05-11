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

abstract class Magestore_Webpos_Model_Api2_Checkout_Abstract extends Magestore_Webpos_Model_Api2_Abstract implements Magestore_Webpos_Api_CheckoutInterface
{

    /**
     * Magestore_Webpos_Model_Api2_Checkout_Abstract constructor.
     */
    public function __construct() {
        $this->_service = $this->_createService('checkout_checkout');
        $this->_helper = Mage::helper('webpos');
    }

    /**
     * Dispatch actions
     */
    public function dispatch()
    {
        $this->_initStore();

        switch ($this->getActionType()) {
            case self::ACTION_SAVE_CART:
                $quoteData = $this->_getQuoteInitData();
                $buyRequests = $this->_getItemsBuyRequest();
                $customerData = $this->_processRequestParams(self::CUSTOMER);
                $updateSections = $this->_processRequestParams(self::SECTION);
                $result = $this->_service->saveCart($quoteData, $buyRequests, $customerData, $updateSections);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_REMOVE_CART:
                $quoteData = $this->_getQuoteInitData();
                $result = $this->_service->removeCart($quoteData);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_REMOVE_ITEM:
                $quoteData = $this->_getQuoteInitData();
                $itemId = $this->_processRequestParams(self::ITEM_ID);
                $result = $this->_service->removeItem($quoteData, $itemId);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_GET_CART_DATA:
                $quoteData = $this->_getQuoteInitData();
                $section = $this->_processRequestParams(self::SECTION);
                $result = $this->_service->getCartData($quoteData, $section);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_SAVE_SHIPPING_METHOD:
                $quoteData = $this->_getQuoteInitData();
                $method = $this->_processRequestParams(self::SHIPPING_METHOD);
                $result = $this->_service->saveShippingMethod($quoteData, $method);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_SAVE_PAYMENT_METHOD:
                $quoteData = $this->_getQuoteInitData();
                $method = $this->_processRequestParams(self::PAYMENT_METHOD);
                $result = $this->_service->savePaymentMethod($quoteData, $method);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_PLACE_ORDER:
                $quoteData = $this->_getQuoteInitData();
                $payment = $this->_processRequestParams(self::CHECKOUT_PAYMENT);
                $data = $this->_processRequestParams(self::QUOTE_DATA);
                $actions = $this->_processRequestParams(self::ACTIONS);
                $integration = $this->_getCheckoutIntegrationData();
                $result = $this->_service->placeOrder($quoteData, $payment, $data, $actions, $integration);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_APPLY_COUPON:
                $quoteData = $this->_getQuoteInitData();
                $couponCode = $this->_processRequestParams(self::CHECKOUT_COUPON_CODE);
                $result = $this->_service->applyCoupon($quoteData, $couponCode);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_CANCEL_COUPON:
                $quoteData = $this->_getQuoteInitData();
                $result = $this->_service->cancelCoupon($quoteData);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_SELECT_CUSTOMER:
                $quoteData = $this->_getQuoteInitData();
                $customerData = $this->_processRequestParams(self::CUSTOMER);
                $result = $this->_service->selectCustomer($quoteData, $customerData);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_SAVE_QUOTE_DATA:
                $quoteData = $this->_getQuoteInitData();
                $data = $this->_processRequestParams(self::QUOTE_DATA);
                $result = $this->_service->saveQuoteData($quoteData, $data);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_CHECK_PROMOTION:
                $customerId = $this->_processRequestParams(self::CHECKOUT_CUSTOMER_ID);
                $items = $this->_getItemsBuyRequest();
                $payment = $this->_getCheckoutPaymentData();
                $shipping = $this->_getCheckoutShippingData();
                $config = $this->_getCheckoutConfigData();
                $couponCode = $this->_processRequestParams(self::CHECKOUT_COUPON_CODE);
                $result = $this->_service->checkPromotion($customerId, $items, $payment, $shipping, $config, $couponCode);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_SYNC_ORDER:
                $tillId = $this->_processRequestParams(Magestore_Webpos_Api_Checkout_PaymentItemInterface::TILL_ID);
                $customerId = $this->_processRequestParams(self::CHECKOUT_CUSTOMER_ID);
                $items = $this->_getItemsBuyRequest();
                $payment = $this->_getCheckoutPaymentData();
                $shipping = $this->_getCheckoutShippingData();
                $config = $this->_getCheckoutConfigData();
                $couponCode = $this->_processRequestParams(self::CHECKOUT_COUPON_CODE);
                $extensionData = $this->_processRequestParams(self::CHECKOUT_EXTENSION_DATA);
                $sessionData = $this->_processRequestParams(self::CHECKOUT_SESSION_DATA);
                $integration = $this->_getCheckoutIntegrationData();
                $result = $this->_service->syncOrder($customerId, $items, $payment, $shipping, $config, $couponCode, $extensionData, $sessionData, $integration, $tillId);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_SEND_ORDER_EMAIL:
                $orderIncrementId = $this->_processRequestParams(self::ORDER_INCREMENT_ID);
                $customerEmail = $this->_processRequestParams(self::EMAIL);
                $result = $this->_service->sendOrderEmail($orderIncrementId, $customerEmail);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
        }
    }

    /**
     * @return Magestore_Webpos_Api_Cart_Magestore_Webpos_Api_Cart_QuoteDataInitInterface
     */
    protected function _getQuoteInitData(){
        $quoteData = $this->_processRequestParams(array(
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_ID,Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_ID,
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::STORE_ID,Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CURRENCY_ID,
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::TILL_ID,Magestore_Webpos_Api_Cart_QuoteDataInitInterface::SHIFT_ID
        ));
        $quoteDataModel = $this->_getDataModel(self::SCOPE_CART, self::DATA_QUOTE_INIT);
        $quoteDataModel->setData($quoteData);
        return $quoteData;
    }

    /**
     * @return Magestore_Webpos_Api_Cart_ItemRequestInterface[]
     */
    protected function _getItemsBuyRequest(){
        $buyRequests = array();
        $items = $this->_processRequestParams(self::CHECKOUT_ITEMS);
        if(!empty($items) && is_array($items)){
            foreach ($items as $item){
                $itemRequest = $this->_getDataModel(self::SCOPE_CART, self::DATA_ITEM_REQUEST);
                $itemRequest->setData($item);
                $itemRequest->convertData();
                $buyRequests[] = $itemRequest;
            }
        }
        return $buyRequests;
    }

    /**
     * @return Magestore_Webpos_Api_Checkout_ConfigInterface
     */
    protected function _getCheckoutConfigData(){
        $config =  $this->_getDataModel(self::SCOPE_CHECKOUT, self::DATA_CONFIG);
        $data = $this->_processRequestParams(self::CHECKOUT_CONFIG);
        if(!empty($data) && is_array($data)){
            $config->setData($data);
        }
        return $config;
    }

    /**
     * @return Magestore_Webpos_Api_Checkout_Integration_ModuleInterface[]
     */
    protected function _getCheckoutIntegrationData(){
        $integration = array();
        $extensions = $this->_processRequestParams(self::CHECKOUT_INTEGRATION);
        if(!empty($extensions) && is_array($extensions)){
            foreach ($extensions as $data){
                $extension =  $this->_getDataModel(self::SCOPE_CHECKOUT, self::DATA_INTEGRATION);
                $extension->setData($data);
                $integration[] = $extension;
            }
        }
        return $integration;
    }

    /**
     * @return Magestore_Webpos_Api_Checkout_PaymentInterface
     */
    protected function _getCheckoutPaymentData(){
        $payments = $this->_getDataModel(self::SCOPE_CHECKOUT, self::DATA_PAYMENT);
        $address = $this->_getDataModel(self::SCOPE_CHECKOUT, self::DATA_ADDRESS);
        $paymentParams = $this->_processRequestParams(self::CHECKOUT_PAYMENT);
        if(!empty($paymentParams) && is_array($paymentParams)){
            $payments->setData($paymentParams);
            $payments->setAddress($address->setData($payments->getAddress()));
        }
        return $payments;
    }

    /**
     * @return Magestore_Webpos_Api_Checkout_ShippingInterface
     */
    protected function _getCheckoutShippingData(){
        $shipping = $this->_getDataModel(self::SCOPE_CHECKOUT, self::DATA_SHIPPING);
        $address = $this->_getDataModel(self::SCOPE_CHECKOUT, self::DATA_ADDRESS);
        $shippingParams = $this->_processRequestParams(self::CHECKOUT_SHIPPING);
        if(!empty($shippingParams) && is_array($shippingParams)){
            $shipping->setData($shippingParams);
            $shipping->setAddress($address->setData($shipping->getAddress()));
            $tracks = array();
            $tracksParams = $shipping->getTracks();
            if(count($tracksParams) > 0){
                foreach ($tracksParams as $track) {
                    $trackModel = $this->_getDataModel(self::SCOPE_CHECKOUT, self::DATA_SHIPPING_TRACK);
                    $trackModel->setData($track);
                    $tracks[] = $trackModel;
                }
            }
            $shipping->setTracks($tracks);
        }
        return $shipping;
    }
}
