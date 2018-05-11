<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Integration\Data;

/**
 * Class Magestore\Webpos\Model\Integration\Data
 *
 */
class StoreCredit extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Magestore\Webpos\Api\Data\Integration\Storecredit\StoreCreditInterface
{
    /**
     * Get credit ID
     *
     * @api
     * @return string
     */
    public function getCreditId(){
        return $this->_get(self::CREDIT_ID);
    }

    /**
     * Set credit ID
     *
     * @api
     * @param string $creditId
     * @return $this
     */
    public function setCreditId($creditId){
        return $this->setData(self::CREDIT_ID, $creditId);
    }

    /**
     * Get customer ID
     *
     * @api
     * @return string
     */
    public function getCustomerId(){
        return $this->_get(self::CUSTOMER_ID);
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
     * Get credit balance
     *
     * @api
     * @return string
     */
    public function getCreditBalance(){
        return $this->_get(self::CREDIT_BALANCE);
    }

    /**
     * Set credit balance
     *
     * @api
     * @param string $creditBalance
     * @return $this
     */
    public function setCreditBalance($creditBalance){
        return $this->setData(self::CREDIT_BALANCE, $creditBalance);
    }
}
