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
class UsedCode extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Magestore\Webpos\Api\Data\Integration\Giftcard\UsedCodeInterface
{
    /**
     * Get code
     *
     * @api
     * @return string|null
     */
    public function getCode(){
        return $this->getData(self::CODE);
    }

    /**
     * Get amount
     *
     * @api
     * @return string|null
     */
    public function getAmout(){
        return $this->getData(self::AMOUNT);
    }

    /**
     * Get balance
     *
     * @api
     * @return string|null
     */
    public function getBalance(){
        return $this->getData(self::BALANCE);
    }

    /**
     * Set code
     *
     * @api
     * @param string $code
     * @return $this
     */
    public function setCode($code){
        return $this->setData(self::CODE, $code);
    }

    /**
     * Set amount
     *
     * @api
     * @param string $amount
     * @return $this
     */             
    public function setAmout($amount){
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * Set balance
     *
     * @api
     * @param string $balance
     * @return $this
     */
    public function setBalance($balance){
        return $this->setData(self::CODE, $balance);
    }
}
