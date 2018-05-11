<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Sales\Order;

use Magestore\Webpos\Api\Data\Sales\CreditmemoInterface;

class Creditmemo extends \Magento\Sales\Model\Order\Creditmemo implements CreditmemoInterface
{
    /**
     * Gets refund points in current creditmemo
     *
     * @return int|null refund points.
     */
    public function getRefundPoints(){
        return $this->getData(CreditmemoInterface::REFUND_POINTS);
    }

    /**
     * Sets refund points for current creditmemo
     *
     * @param int|null $refundPoints.
     * @return $this
     */
    public function setRefundPoints($refundPoints){
        return $this->setData(CreditmemoInterface::REFUND_POINTS, $refundPoints);
    }

    /**
     * Gets refund points in current creditmemo
     *
     * @return int|null refund points.
     */
    public function getRefundByCash(){
        return $this->getData(CreditmemoInterface::REFUND_BY_CASH);
    }

    /**
     * Sets refund points for current creditmemo
     *
     * @param int|null $refundPoints.
     * @return $this
     */
    public function setRefundByCash($refundByCash){
        return $this->setData(CreditmemoInterface::REFUND_BY_CASH, $refundByCash);
    }

    /**
     * Gets refund earned points in current creditmemo
     *
     * @return int|null refund points.
     */
    public function getRefundEarnedPoints(){
        return $this->getData(CreditmemoInterface::REFUND_EARNED_POINTS);
    }

    /**
     * Sets refund earned points for current creditmemo
     *
     * @param int|null $refundEarnedPoints.
     * @return $this
     */
    public function setRefundEarnedPoints($refundEarnedPoints){
        return $this->setData(CreditmemoInterface::REFUND_EARNED_POINTS, $refundEarnedPoints);
    }
}
