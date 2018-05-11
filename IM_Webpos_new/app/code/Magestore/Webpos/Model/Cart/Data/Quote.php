<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Cart\Data;

/**
 * Class Quote
 * @package Magestore\Webpos\Model\Cart\Data
 */
class Quote extends \Magento\Framework\Model\AbstractExtensibleModel implements \Magestore\Webpos\Api\Data\Cart\QuoteInterface
{
    /**
     * Set quote ID
     *
     * @param int $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData(self::QUOTE_ID, $quoteId);
    }

//    /**
//     * Get quote id
//     *
//     * @return string|null
//     */
//    public function getQuoteIdMask()
//    {
//        return $this->getData(self::QUOTE_ID_MASK);
//    }
//
//    /**
//     * Set quote ID
//     *
//     * @param int $quoteIdMask
//     * @return $this
//     */
//    public function setQuoteIdMask($quoteId)
//    {
//        return $this->setData(self::QUOTE_ID_MASK, $quoteId);
//    }

    /**
     * Get quote id
     *
     * @return string|null
     */
    public function getQuoteId()
    {
        return $this->getData(self::QUOTE_ID);
    }

    /**
     * Set customer ID
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get customer id
     *
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set currency ID
     *
     * @param int $currencyId
     * @return $this
     */
    public function setCurrencyId($currencyId)
    {
        return $this->setData(self::CURRENCY_ID, $currencyId);
    }

    /**
     * Get currency id
     *
     * @return string|null
     */
    public function getCurrencyId()
    {
        return $this->getData(self::CURRENCY_ID);
    }

    /**
     * Set till ID
     *
     * @param int $tillId
     * @return $this
     */
    public function setTillId($tillId)
    {
        return $this->setData(self::TILL_ID, $tillId);
    }

    /**
     * Get till id
     *
     * @return string|null
     */
    public function getTillId()
    {
        return $this->getData(self::TILL_ID);
    }

    /**
     * Get store id
     *
     * @return string|null
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->setData(self::STORE_ID, storeId);
        return $this;
    }

}