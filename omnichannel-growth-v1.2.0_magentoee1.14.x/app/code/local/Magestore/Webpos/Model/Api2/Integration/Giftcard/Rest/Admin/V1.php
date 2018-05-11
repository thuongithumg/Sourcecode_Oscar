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

class Magestore_Webpos_Model_Api2_Integration_Giftcard_Rest_Admin_V1 extends Magestore_Webpos_Model_Api2_Checkout_Abstract implements Magestore_Webpos_Api_Integration_GiftcardInterface
{
    /**
     * Magestore_Webpos_Model_Api2_Integration_Giftcard_Rest_Admin_V1 constructor.
     */
    public function __construct() {
        $this->_service = $this->_createService('integration_giftcard');
        $this->_helper = Mage::helper('webpos');
    }

    /**
     * Dispatch actions
     */
    public function dispatch()
    {
        $this->_initStore();
        switch ($this->getActionType()) {
            case self::ACTION_GET_BALANCE:
                $customerId = $this->_processRequestParams(self::CHECKOUT_CUSTOMER_ID);
                $items = $this->_getItemsBuyRequest();
                $payment = $this->_getCheckoutPaymentData();
                $shipping = $this->_getCheckoutShippingData();
                $config = $this->_getCheckoutConfigData();
                $couponCode = $this->_processRequestParams(self::CHECKOUT_COUPON_CODE);
                $result = $this->_service->getBalance($customerId, $items, $payment, $shipping, $config, $couponCode);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_REFUND_BALANCE:
                $orderIncrementId = $this->_processRequestParams(self::ORDER_INCREMENT_ID);
                $amount = $this->_processRequestParams(self::AMOUNT);
                $baseAmount = $this->_processRequestParams(self::BASE_AMOUNT);
                $result = $this->_service->refundBalance($orderIncrementId, $amount, $baseAmount);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_APPLY_GIFTCARD:
                $quoteData = $this->_getQuoteInitData();
                $couponCode = $this->_processRequestParams(self::CHECKOUT_COUPON_CODE);
                $amount = $this->_processRequestParams('amount');
                $result = $this->_service->applyGiftcard($quoteData, $couponCode, $amount);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_REMOVE_GIFTCARD:
                $quoteData = $this->_getQuoteInitData();
                $couponCode = $this->_processRequestParams(self::CHECKOUT_COUPON_CODE);
                $result = $this->_service->removeGiftcard($quoteData, $couponCode);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
        }
    }

    /**
     * @return Magestore_Webpos_Api_Cart_QuoteDataInitInterface
     */
    protected function _getQuoteInitData(){
        $quoteData = $this->_processRequestParams(array(
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_ID,Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_ID,Magestore_Webpos_Api_Cart_QuoteDataInitInterface::STORE_ID,Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CURRENCY_ID
        ));
        $quoteDataModel = $this->_getDataModel(self::SCOPE_CART, self::DATA_QUOTE_INIT);
        $quoteDataModel->setData($quoteData);
        return $quoteData;
    }
}
