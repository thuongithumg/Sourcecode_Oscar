<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Integration\Rewardpoints;

interface PointInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const REWARD_ID = 'reward_id';
    const CUSTOMER_ID = 'customer_id';
    const POINT_BALANCE = 'point_balance';
    const HOLDING_BALANCE = 'holding_balance';
    const IS_NOTIFICATION = 'is_notification';
    const EXPIRE_NOTIFICATION = 'expire_notification';
    /**#@-*/

    /**
     * Get reward ID
     *
     * @api
     * @return string
     */
    public function getRewardId();

    /**
     * Set reward ID
     *
     * @api
     * @param string $rewardId
     * @return $this
     */
    public function setRewardId($rewardId);

    /**
     * Get customer ID
     *
     * @api
     * @return string
     */
    public function getCustomerId();

    /**
     * Set customer ID
     *
     * @api
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get point balance
     *
     * @api
     * @return string
     */
    public function getPointBalance();

    /**
     * Set point balance
     *
     * @api
     * @param string $pointBalance
     * @return $this
     */
    public function setPointBalance($pointBalance);

    /**
     * Get holding balance
     *
     * @api
     * @return string
     */
    public function getHoldingBalance();

    /**
     * Set holding balance
     *
     * @api
     * @param string $holdingBalance
     * @return $this
     */
    public function setHoldingBalance($holdingBalance);

    /**
     * Get is notification
     *
     * @api
     * @return string
     */
    public function getIsNotification();

    /**
     * Set is notification
     *
     * @api
     * @param string $isNofitication
     * @return $this
     */
    public function setIsNotification($isNofitication);

    /**
     * Get expire notification
     *
     * @api
     * @return string
     */
    public function getExpireNotification();

    /**
     * Set expire notification
     *
     * @api
     * @param string $expireNofitication
     * @return $this
     */
    public function setExpireNotification($expireNofitication);

}
