<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created by PhpStorm.
 * User: steve
 * Date: 06/06/2016
 * Time: 13:29
 */

namespace Magestore\Webpos\Model\Shift;

use \Magento\Framework\Model\AbstractModel as AbstractModel;


/**
 * Class Transaction
 * @package Magestore\Webpos\Model\SaleSummary
 */

class SaleSummary extends AbstractModel implements \Magestore\Webpos\Api\Data\Shift\SaleSummary
{
    /** @var \Magestore\Webpos\Model\ResourceModel\Shift\SaleSummary\CollectionFactory  */
    protected $_saleSummaryCollectionFactory;

    /** @var  \Magestore\Webpos\Model\ResourceModel\Payment\OrderPayment\CollectionFactory */
    protected $_orderPaymentCollectionFactory;

    /** @var  \Magento\Sales\Model\OrderFactory */
    protected $_salesOrderFactory;

    /** @var  \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory */
    protected $_salesCreditmemoCollectionFactory;

    /** @var $shiftFactory  \Magestore\Webpos\Model\Shift\ShiftFactory */
    protected $_shiftFactory;

    /** @var  \Magestore\Webpos\Helper\Currency */
    protected $_webposCurrencyHelper;

    /**
     * SaleSummary constructor.
     * @param \Magestore\Webpos\Model\ResourceModel\Shift\SaleSummary\CollectionFactory $saleSummaryCollectionFactory
     * @param \Magestore\Webpos\Model\ResourceModel\Payment\OrderPayment\CollectionFactory $orderPaymentCollectionFactory
     * @param \Magento\Sales\Model\OrderFactory $salesOrderFactory
     * @param \Magento\Sales\Model\Order\CreditmemoFactory $salesCredismemoFactory
     * @param ShiftFactory $shiftFactory
     * @param \Magestore\Webpos\Helper\Currency $webposCurrencyHelper
     */
    public function __construct(
        \Magestore\Webpos\Model\ResourceModel\Shift\SaleSummary\CollectionFactory $saleSummaryCollectionFactory,
        \Magestore\Webpos\Model\ResourceModel\Payment\OrderPayment\CollectionFactory $orderPaymentCollectionFactory,
        \Magento\Sales\Model\OrderFactory $salesOrderFactory,
        \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $salesCreditmemoCollectionFactory,
        \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory,
        \Magestore\Webpos\Helper\Currency $webposCurrencyHelper
    ) {

        $this->_saleSummaryCollectionFactory = $saleSummaryCollectionFactory;
        $this->_orderPaymentCollectionFactory = $orderPaymentCollectionFactory;
        $this->_salesOrderFactory = $salesOrderFactory;
        $this->_salesCreditmemoCollectionFactory = $salesCreditmemoCollectionFactory;
        $this->_shiftFactory = $shiftFactory;
        $this->_webposCurrencyHelper = $webposCurrencyHelper;

    }

    /**
     * get sales summary for a shift has $shiftId
     * @param int $shiftId
     * @return mixed
     */
    public function getSaleSummary($shiftId)
    {
        $currentCurrencyCode = $this->_webposCurrencyHelper->getCurrentCurrencyCode();
        $baseCurrencyCode = $this->_webposCurrencyHelper->getBaseCurrencyCode();

        $data = [];

        $orderPaymentCollection = $this->getOrderPaymentCollection($shiftId);
        //$orderPaymentCollection = $this->_orderPaymentCollectionFactory->create();
        //$orderPaymentCollection->addFieldToFilter("shift_id", $shiftId);

        foreach ($orderPaymentCollection as $item) {
            $row = $item->getData();
            $data[] = array("payment_method"=>$row['method'],
                            "payment_amount"=>$this->_webposCurrencyHelper->currencyConvert($row['base_payment_amount'],$baseCurrencyCode, $currentCurrencyCode),
                            "base_payment_amount"=>$row['base_payment_amount'],
                            "method_title"=>$row['method_title']
                );
        }
        return $data;
    }

    /**
     * get order payment from webpos_order_payment where order_id
     * from sales_order table and webpos_shift_id = $shiftId
     * we group result by payment_method and sum all sale for each payment method
     * @param $shiftId
     * @return mixed
     */
    public function getOrderPaymentCollection($shiftId)
    {
        /** @var \Magestore\Webpos\Model\ResourceModel\Payment\OrderPayment\Collection $orderPaymentCollection */
        $orderPaymentCollection = $this->_orderPaymentCollectionFactory->create();
        $orderPaymentSelect = $orderPaymentCollection->getSelect();

        $orderPaymentSelect->columns('sum(real_amount) as payment_amount');
        $orderPaymentSelect->columns('sum(base_real_amount) as base_payment_amount');
        $orderPaymentSelect->group("method");

        $orderPaymentCollection->addFieldToFilter("shift_id", $shiftId);
        $orderPaymentCollection->addFieldToFilter("payment_amount", array("gt"=> 0));
        return $orderPaymentCollection; 
    }

