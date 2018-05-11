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

class Magestore_Webpos_Model_Observer_General extends Magestore_Webpos_Model_Observer_Abstract
{
    const TYPE_REFUND = 'refund';
    /**
     * @param $observer
     * @return $this
     */
    public function webposEmptyCartAfter($observer)
    {
        try{
            $checkoutSession = $this->_getModel('checkout/session');
            if ($this->_helper->isRewardPointsEnable()) {
                $checkoutSession->setUsePoint(false);
                $checkoutSession->setRewardSalesRules('use_point', array());
            }
            if ($this->_helper->isGiftCardEnable()) {
                if ($checkoutSession->getUseGiftCard()) {
                    $checkoutSession->setUseGiftCard(null)
                        ->setGiftCodes(null)
                        ->setBaseAmountUsed(null)
                        ->setBaseGiftVoucherDiscount(null)
                        ->setGiftVoucherDiscount(null)
                        ->setCodesBaseDiscount(null)
                        ->setCodesDiscount(null)
                        ->setGiftMaxUseAmount(null);
                }
                if ($checkoutSession->getUseGiftCardCredit()) {
                    $checkoutSession->setUseGiftCardCredit(null)
                        ->setMaxCreditUsed(null)
                        ->setBaseUseGiftCreditAmount(null)
                        ->setUseGiftCreditAmount(null);
                }
            }
        }catch(Exception $e){
            Mage::log($e->getMessage(), null, 'system.log', true);
        }
    }

    /**
     * @param $observer
     * @return $this
     */
    public function webposSaveCartAfter($observer)
    {
        try{
            if ($this->_helper->isRewardPointsEnable()) {

            }
        }catch(Exception $e){
            Mage::log($e->getMessage(), null, 'system.log', true);
        }
    }

    /**
     * @param $observer
     * @return $this
     */
    public function webposSendResponseBefore($observer)
    {
        try{
            $quote = $observer->getData('quote');
            $response = $observer->getData('response');
            $checkoutSession = $this->_getModel('checkout/session');
            if($quote) {
                $quoteCurrency = $quote->getQuoteCurrencyCode();
            } else {
                $quoteCurrency = Mage::app()->getStore()->getCurrentCurrencyCode();
            }
            if ($this->_helper->isRewardPointsEnable()) {
                if($response){
                    $spendingHelper = $this->_getHelper('rewardpoints/calculation_spending');
                    $helperCustomer = $this->_getHelper('rewardpoints/customer');
                    $usedPoint = $spendingHelper->getTotalPointSpent();
                    $data = $response->getResponseData();
                    if(!is_array($data)){
                        $data = array();
                    }
                    $data['rewardpoints']['used_point'] = $usedPoint;
                    if(!$this->_helper->isGuestCustomer($quote)) {
                        $data['rewardpoints']['balance'] = $helperCustomer->getBalance() ? $helperCustomer->getBalance() : 0;
                    }else {
                        $data['rewardpoints']['balance'] = 0;
                    }

                    $minPoints = (int)Mage::getStoreConfig('rewardpoints/spending/redeemable_points', Mage::app()->getStore()->getId());
                    if($minPoints && $minPoints > $data['rewardpoints']['balance']) {
                        $data['rewardpoints']['balance'] = 0;
                    }

                    if($quote){
                        $rule = $spendingHelper->getQuoteRule();
                        if($rule){
                            $maxPoint = $this->getMaxPoint($rule, $quote);
                            $data['rewardpoints']['max_point'] = $maxPoint;
                            $data['rewardpoints']['max_points'] = $maxPoint;
                        }
                    }
                    $response->setResponseData($data);
                }
            }
            if ($this->_helper->isGiftCardEnable()) {
                if($response){
                    $data = $response->getResponseData();
                    if(!is_array($data)){
                        $data = array();
                    }
                    $isUseGiftcard = $checkoutSession->getUseGiftCard();
                    if($isUseGiftcard){
                        $giftcardData = array();
                        $giftcardDataApp = array();
                        $baseAmountUsed = explode(',', $checkoutSession->getBaseAmountUsed());
                        $codes = array_filter(explode(',', $checkoutSession->getGiftCodes()));
                        if(count($codes) > 0){
                            foreach ($codes as $key => $code){
                                $giftVoucher = Mage::getModel('giftvoucher/giftvoucher')->loadByCode($code);
                                $giftVoucherCurrency = $giftVoucher->getCurrency();
                                $giftcardData[$code] = $baseAmountUsed[$key];
                                $amountUsed = Mage::helper('directory')
                                    ->currencyConvert($baseAmountUsed[$key], $giftVoucherCurrency, $quoteCurrency);
                                $balance = Mage::helper('directory')
                                    ->currencyConvert($giftVoucher->getBalance(), $giftVoucherCurrency, $quoteCurrency);
                                $giftcardDataApp = array (
                                                        'code' => $code,
                                                        'amount' => $amountUsed,
                                                        'balance' => $balance
                                                    );
                                $data['giftcard']['used_codes_app'][] = $giftcardDataApp;
                            }
                            $data['giftcard']['used_codes'] = $giftcardData;
                        }
                    }
                    if($quote){
                        $giftcardService = $this->_getModel('magestore_webpos_service_integration_giftcard');
                        $existedCode = $giftcardService->getCustomerExistedGiftCard($quote);
                        $data['giftcard']['existed_codes'] = $existedCode;
                    }
                    $response->setResponseData($data);
                }
            }
            if ($this->_helper->isStoreCreditEnable()) {
                if($quote) {
                    if($quote->getCustomerId()) {
                        $data = $response->getResponseData();
                        if(!is_array($data)){
                            $data = array();
                        }
                        $customercredit = Mage::getModel('customer/customer')->load($quote->getCustomerId());
                        $baseBalance = $customercredit->getCreditValue();
                        $baseCurrency = Mage::app()->getStore()->getBaseCurrencyCode();
                        $balance = Mage::helper('directory')
                            ->currencyConvert($baseBalance, $baseCurrency, $quoteCurrency);
                        $data['storecredit']['base_currency'] = $quoteCurrency;
                        if(!$this->_helper->isGuestCustomer($quote)) {
                            $data['storecredit']['balance'] = $balance;
                        } else {
                            $data['storecredit']['balance'] = 0;
                        }
                        $response->setResponseData($data);
                    }
                }
            }
        }catch(Exception $e){
            Mage::log($e->getMessage(), null, 'system.log', true);
        }
    }

