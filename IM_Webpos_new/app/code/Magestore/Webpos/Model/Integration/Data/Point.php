<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Integration\Data;

/**
 * Class Point
 * @package Magestore\Webpos\Model\Integration\Data
 */
class Point extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Magestore\Webpos\Api\Data\Integration\Rewardpoints\PointInterface
{
    /**
     * Get reward ID
     *
     * @api
     * @return string
     */
    public function getRewardId(){
        return $this->getData(self::REWARD_ID);
    }

    /**
     * Set reward ID
     *
     * @api
     * @param string $rewardId
     * @return $this
     */
    public function setRewardId($rewardId){
        return $this->setData(self::REWARD_ID, $rewardId);
    }

    /**
     * Get customer ID
     *
     * @api
     * @return string
     */
    public function getCustomerId(){
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set customer ID
     *
     * @api
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId){
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get point balance
     *
     * @api
     * @return string
     */
    public function getPointBalance(){
        return $this->getData(self::POINT_BALANCE);
    }

    /**
     * Set point balance
     *
     * @api
     * @param string $pointBalance
     * @return $this
     */
    public function setPointBalance($pointBalance){
        return $this->setData(self::POINT_BALANCE, $pointBalance);
    }

    /**
     * Get holding balance
     *
     * @api
     * @return string
     */
    public function getHoldingBalance(){
        return $this->getData(self::HOLDING_BALANCE);
    }

    /**
     * Set holding balance
     *
     * @api
     * @param string $holdingBalance
     * @return $this
     */
    public function setHoldingBalance($holdingBalance){
        return $this->setData(self::HOLDING_BALANCE, $holdingBalance);
    }

    /**
     * Get is notification
     *
     * @api
     * @return string
     */
    public function getIsNotification(){
        return $this->getData(self::IS_NOTIFICATION);
    }

    /**
     * Set is notification
     *
     * @api
     * @param string $isNofitication
     * @return $this
     */
    public function setIsNotification($isNofitication){
        return $this->setData(self::IS_NOTIFICATION, $isNofitication);
    }

    /**
     * Get expire notification
     *
     * @api
     * @return string
     */
    public function getExpireNotification(){
        return $this->getData(self::EXPIRE_NOTIFICATION);
    }

    /**
     * Set expire notification
     *
     * @api
     * @param string $expireNofitication
     * @return $this
     */
    public function setExpireNotification($expireNofitication){
        return $this->setData(self::EXPIRE_NOTIFICATION, $expireNofitication);
    }
}