    /**
     * get Grand Total, Discount Amount and Total Refunded
     * for all order that has webpos_shift_id=$shiftId
     * @param $shiftId
     * @return array
     */
    public function getZReportSalesSummary($shiftId){
        $currentCurrencyCode = $this->_webposCurrencyHelper->getCurrentCurrencyCode();
        $shiftData = $this->_shiftFactory->create()->load($shiftId,"shift_id");
        /** @var \Magento\Sales\Model\Order $salesOrderModel */
        $salesOrderModel = $this->_salesOrderFactory->create();
        $salesOrderCollection = $salesOrderModel->getCollection();
        $salesOrderCollection->addFieldToFilter("webpos_shift_id", $shiftId);

        $grandTotal = $shiftData["total_sales"];
        $discountAmount = 0;
        $totalRefunded = 0;
        $giftcard = 0;
        $points = 0;

        foreach ($salesOrderCollection as $order){
            $orderCurrencyCode = $order->getOrderCurrencyCode();
            $discountAmount = $discountAmount + $this->_webposCurrencyHelper->currencyConvert($order->getDiscountAmount(),$orderCurrencyCode, $currentCurrencyCode);
//            $totalRefunded = $totalRefunded + $this->_webposCurrencyHelper->currencyConvert($order->getTotalRefunded(),$orderCurrencyCode, $currentCurrencyCode);
            $discountAmount -= $this->_webposCurrencyHelper->currencyConvert($order->getGiftVoucherDiscount(),$orderCurrencyCode, $currentCurrencyCode);
            $discountAmount -= $this->_webposCurrencyHelper->currencyConvert($order->getRewardpointsDiscount(),$orderCurrencyCode, $currentCurrencyCode);
        }

        $salesCreditmemoCollection = $this->_salesCreditmemoCollectionFactory->create();
        $salesCreditmemoCollection->addFieldToFilter("webpos_shift_id", $shiftId);
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        foreach ($salesCreditmemoCollection as $creditmemo){
            $totalRefunded += $this->_webposCurrencyHelper->currencyConvert($creditmemo->getGrandTotal(),$creditmemo->getOrderCurrencyCode(), $currentCurrencyCode);
        }

        $data = $this;
        $data->setData([
            "grand_total"=>$grandTotal,
            "discount_amount"=>$discountAmount,
            "total_refunded"=>$totalRefunded,
            "giftvoucher_discount" => $giftcard,
            "rewardpoints_discount" => $points
        ]);
        
        
        return $data;
    }

    /**
     *  get grand total
     * @return string|null
     */
    public function getGrandTotal()
    {
        return $this->getData(self::GRAND_TOTAL);
    }


    /**
     * Set grand total
     *
     * @param int $grandTotal
     * @return $this
     */
    public function setGrandTotal($grandTotal)
    {
        return $this->setData(self::GRAND_TOTAL, $grandTotal);
    }

    /**
     *  get discount amount
     * @return string|null
     */
    public function getDiscountAmount()
    {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }


    /**
     * Set discount amount
     *
     * @param int $discountAmount
     * @return $this
     */
    public function setDiscountAmount($discountAmount)
    {
        return $this->setData(self::DISCOUNT_AMOUNT, $discountAmount);
    }

    /**
     *  get total refunded
     * @return string|null
     */
    public function getTotalRefunded()
    {
        return $this->getData(self::TOTAL_REFUNDED);
    }


    /**
     * set total refunded
     *
     * @param int $totalRefunded
     * @return $this
     */
    public function setTotalRefunded($totalRefunded)
    {
        return $this->setData(self::TOTAL_REFUNDED, $totalRefunded);
    }

    /**
     *  get giftvoucher discount
     * @return string|null
     */
    public function getGiftvoucherDiscount()
    {
        return $this->getData(self::GIFTVOUCHER_DISCOUNT);
    }


    /**
     * Set giftvoucher discount
     *
     * @param int $giftvoucherDiscount
     * @return $this
     */
    public function setGiftvoucherDiscount($giftvoucherDiscount)
    {
        return $this->setData(self::GIFTVOUCHER_DISCOUNT, $giftvoucherDiscount);
    }

    /**
     *  get rewardpoints discount
     * @return string|null
     */
    public function getRewardpointsDiscount()
    {
        return $this->getData(self::REWARDPOINTS_DISCOUNT);
    }


    /**
     * Set rewardpoints discount
     *
     * @param int rewardpoints discount
     * @return $this
     */
    public function setRewardpointsDiscount($rewardpointsDiscount)
    {
        return $this->setData(self::REWARDPOINTS_DISCOUNT, $rewardpointsDiscount);
    }
}