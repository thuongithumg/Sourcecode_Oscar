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

class Magestore_Webpos_Model_Observer_Integration_WebposCreateOrderWithGiftcardAfter extends Magestore_Webpos_Model_Observer_Abstract
{
    /**
     * @param $observer
     * @return $this
     */
    public function execute($observer)
    {
        try{
            if (!$this->_helper->isGiftCardEnable()) {
                return $this;
            }
            $order = $observer->getEvent()->getOrder();
            $data = $observer->getEvent()->getExtensionData();
            if(isset($order) && $order->getId() && $order->getBaseGiftVoucherDiscount() > 0){
                if(count($data) > 0){
                    $orderCurrencyCode = $order->getData('order_currency_code');
                    $baseCurrencyCode = $order->getBaseCurrencyCode();
                    foreach ($data as $code => $baseDiscount){
                        $giftVoucher = $this->_getModel('giftvoucher/giftvoucher')->loadByCode($code);
                        $baseCurrency = $this->_getModel('directory/currency')->load($baseCurrencyCode);
                        $currentCurrency = $this->_getModel('directory/currency')->load($giftVoucher->getData('currency'));

                        $codeDiscount = $this->_getHelper('directory')
                            ->currencyConvert($baseDiscount, $baseCurrencyCode, $giftVoucher->getData('currency'));
                        $codeCurrentDiscount = $this->_getHelper('directory')
                            ->currencyConvert($baseDiscount, $baseCurrencyCode, $orderCurrencyCode);
                        $balance = $giftVoucher->getBalance() - $codeDiscount;
                        if ($balance > 0) {
                            $baseBalance = $balance * $balance / $baseCurrency->convert($balance, $currentCurrency);
                        } else {
                            $baseBalance = 0;
                        }
                        $currentBalance = $this->_getHelper('directory')
                            ->currencyConvert($baseBalance, $baseCurrencyCode, $orderCurrencyCode);
                        $giftVoucher->setData('balance', $balance)->save();
                        if ($order->getData('customer_id') == null) {
                            $customerName = $this->__('Used by Guest');
                        } else {
                            $customerName = $this->_helper->__('Used by %s %s', $order->getData('customer_firstname'), $order->getData('customer_lastname'));
                        }
                        $history = $this->_getModel('giftvoucher/history')->setData(array(
                            'order_increment_id' => $order->getIncrementId(),
                            'giftvoucher_id' => $giftVoucher->getId(),
                            'created_at' => date("Y-m-d H:i:s"),
                            'action' => Magestore_Giftvoucher_Model_Actions::ACTIONS_SPEND_ORDER,
                            'amount' => $codeCurrentDiscount,
                            'balance' => $currentBalance,
                            'currency' => $orderCurrencyCode,
                            'status' => $giftVoucher->getStatus(),
                            'order_amount' => $codeCurrentDiscount,
                            'comments' => $this->_helper->__('Spent on order %s', $order->getIncrementId()),
                            'extra_content' => $customerName,
                            'customer_id' => $order->getData('customer_id'),
                            'customer_email' => $order->getData('customer_email')
                        ));
                        $history->save();

                        // add gift code to customer list
                        if ($order->getCustomerId()) {
                            $collection = $this->_getModel('giftvoucher/customervoucher')->getCollection()
                                ->addFieldToFilter('customer_id', $order->getCustomerId())
                                ->addFieldToFilter('voucher_id', $giftVoucher->getId());
                            if (!$collection->getSize()) {
                                try {
                                    $timeSite = date(
                                        "Y-m-d",
                                        $this->_getModel('core/date')->timestamp(time())
                                    );
                                    $this->_getModel('giftvoucher/customervoucher')
                                        ->setCustomerId($order->getCustomerId())
                                        ->setVoucherId($giftVoucher->getId())
                                        ->setAddedDate($timeSite)
                                        ->save();
                                } catch (Exception $e) {
                                    Mage::log($e->getMessage(), null, 'system.log', true);
                                }
                            }
                        }
                    }
                }
            }
        }catch(Exception $e){
            Mage::log($e->getMessage(), null, 'system.log', true);
        }
    }
}