    /**
     * @param $observer
     * @return $this
     */
    public function webposRefundByCashAfter($observer)
    {
        try{
            $creditmemo = $observer->getData('creditmemo');
            if($creditmemo && $creditmemo->getId()){
                $order = $this->_getModel('sales/order')->load($creditmemo->getOrderId());
                $permission = $this->_getHelper('webpos/permission');
                $session = $permission->getCurrentSessionModel();
                $currentShiftId = Mage::helper('webpos/shift')->getCurrentShiftId();
                /** @var Magestore_Webpos_Model_Shift $currentShift */
                $currentShift = Mage::getModel('webpos/shift')->load($currentShiftId, 'shift_id');
                if($session){
                    $transaction = $this->_getModel('webpos/shift_cashtransaction');
                    $transaction->setData(array(
                        Magestore_Webpos_Api_TransactionInterface::STAFF_ID => $session->getData('staff_id'),
                        Magestore_Webpos_Api_TransactionInterface::TILL_ID => $session->getData('current_till_id'),
                        Magestore_Webpos_Api_TransactionInterface::SHIFT_ID => $currentShiftId,
                        Magestore_Webpos_Api_TransactionInterface::ORDER_INCREMENT_ID => 0,
                        Magestore_Webpos_Api_TransactionInterface::TRANSACTION_CURRENCY_CODE => $order->getData('order_currency_code'),
                        Magestore_Webpos_Api_TransactionInterface::BASE_CURRENCY_CODE => $order->getData('base_currency_code'),
                        Magestore_Webpos_Api_TransactionInterface::AMOUNT => -$creditmemo->getGrandTotal(),
                        Magestore_Webpos_Api_TransactionInterface::BASE_AMOUNT => $creditmemo->getBaseGrandTotal(),
                        Magestore_Webpos_Api_TransactionInterface::VALUE => $creditmemo->getGrandTotal(),
                        Magestore_Webpos_Api_TransactionInterface::BALANCE => $currentShift->getBalance() - $creditmemo->getGrandTotal(),
                        Magestore_Webpos_Api_TransactionInterface::BASE_VALUE => $creditmemo->getBaseGrandTotal(),
                        Magestore_Webpos_Api_TransactionInterface::BASE_BALANCE => $currentShift->getBaseBalance() - $creditmemo->getBaseGrandTotal(),
                        Magestore_Webpos_Api_TransactionInterface::TYPE => self::TYPE_REFUND,
                        Magestore_Webpos_Api_TransactionInterface::NOTE => '#'.$creditmemo->getIncrementId().' - '.$this->__('Refund order').' #'.$order->getIncrementId()
                    ));
                    $transaction->save();
                    $currentShift->recalculateData(array('cashTransaction'=>$transaction->getData()));
//                    Mage::helper('webpos/shift')->updateShiftWhenCreateCreditmemo($creditmemo, $currentShiftId);
                }
            }
        }catch(Exception $e){
            Mage::log($e->getMessage(), null, 'system.log', true);
        }
    }

