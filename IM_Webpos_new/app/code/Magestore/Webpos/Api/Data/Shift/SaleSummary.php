<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created by PhpStorm.
 * User: steve
 * Date: 07/06/2016
 * Time: 09:21
 */

namespace Magestore\Webpos\Api\Data\Shift;

/**
 * Interface CashTransactionInterface
 * @package Magestore\Webpos\Api\Data\Shift
 */
interface SaleSummary
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
     *  get grand total
     * @return string|null
     */
    public function getGrandTotal();


    /**
     * Set grand total
     *
     * @param int $grandTotal
     * @return $this
     */
    public function setGrandTotal($grandTotal);

    /**
     *  get discount amount
     * @return string|null
     */
    public function getDiscountAmount();


    /**
     * Set discount amount
     *
     * @param int $discountAmount
     * @return $this
     */
    public function setDiscountAmount($discountAmount);

    /**
     *  get total refunded
     * @return string|null
     */
    public function getTotalRefunded();


    /**
     * set total refunded
     *
     * @param float $totalRefunded
     * @return $this
     */
    public function setTotalRefunded($totalRefunded);

    /**
     *  get giftvoucher discount
     * @return string|null
     */
    public function getGiftvoucherDiscount();


    /**
     * Set giftvoucher discount
     *
     * @param float $giftvoucherDiscount
     * @return $this
     */
    public function setGiftvoucherDiscount($giftvoucherDiscount);

    /**
     *  get rewardpoints discount
     * @return string|null
     */
    public function getRewardpointsDiscount();


    /**
     * Set rewardpoints discount
     *
     * @param float rewardpoints discount
     * @return $this
     */
    public function setRewardpointsDiscount($rewardpointsDiscount);
}