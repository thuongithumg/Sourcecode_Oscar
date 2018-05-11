<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Integration\Response;

/**
 * Interface RewardpointsInterface
 * @package Magestore\Webpos\Api\Integration\Response
 */
interface RewardpointsInterface
{

    const USED_POINT = 'used_point';
    const BALANCE = 'balance';
    const MAX_POINTS = 'max_points';

    /**
     * Get used point
     *
     * @api
     * @return string
     */
    public function getUsedPoint();

    /**
     * Set used point
     *
     * @api
     * @param string $usedPoint
     * @return $this
     */
    public function setUsedPoint($usedPoint);

    /**
     * Get balance
     *
     * @api
     * @return string
     */
    public function getBalance();

    /**
     * Set balance
     *
     * @api
     * @param string $balance
     * @return $this
     */
    public function setBalance($balance);

    /**
     * Get max points
     *
     * @api
     * @return string
     */
    public function getMaxPoints();

    /**
     * Set max points
     *
     * @api
     * @param string $maxPoints
     * @return $this
     */
    public function setMaxPoints($maxPoints);

}
