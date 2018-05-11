<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Cart\Data;

/**
 * Class QuoteData
 * @package Magestore\Webpos\Model\Cart\Data
 */
class QuoteData extends \Magento\Framework\Model\AbstractExtensibleModel implements \Magestore\Webpos\Api\Data\Cart\QuoteDataInterface
{
    /**
     * Sets is web version
     *
     * @param string $isWebVersion
     * @return $this
     */
    public function setIsWebVersion($isWebVersion)
    {
        return $this->setData(self::IS_WEB_VERSION, $isWebVersion);
    }

    /**
     * Gets is web version
     *
     * @return string.
     */
    public function getIsWebVersion()
    {
        return $this->getData(self::IS_WEB_VERSION);
    }

    /**
     * Set customer note
     *
     * @param int $customerNote
     * @return $this
     */
    public function setCustomerNote($customerNote)
    {
        return $this->setData(self::CUSTOMER_NOTE, $customerNote);
    }

    /**
     * Get customer note
     *
     * @return string|null
     */
    public function getCustomerNote()
    {
        return $this->getData(self::CUSTOMER_NOTE);
    }

    /**
     * Sets webpos cart discount name
     *
     * @param string $discountName
     * @return $this
     */
    public function setWebposCartDiscountName($discountName)
    {
        return $this->setData(self::DISCOUNT_NAME, $discountName);
    }

    /**
     * Gets webpos cart discount name
     *
     * @return string.
     */
    public function getWebposCartDiscountName()
    {
        return $this->getData(self::DISCOUNT_NAME);
    }

    /**
     * Sets webpos cart discount type
     *
     * @param string $discountType
     * @return $this
     */
    public function setWebposCartDiscountType($discountType)
    {
        return $this->setData(self::DISCOUNT_TYPE, $discountType);
    }

    /**
     * Gets webpos cart discount type
     *
     * @return string.
     */
    public function getWebposCartDiscountType()
    {
        return $this->getData(self::DISCOUNT_TYPE);
    }


    /**
     * Sets webpos cart discount value
     *
     * @param string $discountValue
     * @return $this
     */
    public function setWebposCartDiscountValue($discountValue)
    {
        return $this->setData(self::DICOUNT_VALUE, $discountValue);
    }

    /**
     * Gets webpos cart discount value
     *
     * @return string.
     */
    public function getWebposCartDiscountValue()
    {
        return $this->getData(self::DICOUNT_VALUE);
    }
}