    /**
     * @param $observer
     * @return $this
     */
    public function webposPlaceOrderAfter($observer)
    {
        try{
            $orderId = $observer->getData('order_id');
            if($orderId){
                // Change total paid after place order
                $order = Mage::getModel('sales/order')->load($orderId);
                if($order->getId() && ($order->getData('webpos_change') > 0)){
                    $order->setData('total_paid', (float)$order->getData('total_paid')-(float)$order->getData('webpos_change'));
                    $order->setData('base_total_paid', (float)$order->getData('base_total_paid')-(float)$order->getData('webpos_change'));
                    $order->save();
                }

                $order = $this->_getModel('sales/order')->load($orderId);
                if ($order && $order->getId()) {
                    $payment = $order->getPayment();
                    if ($payment && ($payment->getMethod() == 'pay_payment_instore')) {
                        $transactionId = $payment->getAdditionalInformation('paynl_transaction_id');
                        $payments = $this->_getModel('webpos/payment_orderPayment')->getCollection();
                        $payments->addFieldToFilter('method', 'pay_payment_instore');
                        $payments->addFieldToFilter('order_id', $orderId);
                        if($payments->getSize() > 0){
                            $orderPayment = $payments->getFirstItem();
                            $orderPayment->setData('reference_number', $transactionId);
                            $orderPayment->save();
                        }
                    }
                    $items = $order->getAllVisibleItems();
                    if(!empty($items)){
                        $shoppingCartItems = array();
                        foreach ($items as $item){
                            $buyRequest = $item->getBuyRequest();
                            $isFromShoppingCart = $buyRequest->getData('move_from_shopping_cart');
                            $qty = $buyRequest->getData('shopping_cart_qty');
                            $itemId = $buyRequest->getData('shopping_cart_item_id');
                            if($isFromShoppingCart){
                                $ordered = floatval($item->getData('qty_ordered'));
                                $qty = ($qty < $ordered)?$qty:$ordered;
                                $shoppingCartItems[$itemId] = $qty;
                            }
                        }
                        if(!empty($shoppingCartItems)){
                            $cart = $this->_getModel('sales/quote')->loadByCustomer($order->getCustomerId());
                            if($cart && $cart->getId()){
                                foreach ($shoppingCartItems as $itemId => $qty){
                                    $item = $cart->getItemById($itemId);
                                    $qty = $item->getQty() - $qty;
                                    if($qty > 0){
                                        $item->setQty($qty);
                                    }else{
                                        $cart->removeItem($itemId);
                                    }
                                }
                                $cart->collectTotals()->save();
                            }
                        }
                    }
                }
            }
        }catch(Exception $e){
            Mage::log($e->getMessage(), null, 'system.log', true);
        }
    }

    /**
     * get max point
     *
     * @param array $rule
     * @param array $quote
     *
     * @return float
     */
    public function getMaxPoint($rule, $quote)
    {
        $helperSpend = $this->_getHelper('rewardpoints/block_spend');
        $spendingHelper = $this->_getHelper('rewardpoints/calculation_spending');
        $maxPoints = $helperSpend->getCustomerPoint();
        $pointStep = (int)$rule->getPointsSpended();
        if ($rule->getMaxPointsSpended() && $maxPoints > $rule->getMaxPointsSpended()) {
            $maxPoints = $rule->getMaxPointsSpended();
        }
        if ($maxPoints > $spendingHelper->getRuleMaxPointsForQuote($rule, $quote)) {
            $maxPoints = $spendingHelper->getRuleMaxPointsForQuote($rule, $quote);
        }
        if ($pointStep) {
            $maxPoints = floor($maxPoints / $pointStep) * $pointStep;
        }
        $maxPoints = max(0, $maxPoints);

        return $maxPoints;
    }

}