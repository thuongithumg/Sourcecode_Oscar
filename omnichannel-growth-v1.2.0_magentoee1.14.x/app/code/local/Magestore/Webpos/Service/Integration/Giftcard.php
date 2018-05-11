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

class Magestore_Webpos_Service_Integration_Giftcard extends Magestore_Webpos_Service_Checkout_Checkout
{
    /**
     * @param $customerId
     * @param $items
     * @param $payment
     * @param $shipping
     * @param $config
     * @param $couponCode
     * @return mixed
     */
    public function getBalance($customerId, $items, $payment, $shipping, $config, $couponCode){
        $data = array();
        $message = array();
        if($this->_helper->isGiftCardEnable()){
            $orderCreateModel = $this->getCheckoutModel();
            $result = $orderCreateModel->checkGiftcard($customerId, $items, $payment, $shipping, $config, $couponCode);
            if(isset($result['success'])){
                $data = $result['data'];
                $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
            }elseif(isset($result['error'])){
                $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
                $message[] = $result['message'];
            }
        }else{
            $message[] = $this->__('Gift Voucher module has been disabled or have not been installed yet');
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
        }   
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param $orderIncrementId
     * @param $amount
     * @param $baseAmount
     * @return mixed
     */
    public function refundBalance($orderIncrementId, $amount, $baseAmount){
        $data = array();
        $message = array();
        if($this->_helper->isGiftCardEnable()){
            if($orderIncrementId){
                $order = $this->_getModel('sales/order');
                $order->loadByIncrementId($orderIncrementId);
                if($order->getId()){
                    $helperData = $this->_getHelper('giftvoucher');
                    $customer = $this->_getModel('customer/customer')->load($order->getCustomerId());
                    if ($customer->getId() && $helperData->getGeneralConfig('enablecredit', $order->getStoreId())) {
                        $credit = $this->_getModel('giftvoucher/credit')->load($customer->getId(), 'customer_id');
                        if (!$credit->getId()) {
                            $credit->setCustomerId($customer->getId())
                                ->setCurrency($order->getBaseCurrencyCode())
                                ->setBalance(0);
                        }
                        $refundAmount = $baseAmount;
                        if ($refundAmount) {
                            $creditBalance = $refundAmount;
                            try {
                                $credit->setBalance($credit->getBalance() + $creditBalance)
                                    ->save();

                                if ($order->getOrderCurrencyCode() != $order->getBaseCurrencyCode()) {
                                    $baseCurrency = $this->_getModel('directory/currency')->load($order->getBaseCurrencyCode());
                                    $currentCurrency = $this->_getModel('directory/currency')->load($order->getOrderCurrencyCode());
                                    $currencyBalance = $baseCurrency->convert(round($credit->getBalance(), 4), $currentCurrency);
                                } else {
                                    $currencyBalance = round($credit->getBalance(), 4);
                                }

                                $credithistory = $this->_getModel('giftvoucher/credithistory')->setData($credit->getData());
                                $credithistory->addData(array(
                                    'action' => 'Refund',
                                    'currency_balance' => $currencyBalance,
                                    'order_id' => $order->getId(),
                                    'order_number' => $order->getIncrementId(),
                                    'balance_change' => $amount,
                                    'created_date' => date("Y-m-d H:i:s"),
                                    'currency' => $order->getOrderCurrencyCode(),
                                    'base_amount' => $refundAmount,
                                    'amount' => $amount
                                ))->setId(null)->save();
                            } catch (Exception $e) {
                                Mage::logException($e);
                            }
                        }
                    } else {
                        if ($baseAmount) {
                            $this->_refundOffline($order, $baseAmount);
                        }
                    }
                    $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
                }else{
                    $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
                    $message[] = $this->__('Order not found');
                }
            }else{
                $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
                $message[] = $this->__('Order not found');
            }
        }else{
            $message[] = $this->__('Gift Voucher module has been disabled or have not been installed yet');
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * Process Gift Card data when refund offline
     * @param $order
     * @param $baseGrandTotal
     */
    protected function _refundOffline($order, $baseGrandTotal)
    {
        if ($codes = $order->getGiftCodes()) {
            $codesArray = explode(',', $codes);
            $store = Mage::app()->getStore($order->getStoreId());
            $baseCurrency = $store->getBaseCurrency();
            $baseCurrencyCode = $store->getBaseCurrencyCode();
            foreach ($codesArray as $code) {
                if ($store->roundPrice($baseGrandTotal) == 0) {
                    return;
                }
                $giftVoucher = $this->_getModel('giftvoucher/giftvoucher')->loadByCode($code);
                $history = $this->_getModel('giftvoucher/history');
                $availableDiscount = 0;
                if ($rate = $baseCurrency->getRate($order->getOrderCurrencyCode())) {
                    $availableDiscount = ($history->getTotalSpent($giftVoucher, $order)
                            - $history->getTotalRefund($giftVoucher, $order)) / $rate;
                }
                if ($store->roundPrice($availableDiscount) == 0) {
                    continue;
                }

                if ($availableDiscount < $baseGrandTotal) {
                    $baseGrandTotal = $baseGrandTotal - $availableDiscount;
                } else {
                    $availableDiscount = $baseGrandTotal;
                    $baseGrandTotal = 0;
                }
                $baseCurrency = $this->_getModel('directory/currency')->load($baseCurrencyCode);
                $currentCurrency = $this->_getModel('directory/currency')->load($giftVoucher->getData('currency'));

                $discountRefund = $this->_getHelper('directory')->currencyConvert($availableDiscount, $baseCurrencyCode, $giftVoucher->getData('currency'));


                $discountCurrentRefund = $this->_getHelper('directory')->currencyConvert($availableDiscount, $baseCurrencyCode, $order->getOrderCurrencyCode());
                $balance = $giftVoucher->getBalance() + $discountRefund;
                $baseBalance = $balance * $balance / $baseCurrency->convert($balance, $currentCurrency);
                $currentBalance = $this->_getHelper('directory')->currencyConvert($baseBalance, $baseCurrencyCode, $order->getOrderCurrencyCode());

                if ($giftVoucher->getStatus() == Magestore_Giftvoucher_Model_Status::STATUS_USED) {
                    $giftVoucher->setStatus(Magestore_Giftvoucher_Model_Status::STATUS_ACTIVE);
                }
                $giftVoucher->setData('balance', $balance)->save();

                $history->setData(array(
                    'order_increment_id' => $order->getIncrementId(),
                    'giftvoucher_id' => $giftVoucher->getId(),
                    'created_at' => date("Y-m-d H:i:s"),
                    'action' => Magestore_Giftvoucher_Model_Actions::ACTIONS_REFUND,
                    'amount' => $discountCurrentRefund,
                    'balance' => $currentBalance,
                    'currency' => $order->getOrderCurrencyCode(),
                    'status' => $giftVoucher->getStatus(),
                    'comments' => $this->__('Refund from order %1', $order->getIncrementId()),
                    'customer_id' => $order->getData('customer_id'),
                    'customer_email' => $order->getData('customer_email'),
                ))->save();
            }
        }
    }

    /**
     * @param $quoteData
     * @param $couponCode
     * @return mixed
     */
    public function applyGiftcard($quoteData, $couponCode, $amount = null){
        $data = array();
        $message = array();
        $checkoutSession = $this->_getModel('checkout/session');
        if($this->_helper->isGiftCardEnable()){
            $orderCreateModel = $this->_startAction($quoteData);

//            $curent_store_id = Mage::helper('webpos/permission')->getCurrentSessionModel()->getData('current_store_id');
//            Mage::app()->setCurrentStore($curent_store_id);

            $giftVoucher = $this->_getModel('giftvoucher/giftvoucher')->loadByCode($couponCode);
            if ($giftVoucher->getId() && $giftVoucher->getBaseBalance() > 0
                && $giftVoucher->getStatus() == Magestore_Giftvoucher_Model_Status::STATUS_ACTIVE
            ) {
                $quote = $orderCreateModel->getQuote();
                if ($quote->getCouponCode() && !Mage::helper('giftvoucher')->getGeneralConfig('use_with_coupon')) {
                    $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
                    $message[] = $this->__('A coupon code has been used. You cannot apply gift codes with the coupon to get discount.');
                }else{
                    if (Mage::helper('giftvoucher')->canUseCode($giftVoucher)) {
                        $flag = false;
                        foreach ($quote->getAllItems() as $item) {
                            if ($giftVoucher->getActions()->validate($item)) {
                                $flag = true;
                            }
                        }
                        if ($flag != true || !$giftVoucher->validate($quote->setQuote($quote))) {
                            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
                            $message[] = $this->__('Can’t use this gift code since its conditions haven’t been met.');
                        } else {
                            $checkoutSession->setUseGiftCard(1);
                            $giftVoucher->addToSession($checkoutSession);
                            if($amount) {
                                $giftMaxUseAmount[$couponCode] = $amount;
                                $checkoutSession->setGiftMaxUseAmount(serialize($giftMaxUseAmount));
                            }
                            if ($giftVoucher->getCustomerId() == Mage::getSingleton('customer/session')->getCustomerId()
                                && $giftVoucher->getRecipientName() && $giftVoucher->getRecipientEmail()
                                && $giftVoucher->getCustomerId()
                            ) {
                                $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
                                $message[] = $this->__('Please note that gift code "%s" has been sent to your friend. When using, both you and your friend will share the same balance in the gift code.', Mage::helper('giftvoucher')->getHiddenCode($couponCode));
                            }
                            $quote->setTotalsCollectedFlag(false)->collectTotals();
                            if ($flag == true && $giftVoucher->validate($quote->setQuote($quote))) {
                                $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
                                $message[] = $this->_helper->__('Gift Card "%s" has been applied successfully.', $couponCode);
                            }
                        }
                    }
                }
            }else{
                $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
                $message[] = $this->__('Gift code is invalid');
            }
            $this->_finishAction();
            $data = $this->_getQuoteData(array(), $orderCreateModel);
        }else{
            $checkoutSession->unsGiftCodes();
            $message[] = $this->__('Gift Voucher module has been disabled or have not been installed yet');
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param $quoteData
     * @param $couponCode
     */
    public function removeGiftcard($quoteData, $couponCode)
    {
        $data = array();
        $message = array();
        $checkoutSession = $this->_getModel('checkout/session');
        if($this->_helper->isGiftCardEnable()){
            $orderCreateModel = $this->_startAction($quoteData);
            $codes = $checkoutSession->getGiftCodes();
            $success = false;
            if ($couponCode && $codes) {
                $codesArray = explode(',', $codes);
                foreach ($codesArray as $key => $value) {
                    if ($value == $couponCode) {
                        unset($codesArray[$key]);
                        $success = true;
                        $giftMaxUseAmount = unserialize($checkoutSession->getGiftMaxUseAmount());
                        if (is_array($giftMaxUseAmount) && array_key_exists($couponCode, $giftMaxUseAmount)) {
                            unset($giftMaxUseAmount[$couponCode]);
                            $checkoutSession->setGiftMaxUseAmount(serialize($giftMaxUseAmount));
                        }
                        break;
                    }
                }
            }
            if ($success) {
                $codes = implode(',', $codesArray);
                $checkoutSession->setGiftCodes($codes);
                $message[] = $this->_helper->__('Gift card "%s" has been removed successfully.', $couponCode);
                $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
            } else {
                $message[] = $this->_helper->__('Gift card "%s" not found!', $couponCode);
                $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
            }
            $this->_finishAction();
            $data = $this->_getQuoteData(array(), $orderCreateModel);
        }else{
            $checkoutSession->unsGiftCodes();
            $message[] = $this->__('Gift Voucher module has been disabled or have not been installed yet');
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * get existed gift Card
     *
     * @return array
     */
    public function getCustomerExistedGiftCard($quote = false) {
        $giftCards = array();
        $quote = ($quote instanceof Mage_Sales_Model_Quote)?$quote:$this->getQuote();
        $customerId = $quote->getCustomerId();
        $customer = $quote->getCustomer();
        if($this->_helper->isGiftCardEnable() && $customerId) {
            $collection = $this->_getResource('giftvoucher/customervoucher_collection')
                ->addFieldToFilter('main_table.customer_id', $customerId);
            $voucherTable = $collection->getTable('giftvoucher/giftvoucher');
            $collection->getSelect()
                ->join(array('v' => $voucherTable), 'main_table.voucher_id = v.giftvoucher_id', array('gift_code', 'balance', 'currency', 'conditions_serialized')
                )->where('v.status = ?', Magestore_Giftvoucher_Model_Status::STATUS_ACTIVE)
                ->where("v.recipient_name IS NULL OR v.recipient_name = '' OR (v.customer_id <> '" .
                    $customerId . "' AND v.customer_email <> ?)", $customer->getEmail())
                ->where("v.set_id IS NULL OR v.set_id <= 0 ");
            $giftCards = array();
            $addedCodes = array();
            if ($codes = $this->_getModel('checkout/session')->getGiftCodes()) {
                $addedCodes = explode(',', $codes);
            }
            $conditions = $this->_getModel('giftvoucher/giftvoucher', true)->getConditions();
            $quote->setQuote($quote);
            foreach ($collection as $item) {
                if (in_array($item->getGiftCode(), $addedCodes)) {
                    continue;
                }
                if ($item->getConditionsSerialized()) {
                    $conditionsArr = unserialize($item->getConditionsSerialized());
                    if (!empty($conditionsArr) && is_array($conditionsArr)) {
                        $conditions->setConditions(array())->loadArray($conditionsArr);
                        if (!$conditions->validate($quote)) {
                            continue;
                        }
                    }
                }
                $giftCards[] = array(
                    'code' => $item->getGiftCode(),
                    'balance' => $this->_helper->stripTags($this->getGiftCardBalance($item)),
                    'label' => $item->getGiftCode() . " (". $this->_helper->stripTags($this->getGiftCardBalance($item)).")"
                );
            }
        }
        return $giftCards;
    }

    /**
     * @param $item
     * @return mixed
     */
    public function getGiftCardBalance($item) {
        $cardCurrency = $this->_getModel('directory/currency')->load($item->getCurrency());
        $store = $this->getQuote()->getStore();
        $baseCurrency = $store->getBaseCurrency();
        $currentCurrency = $store->getCurrentCurrency();
        if ($cardCurrency->getCode() == $currentCurrency->getCode()) {
            return $store->formatPrice($item->getBalance());
        }
        if ($cardCurrency->getCode() == $baseCurrency->getCode()) {
            return $store->convertPrice($item->getBalance(), true);
        }
        if ($baseCurrency->convert(100, $cardCurrency)) {
            $amount = $item->getBalance() * $baseCurrency->convert(100, $currentCurrency) / $baseCurrency->convert(100, $cardCurrency);
            return $store->formatPrice($amount);
        }
        return $cardCurrency->format($item->getBalance(), array(), true);
    }
}
