<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Sales;

interface CreditmemoInterface extends \Magento\Sales\Api\Data\CreditmemoInterface
{
    /*
     *  Refund_points in current creditmemo
     */
    const REFUND_POINTS = 'refund_points';
    
    const REFUND_EARNED_POINTS = 'refund_earned_points';

    /*
     *  Refund by cash in current creditmemo
     */
    const REFUND_BY_CASH = 'refund_by_cash';

    /**
     * Gets refund points in current creditmemo
     *
     * @return int|null refund points.
     */
    public function getRefundPoints();

    /**
     * Sets refund points for current creditmemo
     *
     * @param int|null $refundPoints.
     * @return $this
     */
    public function setRefundPoints($refundPoints);


    /**
     * Gets refund by cash in current creditmemo
     *
     * @return int|null refund points.
     */
    public function getRefundByCash();

    /**
     * Sets refund by cash for current creditmemo
     *
     * @param int|null $refundbyCash.
     * @return $this
     */
    public function setRefundByCash($refundbyCash);

    /**
     * Gets refund earned points in current creditmemo
     *
     * @return int|null refund points.
     */
    public function getRefundEarnedPoints();

    /**
     * Sets refund earned points for current creditmemo
     *
     * @param int|null $refundEarnedPoints.
     * @return $this
     */
    public function setRefundEarnedPoints($refundEarnedPoints);
}
