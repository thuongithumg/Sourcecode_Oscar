<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Integration\Giftcard;

/**
 * Store credit api model
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GiftcardRepository implements \Magestore\Webpos\Api\Integration\Giftcard\GiftcardRepositoryInterface
{
    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * GiftcardRepository constructor.
     * @param \Magestore\Webpos\Helper\Data $helperData
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magestore\Webpos\Helper\Data $helperData,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        $this->_helper = $helperData;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_priceCurrency = $priceCurrency;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function refundGiftcardCode($orderId, $amount, $baseAmount){
        $data = [];
        if($orderId){
            $order = $this->_objectManager->create('Magento\Sales\Model\Order');
            $order->loadByIncrementId($orderId);
            if($order->getId()){
                $customer = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($order->getCustomerId());
                $helperData = $this->_objectManager->create('\Magestore\Giftvoucher\Helper\Data');
                $webposHelper = $this->_objectManager->create('\Magestore\Webpos\Helper\Data');
                $isGiftcardRebuild = $webposHelper->isGiftcardRebuild();
                if($isGiftcardRebuild == false) {
                    if ($customer->getId() && $helperData->getGeneralConfig('enablecredit', $order->getStoreId())) {
                        $credit = $this->_objectManager->create('Magestore\Giftvoucher\Model\Credit')
                            ->load($customer->getId(), 'customer_id');
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
                                    $baseCurrency = $this->_objectManager->create('Magento\Directory\Model\Currency')
                                        ->load($order->getBaseCurrencyCode());
                                    $currentCurrency = $this->_objectManager->create('Magento\Directory\Model\Currency')
                                        ->load($order->getOrderCurrencyCode());
                                    $currencyBalance = $baseCurrency
                                        ->convert(round($credit->getBalance(), 4), $currentCurrency);
                                } else {
                                    $currencyBalance = round($credit->getBalance(), 4);
                                }

                                $credithistory = $this->_objectManager->create('Magestore\Giftvoucher\Model\Credithistory')
                                    ->setData($credit->getData());
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
                            } catch (\Exception $e) {
                            }
                        }
                    } else {
                        if ($baseAmount) {
                            $this->_refundOffline($order, $baseAmount);
                        }
                    }
                }
                $data['success'] = true;
            }else{
                $data['message'] = __('Order not found');
                $data['error'] = true;
            }
        }else{
            $data['message'] = __('Order not found');
            $data['error'] = true;
        }
        return \Zend_Json::encode($data);
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
            foreach ($codesArray as $code) {
                if ($this->_priceCurrency->round($baseGrandTotal) == 0) {
                    return;
                }

                $giftVoucher = $this->_objectManager->create('Magestore\Giftvoucher\Model\Giftvoucher')
                    ->loadByCode($code);
                $history = $this->_objectManager->create('Magestore\Giftvoucher\Model\History');
                $baseCurrency = $this->_helper->getStoreManager()->getStore($order->getStoreId())->getBaseCurrency();
                $availableDiscount = 0;
                if ($rate = $baseCurrency->getRate($order->getOrderCurrencyCode())) {
                    $availableDiscount = ($history->getTotalSpent($giftVoucher, $order)
                            - $history->getTotalRefund($giftVoucher, $order)) / $rate;
                }
                if ($this->_priceCurrency->round($availableDiscount) == 0) {
                    continue;
                }

                if ($availableDiscount < $baseGrandTotal) {
                    $baseGrandTotal = $baseGrandTotal - $availableDiscount;
                } else {
                    $availableDiscount = $baseGrandTotal;
                    $baseGrandTotal = 0;
                }
                $baseCurrencyCode = $order->getBaseCurrencyCode();
                $baseCurrency = $this->_objectManager->create('Magento\Directory\Model\Currency')
                    ->load($baseCurrencyCode);
                $currentCurrency = $this->_objectManager->create('Magento\Directory\Model\Currency')
                    ->load($giftVoucher->getData('currency'));

                $discountRefund = $this->_objectManager->create('Magento\Directory\Helper\Data')
                    ->currencyConvert($availableDiscount, $baseCurrencyCode, $giftVoucher->getData('currency'));


                $discountCurrentRefund = $this->_objectManager->create('Magento\Directory\Helper\Data')
                    ->currencyConvert($availableDiscount, $baseCurrencyCode, $order->getOrderCurrencyCode());
                $balance = $giftVoucher->getBalance() + $discountRefund;
                $baseBalance = $balance * $balance / $baseCurrency->convert($balance, $currentCurrency);
                $currentBalance = $this->_objectManager->create('Magento\Directory\Helper\Data')
                    ->currencyConvert($baseBalance, $baseCurrencyCode, $order->getOrderCurrencyCode());

                if ($giftVoucher->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_USED) {
                    $giftVoucher->setStatus(\Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE);
                }
                $giftVoucher->setData('balance', $balance)->save();

                $history->setData(array(
                    'order_increment_id' => $order->getIncrementId(),
                    'giftvoucher_id' => $giftVoucher->getId(),
                    'created_at' => date("Y-m-d H:i:s"),
                    'action' => \Magestore\Giftvoucher\Model\Actions::ACTIONS_REFUND,
                    'amount' => $discountCurrentRefund,
                    'balance' => $currentBalance,
                    'currency' => $order->getOrderCurrencyCode(),
                    'status' => $giftVoucher->getStatus(),
                    'comments' => __('Refund from order %1', $order->getIncrementId()),
                    'customer_id' => $order->getData('customer_id'),
                    'customer_email' => $order->getData('customer_email'),
                ))->save();
            }
        }
    }
}