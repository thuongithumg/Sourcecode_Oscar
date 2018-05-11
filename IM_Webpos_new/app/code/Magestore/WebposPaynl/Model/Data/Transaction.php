<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposPaynl\Model\Data;

/**
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Transaction extends \Magento\Framework\Api\AbstractExtensibleObject implements \Magestore\WebposPaynl\Api\Data\TransactionInterface
{
    /**
     * Get quoteId
     *
     * @api
     * @return float|null
     */
    public function getQuoteId(){
        return $this->_get(self::QUOTE_ID);
    }

    /**
     * Set orderId
     *
     * @api
     * @param float $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId){
        return $this->setData(self::QUOTE_ID, $quoteId);
    }

    /**
     * Get currency
     *
     * @api
     * @return string|null
     */
    public function getCurrency(){
        return $this->_get(self::CURRENCY);
    }

    /**
     * Set orderId
     *
     * @api
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency){
        return $this->setData(self::CURRENCY, $currency);
    }

    /**
     * Get amount
     *
     * @api
     * @return float|null
     */
    public function getAmount(){
        return $this->_get(self::AMOUNT);
    }


    /**
     * Set amount
     *
     * @api
     * @param float $total
     * @return $this
     */
    public function setAmount($amount){
        return $this->setData(self::TOTAL, $amount);
    }

    /**
     * Set total
     *
     * @api
     * @param float $total
     * @return $this
     */
    public function setTotal($total){
        return $this->setData(self::TOTAL, $total);
    }

    /**
     * Get total
     *
     * @api
     * @return float|null
     */
    public function getTotal(){
        return $this->_get(self::TOTAL);
    }

    /**
     * Set orderId
     *
     * @api
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get description
     *
     * @api
     * @return string|null
     */
    public function getDescription(){
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * Set bank id
     *
     * @api
     * @param string $bankId
     * @return $this
     */
    public function setBankId($bankId)
    {
        return $this->setData(self::BANK_ID, $bankId);
    }

    /**
     * Get bankId
     *
     * @api
     * @return string|null
     */
    public function getBankId(){
        return $this->_get(self::BANK_ID);
    }

}