<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Integration\Response;

/**
 * Class Rewardpoints
 * @package Magestore\Webpos\Model\Integration\Data
 */
class Rewardpoints extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Magestore\Webpos\Api\Integration\Response\RewardpointsInterface
{
   /**
     * Get used point
     *
     * @api
     * @return string
     */
    public function getUsedPoint()
    {
        return $this->getData(self::USED_POINT);
    }

    /**
     * Set used point
     *
     * @api
     * @param string $usedPoint
     * @return $this
     */
    public function setUsedPoint($usedPoint)
    {
        return $this->setData(self::USED_POINT, $usedPoint);
    }

    /**
     * Get balance
     *
     * @api
     * @return string
     */
    public function getBalance()
    {
        return $this->getData(self::BALANCE);
    }

    /**
     * Set balance
     *
     * @api
     * @param string $balance
     * @return $this
     */
    public function setBalance($balance)
    {
        return $this->setData(self::BALANCE, $balance);
    }

    /**
     * Get max points
     *
     * @api
     * @return string
     */
    public function getMaxPoints()
    {
        return $this->getData(self::MAX_POINTS);
    }

    /**
     * Set max points
     *
     * @api
     * @param string $maxPoints
     * @return $this
     */
    public function setMaxPoints($maxPoints)
    {
        return $this->setData(self::MAX_POINTS, $maxPoints);
    }
}
