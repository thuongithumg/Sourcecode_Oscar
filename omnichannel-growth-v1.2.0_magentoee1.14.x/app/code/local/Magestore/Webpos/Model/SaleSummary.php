<?php
/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */



class Magestore_Webpos_Model_SaleSummary extends Mage_Core_Model_Abstract
{
    /*#@+
 * Constants defined for keys of data array
 */
    const GRAND_TOTAL = "grand_total";
    const DISCOUNT_AMOUNT = "discount_amount";
    const TOTAL_REFUNDED = "total_refunded";
    const GIFTVOUCHER_DISCOUNT = "giftvoucher_discount";
    const REWARDPOINTS_DISCOUNT = "rewardpoints_discount";

    /**
     * get sales summary for a shift has $shiftId
     * @param int $shiftId
     * @return mixed
     */
    public function getSaleSummary($shiftId)
    {
        $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
        $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();

        $data = array();

        $orderPaymentCollection = $this->getOrderPaymentCollection($shiftId);
        //$orderPaymentCollection = $this->_orderPaymentCollectionFactory->create();
        //$orderPaymentCollection->addFieldToFilter("shift_id", $shiftId);

        foreach ($orderPaymentCollection as $item) {
            $row = $item->getData();
            $data[] = array("payment_method"=>$row['method'],
                "payment_amount"=> Mage::helper('directory')->currencyConvert($row['base_payment_amount'],$baseCurrencyCode, $currentCurrencyCode),
                "base_payment_amount"=>$row['base_payment_amount'],
                "method_title"=>$row['method_title']
            );
        }
        return $data;
    }

    /**
     * get order payment from webpos_order_payment where order_id
     * from sales_order table and shift_id = $shiftId
     * we group result by payment_method and sum all sale for each payment method
     * @param $shiftId
     * @return mixed
     */
    public function getOrderPaymentCollection($shiftId)
    {
        $orderPaymentCollection = Mage::getModel('webpos/payment_orderPayment')->getCollection();
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
     * for all order that has shift_id=$shiftId
     * @param $shiftId
     * @return array
     */
    public function getZReportSalesSummary($shiftId){
        $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
        $shiftData = Mage::getModel('webpos/shift')->load($shiftId,"shift_id");
        $salesOrderModel = Mage::getModel('sales/order');
        $salesOrderCollection = $salesOrderModel->getCollection();
        $salesOrderCollection->addFieldToFilter("shift_id", $shiftId);

//        $grandTotal = $shiftData["total_sales"];
        $grandTotal = 0;
        $discountAmount = 0;
        $totalRefunded = 0;
        $giftcard = 0;
        $points = 0;

        foreach ($salesOrderCollection as $order){
            $orderCurrencyCode = $order->getOrderCurrencyCode();
            $discountAmount = $discountAmount + Mage::helper('directory')->currencyConvert($order->getDiscountAmount(),$orderCurrencyCode, $currentCurrencyCode);
            $totalRefunded = $totalRefunded + Mage::helper('directory')->currencyConvert($order->getTotalRefunded(),$orderCurrencyCode, $currentCurrencyCode);
            $giftcard += Mage::helper('directory')->currencyConvert($order->getGiftVoucherDiscount(),$orderCurrencyCode, $currentCurrencyCode);
            $points += Mage::helper('directory')->currencyConvert($order->getRewardpointsDiscount(),$orderCurrencyCode, $currentCurrencyCode);
            $grandTotal += Mage::helper('directory')->currencyConvert($order->getGrandTotal(),$orderCurrencyCode, $currentCurrencyCode);
        }

        $data = $this;
        $data->setData(array(
            "grand_total"=>$grandTotal,
            "discount_amount"=>$discountAmount,
            "total_refunded"=>$totalRefunded,
            "giftvoucher_discount" => $giftcard,
            "rewardpoints_discount" => $points
        ));


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