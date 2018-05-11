<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposPaypal\Cron;

class UpdateOrderPaymentInfo
{
    const WEBPOS_PAYPAL_ACTIVE = 1;
    const WEBPOS_PAYPAL_INACTIVE = 0;

    /**
     * @var \Magestore\WebposPaypal\Api\PaypalServiceInterface
     */
    protected $paypalService;

    /**
     * @var \Magestore\WebposPaypal\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magestore\Webpos\Model\Payment\OrderPaymentFactory
     */
    protected $orderPaymentFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var \Magestore\Webpos\Helper\Order
     */
    protected $helperOrder;

    /**
     * UpdateOrderPaymentInfo constructor.
     * @param \Magestore\WebposPaypal\Helper\Data $helper
     * @param \Magestore\WebposPaypal\Api\PaypalServiceInterface $paypalService
     * @param \Magestore\Webpos\Model\Payment\OrderPaymentFactory $orderPaymentFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magestore\Webpos\Helper\Order $helperOrder
     */
    public function __construct(
        \Magestore\WebposPaypal\Helper\Data $helper,
        \Magestore\WebposPaypal\Api\PaypalServiceInterface $paypalService,
        \Magestore\Webpos\Model\Payment\OrderPaymentFactory $orderPaymentFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magestore\Webpos\Helper\Order $helperOrder
    ) {
        $this->helper = $helper;
        $this->paypalService = $paypalService;
        $this->orderPaymentFactory = $orderPaymentFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
        $this->helperOrder = $helperOrder;
    }

    /**
     * Execute cron to process data
     */
    public function execute()
    {
        $this->disableOtherPayment();
        $items = $this->getRemainingItems();
        if($items) {
            $payments = $items['payments'];
            $orders = $items['orders'];
            if ($orders && $orders->getSize() > 0) {
                foreach ($orders as $order) {
                    $this->checkPaypalInvoiceDataForOrder($order, $payments[$order->getId()]);
                }
            }
        }
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Magestore\Webpos\Model\Payment\OrderPayment $payment
     */
    protected function checkPaypalInvoiceDataForOrder($order, $payment){
        $invoiceId = $order->getData('webpos_paypal_invoice_id');
        if($invoiceId){
            try{
                $invoice = $this->paypalService->getInvoice($invoiceId);
                if($this->paypalService->isInvoicePaid($invoice)){
                    //get paid summary data
                    $paidSummary = $this->paypalService->getInvoicePaidAmount($invoice);
                    $this->processPaidAmount($order, $paidSummary, $payment);
                }
            }catch (\Exception $e){
                $this->helper->addLog($e->getMessage());
            }
        }
    }

    /**
     * @return bool
     */
    protected function getRemainingItems(){
        $ignoreStates = [
            \Magento\Sales\Model\Order::STATE_COMPLETE,
            \Magento\Sales\Model\Order::STATE_CLOSED,
            \Magento\Sales\Model\Order::STATE_CANCELED,
            \Magento\Sales\Model\Order::STATE_HOLDED,
            \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW
        ];
        $orderIds = [];
        $payments = [];
        $orderPayment = $this->orderPaymentFactory->create();
        $paymentCollection = $orderPayment->getCollection()
            ->addFieldToFilter('method', 'paypal_integration')
            ->addFieldToFilter('webpos_paypal_active', self::WEBPOS_PAYPAL_ACTIVE);
        if($paymentCollection->getSize() > 0){
            foreach ($paymentCollection as $payment){
                $orderIds[$payment->getId()] = $payment->getOrderId();
                $payments[$payment->getOrderId()] = $payment;
            }
        }
        if(!empty($orderIds)){
            $orders = $this->orderCollectionFactory->create();
            $orders->addFieldToFilter('state', ['nin' => $ignoreStates])
                ->addFieldToFilter('entity_id', ['in' => array_values($orderIds)])
                ->addFieldToFilter('base_total_due', ['gt' => 0])
                ->addFieldToFilter('webpos_paypal_invoice_id', ['notnull' => true]);
            if($orders->getSize() > 0){
                return ['orders' => $orders, 'payments' => $payments];
            }
        }
        return false;
    }

    /**
     * @param string $amount
     * @param string|null $fromCurrency
     * @param string|null $toCurrency
     * @return float
     */
    protected function currencyConvert($amount, $fromCurrency = null, $toCurrency = null)
    {
        if(!$fromCurrency){
            $fromCurrency = $this->storeManager->getStore()->getBaseCurrency();
        }
        if(!$toCurrency){
            $toCurrency = $this->storeManager->getStore()->getCurrentCurrency();
        }

        if (is_string($fromCurrency)) {
            $rateToBase = $this->currencyFactory->create()->load($fromCurrency)->getAnyRate($this->storeManager->getStore()->getBaseCurrency()->getCode());
        } elseif ($fromCurrency instanceof \Magento\Directory\Model\Currency) {
            $rateToBase = $fromCurrency->getAnyRate($this->storeManager->getStore()->getBaseCurrency()->getCode());
        }
        $rateFromBase = $this->storeManager->getStore()->getBaseCurrency()->getRate($toCurrency);

        if($rateToBase && $rateFromBase){
            $amount = $amount * $rateToBase * $rateFromBase;
        }
        return floatval($amount);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param string|float $amount
     * @param string $currencyCode
     * @return array
     */
    protected function getAmountsForOrder($order, $amount, $currencyCode){
        $data = ['amount' => 0, 'base_amount' => 0];
        $orderBaseCurrencyCode = $order->getBaseCurrencyCode();
        $orderCurrencyCode = $order->getOrderCurrencyCode();
        if($currencyCode == $orderBaseCurrencyCode){
            $data['base_amount'] = floatval($amount);
            $data['amount'] = $this->currencyConvert($amount, $orderBaseCurrencyCode, $orderCurrencyCode);
        }elseif($currencyCode == $orderCurrencyCode){
            $data['amount'] = floatval($amount);
            $data['base_amount'] = $this->currencyConvert($amount, $orderCurrencyCode, $orderBaseCurrencyCode);
        }else{
            $data['amount'] = $this->currencyConvert($amount, $currencyCode, $orderCurrencyCode);
            $data['base_amount'] = $this->currencyConvert($amount, $currencyCode, $orderBaseCurrencyCode);
        }
        return $data;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \PayPal\Api\PaymentSummary $paidSummary
     * @param \Magestore\Webpos\Model\Payment\OrderPayment $payment
     */
    protected function processPaidAmount($order, $paidSummary, $payment){
        $paidAmount = 0;
        $basePaidAmount = 0;
        //calculate summary paid by paypal
        $paypalPaid = $paidSummary->getPaypal();
        if($paypalPaid){
            $paypalPaidAmounts = $this->getAmountsForOrder($order, $paypalPaid->getValue(), $paypalPaid->getCurrency());
            $basePaidAmount += $paypalPaidAmounts['base_amount'];
            $paidAmount += $paypalPaidAmounts['amount'];
        }

        //calculate summary paid by other
        $otherPaid = $paidSummary->getOther();
        if($otherPaid){
            $otherPaidAmounts = $this->getAmountsForOrder($order, $otherPaid->getValue(), $otherPaid->getCurrency());
            $basePaidAmount += $otherPaidAmounts['base_amount'];
            $paidAmount += $otherPaidAmounts['amount'];
        }
        if($paypalPaid || $otherPaid){
            $baseTotalPaid = $basePaidAmount;
            $totalPaid = $paidAmount;
            $orderPayment = $this->orderPaymentFactory->create();
            $paymentCollection = $orderPayment->getCollection()
                ->addFieldToFilter('order_id', $order->getId())
                ->addFieldToFilter('method', ['neq' => 'paypal_integration']);
            if($paymentCollection->getSize() > 0){
                foreach ($paymentCollection as $paidPayment){
                    $basePaidAmount -= floatval($paidPayment->getBasePaymentAmount());
                    $paidAmount -= floatval($paidPayment->getPaymentAmount());
                }
            }

            $payment->setBasePaymentAmount($basePaidAmount);
            $payment->setPaymentAmount($paidAmount);
            $payment->setBaseDisplayAmount($basePaidAmount);
            $payment->setDisplayAmount($paidAmount);

            //save order totals
            if($baseTotalPaid >= $order->getBaseGrandTotal()){
                $payment->setData('webpos_paypal_active', self::WEBPOS_PAYPAL_INACTIVE);
                $order->setBaseTotalPaid($order->getBaseGrandTotal());
                $order->setTotalPaid($order->getGrandTotal());
                $order->save();
                $this->helperOrder->createShipmentAndInvoice($order->getId(), $order, true, false);
            }else{
                $order->setBaseTotalPaid($baseTotalPaid);
                $order->setTotalPaid($totalPaid);
                $order->setBaseTotalDue($order->getBaseGrandTotal() - $order->getBaseTotalPaid());
                $order->setTotalDue($order->getGrandTotal() - $order->getTotalPaid());
                $order->save();
            }
            $payment->save();
        }
    }

    /**
     * Disable other payment payment
     * @return $this
     */
    protected function disableOtherPayment(){
        $orderPayment = $this->orderPaymentFactory->create();
        $paymentCollection = $orderPayment->getCollection()
            ->addFieldToFilter('method', 'paypal_integration')
            ->addFieldToFilter('webpos_paypal_active', self::WEBPOS_PAYPAL_ACTIVE);
        if($paymentCollection->getSize() > 0){
            foreach ($paymentCollection as $payment){
                $orders = $this->orderCollectionFactory->create();
                $order = $orders->addFieldToFilter('entity_id', $payment->getOrderId())
                    ->addFieldToFilter('webpos_paypal_invoice_id', ['null' => true])
                    ->getFirstItem();
                if($order && $order->getId()){
                    $payment->setData('webpos_paypal_active', self::WEBPOS_PAYPAL_INACTIVE);
                    $payment->save();
                }
            }
        }

        $orderPayment = $this->orderPaymentFactory->create();
        $paymentCollection = $orderPayment->getCollection()
            ->addFieldToFilter('method', ['neq' => 'paypal_integration'])
            ->addFieldToFilter('webpos_paypal_active', self::WEBPOS_PAYPAL_ACTIVE);
        if($paymentCollection->getSize() > 0){
            foreach ($paymentCollection as $payment){
                $payment->setData('webpos_paypal_active', self::WEBPOS_PAYPAL_INACTIVE);
                $payment->save();
            }
        }
        return $this;
    }
}
